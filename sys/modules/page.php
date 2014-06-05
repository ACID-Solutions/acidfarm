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

/**
 * Module Page de l'utilisateur
 * @package   User Module
 */
class Page extends AcidModule {
	const TBL_NAME = 'page';
	const TBL_PRIMARY = 'id_page';

	/**
	 * Constructeur
	 * @param mixed $init_id initialisateur
	 */
	public function __construct($init_id=null) {

		$this->vars['id_page'] = new AcidVarInt($this->modTrad('id_page'));

		if ($langs = Acid::get('lang:available')) {
			/*AUTODETECTION DU MULTILINGUE*/
			//commenter cette ligne pour desactiver le multilingue auto
			$have_lang_keys = (count($langs)>1) || Acid::get('lang:use_nav_0');
			//POUR CHAQUE LANGUE
			foreach ($langs as $l) {
				//AUTODETECT
				$ks = !empty($have_lang_keys) ? ('_'.$l) : '';
				$ds = !empty($have_lang_keys) ? (' '.$l) : '';
				//DECLARATION DES VARS
				$this->vars['ident'.$ks] =  new AcidVarString($this->modTrad('ident').$ds);
				$this->vars['title'.$ks] =  new AcidVarString($this->modTrad('title').$ds,50);
				$this->vars['content'.$ks] = new AcidVarTextarea($this->modTrad('content').$ds,80,30);
				//CONFIGURATION
				$this->config['print']['content'.$ks] = array('type'=>'split','size'=>100);
			}
			//CONFIGURATION DU MULTILINGUE DANS LES FORMULAIRES ADMIN
			$this->config['multilingual']['flags']['default']  = !empty($have_lang_keys);
		}

		$this->vars['adate']	= new AcidVarDateTime($this->modTrad('adate'));
		$this->vars['active']	= new AcidVarBool($this->modTrad('active'));


		/*--- CONFIGURATION ---*/

		$this->config['admin']['add']['def'] = array('adate'=>date('Y-m-d H:i:s'));


		return parent::__construct($init_id);
	}


	/**
	 * Initialise une page selon l'identifiant $ident
	 * Retourne l'id de la page en cas de réussite, false sinon.
	 *
	 * @param unknown_type $ident
	 */
	public function init($ident,$lang=null) {
		if ($ident !== null) {
			$this->dbInitSearch(array($this->langKey('ident',$lang)=>$ident));
			return $this->getId();
		}
		return false;
	}

	/**
	 * Retourne l'url d'un page
	 * @param array $vals configuration
	 * @param $page valeur de print_page
	 *
	 * @return string
	 */
	public static function buildUrl($vals=array(),$page=false) {
		$vals =  !is_array($vals) ? array('ident'=>$vals) : $vals;
		$vals['print_page'] = $page;

		return Route::buildUrl(static::checkTbl(),$vals);
	}

	/**
	 * Retourne true si la page peut être créés
	 * @param array $vals informations sur la page à créer
	 * @return bool
	 */
	public function checkAuth($vals,$key=null) {

		$key = $key===null ? $this->langKey('ident') : $key;

		$ident = $vals[$key];
		if (!empty($ident)) {
			$admin_pages = Conf::exist('admin_pages') ? Conf::get('admin_pages') : array();
			return in_array($ident,$admin_pages) ? User::curLevel(Acid::get('lvl:dev')) : true;
		}

		return false;
	}

	/**
	 * Retourne true si la création d'une page d'identifiant désigné par $vals['ident'] est possible
	 * @param array $vals informations sur la page à créer
	 * @return bool
	 */
	public function goodIdent($vals,$key=null) {

		$key = $key===null ? $this->langKey('ident') : $key;

		$ident = $vals[$key];
		$class = get_called_class();
		if (!empty($ident)) {
			if (!in_array($ident,Conf::get('keys:reserved'))) {
				if (preg_match('`^[-/a-z0-9]+$`',$ident)) {
					$current = '';
					if (isset($vals[$this->tblId()]) && $vals[$this->tblId()]) {
						$obj = new $class($vals[$this->tblId()]);
						$current = $obj->get($key);
					}
					if ($ident == $current || !$this->dbCount(array(array($key,'=',$ident)))){
						AcidSession::getInstance()->data['page_form'] = array();
						return true;
					} else {
						AcidDialog::add('banner',Acid::trad('admin_page_ident_exists').' ('.$this->getLabel($key).')');
					}
				} else {
					AcidDialog::add('banner',Acid::trad('admin_page_ident_config').' ('.$this->getLabel($key).')');
				}
			}else {
				AcidDialog::add('banner',Acid::trad('admin_page_ident_reserved_key').' ('.$this->getLabel($key).')');
			}
		} else {
			AcidDialog::add('banner',Acid::trad('admin_page_ident_empty').' ('.$this->getLabel($key).')');
		}


		AcidSession::getInstance()->data['page_form'] = $_POST;
		return false;
	}

	/**
	 *  Traite la procédure de mise à jour d'une page depuis un formulaire.
	 *
	 * @param array $vals
	 * @param mixed $dialog
	 *
	 * @return object | bool
	 */
	public function postUpdate($vals,$dialog=null) {

		$vals['adate'] = date('Y-m-d H:i:s');

		$pass = true;
		$pass_error = array();
		foreach ($this->langKeyDecline('ident') as $lkey) {
			if (!(isset($vals[$lkey]) && $this->goodIdent($vals,$lkey) && $this->checkAuth($vals,$lkey))) {
				$pass = false;
				$pass_error[] = $lkey;
			}

		}

		if ($pass) {
			AcidSession::tmpKill('active_pages');
			return parent::postUpdate($vals,$dialog);
		} else {
			if (!isset($vals[$this->langKey('ident')])) {
				AcidSession::tmpKill('active_pages');
				return parent::postUpdate($vals,$dialog);
			}
			AcidSession::tmpSet(static::preKey('update'),$vals,100);
			AcidDialog::add('banner',Acid::trad('admin_page_update_canceled'));
		}
	}

	/**
	 *  Traite la procédure de l'ajout d'une page depuis un formulaire.
	 *
	 * @param array $vals
	 * @param mixed $dialog
	 *
	 * @return object | bool
	 */
	public function postAdd($vals,$dialog=null) {
		$vals['adate'] = date('Y-m-d H:i:s');
		unset($_POST['next_page']);

		$pass = true;
		$pass_error = array();
		foreach ($this->langKeyDecline('ident') as $lkey) {
			if (!(isset($vals[$lkey]) && $this->goodIdent($vals,$lkey) && $this->checkAuth($vals,$lkey))) {
				$pass = false;
				$pass_error[] = $lkey;
			}

		}

		if ($pass) {
			if ($res = parent::postAdd($vals,$dialog)) {
				 AcidSession::tmpKill('active_pages');
				 $_POST['next_page'] = AcidUrl::build(array($this->preKey('do')=>'update', $this->preKey('id')=>$res->getId()));
			}
			return $res;
		} else {
			AcidSession::tmpSet(static::preKey('add'),$vals,100);
			AcidDialog::add('banner',Acid::trad('admin_page_update_canceled'));
		}
	}

	/**
	 *  Traite la procédure de suppression d'une page depuis un formulaire.
	 *
	 * @param array $id
	 * @param mixed $dialog
	 *
	 * @return object | bool
	 */
	public function postRemove($id=null,$dialog=null) {
		if (!User::curLevel(Acid::get('lvl:dev'))) {
			$admin_pages = Conf::exist('admin_pages') ? Conf::get('admin_pages') : array();
			$count = self::dbCount(array(array('id_page','=',$id),array('ident','NOT IN',$admin_pages)));

			if (!$count) {
				return false;
			}
		}

		parent::postRemove($id,$dialog);
	}

	/**
	 * Retourne une portion de code html correspondant à la page
	 * @return string
	 */
	public function printPreview() {
		$content = '';
		$cur = $this->getAdminCurNav();

		if (isset($cur[$this->preKey('id')])) {
			if ($obj = new Page($cur[$this->preKey('id')])) {
				$content .= '<a href="'.AcidUrl::build(array($this->preKey('do')=>'update')).'">'.Acid::trad('admin_page_update_page').'</a>' .
							'<div class="admin_preview">' .
								$obj->printPage().
							'</div>' .
							'<a href="'.AcidUrl::build(array($this->preKey('do')=>'update')).'">'.Acid::trad('admin_page_update_page').'</a>';
			}
		} else {
			$content .= Acid::trad('admin_page_choose_page');
		}

		return $content;
	}

	/**
	 * (non-PHPdoc)
	 * @param array $config
	 * @see AcidModuleCore::printAdminInterface()
	 */
	public function printAdminInterface($config=null) {

		$do = $this->preKey('do');

		$controller = array('list','update','add','print','search');
		$menu = array();

		$config = ($config===null) ? array('onglets'=>$menu,'controller'=>$controller) : $config;

		/*
		$controller['view'] = array(array('$','this','printPreview'),array());
		$menu = array(
					array('url'=>AcidUrl::build(array($do=>'list')),'selector'=>array($do=>'list'),'name'=>Acid::trad('admin_onglet_list')),
					array('url'=>AcidUrl::build(array($do=>'add')),'name'=>Acid::trad('admin_onglet_add'))
				);
		*/

		/*
		$this->config['onglets']['default'] = 'false';
		*/

		return parent::printAdminInterface(array('onglets'=>$menu,'controller'=>$controller));
	}

	/**
	 * (non-PHPdoc)
	 * @param array $conf
	 * @see AcidModuleCore::printAdminList()
	 */
	public function printAdminList($conf=array()) {
		$this->config['admin']['list']['keys'] = array('id_page',$this->langKey('title'),$this->langKey('ident'),$this->langKey('content'),'active');
		$this->config['admin']['list']['limit'] = 50;
		$this->config['admin']['list']['order'] = array('()'=>'LOWER('.$this->langKey('title').')');

		//$this->config['print']['active']= array('type'=>'bool');
		$this->config['print']['active']= array('type'=>'toggle','ajax'=>true);

		$this->config['admin']['list']['actions'] = array('print','update','delete');

		/*
		$other = array(
				'link'=>AcidUrl::build(array($this->preKey('do')=>'view')),
				'image'=>$GLOBALS['acid']['url']['img'].'admin/btn_afficher.png',
				'title'=>'Preview',
				'click'=>null,
				'key'=>'other'
			);
		$this->config['admin']['list']['actions'][] = $other;
		$this->config['admin']['list']['actions_func']['update'] = array('name'=>'is_array','args'=>array('__ELT__'));
		$this->config['admin']['list']['actions_func']['other'] = array('name'=>'rand','args'=>array(0,1));
		*/

		/*
		$this->config['admin']['list']['disable_actions'] = true;
		*/

		if (!User::curLevel($GLOBALS['acid']['lvl']['dev'])) {
			$admin_pages = Conf::exist('admin_pages') ? Conf::get('admin_pages') : array();
			$filter = count($admin_pages)? array(array($this->langKey('title',Acid::get('lang:default')),'NOT IN',$admin_pages)) : array();
		}else{
			$filter = array();
		}

		return parent::printAdminList(array('filter'=>$filter));
	}

	/**
	 * (non-PHPdoc)
	 * @param string $do
	 * @see AcidModuleCore::printAdminForm()
	 */
	public function printAdminForm($do) {

		foreach ($this->langKeyDecline('title') as $lk) {
			$this->config['admin'][$do]['params'][$lk] = array('class'=>'head_field');
		}

		$this->config['admin'][$do]['keys'] = array_merge($this->langKeyDecline('title'),$this->langKeyDecline('ident'),$this->langKeyDecline('content'));

		return parent::printAdminForm($do);
	}

	/**
	 * (non-PHPdoc)
	 * @param object $form AcidForm
	 * @param string $do
	 * @see AcidModuleCore::printAdminFormStop()
	 */
	public function printAdminFormStop(&$form, $do) {

		$forms = '<div class="form_subline">' . "\n" .
				 '	<div class="form_subline_elt first">'.$this->getLabel('adate').' '.$this->getVarForm('adate') . '</div>' . "\n" .
				 '	<div class="form_subline_elt">'.$this->getLabel('active').' '.$this->getVarForm('active') . '</div>' . "\n" .
				 '	<div class="clear"></div>' . "\n" .
				 '</div>';

		$form->addFreeText('',$forms);

		parent::printAdminFormStop($form,$do);
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidPage::printPage()
	 */
	public function printPage() {

			$v = array(	);
			return Acid::tpl('pages/page.tpl',$v,$this);
	}


}