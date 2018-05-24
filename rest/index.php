<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.6
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


//Prevent caching (php)
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: ' . gmdate(DATE_RFC1123, time()-1));

//Every One can get content
//header('Access-Control-Allow-Origin: *');

$ajax_lang = isset($_GET['lang']) ? $_GET['lang'] : null;
$permission_active = false;

$acid_page_type = 'rest';
$acid_set_routes = ['api'];
require('../sys/glue.php');

Acid::set('rest:realm','alpha');
Acid::set('rest:nonce','beta');

Acid::set('router:use_lang',false);
Acid::set('router:folder','rest/');

Acid::set('out','empty');

Acid::set('rest:routes:unlog',array('usersalt'));

//Auth function
$need_auth = false;

/**
 * Fonction d'authentification REST
 */
function RestAuthFunction() {
    if (!in_array(AcidRouter::getCurrentRouteName(), Acid::get('rest:routes:unlog'))) {
       $GLOBALS['sess'] = Rest::authentification();
    }
}

require SITE_PATH.'routes/api.php';

//Lancement du Router
if ($need_auth) {
    //Avec authentification
    AcidRouter::before('*','RestAuthFunction')->run();
}else{
    //Sans authentification
    AcidRouter::run();
}

require('../sys/stop.php');

?>
