<?php



$opt = getopt('c::f:t:p:l:');
if (isset($opt['c']) ) {

	function getThemes() {

		$themes = array();
		$path = SITE_PATH.Acid::get('keys:theme');

		if (is_dir($path)) {
			if ($handle = opendir($path)) {
				while (false !== ($entry = readdir($handle))) {
					if (!in_array($entry,array('.','..'))) {
						if (is_dir($path.'/'.$entry)) {
							$themes[$entry] = $entry;
						}
					}
				}
				closedir($handle);
			}

		}

		return $themes;
	}

	function sass_prepare_files($theme=null) {

		$theme = $theme===null ? Acid::get('theme') : $theme;
		$base_from = SITE_PATH.Acid::get('keys:theme') . '/' . $theme.'/';

		//sauvegarde du thème courant avant changement
		$bktheme = Acid::get('theme');

		//changement du thème courant vers le temporaire
		Acid::set('theme',$theme);

		$tpl_path = $base_from.'tpl/sass/';
		$dyn_path = $base_from.'css/sass/_dynamic/';
		if (!file_exists($dyn_path)) {
			mkdir($dyn_path);
		}



		if (is_dir($tpl_path)) {
			if ($dh = opendir($tpl_path)) {

				while (($file = readdir($dh)) !== false) {
					if (AcidFs::getExtension($file)=='tpl') {
						$scss_name = $dyn_path.AcidFs::removeExtension($file).'.scss';
						file_put_contents($scss_name,Acid::tpl('sass/'.$file));
					}
				}
				closedir($dh);
			}
		}

		//retour au thème courant
		Acid::set('theme',$bktheme);

	}

	function sass_compilation_from_file($file,$path_to,$theme=null) {
		if (AcidFs::getExtension($file)=='scss') {
			$theme = $theme===null ? Acid::get('theme') : $theme;
			$base_from = SITE_PATH.Acid::get('keys:theme') . '/' . $theme.'/';

			echo 'theme is '.$theme."\n";

			$scss = new scssc();
			$scss->addImportPath($base_from.'css/');

			echo 'preparing environement...'."\n";
			sass_prepare_files($theme);

			$fname = AcidFS::removeExtension(basename($file)).'.css';
			echo 'translating '.$file." to ".$path_to.$fname."\n";
			file_put_contents($path_to.$fname, $scss->compile(file_get_contents($file)));

			echo '----------------------------------'."\n";
		}
	}

	include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';
	Acid::load(Acid::get('externals:sass:path:lib'));

	$themes =  (empty($opt['f']) && empty($opt['t']) && empty($opt['p']) && empty($opt['b'])) ?
						(
							empty($opt['l']) ?
								getThemes() :
								(
									$opt['l']=='usefull' ?
									array(Acid::get('def_theme')=>Acid::get('def_theme'),Acid::get('theme')=>Acid::get('theme')) :
									explode(';' , $opt['l'])
								)
						) : array();

	$recursion = array();

	if ($themes) {

		foreach ($themes as $themekey) {
			$base_from = Acid::get('keys:theme') . '/' . $themekey . '/css/';
			$recursion[$themekey] = array(
				'f'=>($base_from . 'sass/'),
				't'=>$base_from.Acid::get('sass:path:compiled'),
				'b'=>$themekey
			);
		}

	}else{
		foreach (array('f','t','p','b') as $var) {
			if (!empty($opt[$var])) {
				$recursion[0][$var] = $opt[$var];
			}
		}
	}

	foreach ($recursion as $kelt => $relt) {

		$path_from = empty($relt['f']) ? (SITE_PATH.Acid::get('rel:css') . 'sass/') : (SITE_PATH.$relt['f']) ;

		$path_to = empty($relt['t']) ? (SITE_PATH.Acid::get('rel:css').Acid::get('sass:path:compiled')) : (SITE_PATH.$relt['t']) ;

		$path_unique = empty($relt['p']) ? (false) : (SITE_PATH.$relt['p']) ;

		$theme_from = empty($relt['b']) ? null : $relt['b'] ;

		echo '================='.$kelt.'================='."\n";

		if (!is_dir($path_to)) {
			mkdir($path_to);
		}

		if ($path_unique) {

			if (is_file($path_unique)) {
				if (is_dir($path_to)) {
					sass_compilation_from_file($path_unique,$path_to,$theme_from);
				}
			}

		}else{
			if (is_dir($path_from)) {
				if (is_dir($path_to)) {
					if ($dh = opendir($path_from)) {
						while (($file = readdir($dh)) !== false) {
							if (strpos(basename($file),'_')!==0) {
								sass_compilation_from_file($path_from.$file,$path_to,$theme_from);
							}
						}
						closedir($dh);
					}
				}
			}
		}

	}





}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n" .
		 "-f chemin du dossier source scss (optionnel)" . "\n" .
		 "-t chemin du dossier des destinations des fichiers css (optionnel)" . "\n" .
		 "-p chemin vers un fichier unique (optionnel)" . "\n" .
		 "-b base folder (optionnel)" . "\n" .
	     "-l liste de thèmes à traiter separés de ; - choix par default si pas paramètres (optionnel)";
	exit();
}

