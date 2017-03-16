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
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

require 'sys/start.php';

// Config référencement
$meta_desc_start = '';
$meta_desc_base = '';
$meta_desc =  '';
$meta_keys =  array();
$meta_image =  '';

//Ariane
Acid::set('ariane',array());

//Metas
$page_title = '';
$page_title_alone = false;
Acid::set('title:right',' - '.Acid::get('site:name'));
Acid::set('title:left','');

//Meta keywords autodetection
Conf::set('meta:check_for_keywords',3);

//Hook for index
AcidHook::call('index');

//Setting Default Meta Keys

Conf::setMetaDescBase($meta_desc_base);
Conf::setMetaDescStart($meta_desc_start);
Conf::setMetaDesc($meta_desc);
Conf::setPageTitleAlone($page_title_alone);

//Std Metas : default
if ($page_title) {
	Conf::set('meta:title:'.Acid::get('lang:default').':default',$page_title);
}

if ($meta_keys) {
	Conf::set('meta:keywords:'.Acid::get('lang:default').':default',$meta_keys);
}

if ($meta_image) {
	Conf::set('meta:image:'.Acid::get('lang:default').':default',$meta_image);
}

$searched_key = AcidRouter::searchKey($nav[0]);
Conf::executeMetaDefault($searched_key);

//Définitions des Routes Controller

//Hooks
AcidHook::call('routes');

// Page Actu avec X Params Facultatif
AcidRouter::addRoute('news', new AcidRoute('@news',array('controller'=>'ActuController'),1));

//AcidRouter::addRoute('page',new AcidRoute('page/:page_key',array('controller'=>'PageController'),1));
//AcidRouter::addRoute('allpage',new AcidRoute('@page',array('controller'=>'PageController','action'=>'listAction')));

// Route Contact de base, sans parametre
AcidRouter::addRoute('contact', new AcidRoute('@contact',array('controller'=>'ContactController')));

// Route Gallery
AcidRouter::addRoute('gallery', new AcidRoute('@gallery',array('controller'=>'GalleryController')));

// Route Account
AcidRouter::addRoute('userspace',new AcidRoute('@account',array('controller'=>'UserspaceController'),1));

// Routes d'accès au fichiers dynamics sitemap.xml / robots.txt
AcidRouter::addRoute('sitemap', new AcidRoute('sitemap.xml',array('controller'=>'SitemapController')));
AcidRouter::addRoute('robots',new AcidRoute('robots.txt',array('controller'=>'RobotsController','action'=>'index')));

// Routes RSS
AcidRouter::addRoute('rss',new AcidRoute('@rss',array('controller'=>'RssController','action'=>'index')));

// Route vers la page search
AcidRouter::addRoute('searchPage', new AcidRoute('@search',array('controller'=>'SearchController'),1));

// Redirections
//AcidRouter::addRoute('srcRedirect', new AcidRoute('src/:version',array('controller'=>'RedirectController','action'=>'src'),1));

//Par défault : affichage d'une page
AcidRouter::addRoute('page',new AcidRoute(':page_key',array('controller'=>'PageController'),1));

//Si pas de page définie, affichage de la home
AcidRouter::addDefaultRoute('index',new AcidRoute('default',array('controller'=>'IndexController','action'=>'index')));

//Lancement du Router
AcidRouter::after('*',function() { Seo::prepare(); })->run();

require 'sys/stop.php';