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
 * @since     Version 0.9
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

//Hooks
AcidHook::call('routes');
AcidHook::call('routes_api');

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