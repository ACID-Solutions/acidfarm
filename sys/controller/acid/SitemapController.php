<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur du sitemap.xml
 * @package   Acidfarm\Controller
 */
class SitemapController{

    /**
     *
     * @var string contenu courant du sitemap
     */
    public $xml_content = '';

    /**
     * Ajoute au sitemap les éléments de $tab et retourne le contenu courant du sitemap
     * @param array $tab liste des pages
     * @param string $url_base url en prefixe
     * @param float $priority seuil de priorité
	 * @param string $changefreq fréquence de modification
     * @return string
     */
	public function decline_sitemap ($tab, $url_base, $priority=0.9, $changefreq='monthly') {
		foreach ($tab as $elt) {
			if(!isset($elt['url'])){
				$this->xml_content .= AcidSitemap::getMultilangElt($elt, $priority, $changefreq,null,$url_base);
			}
			else{

				$this->xml_content .= AcidSitemap::getElt($elt['url'], $priority, $changefreq,null,$url_base);
			}
		}
		return $this->xml_content;
	}


    /**
     * Affiche le sitemap.xml
     */
    public function index(){

    	$full_lang_file = false;

    	if ($full_lang_file || Acid::get('lang:root_file')) {
			$langs =  Acid::get('lang:use_nav_0') ? Acid::get('lang:available') : array(Acid::get('lang:default'));
		}else{
			$langs = array(Acid::get('lang:current'));
		}

		$respage = Acid::mod('Page')->dbList(array(array('active','=',1)));
		$resnews = Acid::mod('News')->dbList(array(array('active','=',1)),array('adate'=>'DESC'));

		foreach ($langs as $lang) {

			//Acid::set('lang:current',$lang);
			Lang::switchTo($lang);

			//Index
			$this->decline_sitemap(array(array('url'=>Acid::get('url:system_lang'))),'',1);

			//Site keys
			$map = array();
			foreach (Conf::get('site_keys') as $key) {
				$map[] =  array('url'=>AcidUrl::absolute(Route::buildUrl($key)));
			}
			$this->decline_sitemap($map,'',0.9);

			//Pages
			$map = array();
			foreach ($respage as $elt) {
				$mod = new Page($elt);

				$map[] =  array('url'=>AcidUrl::absolute($mod->url()));
			}

			$this->decline_sitemap($map,'',0.8);

            //Actus
            $map = array();
            foreach ($resnews as $elt) {
                $mod = new News($elt);

                $map[] =  array('url'=>AcidUrl::absolute($mod->url()));
            }

            $this->decline_sitemap($map,'',0.7);


			Lang::rollback();
		}

		Conf::addToContent(AcidSitemap::printSitemap($this->xml_content,false));

	}

	/**
	 * Retourne le plan du site pour le web
	 */
	public static function webmap(){



		$map = array();

        //home page
		$map[] = array('url'=>Acid::get('url:folder_lang'),'title'=>AcidRouter::getName('index'), 'class'=>"smmain");

        //site keys
        //$map[] = array('url'=>News::buildUrl(),'title'=>AcidRouter::getName('news'), 'class'=>"smmain");
		//$map[] = array('url'=>Photo::buildUrl(),'title'=>AcidRouter::getName('gallery'), 'class'=>"smmain");

        //site keys exclusion
        $delayed = array(
            'contact'=>false,
            'search'=>false
        );

        //site keys treatment
        foreach (Conf::get('site_keys') as $key) {
            if (!isset($delayed[$key])) {
                $map[] = array('url' => Route::buildUrl($key), 'title' => AcidRouter::getName($key), 'class' => "smmain");
            }else{
                $delayed[$key] = array('url' => Route::buildUrl($key), 'title' => AcidRouter::getName($key), 'class' => "smmain");
            }
        }

        //dynamic pages
        $res = Page::dbList(array(array('active','=',1)));
        foreach ($res as $elt) {
			$p = new Page($elt);
			$map[] = array('url'=>$p->url(),'title'=>$p->hscTrad('title'), 'class'=>"smpage");
		}

        //delayed keys
        //$map[] = array('url'=>Route::buildUrl('contact'),'title'=>AcidRouter::getName('contact'), 'class'=>"smmain");
        //$map[] = array('url'=>Route::buildUrl('search'),'title'=>AcidRouter::getName('search'), 'class'=>"smmain");
        foreach ($delayed as $key =>$elt) {
            $map[] = $elt;
        }




		return $map;
	}

}