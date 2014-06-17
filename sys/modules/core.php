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
	 * Retourne l'url associé à l'objet
	 * @return string
	 */
	public function url() {
		return $this->buildUrl($this->getVals());
	}

	/**
	 * Methode d'affichage des champs multilangues
	 * @param array $elt
	 * @param string $key
	 * @param int $split
	 * @return string
	 */
	public static function printAdminListLang($elt,$key,$split=null) {
		$val = '';
		$m = static::build($elt);

		$val = '<table style="width:100%; min-width:0px;">';
		foreach (Acid::get('lang:available') as $l) {
			$value = $m->get($m->langKey($key,$l));
			$value = $split ? AcidVarString::split($value,$split) : $value;
			if ($value) {
				$val .= '<tr><td style="width:15px;" ><img style="max-width:15px;" src="'. Acid::get('url:img') . 'langs/'.$l.'_sel.png" alt="'.$l.'" title="'.$l.'" /><td>'.$value.'</td></td></tr>';
			}
		}
		$val .= '</table>';

		return $val;
	}

}