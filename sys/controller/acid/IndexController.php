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
class IndexController{
	
	/**
	 * Affichage de l'index
	 */
    public function index(){
        $this->home();
    }

    /**
     * Affiche la home page
     */
    public function home(){

        Conf::setPageTitleAlone(true);

        if (!Conf::getPageTitle()) {
            Conf::setPageTitle(Acid::get('site:name'));
        }

        Conf::setCanonicalUrl(Acid::get('url:system_lang'));
        Conf::setAriane(array());

        if (isset($_GET['search'])) {
            AcidUrl::redirection301(Route::buildUrl('search').'/'.$_GET['search']);
        }



        //$page = new Page();
        //$page->init('home');

        $page = new Page();
        $page->initVars(array($page->langKey('content')=>SiteConfig::getCurrent()->getConf('home_'.$page->langKey('content'))));

        $vars = array ();

        Conf::addToContent(Acid::tpl('pages/home.tpl',$vars,$page));
    }
}
