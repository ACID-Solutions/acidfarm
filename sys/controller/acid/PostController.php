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
 * Contrôleur POST
 * @package   Acidfarm\Controller
 */
class PostController{
	
	/**
	 * Re-routage en fonction du $_POST
	 */
    public function index(){
        if (isset($_POST['search_form'])){
            AcidRouter::accessTo('searchPage')->setPartialParams($_POST['search_form'])->dispatch();
        }
    }
    
}
