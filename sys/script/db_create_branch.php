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
 * Permet de cloner les tables du site
 */

$opt = getopt('c::p:b:r::');
if (isset($opt['c']) && isset($opt['b'])) {

    $acid_custom_log = '[SCRIPT]';
    include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';



    if ($branch = trim($opt['b'])) {

        if (!empty($opt['p'])) {
            Acid::set('db:prefix', trim($opt['p']));
        }

        echo 'Prefix used is :'. Acid::get('db:prefix') . "\n";

        $tbl_list = array();
        if ($req = AcidDB::query('show tables')->fetchAll(PDO::FETCH_ASSOC)) {

            foreach ($req as $key => $value) {
                $tbl_list[] = $value['Tables_in_'.Acid::get('db:base')];
            }

            $tblname = '';
            foreach ($req as $key => $value) {

               if (strpos($value['Tables_in_'.Acid::get('db:base')], Acid::get('db:prefix'))===0) {

                   $old_table = $value['Tables_in_'.Acid::get('db:base')];

                   if (isset($opt['r'])) {
                       $new_table = str_replace(Acid::get('db:prefix'), $branch.'_', $old_table);
                   }else{
                       $new_table = str_replace(Acid::get('db:prefix'), $branch.'_'.Acid::get('db:prefix'),$old_table);
                   }


                   if (!in_array($new_table, $tbl_list)) {
                       AcidDB::query('CREATE TABLE '.$new_table.' LIKE '.$old_table.';');
                       AcidDB::exec('INSERT INTO '.$new_table.' SELECT * FROM '.$old_table.';');
                       echo  $new_table.' created from ' . $old_table  . "\n" ;
                   }else{
                       echo  $new_table.' already exists' . "\n" ;
                   }
               }




            }

        }

    }

}else{
    echo "Pour effectuer l'opération, merci d'ajouter l'argument -c  à la commande actuelle." . "\n"  ;
    echo "-b [nbranche] : la nouvelle branche" . "\n"  ;
    echo "-p [prefix] : force le préfixe" . "\n"  ;
    echo "-r : si défini, remplace le préfixe au lieu de l'ajouter" . "\n"  ;
    exit();
}
