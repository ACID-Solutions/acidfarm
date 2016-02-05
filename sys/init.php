<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Controller
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

//Timer Time
$start_time = microtime();

//Magic Quote Eraser
if (!empty($acid['magic_quote_eraser'])) {

	if (get_magic_quotes_gpc()) {


		/**
		 * Applique un stripslashes sur un tableau ou une chaine
		 * @param mixed $value
		 * @return mixed
		 */
		function stripslashes_deep($value) {
			$value = is_array($value) ?
						array_map('stripslashes_deep', $value) :
						stripslashes($value);
			return $value;
		}

		$_POST = array_map('stripslashes_deep', $_POST);
		$_GET = array_map('stripslashes_deep', $_GET);
		$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
		$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
	}

}

//Time_zone
date_default_timezone_set('Europe/Paris');

// Constantes utiles
define('DS'	,DIRECTORY_SEPARATOR);

// Fucking windows
if (strpos(__DIR__,'\\') !== false) {
	define('SITE_PATH'	,substr(__DIR__,0,strrpos(__DIR__,'\\')).'/');
}
// Nice UNIX
else {
	define('SITE_PATH'	,substr(__DIR__,0,strrpos(__DIR__,'/')).'/');
}

//AcidFarm
include (SITE_PATH . 'sys/config.php');
include (ACID_PATH . 'start.php');

//Hook for includes
AcidHook::call('includes');

if (!empty($acid_page_type)) {
	switch ($acid_page_type) {
		case 'ajax' :
			Acid::set('log:custom', '[AJAX]');
			Ajax::enableAjax();
		break;

		default :
			Acid::set('log:custom', '['.strtoupper($acid_page_type).']');
		break;
	}

}

if (!empty($acid_custom_log)) {
	Acid::set('log:custom', $acid_custom_log);
}

$rest_mode = (!empty($acid_page_type)) && ($acid_page_type=='rest');

//Chargement de la configuration Bdd du site
$site_config = new SiteConfig();
$site_config->getInstance();

//Chargement des permissions
UserPermission::setPermissions();

//Initialisation du contenu de la page
$html = '';

// Maintenance du site
if (Acid::get('maintenance')) {
	AcidUrl::error503();
}

// Activation des sessions
if ( (Acid::get('session:enable')) && (!$rest_mode)) {
	$sess = &AcidSession::getInstance()->data;
}


//FULL STACK
//Gestion de la navigation pour le mode full stack
if (Acid::get('include:mode')=='full_stack') {

	// Navigation
	$nav_empty = empty($_GET['acid_nav']);
	$path_info = pathinfo(substr($_SERVER['PHP_SELF'],strlen(Acid::get('url:folder'))));

	if ( (isset($path_info['basename'])) && (isset($path_info['dirname'])) )  {
		$use_nav_page = ($path_info['basename'] == 'index.php') && ($path_info['dirname'] == '.');
	}else{
		$use_nav_page = false;
	}

	Acid::set('index:key','index');
	Acid::set('index:urls',array(Acid::get('url:folder').'index.php', Acid::get('url:folder').'index' ));

	$is_index_nav = (isset($_GET['acid_nav']) && ($_GET['acid_nav'] == Acid::get('index:key')));

	//si c'est une des multiples désignation de l'index
	if ($is_index_nav || in_array($_SERVER['REQUEST_URI'],Acid::get('index:urls'))) {
		if (empty($_POST)) {
			AcidUrl::redirection301(Acid::get('url:system'));
		}
	//si une navigation est définie
	}elseif (isset($_GET['acid_nav'])) {
		$spage = $_GET['acid_nav'];
	//sinon
	} else {

		$file_request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$local_path = $file_request;
		if (strpos($local_path,Acid::get('url:folder'))===0) {
			$local_path = substr($local_path,strlen(Acid::get('url:folder')));
		}
		$file_path = SITE_PATH . $local_path;

		$spage = 'index';

		//si ce n'est pas un fichier et qu'on est pas à la racine, on redirige vers l'index.php
		if (!is_file($file_path) && $_SERVER['REQUEST_URI']!=Acid::get('url:folder')) {
			if (empty($_GET)) {
				AcidUrl::redirection301(Acid::get('url:system'));
			}else{
				$file = 'index.php'.AcidUrl::buildParams();
				AcidUrl::redirection301(Acid::get('url:system').$file);
			}
		}

	}

	// Nav parse
	unset($_GET['acid_nav']);
	$nav = explode('/',$spage);


	// Referer parse
	if (!empty($_SERVER['HTTP_REFERER'])) {
		$pre_nav = Lib::parseUrl($_SERVER['HTTP_REFERER']);
	}else{
		$pre_nav = array();
	}

	// Lang for ajax mode
	if (!empty($ajax_lang)) {
		$admin_lang = $ajax_lang;
	}

	// Getting http default lang
	$nav_lang = null;
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$langs_accepted = explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
		foreach ($langs_accepted as $lang_accepted) {
			if ($nav_lang === null) {
				 $lang_accepted_tab = explode(',',$lang_accepted);
				 foreach ($lang_accepted_tab as $lang_elt) {
					 if (in_array($lang_elt,Acid::get('lang:available'))) {
						 $nav_lang = $lang_elt;
						 break;
					 }
				}
			}
		}
	}

	//Langue pré-définie
	if (Acid::get('session:enable')) {
		if (User::curValue('lang')) {
			$acid_user_lang = in_array(User::curValue('lang'),Acid::get('lang:available')) ? User::curValue('lang') : Acid::get('lang:default');
			Acid::set('lang:current',$acid_user_lang);
		}
	}

	//Langue pré-définie
	if (!empty($admin_lang)) {
		$acid_cur_lang = in_array($admin_lang,Acid::get('lang:available')) ? $admin_lang : Acid::get('lang:default');
		Acid::set('lang:current',$acid_cur_lang);
	}

	//Detection de la langue si navigation multilangue
	if ((Acid::get('lang:use_nav_0')) && ($use_nav_page)) {
		$root = Conf::exists('root_keys') ? Conf::get('root_keys') : array();
		if (!in_array($nav[0],$root)) {
			$to_use_nav_lang = empty($acid_user_lang) ? ( $nav_lang ? $nav_lang : Acid::get('lang:default') ) : $acid_user_lang;
			$use_nav_lang = empty($nav[0]) ? $to_use_nav_lang : $nav[0];
			$use_nav_lang = in_array($use_nav_lang,Acid::get('lang:available')) ? $use_nav_lang : $to_use_nav_lang;
			$redirect_nav_lang = $nav_empty || ($use_nav_lang != $nav[0]) || !isset($nav[1]);

			Acid::set('lang:current',$use_nav_lang);
			$nav = array_slice($nav,1);
			$nav[0] = (!empty($nav[0])) ? $nav[0] : 'index';

			Acid::set('url:folder_lang', (Acid::get('url:folder').Acid::get('lang:current').'/') );
			Acid::set('url:system_lang', (Acid::get('url:scheme').Acid::get('url:domain').Acid::get('url:folder_lang')) );

			if ($redirect_nav_lang) {
				if ($nav_empty) {
					AcidUrl::redirection301( substr(Acid::get('url:system_lang'), 0, strlen(Acid::get('url:system_lang')) - 1) );
				}

				AcidUrl::redirection(Acid::get('url:system_lang'));
			}
		}else{
			Acid::set('lang:root_file',$nav[0]);
		}
	}

	//Intégration des fichiers de traduction
	Lang::loadLang(Acid::get('lang:current'));

}

//Définition des variables dynamiques
include (SITE_PATH.'sys/dynamic.php');

//Chargement des librairies utilitaires
include (Acid::outPath('functions.php'));


//Si on est pas en mode REST
if (!$rest_mode) {

	//initialisation de l'utilisateur
	if (Acid::get('session:enable')) {
		User::initUser();
		User::updateInstance();
	}

	//routage par défaut
	AcidRouter::addDefaultRoute('index',new AcidRoute('default',array('controller'=>'PageController','action'=>'home')));

	// Traitements des actions formulaires
	if (!empty($_POST)) {

		require 'post.php';

	    if (!isset($_POST['dontreload'])) {


			if (isset($_POST['next_page'])) {
				$next_page = $_POST['next_page'];
			}
			else {
				$next_page = $_SERVER['REQUEST_URI'];
			}
			$next_page = str_replace('&amp;','&',$next_page);

			header('HTTP/1.1 204 No Content');
			AcidUrl::redirection($next_page);

		}
	}

}

//FULL STACK
//Gestion des redirection après le POST pour le mode full stack
if (Acid::get('include:mode')=='full_stack') {

	// Suppression du slash de fin
	$need_redirect = false;

	$inav = count($nav)-1;

	while ( (empty($nav[$inav])) && ($inav > 0) ) {
		unset($nav[$inav]);
		$inav--;
		$need_redirect = true;
	}

	if ($need_redirect) {
		AcidUrl::redirection301(Acid::get('url:folder_lang').implode('/',$nav));
	}

}

