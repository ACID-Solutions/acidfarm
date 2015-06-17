<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Config
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


// INIT
//************************************************************************************//

//Acid path
$acid['folder'] = 'acid/';
define('ACID_PATH',	SITE_PATH.$acid['folder']);

//Config path
include (ACID_PATH.'config.php');
$server_file = SITE_PATH.'sys/server.php';

//Checking Configuration
if (file_exists($server_file)) {
	include ($server_file);
}else{

	$base_folder = substr(__DIR__,strlen($_SERVER['DOCUMENT_ROOT']),(-1*strlen('/sys')));

	echo 	'<div style="margin-top:250px; text-align:center;">'. "\n" .
			'	<h2>AcidFarm</h2>You must configure your website ( by running <b><a href="'.$base_folder.'/install.php">install.php</a></b> / or by including file <b>'.$server_file . '</b> )' . "\n" .
			'</div>' ;
	exit();
}

// Maintenance
if (file_exists(SITE_PATH.'sys/maintenance.txt')) {
	Acid::set('maintenance',true);
}

// Mods path
include(SITE_PATH.'sys/includes.php');


// COOKIE
//************************************************************************************//

if (empty($acid['cookie']['use_server'])) {

	$acid['cookie']['path']      = $acid['url']['folder']; //$acid['url']['folder']; // Dossier pour lequel le cookie est accessible (TODO : voir pour focer le logout à la racine du site)
	$acid['cookie']['domain']	 = $acid['url']['domain']; // Domaine pour lequel le cookie est accessible
	$acid['cookie']['dyndomain'] = true;	// Si true, autorise le cookie sur un domaine à la volée

}

// LANG
//************************************************************************************//

if (empty($acid['lang']['use_server'])) {

	$def_lang = 'fr'; //fr
	$acid['lang']['use_nav_0'] 		= false;
	$acid['lang']['default']        = $def_lang;
	$acid['lang']['available']      = array($def_lang);

}

$acid['lang']['current']      	= $acid['lang']['default'];

// SITE CONFIGURATION
//************************************************************************************//

$acid['permission_active']		=  empty($permission_active) ? false : true;

$acidconf['root_keys'] = array('sitemap.xml','robots.txt','rss');
$acidconf['site_keys'] = array('news','gallery','search','contact');

$acidconf['lvl']['seo'] = $acid['lvl']['dev'] ;


$acid['def_theme'] = 'default';
$acid['theme'] = empty($acid['server_theme']) ?  $acid['def_theme'] : $acid['server_theme'];

$acidconf['page']['categories'] = array(0=>'page_standard',1=>'page_special'); //traduit dans /sys/dynamic.php

$acidconf['page']['autoident'] = true;
$acidconf['page']['special'] = array('home');

$acidconf['photo']['limit'] = 12;
$acidconf['photo_home']['limit'] = null;

$acidconf['contact']['shield'] = true;
$acidconf['contact']['shield_key'] = 'form_who_i_am';
$acidconf['contact']['shield_value'] = 'human';

$acid['sass']['path']['compiled'] = 'compiled/';

//$acid['plupload']['runtimes'] = array('html5','flash');

// PAGE CONTROLLER
//************************************************************************************//

//--page reserved by dev
$acidconf['admin_pages'] = array();


//--reserved keys
$acidconf['keys']['reserved']=$acidconf['site_keys'];



// SEO
//************************************************************************************//

//--keywords

/**
 * Configuration des meta keywords de base
 */

//$acidconf['meta']['keywords']['fr']['default']		= array('exemple', 'de', 'mots-clé');
//$acidconf['meta']['keywords']['fr']['default']		= array();
//$acidconf['meta']['keywords']['en']['default']		= array();
//$acidconf['meta']['keywords']['es']['default']		= array();
//$acidconf['meta']['keywords']['de']['default']		= array();
//$acidconf['meta']['keywords']['it']['default']		= array();


//--description

/**
 * Configuration des meta description de base
 */

//$acidconf['meta']['description']['fr']['default']		= "exemple de metadesc";
//$acidconf['meta']['description']['fr']['default']		= "";
//$acidconf['meta']['description']['en']['default']		= "";
//$acidconf['meta']['description']['es']['default']		= "";
//$acidconf['meta']['description']['de']['default']		= "";
//$acidconf['meta']['description']['it']['default']		= "";

//--image

/**
 * Configuration des meta image de base
 */

//$acidconf['meta']['image']['fr']['default']		= "/ascreen.jpg";
//$acidconf['meta']['image']['fr']['default']		= "";
//$acidconf['meta']['image']['en']['default']		= "";
//$acidconf['meta']['image']['es']['default']		= "";
//$acidconf['meta']['image']['de']['default']		= "";
//$acidconf['meta']['image']['it']['default']		= "";

//--title

/**
 * Configuration des meta title de base
 */

//$acidconf['meta']['title']['fr']['default']		= 'exemple';

$acidconf['meta']['title']['fr']		= array(
		'news'=>"Actualités",
		'contact'=>"Contact",
		'search'=>"Recherche",
);
$acidconf['meta']['title']['en']		= array(
		'news'=>"News",
		'contact'=>"Contact",
		'search'=>"Search",
);
$acidconf['meta']['title']['es']		= array(
		'news'=>"Noticias",
		'contact'=>"Contacto",
		'search'=>"Búsqueda",
);
$acidconf['meta']['title']['de']		= array(
		'news'=>"Aktualitäten",
		'contact'=>"Kontakt",
		'search'=>"Suche",
);
$acidconf['meta']['title']['it']		= array(
		'news'=>"Notizie",
		'contact'=>"Contatto",
		'search'=>"Ricerca",
);
