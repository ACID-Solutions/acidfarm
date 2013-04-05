<?php
$opt = getopt('c::p:');
if (isset($opt['c'])) {

	$acid_custom_log = '[SCRIPT]';
	include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';
	
	$password_value = isset($opt['p']) ? trim($opt['p']) : 'password';
	
	AcidDB::exec("UPDATE ".User::tbl()." SET `password`='".User::getHashedPassword($password_value,'')."', `user_salt`='' WHERE 1 ");
	
}else{
	echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n" .
		 "-p mdp (optionnel)" . "\n" ;
	exit();
}
