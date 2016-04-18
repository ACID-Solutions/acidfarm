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
 * @since     Version 0.7
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur de Redirections
 * @package   Acidfarm\Controller
 */
class RedirectController{

    /**
     * Pour des raisons de sécurité, on définit si le dossier est accéssible ou non
     * @param string $path : le chemin à tester
     * @param array $allowed : les chemins accessibles sans le SITE_PATH
     * @return bool
     */
    public static function allowed($path,$allowed=null) {
        $allowed = $allowed!==null ? $allowed : array('js',Acid::get('keys:theme'));
        if ($allowed) {
            foreach ($allowed as $p) {
                if (strpos(realpath($path),realpath(SITE_PATH.$p))===0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Redirection des ressources
     */
    public function src(){

        if (AcidRouter::getParam('version') == AcidTemplate::versioningVal()) {
            $realpath = realpath(SITE_PATH.implode('/',AcidRouter::getPartialParams()));
            if (self::allowed($realpath)) {
                if (file_exists($realpath)) {
                    $mt = false;
                    $default_mime_type = 'application/octet-stream';
                    if ($assoc = json_decode(file_get_contents(SITE_PATH.'sys/mime.json'),true)) {
                        $mt = Lib::getIn(AcidFs::getExtension($realpath),$assoc,$default_mime_type);
                    }

                    header('Content-Type: ' . ($mt ? $mt : $default_mime_type));
                    readfile($realpath);
                    exit();
                }
            }
        }

        AcidUrl::error404();
    }

    /**
     * Route par défaut
     */
    public function index(){

    }

}
