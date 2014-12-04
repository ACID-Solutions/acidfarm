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
class Actu extends AcidModule {
	const TBL_NAME = 'actu';
	const TBL_PRIMARY = 'id_actu';

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

		$this->vars['id_actu'] = new AcidVarInt($this->modTrad('id_actu'),true);

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
				$this->vars['title'.$ks] = new AcidVarString($this->modTrad('title').$ds,60);
				$this->vars['head'.$ks] = new AcidVarText($this->modTrad('head').$ds);
				$this->vars['content'.$ks] = new AcidVarText($this->modTrad('content').$ds);
				$this->vars['seo_title'.$ks] =  new AcidVarString($this->modTrad('seo_title').$ds,60);
				$this->vars['seo_desc'.$ks] =  new AcidVarString($this->modTrad('seo_desc').$ds,60);
				$this->vars['seo_keys'.$ks] = new AcidVarString($this->modTrad('seo_keys').$ds,60);
				//CONFIGURATION
				$this->config['print']['head'.$ks] = $this->config['print']['content'.$ks] = array('type'=>'split');
			}
			$this->config['multilingual']['flags']['default']  = !empty($have_lang_keys);
		}

		$this->vars['src'] 	= 	new AcidVarImage( self::modTrad('src'), Acid::get('path:files').'actu/', $config);
		$this->vars['adate'] = new AcidVarDateTime($this->modTrad('adate'));
		$this->vars['active'] = new AcidVarBool($this->modTrad('active'));

		parent::__construct($init_id);

		/*--- CONFIGURATION ---*/
		$this->config['print']['adate']= array('type'=>'date','format'=>'datetime','empty_val'=>'-');
		//$this->config['print']['active']= array('type'=>'bool');
		$this->config['print']['active']= array('type'=>'toggle','ajax'=>true);

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
	 * Retourne l'url de la liste des actualités (gère la pagination)
	 * @param int $page page à afficher
	 * @return string
	 */
	public static function buildUrlList($page=null) {
		return Route::buildUrl(static::checkTbl().'_list',array('page'=>$page));
	}

	/**
	 * Retourn la dernière actualité sous forme d'objet
	 * @return object
	 */
	public static function getLast() {
		$elts = Acid::mod('Actu')->dbList(array(array('active','=','1')),array('adate'=>'DESC'),1);
		if (count($elts)) {
			$a = new Actu();
			$a->initVars($elts[0]);
			return $a;
		}else{
			return null;
		}
	}

	/**
	 * (non-PHPdoc)
	 * @param array $conf
	 * @see AcidModuleCore::printAdminList()
	 */
	public function printAdminConfigure($do='default',$conf=array()) {

		$this->config['admin']['list']['keys'] = array('id_actu','src',$this->langKey('title'),$this->langKey('head'),$this->langKey('content'),'adate','active');
		$this->config['admin']['list']['order'] = array('adate'=>'DESC');

		return parent::printAdminConfigure($do,$conf);
	}

	/**
	 * (non-PHPdoc)
	 * @see AcidModuleCore::printAdminAdd()
	 */
	public function printAdminAdd() {
		$this->config['admin']['add']['def'] = array('adate'=>date('Y-m-d H:i:s'));
		return parent::printAdminAdd();
	}

	/**
	 * (non-PHPdoc)
	 * @param string $type
	 * @see AcidModuleCore::printAdminForm()
	 */
	public function printAdminForm($type) {

		$seo_keys = User::curLevel(Conf::get('lvl:seo')) ?
						array_merge($this->langKeyDecline('seo_title'),$this->langKeyDecline('seo_desc'),$this->langKeyDecline('seo_keys'))
						: array();

		$this->config['admin']['add']['keys'] =
		$this->config['admin']['update']['keys'] = array_merge(
														$this->langKeyDecline('title'),
														$this->langKeyDecline('head'),
														array('src'),
														$this->langKeyDecline('content'),
														$seo_keys
		);

		foreach ($this->langKeyDecline('title') as $lk) {
			$this->config['admin'][$type]['params'][$lk] = array('class'=>'head_field');
		}

		$GLOBALS['tinymce']['all'] = false;
		$GLOBALS['tinymce']['ids'] = array();
		foreach ($this->langKeyDecline('content') as $lk) {
			$id_name = $lk.'_textarea';
			$this->config['admin'][$type]['params'][$lk] = array('id'=>$id_name);
			$this->config['admin'][$type]['body_attrs'][$lk] = array('class'=>'form_spaced');
			$GLOBALS['tinymce']['ids'][] = $id_name;
		}

		//$this->vars['adate']->setForm('hidden');


		return parent::printAdminForm($type);
	}

	/**
	 * (non-PHPdoc)
	 * @param object $form
	 * @param string $do
	 * @see AcidModuleCore::printAdminFormStop()
	 */
	public function printAdminFormStop(&$form, $do) {

		$forms = '<div class="form_subline">' . "\n" .
				 '	<div class="form_subline_elt first">'.$this->getLabel('adate').' '.$this->getVarForm('adate') . '</div>' . "\n" .
				 '	<div class="form_subline_elt">'.$this->getLabel('active').' '.$this->getVarForm('active') . '</div>' . "\n" .
				 '	<div class="clear"></div>' . "\n" .
				 '</div>';

		$form->addFreeText('',$forms);

		parent::printAdminFormStop($form,$do);
	}

	/**
	 * Retourne la liste des actus sous forme HTML (gère la pagination)
	 * @param int $page page à afficher
	 * @return string
	 */
	public static function printList($page=1) {
		$nb_elts_per_page = 5;

		$filter = array(array('active','=',1));
		$count = self::dbCount($filter);

		$page = AcidPagination::getPage($page,$count,$nb_elts_per_page);
		$limit = ($nb_elts_per_page*($page-1)).','.$nb_elts_per_page;
		$elts = self::dbList($filter,array('adate'=>'DESC'),$limit);

		$link_function = array('func'=>'Actu::buildUrlList','args'=>array('__PAGE__'));
		$pagination = AcidPagination::getNav($page,$count,$nb_elts_per_page,'tools/pagination.tpl',array('link_func'=>$link_function));


		$v = array(
				'url'=>self::buildUrl(),
				'elts' => $elts,
				'pagination' => $pagination
		);



		return Acid::tpl('pages/actu-list.tpl',$v,Acid::mod('Actu'));

	}

	/**
	 * Retourne une actualité sous forme HTML
	 * @return string
	 */
	public function printActu() {

		$v = array(  );


		return Acid::tpl('pages/actu.tpl',$v,$this);

	}

}