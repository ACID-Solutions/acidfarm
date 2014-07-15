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
$acid['ariane'] = array();

//Metas
$page_title = '';
$page_title_alone = false;
Acid::set('title:left',Acid::get('site:name').' - ');
Acid::set('title:right','');

$check_for_keywords = 3;

//Hook for index
AcidHook::call('index');

//Setting Default Meta Keys

Conf::setMetaDescBase($meta_desc_base);
Conf::setMetaDescStart($meta_desc_start);
Conf::setMetaDesc($meta_desc);
Conf::setPageTitleAlone($page_title_alone);

$searched_key = AcidRouter::searchKey($nav[0]);

if (Conf::defaultMetaKeys($searched_key)) {
    Conf::setMetaKeys(Conf::defaultMetaKeys($searched_key));
}

if (Conf::defaultMetaDesc($searched_key)) {
    Conf::setMetaDesc(Conf::defaultMetaDesc($searched_key));
}

if (Conf::defaultPageTitle($searched_key)) {
    Conf::setPageTitle(Conf::defaultPageTitle($searched_key));
}


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

// Route vers la page search
AcidRouter::addRoute('searchPage', new AcidRoute('@search',array('controller'=>'SearchController'),1));

//Par défault : affichage d'une page
AcidRouter::addRoute('page',new AcidRoute(':page_key',array('controller'=>'PageController'),1));

//Si pas de page définie, affichage de la home
AcidRouter::addDefaultRoute('index',new AcidRoute('default',array('controller'=>'PageController','action'=>'home')));


//Lancement du Router
AcidRouter::run();

require 'sys/stop.php';