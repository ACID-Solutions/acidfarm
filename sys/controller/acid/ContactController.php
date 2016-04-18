<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Contrôleur des outils de contact
 * @package   Acidfarm\Controller
 */
class ContactController {
	
	/**
	 * Affiche le formulaire de contact
	 */
    public function index(){
       $url = (Acid::get('url:folder_lang').AcidRouter::getParamById(0));
       Conf::addToAriane(AcidRouter::getName('contact'),$url);       
       
       $c = new Contact();
       Conf::addToContent($c->printContactPage());
    } 
}

