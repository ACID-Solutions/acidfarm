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
 * Régénère les fichiers liés au module Page
 */

$opt = getopt('c::');
if (isset($opt['c'])) {

	$acid_custom_log = '[SCRIPT]';
    include_once pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../../glue.php';
	Page::regenAll();

}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n"  ;
	exit();
}
