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
 * Gestion de la galerie photo (wallart) du site
 * @package   User Module
 */
class Photo extends AcidModule {
	const TBL_NAME = 'photo';
	const TBL_PRIMARY = 'id_photo';

	/**
	 * Constructeur
	 * @param mixed $init_id
	 */
	public function __construct($init_id=null) {

		$photo_format 		=	array(
									'src'=>array(	'size' => array(0,0,false), 'suffix' => '', 'effect' => array() ),
									'large'=>array(	'size' => array(500,500,false),	'suffix' => '_l', 'effect' => array() ),
									'diapo'=>array(	'size' => array(180,180,true),	'suffix' => '_diapo', 'effect' => array() ),
									'mini'=>array(	'size' => array(48,48,true), 'suffix' => '_s', 'effect' => array()	)
								);

		$config = array('format'=>$photo_format,'admin_format'=>'mini');

		$this->vars['id_photo'] = new AcidVarInt(self::modTrad('id_photo'),true);

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

		$this->vars['src'] 	= 	new AcidVarImage( self::modTrad('src'), Acid::get('path:files').'photo/', $config);

		$this->vars['active'] = new AcidVarBool($this->modTrad('active'),true);

		parent::__construct($init_id);

		$this->config['print']['src'] = array('type'=>'img','link'=>'src','size'=>'mini');


		//$this->config['default_do'] = 'add';
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
	 * @param array $conf
	 * @see AcidModuleCore::printAdminList()
	 */
	public function printAdminConfigure($do='default',$conf=array()) {

		$this->config['admin']['list']['order']= array('pos'=>'ASC');
		$this->config['admin']['list']['keys'] = array('id_photo','src',$this->langKey('name'),'active','pos');
		$this->config['print']['pos']= array('type'=>'quickchange','ajax'=>false,'params'=>array('style'=>'width:30px; text-align:center;'));

		$this->config['print']['active']= array('type'=>'toggle','ajax'=>true);

		return parent::printAdminConfigure($do,$conf);
	}

	/**
	 * (non-PHPdoc)
	 * @param array $vals
	 * @param mixed $dialog
	 * @see AcidModuleCore::postAdd()
	 */
	public function postAdd($vals,$dialog=null) {
		$go_on = true;

		$limit = !Conf::exist('photo:limit') ? null : Conf::get('photo:limit');
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
	 * Retourne la galerie/wallart du site en HTML
	 * @return string
	 */
	public static function printGallery() {
		$elts = Acid::mod('Photo')->dbList(array(array('active','=',1)),array('pos'=>'ASC'));
		return Acid::tpl('tools/wall.tpl',array('elts'=>$elts),Acid::mod('Photo'));
	}

}
