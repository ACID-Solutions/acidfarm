<?php

/**
 * AcidFarm - Yet Another Framework
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

AcidRouter::addRoute('sample',new AcidRoute('sample', ['controller' => 'AjaxController', 'action' => 'sample', 'module' => 'ajax']));


AcidRouter::addDefaultRoute('404',
    new AcidRoute('default', ['controller' => 'AjaxController', 'action' => 'call404', 'module' => 'ajax']));