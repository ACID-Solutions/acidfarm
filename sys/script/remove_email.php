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

$opt = getopt('c::r::t:');
if (isset($opt['c'])) {

	$acid_custom_log = '[SCRIPT]';
	include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';

	$tampon = '@'.Acid::get('url:domain');
	if (isset($opt['t'])) {
		$tampon = $opt['t'];
	}
	
	$remove_way = false;
	if (isset($opt['r'])) {
		$remove_way = true;
	}

	$done = 0;

	//Standard Effect
	if (!$remove_way) {
		$res = User::dbList();
		if ($res) {
			foreach ($res as $elt) {
				$user = new User($elt);
				if ($user->get('email')) {
					if (strpos($user->get('email'),$tampon)===false) {
						$changes = $user->initVars(array('email'=>str_replace('@','.at.',$user->get('email')).$tampon));
						$user->dbUpdate($changes);
						$done++;
					}
				}
			}
		}
	}
	
	//remove Effect
	if ($remove_way) {
		$res = User::dbList();
		if ($res) {
			foreach ($res as $elt) {
				$user = new User($elt);
				if ($user->get('email')) {
					if ((strpos($user->get('email'),$tampon)!==false) && (strpos($user->get('email'),'.at.')!==false)) {
						$changes = $user->initVars(array('email'=>str_replace('.at.','@',str_replace($tampon,'',$user->get('email')))));
						$user->dbUpdate($changes);
						$done++;
					}
				}
			}
		}
	}
	
	echo 'Tampon  : '.$tampon."\n" .
		 'Mode    : '.($remove_way ? 'inversé' : 'standard') . "\n" .
		 'Traités : '.$done . "\n";
	
}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n" .
		 "-t pour définir le tampon" . "\n" .
		 "-r pour inverser l'\opération" . "\n" ;
	exit();
}