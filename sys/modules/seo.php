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
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Gestion des actualités du site
 * @package   User Module
 */
class Seo extends AcidModule {
	const TBL_NAME = 'seo';
	const TBL_PRIMARY = 'id_seo';

	public static $_instance = null;
	public $seos = null;


	/**
	 * Constructeur
	 * @param mixed $init_id
	 */
	public function __construct($init_id=null) {
		$this->vars['id_seo'] = new AcidVarInt($this->modTrad('id_seo'),true);

		$this->vars['routename'] = new AcidVarString($this->modTrad('routename'),60);
		$this->vars['url'] = new AcidVarString($this->modTrad('url'),60);

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
				$this->vars['seo_title'.$ks] =  new AcidVarString($this->modTrad('seo_title').$ds,60);
				$this->vars['seo_desc'.$ks] =  new AcidVarString($this->modTrad('seo_desc').$ds,60);
				$this->vars['seo_keys'.$ks] = new AcidVarString($this->modTrad('seo_keys').$ds,60);
			}
			$this->config['multilingual']['flags']['default']  = !empty($have_lang_keys);
		}

		$this->vars['strict_mode'] = new AcidVarBool($this->modTrad('strict_mode'));

		parent::__construct($init_id);

		/*--- CONFIGURATION ---*/
		$this->config['print']['strict_mode']= array('type'=>'toggle','ajax'=>true);
		$this->config['acl']['default'] = Acid::get('lvl:dev');

	}

	public static function getInstance() {
		if (static::$_instance===null) {
			static::$_instance = static::build();
			static::$_instance->seos = static::dbList();
		}

		return static::$_instance;
	}

	public static function seoMatch($seotab) {
		if (is_array($seotab)) {

			if ((empty($seotab['routename'])) && (empty($seotab['url'])))  {
				return false;
			}

			$route_match = false;
			if (!empty($seotab['routename'])) {
				if (AcidRouter::getCurrentRouteName()==$seotab['routename']) {
					$route_match = true;
				}
			}else{
				$route_match = true;
			}

			$url_match = false;
			if (!empty($seotab['url'])) {
				if (strpos($_SERVER['REQUEST_URI'],Acid::get('url:folder'))===0) {
					$checkuri = substr($_SERVER['REQUEST_URI'],strlen(Acid::get('url:folder')));

					if ($checkuri==$seotab['url']) {
						$url_match = true;
					}elseif (empty($seotab['strict_mode'])) {
						if (substr($checkuri,-1)!='/') {
							$checkuri .= '/';
						}
						$url_match = (strpos($checkuri,$seotab['url'])===0);
					}
				}
			}else{
				$url_match = true;
			}


			return $route_match && $url_match;
		}

		return false;
	}

	public static function prepare() {
		if (static::getInstance()->seos) {
			foreach (static::getInstance()->seos as $seo_elt) {
				if (static::seoMatch($seo_elt)) {

					$seo = new Seo($seo_elt);

					if ($seo->trad('seo_title')) {
						Conf::setPageTitle($seo->trad('seo_title'));
					}

					if ($seo->trad('seo_desc')) {
						Conf::setMetaDesc($seo->trad('seo_desc'));
					}

					if ($seo->trad('seo_keys')) {
						Conf::addToMetaKeys(explode(',',$seo->trad('seo_keys')));
					}

					return true;
				}
			}
		}
	}


}