<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model
 * @version   0.1
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Géneration des URL du site
 * @package   Model
 */
class Route {


	/**
	 * Retourne une URL en fonction des paramètres renseignés en entrée
	 * @param string $key identifiant de la page
	 * @param array $vals configuration
	 * @param bool $absolute si true retourne une url en absolute
	 * @param mixed $force_lang si false utilise la langue courante, sinon la valeur de $force_lang
	 *
	 * @return string
	 */

	public static function buildUrl($key=null,$vals=array(),$absolute=false,$force_lang=false) {
		if ($force_lang) {
			$base = $absolute ? Acid::get('url:system').$force_lang.'/' : Acid::get('url:folder').$force_lang.'/';
		}else{
			$base = $absolute ? Acid::get('url:system_lang') : Acid::get('url:folder_lang');
		}

		$rl = $force_lang ? $force_lang : null;

		switch ($key) {

			//Page
			case Page::checkTbl() :
				$mod = new Page($vals);
				$print = !empty($vals['print_page']) ? AcidRouter::getKey('pagination_key',$rl).'/' :'' ;
				$key = $mod->langKey('ident',$rl);
				return $base .$print. $mod->get($key);
			break;

			//News
			case Actu::checkTbl() :

				$base .= AcidRouter::getKey('news',$rl);
				$mod = new Actu($vals);

				if ($mod->getId()) {
					return $base . '/' . $mod->getId() . '/' . AcidUrl::normalize($mod->trad('title'));
				}else{
					return $base;
				}

			break;

			//Gallery
			case Photo::checkTbl() :
				$base .= AcidRouter::getKey('gallery',$rl);
				return $base;
			break;

			//News List
			case Actu::checkTbl().'_list' :

				$base .= AcidRouter::getKey('news',$rl);
				$page = isset($vals['page']) ? $vals['page'] : 1;

				if ($page>1) {
					return $base . '/'.AcidRouter::getKey('pagination_key',$rl).'/' . $page;
				}else{
					return $base;
				}

			break;

			//Admin
			case 'admin' :
				return Conf::get('url:admin');
			break;

			//Index
			case 'index' :
			case null :
				return $base;
			break;

			//Custom
			case 'custom' :
				$next = isset($vals['page']) ? $vals['page'] : '';
				return $base.$next;
			break;

			//Route
			case 'route' :
				$route_name = isset($vals['route']) ? $vals['route'] : '';
				$params = isset($vals['params']) ? $vals['params'] : null;
				$partial_params = isset($vals['partial_params']) ? $vals['partial_params'] : null;
				return AcidRouter::buildUrl($route_name,$params,$partial_params);
			break;

			//Default
			default :
				return $base.AcidRouter::getKey($key,$rl);
			break;
		}

		return '';
	}

}