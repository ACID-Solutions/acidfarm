<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur d'Index
 * @package   Controller
 */
class RestController{

	/**
	 * GET
	 */
    public function get(){
		$module = AcidRouter::getParam('module');
		if (Acid::get('includes:'.$module)) {
			$m = new $module();
			Conf::addToContent(json_encode($m->restGet()));
		}
    }

    /**
     * POST
     */
    public function post(){
		AcidUrl::error403();
    }

    /**
     * PUT
     */
    public function put(){
    	AcidUrl::error403();
    }

    /**
     * DELETE
     */
    public function delete(){
    	AcidUrl::error403();
    }

    /**
     * 404
     */
    public function call404(){
    	AcidUrl::error404();
    }

}
