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
	echo 	'<div style="margin-top:250px; text-align:center;">'. "\n" .
			'	<h2>AcidFarm</h2>You must configure your website ( by running <b><a href="install.php">install.php</a></b> / or by including file <b>'.$server_file . '</b> )' . "\n" .
			'</div>' ;
	exit();
}

// Maintenance
if (file_exists(SITE_PATH.'sys/maintenance.txt')) {
	Acid::set('maintenance',true);
}

// Mods path
include(SITE_PATH.'sys/includes.php');


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

$acidconf['root_keys'] = array('sitemap.xml','robots.txt');
$acidconf['site_keys'] = array('news','gallery','search','contact');


$acid['def_theme'] = 'default';
$acid['theme'] = empty($acid['server_theme']) ?  $acid['def_theme'] : $acid['server_theme'];


$acidconf['photo']['limit'] = 12;
$acidconf['photo_home']['limit'] = null;

$acidconf['contact']['shield'] = true;
$acidconf['contact']['shield_key'] = 'form_who_i_am';
$acidconf['contact']['shield_value'] = 'human';

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
$acidconf['meta']['keywords'][$acid['lang']['default']] 		= array(
																//''=>array('',''),
															);


//--description
$acidconf['meta']['description'][$acid['lang']['default']]		= array(
																//''=>"",
															);


//--title
$acidconf['meta']['title'][$acid['lang']['default']]			= array(
																'news'=>"News",
																'contact'=>"Contact",
																'search'=>"Search",
															);