<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur de l'espace utilisateur
 * @package   Acidfarm\Controller
 */
class UserspaceController{
	
	/**
	 * Affiche l'espace utilisateur
	 */
    public function index(){
        Conf::addToContent(Acid::mod('User')->printUserSpace());
        if (!User::curLevel()) {
            Conf::addToContent(Acid::mod('User')->printCreateForm());
        }
    }
    
}
