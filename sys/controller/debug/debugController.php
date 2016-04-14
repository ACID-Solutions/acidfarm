<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Controller de debug 
 * Egalement utilisé comme exemple d'utilisation d'un controller dans une autre arborescence 
 * Exemple d'appel : AcidRouter::addRoute('debugroute', new AcidRoute('@debug',array('module'=>'debug','controller'=>'DebugController','action'=>'maFctAction')));
 * @package   Controller
 */
class DebugController {
	
	/**
	 * Fonction de debug
	 */
    public function maFctAction(){
        //echo 'dans ma fonction';
    }
    
}