<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Script
 * @version   0.1
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Exemple d'envoi d'email
 */
$opt = getopt('c::');

if (isset($opt['c'])) {

    include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';

    if ($res = User::dbList(array(array('date_connexion','<',AcidTime::addToDate(date('Y-m-d 00:00:00'),-3))))) {
        foreach ($res as $elt) {
            $u = new User($elt);
            //echo $u->getId() . "\n";
            $u->sendMail('Vous nous manquez!','Votre dernière connexion remonte à plus de trois jours.');
        }
    }

}else{
    echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n" ;
    exit();
}

