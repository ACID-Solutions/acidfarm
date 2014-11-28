<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Script
 * @version   0.1
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Régénère les fichiers liés au module Photos
 */

$opt = getopt('c::');
if (isset($opt['c'])) {

	$acid_custom_log = '[SCRIPT]';
	include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';


	$lang_tab = array();
	$lang_csv = array();

	function composeTab($stab,&$dtab,$curkey='') {

		if (is_array($stab)) {
			foreach ($stab as $key => $val) {
				$newkey = $curkey."['$key']";
				if (is_array($val)) {
					composeTab($val,$dtab,$newkey);
				}else{
					$dtab[$newkey] = $val;
				}
			}
		}

	}

	$lang_router = Acid::get('router','lang');
	foreach (Acid::get('lang:available') as $l ) {

		Lang::switchTo($l);
		unset($GLOBALS['lang']['router']);

		$lang_tab[$l] = array();
		composeTab($GLOBALS['lang'],$lang_tab[$l],'$lang');

	}

	foreach ($lang_tab as $l =>$trads ) {
		if ($trads) {
			foreach ($trads as $key => $trad) {
				$lang_csv[$key]['key'] = $key;
				$lang_csv[$key][$l] = $trad;
			}
		}
	}

	print_r($lang_csv); exit();



}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n"  ;
	exit();
}
