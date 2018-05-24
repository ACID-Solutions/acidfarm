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
 * Contrôleur d'Index
 * @package   Acidfarm\Controller
 */
class PolicyController{
	
	/**
	 * Affichage de l'index
	 */
    public function index(){
        Conf::addToAriane(AcidRouter::getName('policy'),Route::buildUrl('policy'));
    
        $scriptCategories = ScriptCategory::dbList([['active','=',1]],['pos'=>'ASC']);
        $page->initVars(array($page->langKey('content')=>SiteConfig::getCurrent()->getConf('home_'.$page->langKey('content'))));
    
        $vars = array ();
    
        Conf::addToContent(Acid::tpl('pages/home.tpl',$vars,$page));
        
    }

    /**
     * Affiche la home page
     */
    public function home(){

       



        //$page = new Page();
        //$page->init('home');

        $page = new Page();
        $page->initVars(array($page->langKey('content')=>SiteConfig::getCurrent()->getConf('home_'.$page->langKey('content'))));

        $vars = array ();

        Conf::addToContent(Acid::tpl('pages/home.tpl',$vars,$page));
    }
}
