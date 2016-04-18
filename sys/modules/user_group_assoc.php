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
 * Gère l'association Utilisateur/Groupe d'utilisateur
 * @package   Acidfarm\User Module
 */
class UserGroupAssoc extends AcidModule {
	const TBL_NAME = 'user_group_assoc';
	const TBL_PRIMARY = 'id_user_group_assoc';
	
	/**
	 * Constructeur
	 * @param mixed $init_id
	 */
	public function __construct($init_id=null) {	
		$this->vars['id_user_group_assoc'] = new AcidVarInt(self::modTrad('id_user_group_assoc'),true);
		$this->vars['id_user'] = new AcidVarInt(self::modTrad('id_user'),true);
		$this->vars['id_user_group'] = new AcidVarInt(self::modTrad('id_user_group'),true);
		
		$this->config['acl']['default'] = Acid::get('lvl:dev');
		
		parent::__construct($init_id);
	}
	
	/**
	 * Applique l'association de $id_user aux groupes $groups
	 * Les associations précédentes seront supprimées
	 * @param array $groups
	 * @param int $id_user
	 */
	public static function synchronize($groups,$id_user) {
		AcidDB::query("DELETE FROM ".Acid::mod('UserGroupAssoc')->tbl()." WHERE `id_user`='".$id_user."' ");
		
		foreach ($groups as $g) {
			$assoc = new UserGroupAssoc(array('id_user'=>$id_user,'id_user_group'=>$g));
			$assoc->dbAdd();			
		}
	}
}