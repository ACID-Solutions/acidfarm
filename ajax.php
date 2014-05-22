<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


// prevent caching (php)
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: ' . gmdate(DATE_RFC1123, time()-1));

$ajax_lang = isset($_GET['lang']) ? $_GET['lang'] : null;
$permission_active = false;
$acid_page_type = 'ajax';


require('sys/start.php');

Acid::set('out','empty');

Ajax::setNextPage();
Ajax::enableAjax();

if (isset($_GET['do'])) {

	switch ($_GET['do']) {

		default :
			//$html .= Ajax::returnJSON('ajax','ajax title',array('other'=>'my other content'),"alert('here my ajax');");
			$html .= 'ajax';
		break;

	}

}

require('sys/stop.php');

?>
