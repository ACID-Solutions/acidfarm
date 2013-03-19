<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model / View
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


Conf::setPageTitle(Acid::trad('url_error404'));
Conf::addToContent('<h1 class="erreur404">'.Acid::trad('url_error404').'</h1>');

include(SITE_PATH.'sys/pages/map.php');

Conf::addToContent($sitemap);

Conf::addToAriane(Acid::trad('404'),$_SERVER['REQUEST_URI']);

