<?php

$opt = getopt('c::f:t:p:');
if (isset($opt['c']) ) {

	include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';
	Acid::load(Acid::get('externals:sass:path:lib'));

	$path_from = empty($opt['f']) ? (SITE_PATH.Acid::get('rel:css') . 'sass/') : (SITE_PATH.$opt['f']) ;

	$path_to = empty($opt['t']) ? (SITE_PATH.Acid::get('rel:css').Acid::get('sass:path:compiled')) : (SITE_PATH.$opt['t']) ;

	$path_unique = empty($opt['p']) ? (false) : (SITE_PATH.$opt['p']) ;

	function sass_prepare_files() {

		$tpl_path = SITE_PATH.Acid::get('rel:tpl').'sass/';

		if (is_dir($tpl_path)) {
			if ($dh = opendir($tpl_path)) {
				while (($file = readdir($dh)) !== false) {
					if (AcidFs::getExtension($file)=='tpl') {
						$scss_name = SITE_PATH.Acid::get('rel:css').'/sass/_dynamic/'.AcidFs::removeExtension($file).'.scss';
						file_put_contents($scss_name,Acid::tpl('sass/'.$file));
					}
				}
				closedir($dh);
			}
		}
	}

	function sass_compilation_from_file($file,$path_to) {
		if (AcidFs::getExtension($file)=='scss') {

			$scss = new scssc();
			$scss->addImportPath(SITE_PATH.Acid::get('rel:css'));

			echo 'preparing environement...'."\n";
			sass_prepare_files();

			$fname = AcidFS::removeExtension(basename($file)).'.css';
			echo 'translating '.$file." to ".$path_to.$fname."\n";
			file_put_contents($path_to.$fname, $scss->compile(file_get_contents($file)));

		}
	}

	if (!is_dir($path_to)) {
		mkdir($path_to);
	}

	if ($path_unique) {

		if (is_file($path_unique)) {
			if (is_dir($path_to)) {
				sass_compilation_from_file($path_unique,$path_to);
			}
		}

	}else{
		if (is_dir($path_from)) {
			if (is_dir($path_to)) {
				if ($dh = opendir($path_from)) {
					while (($file = readdir($dh)) !== false) {
						if (strpos(basename($file),'_')!==0) {
							sass_compilation_from_file($path_from.$file,$path_to);
						}
					}
					closedir($dh);
				}
			}
		}
	}




}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n" .
		 "-f chemin du dossier source scss (optionnel)" . "\n" .
		 "-t chemin du dossier des destinations des fichiers css (optionnel)" . "\n" ;
	exit();
}

