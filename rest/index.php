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
$acid_page_type = 'rest';


require('../sys/glue.php');

Acid::set('rest:realm','alpha');
Acid::set('rest:nonce','beta');

Acid::set('router:use_lang',false);
Acid::set('router:folder','rest/');

Acid::set('out','empty');

Acid::set('rest:routes:unlog',array('usersalt'));

//Auth function
$need_auth = false;
function RestAuthFunction() {
    if (!in_array(AcidRouter::getCurrentRouteName(), Acid::get('rest:routes:unlog'))) {
       $GLOBALS['sess'] = Rest::authentification();
    }
}

//AuthGet : Route
AcidRouter::addGet('usersalt',new AcidRoute('auth/salt/:id_user',array('controller'=>'RestController','action'=>'authsalt','module'=>'rest')));

//Getters
AcidRouter::addGet('list',new AcidRoute(':module/list',array('controller'=>'RestController','action'=>'getList','module'=>'rest')));
AcidRouter::addGet('get',new AcidRoute(':module/get/:id_module',array('controller'=>'RestController','action'=>'get','module'=>'rest')));

//POST
AcidRouter::addPost('post',new AcidRoute(':module',array('controller'=>'RestController','action'=>'post','module'=>'rest')));

//PUT
AcidRouter::addPut('put',new AcidRoute(':module',array('controller'=>'RestController','action'=>'put','module'=>'rest')));

//DELETE
AcidRouter::addDelete('delete',new AcidRoute(':module',array('controller'=>'RestController','action'=>'delete','module'=>'rest')));

//Si pas de page définie, affichage de la home
AcidRouter::addDefaultRoute('404',new AcidRoute('default',array('controller'=>'RestController','action'=>'call404','module'=>'rest')));

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
