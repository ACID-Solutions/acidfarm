<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Config
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

// BRANCHING
//************************************************************************************//

if (file_exists(SITE_PATH.'sys/branch.txt')) {
	if ($sub_branch = trim(file_get_contents(SITE_PATH.'sys/branch.txt'))) {
		$acid['db']['prefix'] = $sub_branch.'_'.$acid['db']['prefix'];
	}
}


// COOKIE
//************************************************************************************//

if (empty($acid['cookie']['use_server'])) {

	$acid['cookie']['path']      = $acid['url']['folder']; // Dossier pour lequel le cookie est accessible (TODO : voir pour focer le logout à la racine du site)
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

//--permissions
$acid['permission_active']		=  empty($permission_active) ? false : true;

//--keys
$acid['conf']['root_keys'] = array('sitemap.xml','robots.txt','rss');
$acid['conf']['site_keys'] = array('news','gallery','search','contact','policy');

//--access
$acid['conf']['lvl']['seo'] = $acid['lvl']['dev'] ;

//--thème
$acid['def_theme'] = 'default';
$acid['theme'] = empty($acid['server_theme']) ?  $acid['def_theme'] : $acid['server_theme'];
//$acid['theme'] = 'bootstrap';

//--versioning
//$acid['versioning']['path'] =  'src/__VERSION__/';

//--sass
$acid['sass']['path']['compiled'] = 'compiled/';


if (!isset($acid['sass']['enable'])) {
	$acid['sass']['enable'] = false;
}

//--upload
//if (empty($acid['plupload']['use_server'])) {
//$acid['plupload']['runtimes'] = array('flash');
//$acid['plupload']['restriction'] = 'all';
//$acid['plupload']['fake_latence'] = 0.5;
//}

//--pages
$acid['conf']['page']['categories'] = array(0=>'page_standard',1=>'page_special'); //traduit dans /sys/dynamic.php

$acid['conf']['page']['autoident'] = true;
$acid['conf']['page']['special'] = array('home');

//--photos
$acid['conf']['photo']['limit'] = null;
$acid['conf']['photo_home']['limit'] = null;

//--contact
$acid['conf']['contact']['shield'] = true;
$acid['conf']['contact']['shield_key'] = 'form_who_i_am';
$acid['conf']['contact']['shield_val'] = 'human';
$acid['conf']['contact']['shield_time'] = 1500;

// PAGE CONTROLLER
//************************************************************************************//

//--page reserved by dev
$acid['conf']['admin_pages'] = array();

//--reserved keys
$acid['conf']['keys']['reserved']=$acid['conf']['site_keys'];

//--admin preview
$acid['conf']['admin_preview']['varname'] = 'admin_preview';
$acid['conf']['admin_preview']['mods'] = array('News','Page');
$acid['url']['params']['allowed'][] = $acid['conf']['admin_preview']['varname'];