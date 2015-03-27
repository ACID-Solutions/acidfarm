<?php

$directory = "stylesheets";
require '../../../sys/glue.php';

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

sass_prepare_files();

Acid::load(Acid::get('externals:sass:path:lib'));
scss_server::serveFrom(__DIR__);