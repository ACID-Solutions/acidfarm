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
 * Test un envoi d'email
 */

$opt = getopt('c::e::');
if (isset($opt['c']) && !empty($opt['e'])) {

	include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';

	Mailer::send(Acid::get('site:name'),Acid::get('site:email'),$opt['e'],'Testing E-mail','my email content');
	Mailer::send(Acid::get('site:name'),Acid::get('site:email'),$opt['e'],'Testing E-mail','<div style="color:red;">my email content</div>',true);

}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n" .
		 "-e email" . "\n" ;
	exit();
}

