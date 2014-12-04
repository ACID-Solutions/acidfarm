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

$opt = getopt('c::t:p:');
if (isset($opt['c'])) {

	$acid_custom_log = '[SCRIPT]';
	include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';

	//langs
	$lang_to = isset($_GET['to']) ? explode(';',$_GET['to']) : Acid::get('lang:available');
	if (isset($opt['t'])) {
		$lang_to = explode(';',$opt['t']);
	}

	//path
	$dir_to = isset($_GET['path']) ? $_GET['path'] : (__DIR__.'/trad/');
	if (isset($opt['p'])) {
		$dir_to = $opt['p'];
	}

	if (!file_exists($dir_to)) {
		mkdir($dir_to);
	}


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

	//Trad
	$csv = new AcidCSV();
	$csv->setConfig(';');

	$head = array_merge(array('variable'),$lang_to);
	$csv->setHead(array_flip($head));

	$rows = array();
	foreach ($lang_csv as $key =>$tab) {
		$line = array();
		$line[$csv->getCol('variable')] = $tab['key'];
		foreach ($lang_to as $l) {
			$line[$csv->getCol($l)] = isset($tab[$l]) ? $tab[$l] : '';
		}
		$rows[] = $line;
	}

	$csv->setRows($rows);
	$csv->writeFile($dir_to.'trad.csv');


	//Router
	$csvrouter = new AcidCSV();
	$csvrouter->setConfig(';');

	$headr = array('key');
	foreach ($lang_to as $lang) {
		$headr[] = 'name_'.$lang;
		$headr[] = 'key_'.$lang;
	}
	$csvrouter->setHead(array_flip($headr));

	$rowsr = array();
	foreach ($lang_router as $key => $langs) {
		$line = array();
		$line[$csvrouter->getCol('key')] = $key;
		foreach ($lang_to as $l) {
			$line[$csvrouter->getCol('name_'.$l)] = isset($langs[$l]['name']) ? $langs[$l]['name'] : '';
			$line[$csvrouter->getCol('key_'.$l)] = isset($langs[$l]['key']) ? $langs[$l]['key'] : '';
		}
		$rowsr[] = $line;
	}

	$csvrouter->setRows($rowsr);
	$csvrouter->writeFile($dir_to.'tradrouter.csv');

	exit();



}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n"  ;
	echo "-p [dossier de destination]=__DIR__.'/trad/' : chemin dans lequel seront mis les dossiers (optionnel)" . "\n" ;
	echo "-t [langues du csv]=Acid::get('lang:available') : les langues pour le csv séparées d'un ;  (optionnel)" . "\n" ;
	exit();
}
