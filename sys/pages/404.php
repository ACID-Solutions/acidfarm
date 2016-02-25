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
Conf::addToAriane(Acid::trad('404'),AcidUrl::requestURI());

Conf::addToContent(Acid::tpl('pages/url/404.tpl',array('elts'=>SitemapController::webmap())));




