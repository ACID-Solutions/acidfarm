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

//Ciblage des routes à utiliser
$acid_set_routes = ['web'];

//Chargement du core du site
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

//Lancement du Router
AcidRouter::after('*',function() { Seo::prepare(); })->run();


require 'sys/stop.php';