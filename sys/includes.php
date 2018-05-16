<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Config
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
$acid['includes']['News']        		 = 'sys/modules/news.php';
$acid['includes']['PhotoHome']	  		 = 'sys/modules/photo_home.php';
$acid['includes']['Photo']	  	  		 = 'sys/modules/photo.php';
$acid['includes']['Seo']	  	  		 = 'sys/modules/seo.php';
$acid['includes']['Menu']	  	  		 = 'sys/modules/menu.php';

//Tools
$acid['includes']['Contact']	 			 = 'sys/tools/contact.php';
$acid['includes']['AcidRegistration'] 	 	 = 'registration/private/lib.php';

// Libraries
$acid['includes']['Lib'] 		  = 'sys/tools/lib.php';
$acid['includes']['Lang'] 		  = 'sys/tools/lang.php';
$acid['includes']['Conf'] 		  = 'sys/tools/conf.php';
$acid['includes']['Ajax'] 	 	  = 'sys/tools/ajax.php';
$acid['includes']['Rest'] 		  = 'sys/tools/rest.php';
$acid['includes']['Route'] 	 	  = 'sys/tools/route.php';
$acid['includes']['Recaptcha']    = 'sys/tools/recaptcha.php';


// Admin Config
$acid['includes']['SiteConfig']	  = 'sys/modules/site_config.php';

// Conroller Tools
$acid['includes']['SitemapController'] 	 	  = 'sys/controller/acid/SitemapController.php';

// Print
$acid['includes']['MyTemplate']   = 'sys/template.php';

//Composer autoload
if (file_exists(SITE_PATH.'vendor/autoload.php')) {
    require_once SITE_PATH.'vendor/autoload.php';
}