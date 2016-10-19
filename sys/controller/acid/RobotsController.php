<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur du fichier robots.txt
 * @package   Acidfarm\Controller
 */
class RobotsController{

	/**
	 * Retourne la liste des dossiers de thème
	 * @return array
	 */
	public static function getThemes() {

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

	/**
	 * Fichier robots.txt
	 */
    public function index(){
      	Acid::set('out','text');

      	$disallow = '';

        if (Acid::get('donotindex')) {
            $disallow .= 'Disallow: '.Acid::get('url:folder')   . "\n" ;
        }else {

            $themes = self::getThemes();
            foreach ($themes as $theme) {
                $disallow .= 'Disallow: ' . Acid::get('url:folder') . Acid::get('keys:theme') . '/' . $theme . '/css/' . "\n";
                $disallow .= 'Disallow: ' . Acid::get('url:folder') . Acid::get('keys:theme') . '/' . $theme . '/out/' . "\n";
                $disallow .= 'Disallow: ' . Acid::get('url:folder') . Acid::get('keys:theme') . '/' . $theme . '/tpl/' . "\n";
                $disallow .= 'Disallow: ' . Acid::get('url:folder') . Acid::get('keys:theme') . '/' . $theme . '/js/' . "\n";
            }
        }


		$robot = 	'User-agent: *' . "\n" .
					$disallow .
					'' . "\n";



		Conf::addToContent($robot);
    }
}
