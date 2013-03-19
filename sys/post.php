<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Controller
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


if (isset($_POST['do'])) {
	
	// Traitement d'une connexion
	if ($_POST['do'] == 'login') {
        $auto = (isset($_POST['auto']) && $_POST['auto']) ? true : false;	
        
	    User::login($_POST['login'],$_POST['pass'],true,$auto);
	 
	} elseif ($_POST['do'] === 'logout') {
	    User::logout();	    
	}
	
}


if (isset($_POST['search_form'])){
   AcidRouter::directlyRun('searchPageX', new AcidRoute('@search',array('controller'=>'SearchController'),1),null,$_POST['search_form']);
}


Acid::mod('User')->exePost();
Acid::mod('User')->exeUser();
$exclued = array('User');
if (isset($_POST['module_do'])) {
	if (isset($acid['includes'][$_POST['module_do']])) {
		$mod = $_POST['module_do'];
		switch ($mod) {

			case 'Contact' :
				Contact::exePost();
			break;
			
			default:
				if (!in_array($mod,$exclued)) {
					$module = new $_POST['module_do']();
					if (isset($_POST[$module->preKey('do')])) {
						$module->exePostProcess();
					}
				}
			break;
		}
		
	}
}


Acid::tool('AcidBrowser');
$fb = new AcidBrowser(Acid::get('path:uploads'));
$fb->exePost();


