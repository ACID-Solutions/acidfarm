<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\User Module
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Gestion des permissions utilisateurs
 * Les permissions permettre à un utilisateur, groupe ou niveau d'utilisateur d'outrepasser ses droits sur les ACL d'un module
 * Pour connaitre les droits d'un utilisateur sur un module, il faut prendre en compte ce que les ACL de ce module lui permettent, puis y rajouter ses permissions 
 * @package   Acidfarm\User Module
 */
class UserPermission extends AcidModule {
	const TBL_NAME = 'user_permission';
	const TBL_PRIMARY = 'id_user_permission';
	
	/**
	 * Constructeur
	 * @param mixed $init_id
	 */
	public function __construct($init_id=null) {
	  	
		$this->vars['id_user_permission'] = new AcidVarInt(self::modTrad('id_user_permission'),true);
		$this->vars['module'] = new AcidVarString(self::modTrad('module'),50);
		$this->vars['do'] = new AcidVarString(self::modTrad('do'),20);
		$this->vars['type'] = new AcidVarString(self::modTrad('type'),20);
		$this->vars['id'] = new AcidVarString(self::modTrad('id'),10);
		
		$this->config['acl']['default'] = Acid::get('lvl:dev');
		
		parent::__construct($init_id);
	}
	
	/**
	 * (non-PHPdoc)
	 * @param array $tab
	 * @param string $do
	 * @see AcidModuleCore::checkVals()
	 */
	public function checkVals($tab,$do) {
		if ( (!empty($tab['module'])) && (!empty($tab['do'])) && (!empty($tab['type'])) && (isset($tab['id'])) && ($tab['id']!=='') ) {
			$filter = array(
						array('module','=',$tab['module']),
						array('do','=',$tab['do']),
						array('type','=',$tab['type']),
						array('id','=',$tab['id'])
			);
			if (!$this->dbCount($filter)) {
				return parent::checkVals($tab,$do);
			}
		}
		
		AcidSession::tmpSet(static::preKey($do),$tab,100);
		AcidDialog::add('error',Acid::trad('checkvals_error_plur'));
	}
	
	/**
	 * Applique les effets de permissions depuis la base de données
	 */
	public static function setPermissions() {
		if (!Acid::isEmpty('permission_active')) {
			$res = self::dbList();
			foreach ($res as $elt) {	
				if (Acid::exists('includes:'.$elt['module'])) {
					$mod = new $elt['module']();
					$mod->setPermission($elt['do'],$elt['id'],$elt['type']);
				}
			}
		}
	}
	
	/**
	 * Retourne tous les modules touchés par une permission
	 * @return array
	 */
	public static function getModules() {
		$tab = array();
		foreach (array_keys(Acid::get('includes')) as $mod) {
			if (is_callable($mod.'::getPermissions')) {
				$tab[$mod] = $mod;
			}
		}
		
		return $tab;
	}
	
	/**
	 * Retourne les différents type d'élements pouvant être liés à une permission (ex : groupe, user, level etc..)
	 * @return array
	 */
	public static function getTypes() {
		$tab = array();	
		foreach (Acid::get('permission_groups') as $type) {
			$tab[$type] = $type;
		}
		
		return $tab;
	}
	
	/**
	 * Retourne l'initulé de la cible d'une permission en fonction du tableauen entrée
	 * @param array $elt tableau representatif de la permission
	 * @return string
	 */
	public static  function printId($elt) {
		switch ($elt['type']) {
			case 'id_group' :
				$req = "SELECT `name` FROM ".Acid::mod('UserGroup')->tbl()." WHERE `id_user_group` = '".addslashes($elt['id'])."' ";
				$res = AcidDB::query($req)->fetch(PDO::FETCH_ASSOC);
				if (count($res)) {
					return htmlspecialchars($res['name']);
				}
			break;
			
			case 'id_user' :
				$req = "SELECT `username` FROM ".Acid::mod('User')->tbl()." WHERE `id_user` = '".addslashes($elt['id'])."' ";
				$res = AcidDB::query($req)->fetch(PDO::FETCH_ASSOC);
				if (count($res)) {
					return htmlspecialchars($res['username']);
				}
			break;
			
			case 'level' :
				$tab = array_flip(Acid::get('lvl'));
				return isset($tab[$elt['id']]) ? $tab[$elt['id']] : $elt['id']; 
			break;
		}
		
		return $elt['id'];
	}
	
	/**
	 * (non-PHPdoc)
	 * @param $conf
	 * @see AcidModuleCore::printAdminList()
	 */
	public function printAdminList($conf=array()) {
		$this->config['print']['id'] = array('type'=>'func','name'=>'UserPermission::printId','args'=>array('__ELT__'));
		return parent::printAdminList($conf);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see AcidModuleCore::printAdminAdd()
	 */
	public function printAdminAdd() {
		$this->config['admin']['add']['def']['do'] = 'default';
		return parent::printAdminAdd();
	}
	
	/**
	 * (non-PHPdoc)
	 * @param string $do
	 * @see AcidModuleCore::printAdminForm()
	 */
	public function printAdminForm($do) {
		$this->config['admin']['add']['keys'] = 
		$this->config['admin']['update']['keys'] = array('id','type','do','module');
		
		$this->vars['module']->setElts(self::getModules());
		$this->vars['module']->setForm('select');
		
		$this->vars['type']->setElts(self::getTypes());
		$this->vars['type']->setForm('select');
		
		$this->vars['id']->setForm('hidden');
		
		$change_func_base = "$('.id_selector').val(''); $('.id_selector').hide(); $('.'+$('[name=type]').val()).show();";
		$change_func = $change_func_base." $('input[name=id]').val('');";
		$change_func_base .= " $('.'+$('[name=type]').val()).val($('input[name=id]').val());";
		$change = array('onchange'=>$change_func);
		$this->config['admin'][$do]['params']['type'] = $change;
		
		$conf=array('onchange'=>"$('input[name=id]').val(this.value);");
				
		//users
		$res = Acid::mod('User')->dbList();
		$user = array(''=>'-');
		foreach ($res as $val) {
			$user[$val['id_user']] = htmlspecialchars($val['username']);
		}
		$conf['class'] = 'id_selector id_user';
		$user_form = AcidForm::select('id_user','',$user,$conf,1,false);
		
		//groups
		$res = Acid::mod('UserGroup')->dbList();
		$group = array(''=>'-');
		foreach ($res as $val) {
			$group[$val['id_user_group']] = htmlspecialchars($val['name']);
		}
		$conf['class'] = 'id_selector id_group';
		$group_form = AcidForm::select('id_group','',$group,$conf,1,false);
		
		//levels
		$lvl = array_merge(array(''=>'-'),array_flip($GLOBALS['acid']['lvl']));
		$conf['class'] = 'id_selector level';
		$lvl_form = AcidForm::select('id_lvl','',$lvl,$conf,1,false);
		
		$this->config['admin'][$do]['stop']['type'] = ' <span class="type_filter">'.$user_form.'<span>'.
													' <span class="type_filter">'.$group_form.'<span>'.
													' <span class="type_filter">'.$lvl_form.'<span>';
		
		$sample = array('default','list','add','update');
		$my_sample = array();
		
		foreach ($sample as $val) {
			$my_sample[] ='<a href="#" onclick="$(\'input[name=do]\').val(\''.$val.'\'); return false;" >'.$val.'</a>';	
		}
		
		$this->config['admin'][$do]['stop']['do'] = '<span style="margin-left:5px;">('.implode(', ',$my_sample).', etc.)</span>';
		
		$js = 	'<script type="text/javascript">' . "\n" .
				'<!--' . "\n" .
				"	$().ready(function() { ".$change_func_base." });". "\n" .
				'-->' . "\n" .
				'</script>';
		
		return parent::printAdminForm($do) . $js;
	}
}