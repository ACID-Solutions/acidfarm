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
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

//Définition du thème css par défaut
$acid['css']['theme'] 		= 'style';
$acid['css']['dialog'] 		= 'dialog';


//Définitions des racccourcis

//chemin
$acid['rel']['theme']		= Acid::get('keys:theme') . '/' . Acid::get('theme') . '/';

$acid['rel']['img']			= Acid::get('rel:theme') . 'img/';
$acid['rel']['css']			= Acid::get('rel:theme') . 'css/';
$acid['rel']['tpl']			= Acid::get('rel:theme') . 'tpl/';
$acid['rel']['js']			= Acid::get('rel:theme') . 'js/';
$acid['rel']['out']			= Acid::get('rel:theme') . 'out/';
$acid['rel']['t_files']		= Acid::get('rel:theme') . 'files/';

//urls
$acid['url']['theme']		= Acid::get('url:folder') . $acid['rel']['theme'];
$acid['url']['prefix']  	= Acid::get('url:scheme').Acid::get('url:domain');

$acid['url']['img']			= Acid::get('url:theme') . 'img/';
$acid['url']['css']			= Acid::get('url:theme') . 'css/';
$acid['url']['tpl']			= Acid::get('url:theme') . 'tpl/';
$acid['url']['js']			= Acid::get('url:theme') . 'js/';
$acid['url']['out']			= Acid::get('url:theme') . 'out/';


$acid['url']['t_files']		= Acid::get('url:theme') . 'files/';
$acid['url']['img_abs']		= Acid::get('url:prefix').Acid::get('url:img');

$acid['url']['ajax']  		= Acid::get('url:folder').'ajax.php';
$acid['url']['upload']  	= Acid::get('url:folder').'upload.php';

//Url des pages
$acidconf['url']['admin'] 		= Acid::get('url:folder').'siteadmin.php';
$acidconf['url']['sitemap']		= Acid::get('url:folder').'sitemap.xml';
$acidconf['url']['robots'] 		= Acid::get('url:folder').'robots.txt';

//configuration du site
$acid['user']['page']		= Acid::get('url:folder_lang') . AcidRouter::getKey('account');
$acid['url']['system']		= Acid::get('url:prefix').Acid::get('url:folder');

//configuration du css
$acid['css']['dynamic']['files'] = array();

//traduction des levels
$level_assoc = array_flip(Acid::get('lvl'));
foreach ($acid['user']['levels'] as $lvl => $value) {
	Acid::set('user:levels:'.$lvl,Acid::get('lvl:'.$level_assoc[$lvl],'lang'));
}

// Hooks
AcidHook::call('dynamic');