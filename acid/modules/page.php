<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Module
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Module AcidPage, Gestionnaire de Pages
 * @package   Acidfarm\Module
 */
abstract class AcidPage extends AcidModule {
	
	/**
	 * Constructeur AcidPage
	 * 
	 * @param mixed $init_id Identifiant pour initialisation (si défini)
	 */
	public function __construct($init_id=null) {
		
		$this->vars['id_page'] = empty($this->vars['id_page']) ? new AcidVarInt($this->modTrad('id_page'),true) : $this->vars['id_page'];
		$this->vars['ident'] =  empty($this->vars['ident']) ? new AcidVarString($this->modTrad('ident')) : $this->vars['ident'];
		$this->vars['title'] =  empty($this->vars['title']) ? new AcidVarString($this->modTrad('title'),50) : $this->vars['title'];
		$this->vars['adate'] =  empty($this->vars['adate']) ? new AcidVarDateTime($this->modTrad('adate')) : $this->vars['adate'];
		$this->vars['content'] =  empty($this->vars['content']) ? new AcidVarTextarea($this->modTrad('content'),80,30) : $this->vars['content'];
		$this->vars['active'] =  empty($this->vars['active']) ? new AcidVarBool($this->modTrad('active')) : $this->vars['active'];

		
		return parent::__construct($init_id);
	
	}
	
	
	
	/**
	 * Initialise une page selon l'identifiant $ident
	 * Retourne l'id de la page en cas de réussite, false sinon.
	 * 
	 * @param unknown_type $ident
	 */
	public function init($ident) {
		if ($ident !== null) {
			$this->dbInitSearch(array('ident'=>$ident));
			return $this->get('id_page');
		}
		return false;
	}
	
	/**
	 *  Traite la procédure de mise à jour d'une page depuis un formulaire.
	 *
	 * @param array $vals
	 * @param mixed $dialog 
	 * 
	 * @return object | bool
	 */
	public function postUpdate($vals,$dialog=null) {
		$vals['adate'] = date('Y-m-d H:i:s');
		return parent::postUpdate($vals,$dialog);
		
	}
	
	/**
	 *  Retourne la page mise en forme.
	 * 
	 * @return string
	 */
	public function printPage() {
		return Acid::tpl('modules/page/print-page.tpl',array(),$this);		
	}
	
	
	/**
	 *  Retourne le formulaire de mise à jour de la page.
	 * 
	 * @return string
	 */
	public function printUpdatePage() {
		
		$form = new AcidForm('post','');
		
		$form->addHidden('',$this->preKey('do'),'update');
		$form->addHidden('',$this->tblId(),$this->getId());
		$form->addText('','title',$this->get('title'));
		$form->addTextarea('','content',$this->get('content'),130,40,array('style'=>'width:100%;height:500px;'));
		$form->addSubmit('','Modifier');
		
		return	'<h4>' . Acid::trad('admin_page_updating_page') . ' ' . $this->hsc('title') . '</h4>' .
				'' . $form->html() . 
				'';
	}
	
	
}
