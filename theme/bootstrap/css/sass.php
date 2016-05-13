<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\ModelView
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

$acid['server_theme'] = basename(realpath(__DIR__.'/..'));
$acid['session']['enable'] = false;
$directory = "stylesheets";
require '../../../sys/glue.php';

/**
 * Generation des fichiers dynamics SAS
 * @param null $theme
 */
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

$themes =array();
$themes[Acid::get('def_theme')] = Acid::get('def_theme');
$themes[Acid::get('theme')] = Acid::get('theme');

foreach ($themes as $t) {
	sass_prepare_files($t);
}

Acid::load(Acid::get('externals:sass:path:lib'));
scss_server::serveFrom(__DIR__);