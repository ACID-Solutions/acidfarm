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
 * Contôleur des Pages
 * @package   Acidfarm\Controller
 */
class PageController{

	/**
	 * Affiche une page ciblée par l'url. Les pages peuvent être en base de données ou dans le dossier des tpl statics
	 */
	public function index(){
        $page_key = AcidRouter::getParam('page_key');
        Conf::setPageTitleAlone(true);

        $tpl_path = 'pages/'.Acid::get('keys:static').'/'.AcidUrl::normalize($page_key).'.tpl';
        if (file_exists(Acid::tplPath($tpl_path))) {

		   Conf::addToContent(Acid::tpl($tpl_path));
	       Conf::addToAriane( (Conf::getPageTitle() ? Conf::getPageTitle() : $page_key), AcidUrl::requestURI());

        }else{

	        $my_page = new Page();
	        $my_page->init($page_key);

	        Acid::set('tmp_current_object',$my_page);

	        if ( (count(AcidRouter::getParams()) == 1) && $my_page->getId() && $my_page->active()) {

	        	//add to ariane
	        	Conf::addToAriane($my_page->trad('title'),$my_page->url());

	            //set meta tags
	            $meta_desc = $my_page->trad('seo_desc')  ? $my_page->hscTrad('seo_desc')  : (AcidVarString::split($my_page->trad('content'),100) . ' - ' .Acid::get('site:name'));
	        	Conf::setMetaDesc($meta_desc);

	            if (!Conf::getPageTitle()) {
	          		Conf::setPageTitle($my_page->trad('seo_title') ? $my_page->hscTrad('seo_title') : $my_page->hscTrad('title'));
	          	}

	          	Conf::addToMetaKeys($my_page->trad('seo_keys') ? explode(',',$my_page->trad('seo_keys')) : $my_page->trad('title'));

	          	//add to HTML
	          	Conf::addToContent($my_page->printPage());

	        } else {
	            AcidUrl::error404();
	        }

        }
    }

    /**
     * Affiche la home page
     */
    public function home(){
        Conf::setPageTitleAlone(true);
        if (!Conf::getPageTitle()) {
        	Conf::setPageTitle(Acid::get('site:name'));
        }
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

    /**
     * Affiche la liste des pages activées
     */
    public function listAction(){
        $v = array();
        $elts = Acid::mod('Page')->dbList(array(array('active','=',1)),array('adate'=>'DESC'));


        $v['elts']= $elts;


    	Conf::addToContent(Acid::tpl('pages/page-list.tpl',$v));
    }

}
