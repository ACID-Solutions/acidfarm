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
 * Contôleur des Pages
 * @package   Controller
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
	       Conf::addToAriane( (Conf::getPageTitle() ? Conf::getPageTitle() : $page_key)  ,$_SERVER['REQUEST_URI']);
	     
	            
        }else{
  
	        $my_page = new Page();
	        $my_page->init($page_key);
	            
	        if ($my_page->trad('title')) {
	        	Conf::addToMetaKeys($my_page->trad('title'));
	        }
	            
	        if (count(AcidRouter::getParams()) == 1 && $my_page->get('active')) {
	            Conf::addToAriane($my_page->trad('title'),$my_page->url());
	            
	            Conf::addToContent($my_page->printPage());
	            
	            $meta_desc = (AcidVarString::split($my_page->trad('content'),100) . ' - ' .Acid::get('site:name'));
	        	Conf::setMetaDesc($meta_desc);
	          	
	            if (!Conf::getPageTitle()) {
	          		Conf::setPageTitle($my_page->hscTrad('title'));
	          	}
	        
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
        Conf::setPageTitle(Acid::get('site:name'));
        
  		Conf::setAriane(array());
        
        if (isset($_GET['search'])) {
            AcidUrl::redirection301(Route::buildUrl('search').'/'.$_GET['search']);
        }
        
        $page = new Page(); 
		$page->init('home');
		
		$message = $page->trad('content');
		$vars = array (	'welcome'=>$message	);
		
		Conf::addToContent(Acid::tpl('pages/home.tpl',$vars));
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
