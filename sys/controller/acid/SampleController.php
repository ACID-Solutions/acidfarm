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
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur exemple
 * @package   Acidfarm\Controller
 */
class SampleController{
	
	/**
	 * Méthode par défaut
	 */
    public function index(){
        /*
        $vars = array();
        $object = User::curUser();
        Acid::set('tmp_current_object',$object);
        Conf::addToContent(Acid::tpl('pages/sample.tpl',$vars,$object));
        */
    }

    /**
     * Affiche une autre vue
     */
    public function other(){
        /*
        $object = User::curUser();

        if ( (count(AcidRouter::getParams()) == 1) && $object->getId() && $object->active() ) {
            $vars = array();
            Acid::set('tmp_current_object',$object);
            Conf::addToContent(Acid::tpl('pages/sample.tpl',$vars,$object));
        }else{
            AcidUrl::error404();
        }
        */
    }
}
