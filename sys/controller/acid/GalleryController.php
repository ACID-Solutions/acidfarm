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
 * Contrôleur de Galerie
 * @package   Controller
 */
class GalleryController {
	
	/**
	 * Affiche le wallart du site
	 */
    public function index(){
        Conf::addToAriane(AcidRouter::getName('gallery'),Photo::buildUrl());       
		
        //ADD TO HTML
        Conf::addToContent(Photo::printGallery());
    }
}