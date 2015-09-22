<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   User Module
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


Acid::load('modules/user.php');

/**
 * Gestion des utilisateurs du site
 * @package   User Module
 */
class User extends AcidUser {
	const TBL_NAME = 'user';
	const TBL_PRIMARY = 'id_user';

	/**
	 * Constructeur
	 * @param mixed $init_id
	 * @return Ambigous <object, boolean>
	 */
	public function __construct($init_id=null) {

		$success = parent::__construct(Acid::get('path:files').'users/',$init_id);

		$this->config['acl']['default'] = Acid::get('lvl:dev');

		$this->config['identification'] = array('username');	//	array('username', 'email', ['alias']..)

		$this->config['print']['date_creation'] = array('type'=>'date');
		$this->config['print']['date_activation'] = array('type'=>'date');
		$this->config['print']['date_deactivation'] = array('type'=>'date');

		$this->config['print']['email'] = array('type'=>'mailto');
		$this->config['print']['image_0'] = array('type'=>'img','size'=>'small');
		$this->config['print']['id_group'] = array('type'=>'func','name'=>'User::printUserGroups','args'=>array('__ELT__'));

		return $success;

	}

	/**
	 * (non-PHPdoc)
	 * @see AcidUser::exePost()
	 */
	public function exePost() {

		//Level control
		if (!User::curLevel(Acid::get('lvl:dev'))) {
			if (isset($_POST['level']) && $_POST['level']==Acid::get('lvl:dev')) {
				unset($_POST['level']);
			}
		}

		//Self update
		if ( (isset($_POST[User::preKey('do')])) && ($_POST[User::preKey('do')]=='self_update') ) {

			if (!empty($_POST['id_user'])) {
				$user = AcidSession::get('user');

				if ($_POST['id_user'] == $user['id_user']) {

					$go_on = true;

					//checking for free email
					if ( (isset($_POST['email'])) && ($_POST['email'] != $user['email']) ) {
						$mail_count = self::dbCount(array(array('email','=',$_POST['email'])));

						if ($mail_count) {
							$go_on = false;
							AcidDialog::add('error',Acid::trad('user_bad_email_exists'));
						}

						$this->config['acl']['keys']['email'] = User::curLevel();
					}

					if ($go_on) {

						$this->config['acl']['keys']['id_user'] = User::curUser()->getId();

						foreach ($this->config['identification'] as $identkey) {
							$this->config['acl']['keys'][$identkey] = User::curLevel();
							if (!isset($_POST[$identkey])) {
								$_POST[$identkey] = User::curValue($identkey);
							}
						}

						$_POST[User::preKey('do')] = 'update';
					}

				}
			}

		}

		return parent::exePost();

	}

	/**
	 * Processus de création/gestion de l'utilisateur.
	 */
	public static function exeUser() {
		$allowed = array('send_mail_confirmation','change_email','change_password','forget_pass','inscription');
		if (isset($_POST['connexion_do']) && in_array($_POST['connexion_do'],$allowed)) {
			parent::exeUser();
		}
	}

	/**
	 * Processus de traitement des groupes de l'utilisateur
	 * @param array $groups
	 */
	public function exeGroup($groups=array()) {
		$u_groups = $this->getGroups();

		sort($groups);
		sort($u_groups);

		if ($groups != $u_groups) {
			UserGroupAssoc::synchronize($groups,$this->getId());
		}
	}

	/**
	 * (non-PHPdoc)
	 * @param array vals
	 * @param mixed $dialog
	 * @see AcidUser::postUpdate()
	 */
	public function postUpdate($vals,$dialog=null) {
		if ($user = parent::postUpdate($vals,$dialog)) {
			$groups = isset($vals['group']) ? $vals['group'] : array();
			$user->exeGroup($groups);

			return $user;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @param array vals
	 * @param mixed $dialog
	 * @see AcidUser::postAdd()
	 */
	public function postAdd($vals,$dialog=null) {
		if ($user = parent::postAdd($vals,$dialog)) {
			$groups = isset($vals['group']) ? $vals['group'] : array();
			$user->exeGroup($groups);

			return $user;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @param int $id
	 * @param mixed $dialog
	 * @see AcidUser::postRemove()
	 */
	public function postRemove($id=null,$dialog=null) {
		if ($user = parent::postRemove($id,$dialog)) {
			AcidDB::query("DELETE FROM ".Acid::mod('UserGroupAssoc')->tbl()." WHERE `id_user`='".$user->getId()."' ");

			return $user;
		}
	}

	/**
	 * Préparation du module pour les différents affichage/formulaires
	 * @param string $do Vue en cours
	 * @param array $conf Configuration
	 */
	public function printAdminConfigure($do='default',$conf=array()) {
		if ($sess_form = AcidSession::tmpGet(self::preKey($do))) {
			unset($sess_form['password']);
			AcidSession::tmpSet(self::preKey($do),$sess_form);
		}

		//Décommenter pour simplifier la liste des accès utilisateur
		//$filter_levels = array(Acid::get('lvl:member'),Acid::get('lvl:admin'),Acid::get('lvl:dev'));

		if (!empty($filter_levels)) {

			$levels = array();

			//Création du tableau associatif du formulaire
			foreach($filter_levels as $num) {
				$levels[$num] = Acid::get('user:levels:'.$num);
			}

			//On autorise le level dev qu'à un dev
			if (!User::curLevel(Acid::get('lvl:dev'))) {
				unset($levels[Acid::get('lvl:dev')]);
			}

			//Si l'accès n'est pas dans la liste filtrée, on l'ajoute
			if ($this->get('level') && !isset($levels[$this->get('level')])) {
				$levels[$this->get('level')] = Acid::get('user:levels:'.$this->get('level'));
			}

			//On applique le filtre les accès utilisateur
			$this->vars['level']->setElts($levels);
		}

		//des champs uniquement informatifs
		$this->vars['date_activation']->setForm('info');
		$this->vars['date_deactivation']->setForm('info');
		$this->vars['date_connexion']->setForm('info');
		$this->vars['date_creation']->setForm('info');
		$this->vars['ip']->setForm('info');

		$this->config['admin']['list']['keys_excluded'] = array('date_activation','date_deactivation','date_connexion');

	}

	/**
	 * Formulaire HTML de changement d'email
	 * @return string
	 */
	public static function printAdminEmailChange() {
		$user = AcidSession::get('user');
		$u = new User($user['id_user']);

		return Acid::tpl('admin/admin-email-form.tpl',array(),$u);
	}

	/**
	 * Formulaire HTML de gestion utilistateur
	 * @return string
	 */
	public static function printAdminUserForms() {
		return Acid::tpl('admin/admin-user-form.tpl',array(),Acid::mod('User'));
	}

	/**
	 * (non-PHPdoc)
	 * @param string $do
	 * @see AcidModuleCore::printAdminForm()
	 */
	public function printAdminForm($do) {
		$this->vars['id_group']->setForm('free',array('free_value'=>Acid::mod('UserGroup')->getCheckBox($this->getGroups())));
		return parent::printAdminForm($do);
	}

	/**
	 * Retourn l'espace Utilisateur en HTML
	 * @return string
	 */
	public static function userPage() {
		Acid::mod('Page');
		$my_page = new Page();
		$my_page->init('espace-pro');

		$vars = array('page'=> $my_page);

		return	Acid::tpl('modules/user/user-page.tpl',$vars,Acid::mod('User'));
	}

	/**
	 * Retourne le niveau d'un utilisateur après sont activation
	 */
	public static function getLevelNextActivation() {
	    return $GLOBALS['acid']['lvl']['registred'];
	}

	/**
	 * Retourne les groupes d'un utilisateur sous forme de chaine
	 * @param mixed $elts identifiant de l'utilisateur
	 * @return string
	 */
	public static function printUserGroups($elts) {
		$user = new User($elts);
		$groups = $user->getGroups();

		$tab =array();

		$res = Acid::mod('UserGroup')->dbList(array(array('id_user_group','IN',$groups)));
		foreach ($res as $group) {
			$tab[] = htmlspecialchars($group['name']);
		}
		return implode(', ',$tab);
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidUser::getGroups()
	 */
	public function getGroups() {
		$tab = array();

		if ($this->get('id_group')) {
			$tab[] = $this->get('id_group');
		}

		$res = Acid::mod('UserGroupAssoc')->dbList(array(array('id_user','=',$this->getId())));
		if (count($res)) {
			foreach ($res as $elt) {
				$tab[] = $elt['id_user_group'];
			}
		}

		return $tab;
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidUser::bodyMail()
	 */
	public function bodyMail($tpl,$vars=array(),$object=null) {

		if ($this->get('lang')) {
			Lang::switchTo($this->get('lang'));
		}

		$res = parent::bodyMail($tpl,$vars,$object);

		if ($this->get('lang')) {
			Lang::rollback();
		}

		return $res;
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidUser::subjectMail()
	 */
	public function subjectMail($subject,$replace=array(),$staff=false) {

		$chang_lang = !$staff ? ($this->get('lang')) : (Conf::get('lang:admin') && (Acid::get('lang:current')!=Conf::get('lang:admin')));

		if ($chang_lang) {
			$lang = !$staff ? $this->get('lang') : Conf::get('lang:admin');
			Lang::switchTo($lang);
		}

		$res = parent::subjectMail($subject,$replace,$staff);

		if ($chang_lang) {
			Lang::rollback();
		}

		return $res;
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidUser::bodyMailStaff()
	 */
	public static function bodyMailStaff($tpl,$vars,$object) {

		$chang_lang = Conf::get('lang:admin') && (Acid::get('lang:current')!=Conf::get('lang:admin'));

		if ($chang_lang) {
			Lang::switchTo(Conf::get('lang:admin'));
		}

		$res = parent::bodyMailStaff($tpl,$vars,$object);

		if ($chang_lang) {
			Lang::rollback();
		}
		return $res;
	}


}
