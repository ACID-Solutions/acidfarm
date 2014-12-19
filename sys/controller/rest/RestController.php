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
	 * List
	 */
    public function getList(){
		$module = AcidRouter::getParam('module');
		if (Acid::get('includes:'.$module)) {
			$m = new $module();
			if ($res = $m->restList()) {
				Rest::status200($res);
			}else{
				Rest::status204();
			}

		}
    }

    /**
     * GET
     */
    public function get(){
    	$module = AcidRouter::getParam('module');
    	$id = AcidRouter::getParam('id_module');
    	if (Acid::get('includes:'.$module)) {
    		$m = new $module($id);
    		if ($res = $m->restGet()) {
    			Rest::status200($res);
    		}else{
    			Rest::status204();
    		}

    	}
    }

    /**
     * POST
     */
    public function post(){
		Rest::status403();
    }

    /**
     * PUT
     */
    public function put(){
    		Rest::status403();
    }

    /**
     * DELETE
     */
    public function delete(){
    	Rest::status403();
    }

    /**
     * 404
     */
    public function call404(){
    	Rest::status404();
    }

}
