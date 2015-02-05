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
	$dir_to = isset($_GET['path']) ? $_GET['path'] : (__DIR__.'/csvtrad/');
	if (isset($opt['p'])) {
		$dir_to = $opt['p'];
	}

	//from
	$fromfile = $dir_to.'trad.csv';


	if (!file_exists($dir_to)) {
		mkdir($dir_to);
	}

	//config
	$delimiter = ';';
	$enclosure = '"';
	$escape = "\\";


	$init = '<?php '. "\n" . "\n" ;
	foreach ($lang_to as $l) {
		$lpath = $dir_to.$l.'.php';
		if (file_exists($lpath)) {
			unlink($lpath);
			file_put_contents($lpath, $init);
		}
		$lpath = $dir_to.'module_'.$l.'.php';
		if (file_exists($lpath)) {
			unlink($lpath);
			file_put_contents($lpath, $init);
		}
	}

	if (file_exists($fromfile)) {

		$file = fopen($fromfile,"r");

		$head = null;
		$colvar = null;
		while(! feof($file))
		{

			if ($head===null) {
				$head = array_flip(fgetcsv($file,null,$delimiter,$enclosure,$escape));
				$colvar = Lib::getIn('variable',$head);
			}else{
				$line = fgetcsv($file,null,$delimiter,$enclosure,$escape);
				foreach ($lang_to as $l) {

					if ($col = Lib::getIn($l,$head)) {
						if ($tradadd = Lib::getIn($col,$line)) {
							if ($varname = Lib::getIn($colvar,$line)) {
								$lpath = strpos($varname,'$lang[\'mod\']')===0 ? ($dir_to.'module_'.$l.'.php') : ($dir_to.$l.'.php');

								$add = $varname."= '".addslashes($tradadd)."';" . "\n" ;
								file_put_contents($lpath, $add, FILE_APPEND | LOCK_EX);
							}
						}
					}
				}
			}

		}

		fclose($file);

	}





	exit();



}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n"  ;
	echo "-p [dossier de destination]=__DIR__.'/csvtrad/' : chemin dans lequel seront mis les fichiers (optionnel)" . "\n" ;
	echo "-t [langues du csv]=Acid::get('lang:available') : les langues du csv séparées d'un ;  (optionnel)" . "\n" ;
	exit();
}