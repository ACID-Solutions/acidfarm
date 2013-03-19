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
 * Convertit toutes les tables (ainsi que leurs champs) en encodage utf-8
 */

include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';



$req = AcidDB::query('show tables')->fetchAll(PDO::FETCH_ASSOC);
$tblname = '';
foreach ($req as $key => $value) {
    AcidDB::exec('ALTER TABLE '.$value['Tables_in_'.Acid::get('db:base')].' DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;COMMIT; ALTER TABLE '.$value['Tables_in_'.Acid::get('db:base')].' CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
}
