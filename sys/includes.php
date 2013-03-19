<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Config
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

// User
$acid['includes']['User']         		 = 'sys/modules/user.php';
$acid['includes']['UserGroup']   		 = 'sys/modules/user_group.php';
$acid['includes']['UserGroupAssoc']    	 = 'sys/modules/user_group_assoc.php';
$acid['includes']['UserPermission']    	 = 'sys/modules/user_permission.php';

//Site
$acid['includes']['Page']         		 = 'sys/modules/page.php';
$acid['includes']['Actu']        		 = 'sys/modules/actu.php';
$acid['includes']['Contact']	  		 = 'sys/modules/contact.php';
$acid['includes']['PhotoHome']	  		 = 'sys/modules/photo_home.php';
$acid['includes']['Photo']	  	  		 = 'sys/modules/photo.php';

// Libraries
$acid['includes']['Lib'] 		  = 'sys/tools/lib.php';
$acid['includes']['Conf'] 		  = 'sys/tools/conf.php';
$acid['includes']['Ajax'] 	 	  = 'sys/tools/ajax.php';
$acid['includes']['Route'] 	 	  = 'sys/tools/route.php';

// Admin Config
$acid['includes']['SiteConfig']	  = 'sys/modules/config.php';

// Print
$acid['includes']['MyTemplate']   = 'sys/template.php';