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
 * Gestion de la galerie photo de la homepage
 * @package   User Module
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
									'diapo'=>array(	'size' => array(780,320,true),	'suffix' => '_diapo', 'effect' => array() ),
									'mini'=>array(	'size' => array(78,32,true), 'suffix' => '_s', 'effect' => array()	)
								);

		
		$config = array('format'=>$photo_format,'admin_format'=>'mini');
		
		$this->vars['id_photo_home'] = new AcidVarInt(self::modTrad('id_photo_home'),true);
		$this->vars['name'] = new AcidVarString(self::modTrad('name'),60);
		$this->vars['pos'] = new AcidVarInt(self::modTrad('position'),true);							
		$this->vars['src'] 	= 	new AcidVarImage( self::modTrad('src'), $GLOBALS['acid']['path']['files'].'home/', $config,'__ID__-__NAME__');
		
		
		parent::__construct($init_id);
		
		//$this->config['admin']['list']['keys_exclued'] = array('id_photo_home','name','pos');
		
		$this->config['print']['src']= array('type'=>'img','view'=>'src','size'=>'mini');
		
	}
	
	/**
	 * Rerourne l'url de l'image en entrée au format $format
	 * @param string $url url de l'image source
	 * @param string $format la format pour l'url retournée
	 */
	public static function genUrlSrc($url=null,$format=null) {
		return self::genUrlKey('src',$url,$format);
	}
		
	/**
	 * Retourne l'url de l'image associée à l'objet au format saisi en entrée
	 * @param string $format format pour l'url retournée
	 */
	public function urlSrc($format=null) {
		return $this->genUrlSrc($this->get('src'),$format);
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
		
		$limit = !Conf::exist('photo_home:limit') ? null : Conf::get('photo_home:limit');
		
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
		$elts = Acid::mod('PhotoHome')->dbList(array(),array('pos'=>'ASC'));
		return Acid::tpl('tools/home_diaporama.tpl',array('elts'=>$elts),Acid::mod('PhotoHome'));
	}
	
}