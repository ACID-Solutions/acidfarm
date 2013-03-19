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
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Permet à l'utilisateur d'altérer tous les AcidModule 
 * @package   User Module
 */
abstract class AcidModule extends AcidModuleCore { 
	
	/**
	 * Retourne l'url du module
	 * @param array $vals
	 * @return string
	 */
	public static function buildUrl($vals=array()) {
		return Route::buildUrl(static::checkTbl(),$vals);
	}
	
	/**
	 * Retourn l'url associé à l'objet
	 * @return string
	 */
	public function url() {
		return $this->buildUrl($this->getVals());
	}
	
}