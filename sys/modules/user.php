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
					}
					
					if ($go_on) {
						$_POST[User::preKey('do')] = 'update';
						$this->config['acl']['keys']['email'] = User::curLevel();
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
		$allowed = array('change_email','change_password','forget_pass','inscription');
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
	
}
