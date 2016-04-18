<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tests
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

// This is global bootstrap for autoloading

require __DIR__.'/../../glue.php';

if (Acid::get('lang:use_nav_0')) {
    Acid::set('url:folder_lang', (Acid::get('url:folder').Acid::get('lang:current').'/') );
    Acid::set('url:system_lang', (Acid::get('url:scheme').Acid::get('url:domain').Acid::get('url:folder_lang')) );
}