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
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur du sitemap.xml
 * @package   Controller
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
     * @return string
     */
	public function decline_sitemap ($tab, $url_base, $priority=0.9, $changefreq='monthly') {
		foreach ($tab as $elt) {
			if(!isset($elt['url'])){
				$this->xml_content .= AcidSitemap::getMultilangElt($elt, $priority, $changefreq);
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

		$langs =  Acid::get('lang:use_nav_0') ? Acid::get('lang:available') : array(Acid::get('lang:default'));

		$res = Acid::mod('Page')->dblist(array(array('active','=',1)));

		foreach ($langs as $lang) {
			$map = array();
			Acid::set('lang:current',$lang);

			//Site keys
			foreach (Conf::get('site_keys') as $key) {
				$map[] =  array('url'=>AcidRouter::getKey($key));
			}

			//Pages
			foreach ($res as $elt) {
				$mod = new Page($elt);

				$map[] =  array('url'=>$mod->trad('ident'));
			}


			$base = Acid::get('lang:use_nav_0') ? Acid::get('url:system').$lang.'/' : '';
			$this->decline_sitemap($map,$base);
		}

		Conf::addToContent(AcidSitemap::printSitemap($this->xml_content,!Acid::get('lang:use_nav_0')));

	}

	/**
	 * Retourne le plan du site pour le web
	 */
	public static function webmap(){

		$res = Page::dblist(array(array('active','=',1)));

		$map = array();
		$map[] = array('url'=>Acid::get('url:folder_lang'),'title'=>AcidRouter::getName('index'), 'class'=>"smmain");
		$map[] = array('url'=>Actu::buildUrl(),'title'=>AcidRouter::getName('news'), 'class'=>"smmain");
		$map[] = array('url'=>Photo::buildUrl(),'title'=>AcidRouter::getName('gallery'), 'class'=>"smmain");

		foreach ($res as $elt) {
			$p = new Page($elt);
			$map[] = array('url'=>$p->url(),'title'=>$p->hscTrad('title'), 'class'=>"smpage");
		}

		$map[] = array('url'=>Route::buildUrl('contact'),'title'=>AcidRouter::getName('contact'), 'class'=>"smmain");
		$map[] = array('url'=>Route::buildUrl('search'),'title'=>AcidRouter::getName('search'), 'class'=>"smmain");


		return $map;
	}

}