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
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Gestion des actualités du site
 * @package   Acidfarm\User Module
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

		$photo_format 		=	array(
				'src'=>array(	'size' => array(0,0,false), 'suffix' => '', 'effect' => array() ),
				'seo'=>array(	'size' => array(300,300,false),	'suffix' => '_l', 'effect' => array() ),
				'mini'=>array(	'size' => array(60,60,true), 'suffix' => '_s', 'effect' => array()	)
		);
		$config = array('format'=>$photo_format,'admin_format'=>'mini');
		$this->vars['seo_img'] 	= 	new AcidVarImage( self::modTrad('seo_img'), Acid::get('path:files').'seo/', $config);

		$this->vars['strict_mode'] = new AcidVarBool($this->modTrad('strict_mode'));

		parent::__construct($init_id);

		/*--- CONFIGURATION ---*/
		$this->config['print']['strict_mode']= array('type'=>'toggle','ajax'=>true);
		$this->config['acl']['default'] = Acid::get('lvl:dev');

	}

	/**
	 * Rerourne l'url de l'image en entrée au format $format
	 * @param string $url url de l'image source
	 * @param string $format la format pour l'url retournée
	 */
	public static function genUrlImg($url=null,$format=null) {
		return self::genUrlKey('seo_img',$url,$format);
	}

	/**
	 * Retourne l'url de l'image associée à l'objet au format saisi en entrée
	 * @param string $format format pour l'url retournée
	 */
	public function urlImg($format=null) {
		return $this->genUrlImg($this->get('seo_img'),$format);
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidModuleCore::printAdminForm()
	 */
	public function printAdminForm($do) {

		$help = '<ul style="margin-top:50px; padding:10px; font-size:11px; line-height:18px;" >' . "\n" .
				'<li><b>' . $this->modTrad('url') .'</b> : __LANG__ </li>' . "\n" .
				'<li><b>' . $this->modTrad('seo_title') .', '.$this->modTrad('seo_desc').', '.$this->modTrad('seo_keys').'</b> : '.
							' __SITENAME__, __ROUTENAME__, __PARAM:key__ , __OBJ:key__</li>' . "\n" .
				'</ul>' . "\n" ;

		return parent::printAdminForm($do). $help;
	}

	/**
	 * Retourne l'instance SEO
	 */
	public static function getInstance() {
		if (static::$_instance===null) {
			static::$_instance = static::build();
			static::$_instance->seos = static::dbList();
		}

		return static::$_instance;
	}

	/**
	 * Retourne l'url après traitement des variables magiques
	 * @param string $url
	 * @return string
	 */
	public static function treatUrl($url) {
		$parse = explode('/',str_replace('__LANG__',Acid::get('lang:current'),$url));
		$params = AcidRouter::getParams();

		foreach ($parse as $k=>$v) {
			if (strpos($v,'@')===0) {
				$parse[$k] = AcidRouter::getKey(substr($v,1));
			}elseif (strpos($v,':')===0) {
				if (isset($params[substr($v,1)])) {
					$parse[$k] = $params[substr($v,1)];
				}
			}
		}

		return implode('/',$parse);
	}

	/**
	 * Retourne la chaine de caractères SEO après traitement des variables magiques
	 * @param string $value
	 * @return mixed
	 */
	public static function treatSEO($value) {

		//s'il y a une variable magique
		if (  strpos($value,'__')!==false ) {

			//s'il y a un objet associé à la page courante
			if (  $obj = Acid::get('tmp_current_object') ) {

				//si on fait appel à une valeur de l'objet
				if (  strpos($value,'__OBJ')!==false ) {

					$objvals = $obj->getVals();

					$results = array();

					if (preg_match_all("/__OBJ:[a-zA-Z0-9_]*__/",$value,$results)) {

						if (!empty($results[0])) {

							foreach ($results[0] as $exp) {
								$sparse = explode(':',str_replace('__', '', $exp));
								if (count($sparse) > 1)  {
									if (isset($objvals[$obj->langKey($sparse[1])])) {
										$value = str_replace($exp,htmlspecialchars($objvals[$obj->langKey($sparse[1])]),$value);
									}
								}
							}

						}

					}

				}
			}

			//si on fait appel un paramètre router
			if (  strpos($value,'__PARAM')!==false ) {
				if ($params = AcidRouter::getParams())  {

					foreach ($params as $pk=>$pv) {
						$value = str_replace('__PARAM:'.$pk.'__',$pv,$value);
					}

				}
			}

			//si on fait appel à un nom lié au router
			if (  strpos($value,'__ROUTENAME')!==false ) {

				$value = str_replace('__ROUTENAME__',AcidRouter::getName(AcidRouter::getCurrentRouteName()),$value);

				if ($routekeys = Acid::get('router','lang')) {
					foreach ($routekeys as $pk=>$pv) {

						$value = str_replace('__ROUTENAME:'.$pk.'__',AcidRouter::getName($pk),$value);
					}
				}

			}

			//si on demande le nom du site
			$value = str_replace('__SITENAME__',Acid::get('site:name'),$value);

		}

		return $value;
	}

	/**
	 * Retourne true si l'url soumise match avec l'url courante
	 * @param array $seotab
	 * @return boolean
	 */
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

				if (strpos(AcidUrl::requestURI(),Acid::get('url:folder'))===0) {
					$checkuri = substr(AcidUrl::requestURI(),strlen(Acid::get('url:folder')));
					$seotab['url'] = static::treatUrl($seotab['url']);

					if ($seotab['url']=='*') {
						$url_match = true;
					}elseif ($checkuri==$seotab['url']) {
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

	/**
	 * Ecrase les valeurs SEO par défaut si définit par le module
	 * @return boolean
	 */
	public static function prepare() {
		if (static::getInstance()->seos) {
			foreach (static::getInstance()->seos as $seo_elt) {
				if (static::seoMatch($seo_elt)) {

					$seo = new Seo($seo_elt);

					if ($seo->trad('seo_title')) {
						Conf::setPageTitle(static::treatSEO($seo->trad('seo_title')));
					}

					if ($seo->trad('seo_desc')) {
						Conf::setMetaDesc(static::treatSEO($seo->trad('seo_desc')));
					}

					if ($seo->trad('seo_keys')) {
						Conf::addToMetaKeys(explode(',',static::treatSEO($seo->trad('seo_keys'))));
					}

					if ($seo->trad('seo_img')) {
						Conf::setMetaImage($seo->urlImg('seo'));
					}

					return true;
				}
			}
		}
	}

}