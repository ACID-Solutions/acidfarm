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
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Permet à l'utilisateur d'altérer tous les AcidModule
 * @package   Acidfarm\User Module
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

    /***
     * Retourne si le module est actif (ou en mode aperçu admin)
     * return boolean
     */
	public function active() {
	    if (Conf::get('admin_preview') && in_array($this->getClass(),Conf::get('admin_preview:mods'))) {
            if (isset($this->vars['active'])) {
                return
                    //l'élement est actif
                    $this->get('active')
                    //OU on est admin (ou plus) et le paramêtre GET est défini à une valeur non nulle
                    || ((!empty($_GET[Conf::get('admin_preview:varname')])) && User::curLevel(Acid::get('lvl:admin')));
            }
        }
    }

	/**
	 * Rerourne l'url de l'image en entrée au format $format
	 * @param string $url url de l'image source
	 * @param string $format la format pour l'url retournée
	 * @param string $cache_time valeur cache
	 */
	public static function genUrlSrc($url=null,$format=null,$cache_time=null) {
		$keys = self::build()->getKeys();
		$key = in_array('src',$keys) ? 'src' : (in_array('img',$keys) ? 'img' : '');
		if ($key) {
			return self::genUrlKey($key, $url, $format, $cache_time);
		}
	}

	/**
	 * Retourne l'url de l'image associée à l'objet au format saisi en entrée
	 * @param string $format format pour l'url retournée
	 */
	public function urlSrc($format=null) {
		$key = isset($this->vars['src']) ? 'src' : (isset($this->vars['img']) ? 'img' : '');
		if ($key) {
		    if ($this->get($key)) {
                return $this->getUrlKey($key, $format);
            }
		}
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