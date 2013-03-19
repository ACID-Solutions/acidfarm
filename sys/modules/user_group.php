<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   User Module
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Gestion des groupes utilisateurs
 * @package   User Module
 */
class UserGroup extends AcidModule {
	
	const TBL_NAME = 'user_group';
	const TBL_PRIMARY = 'id_user_group';
	
	/**
	 * Constructeur
	 * @param mixed $init_id
	 * @return boolean
	 */
	public function __construct($init_id=null) {
		$this->vars['id_user_group'] = new AcidVarInt(self::modTrad('id_user_group'),true);
		$this->vars['name'] = new AcidVarString(self::modTrad('name'));
		
		$this->config['acl']['default'] = Acid::get('lvl:dev');
		$res = parent::__construct($init_id);
		
		return $res;
	}
	
	/**
	 * (non-PHPdoc)
	 * @param string $id
	 * @param mixed $dialog
	 * @see AcidModuleCore::postRemove()
	 */
	public function postRemove($id=null,$dialog=null) {
		if ($group = parent::postRemove($id,$dialog)) {
			AcidDB::query("DELETE FROM ".Acid::mod('UserGroupAssoc')->tbl()." WHERE `id_user_group`='".$group->getId()."' ");
		}
	}
	
	
	/**
	 * Retourn la liste des groupes sous forme de checkbox HTML
	 * @param array $selected liste des éléments selectionnés
	 * @return string
	 */
	public static function getCheckBox($selected=array()) {
		$tab = self::getAssoc('name');
		
		$html = '';
		if (count($tab)) {
			foreach ($tab as $k=>$v) {
				$sel = in_array($k,$selected);
				$html.= '<span class="user_group">'.AcidForm::checkbox('group[]',$k,$sel,htmlspecialchars($v)).'</span>';
			}
		}
		
		return $html;
	}
	
}