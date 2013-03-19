<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur du fichier robots.txt
 * @package   Controller
 */
class RobotsController{
	
	/**
	 * Fichier robots.txt
	 */
    public function index(){
      	Acid::set('out','text');

		$robot = 	'User-agent: *' . "\n" .
				'' . "\n" .
				'Disallow: '.Acid::get('url:css')  . "\n" .
				'Disallow: '.Acid::get('url:out')  . "\n" .
				'Disallow: '.Acid::get('url:tpl')  . "\n" .
				'Disallow: '.Acid::get('url:js')  . "\n" .
				'' . "\n";
		
		Conf::addToContent($robot);
    }
}
