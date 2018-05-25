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
 * @since     Version 0.9
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

//Hooks
AcidHook::call('routes');
AcidHook::call('routes_web');

// Page d'actualités avec X Params Facultatif
AcidRouter::addRoute('news', new AcidRoute('@news',array('controller'=>'NewsController'),1));

//AcidRouter::addRoute('page',new AcidRoute('page/:page_key',array('controller'=>'PageController'),1));
//AcidRouter::addRoute('allpage',new AcidRoute('@page',array('controller'=>'PageController','action'=>'listAction')));

// Route Contact de base, sans parametre
AcidRouter::addRoute('contact', new AcidRoute('@contact',array('controller'=>'ContactController')));

// Route Gallery
AcidRouter::addRoute('gallery', new AcidRoute('@gallery',array('controller'=>'GalleryController')));

// Route Account
AcidRouter::addRoute('userspace',new AcidRoute('@account',array('controller'=>'UserspaceController'),1));

//Route Politique sur les données
AcidRouter::addRoute('policy', new AcidRoute('@policy',array('controller'=>'PolicyController')));

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