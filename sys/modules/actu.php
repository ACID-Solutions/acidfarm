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
		
		$this->vars['id_actu'] = new AcidVarInt($this->modTrad('id_actu'),true);
		$this->vars['title'] = new AcidVarString($this->modTrad('title'),60);
		$this->vars['head'] = new AcidVarText($this->modTrad('head'));
		$this->vars['content'] = new AcidVarText($this->modTrad('content'));
		$this->vars['adate'] = new AcidVarDateTime($this->modTrad('adate'));
		$this->vars['active'] = new AcidVarBool($this->modTrad('active'));
		
		parent::__construct($init_id);
				
		$this->config['print']['head'] = $this->config['print']['content'] = array('type'=>'split');
		$this->config['print']['adate']= array('type'=>'date','format'=>'datetime','empty_val'=>'-');
		
		//$this->config['print']['active']= array('type'=>'bool');
		$this->config['print']['active']= array('type'=>'toggle','ajax'=>true);
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
		$this->config['admin']['add']['keys'] = $this->config['admin']['update']['keys'] = array('title','head','content');
		$this->config['admin'][$type]['params']['title'] = array('class'=>'head_field');
		
		//$this->vars['adate']->setForm('hidden');
		$this->config['admin'][$type]['params']['content'] = array('id'=>'content_textarea');
		$GLOBALS['tinymce']['all'] = false;
		$GLOBALS['tinymce']['ids'] = array('content_textarea');
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