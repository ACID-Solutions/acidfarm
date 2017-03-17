<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Script
 * @version   0.1
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Régénère les fichiers liés au module Actualités
 */

$opt = getopt('c::');
if (isset($opt['c'])) {
    $base = pathinfo(__FILE__,PATHINFO_DIRNAME ).'/';
    include_once $base.'../glue.php';


    if ($handle = opendir($base.'regen/')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                if (AcidFs::getExtension($entry)=='php') {
                    echo  "\n" . '--------------'.$entry.'-------------' . "\n" ;
                    require $base.'regen/'.$entry;
                }
            }
        }
        closedir($handle);
    }



}else{
    echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n"  ;
    exit();
}
