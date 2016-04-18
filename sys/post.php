<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Controller
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

$logpost = $_POST;
if ((isset($logpost['do']) && (in_array($logpost['do'], array('login')))) || (isset($logpost['connexion_do'])) ) {
	foreach (array() as $keylog) {
		if (isset($logpost[$keylog])) {
			$logpost[$keylog] = '******';
		}
	}
}

Acid::log('postinfo', $_SERVER['SERVER_PROTOCOL']. ' ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . (!empty($_SERVER['HTTP_REFERER']) ? (' from ' . $_SERVER['HTTP_REFERER'] ) : '') . ' : ' . json_encode($logpost));

if (isset($_POST['do'])) {

	// Traitement d'une connexion
	if ($_POST['do'] == 'login') {
        $auto = (isset($_POST['auto']) && $_POST['auto']) ? true : false;

	    User::login($_POST['login'],$_POST['pass'],true,$auto);

	} elseif ($_POST['do'] === 'logout') {
	    User::logout();
	}

}

if (isset($_POST['search_form'])){
   AcidRouter::directlyRun('searchPageX', new AcidRoute('@search',array('controller'=>'SearchController'),1),null,$_POST['search_form']);
}

if (Acid::get('session:enable')) {
	Acid::mod('User')->exePost();
	Acid::mod('User')->exeUser();
}

$excluded = array('User');

//Hooks
AcidHook::call('post');

if (isset($_POST['module_do'])) {
	if (isset($acid['includes'][$_POST['module_do']])) {
		$mod = $_POST['module_do'];
		switch ($mod) {

			case 'Contact' :
				Contact::exePost();
			break;

			default:
				if (!in_array($mod,$excluded)) {
					$module = new $_POST['module_do']();
					if (isset($_POST[$module->preKey('do')])) {
						$module->exePostProcess();
					}
				}
			break;
		}

	}
}


Acid::tool('AcidBrowser');
$fb = new AcidBrowser(Acid::get('path:uploads'));
$fb->exePost();


