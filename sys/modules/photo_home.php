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
 * Gestion de la galerie photo de la homepage
 * @package   Acidfarm\User Module
 */
class PhotoHome extends AcidModule {
	const TBL_NAME = 'photo_home';
	const TBL_PRIMARY = 'id_photo_home';

	/**
	 * Constructeur
	 * @param mixed $init_id
	 */
	public function __construct($init_id=null) {

		$photo_format 		=	array(
									'src'=>array(	'size' => array(0,0,false), 'suffix' => '', 'effect' => array() ),
									'diapo'=>array(	'size' => array(960,400,true),	'suffix' => '_diapo', 'effect' => array() ),
									'mini'=>array(	'size' => array(96,40,true), 'suffix' => '_s', 'effect' => array()	)
								);


		$config = array('format'=>$photo_format,'admin_format'=>'mini');

		$this->vars['id_photo_home'] = new AcidVarInt(self::modTrad('id_photo_home'),true);

		if ($langs = Acid::get('lang:available')) {
			/*AUTODETECTION DU MULTILINGUE*/
			//commenter cette ligne pour desactiver le multilingue auto
			$have_lang_keys = (count($langs)>1) || Acid::get('lang:use_nav_0');
			//POUR CHAQUE LANGUE
			foreach ($langs as $l) {
				//AUTODETECT
				$ks = !empty($have_lang_keys) ? ('_'.$l) : '';
				$ds = !empty($have_lang_keys) ? (' '.$l) : '';
				//DEFINITION DE LA VARIABLE
				$this->vars['name'.$ks] = new AcidVarString(self::modTrad('name').$ds,60);
			}
			//CONFIGURATION DU MULTILINGUE DANS LES FORMULAIRES ADMIN
			$this->config['multilingual']['flags']['default']  = !empty($have_lang_keys);
		}

		$this->vars['pos'] = new AcidVarInt(self::modTrad('position'),true);

		$this->vars['src'] 	= 	new AcidVarImage( self::modTrad('src'), $GLOBALS['acid']['path']['files'].'home/', $config,'__ID__-__NAME__');

		$this->vars['active'] = new AcidVarBool($this->modTrad('active'),true);

		$this->vars['cache_time'] = new AcidVarInfo(self::modTrad('cache_time'));


		parent::__construct($init_id);

		//$this->config['admin']['list']['keys_excluded'] = array('id_photo_home','name','pos');

		$this->config['print']['src']= array('type'=>'img','view'=>'src','size'=>'mini');

	}

	/**
	 * Rerourne l'url de l'image en entrée au format $format
	 * @param string $url url de l'image source
	 * @param string $format la format pour l'url retournée
	 * @param string $cache_time valeur cache
	 */
	public static function genUrlSrc($url=null,$format=null,$cache_time=null) {
		return self::genUrlKey('src',$url,$format,$cache_time);
	}

	/**
	 * Retourne l'url de l'image associée à l'objet au format saisi en entrée
	 * @param string $format format pour l'url retournée
	 */
	public function urlSrc($format=null) {
		return $this->getUrlKey('src',$format);
	}

	/**
	 * Override de la configuration de l'admnistration du module
	 * @see AcidModuleCore::printAdminConfigure()
	 */
	public function printAdminConfigure($do='default',$conf=array()) {

		$this->config['admin']['list']['order']= array('pos'=>'ASC');
		$this->config['admin']['list']['keys'] = array('id_photo_home','src',$this->langKey('name'),'active','pos');
		$this->config['print']['pos']= array('type'=>'quickchange','ajax'=>false,'params'=>array('style'=>'width:30px; text-align:center;'));

		$this->config['print']['active']= array('type'=>'toggle','ajax'=>true);

		return parent::printAdminConfigure($do,$conf);
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidModuleCore::printAdminAddForm()
	 */
	public function printAdminAddForm() {
		$res = AcidDB::query('SELECT MAX(`pos`) as max_pos FROM '.$this->tbl())->fetch(PDO::FETCH_ASSOC);
		$plus = ($res['max_pos']+1);
		$this->initVars(array('pos'=>$plus,'name'=>'Photo '.$plus));
		return parent::printAdminAddForm();
	}

	/**
	 * Exemple de stockage d'image dynamique (Sous dossier correspondant à la valeur de "pos")
	 * @param array $vals
	 * @param string $do
	 * @param array $config
	 * @see AcidModuleCore::postConfigure()
	 */
	public function postConfigure($vals=array(),$do=null,$config=null) {
		/*
		$dir = $this->vars['src']->getDirPath().$vals['pos'].'/';
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		$this->vars['src']->setDirPath($dir);
		*/
		parent::postConfigure($vals,$do,$config);
	}

	/**
	 * (non-PHPdoc)
	 * @param array $vals
	 * @param mixed $dialog
	 * @see AcidModuleCore::postAdd()
	 */
	public function postAdd($vals,$dialog=null) {
		$go_on = true;

		$limit = !Conf::exists('photo_home:limit') ? null : Conf::get('photo_home:limit');

		if ($limit) {
			$count = $this->dbCount();
			$go_on = ($count < $limit);
		}

		if ($go_on) {
			return parent::postAdd($vals,$dialog);
		}else{
			AcidDialog::add('error',Acid::trad('photo_above_limit',array('__NUM__'=>$limit)));
		}
	}

	/**
	 * Retourne le diaporama photo en HTML
	 * @return string
	 */
	public static function printGallery() {
		$elts = Acid::mod('PhotoHome')->dbList(array(array('active','=',1)),array('pos'=>'ASC'));
		return Acid::tpl('tools/home_diaporama.tpl',array('elts'=>$elts),Acid::mod('PhotoHome'));
	}

}