<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Core
 * @version   0.4
 * @since     Version 0.4
 * @copyright 2012 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Classe de Routage : configuration de route non bloquante
 * @package   Acidfarm\Core
 */
class AcidEvent extends AcidRoute{

	/**
     * @var boolean Empêche les autres routes d'être appelées si rencontré
     */
    public $unique_match    	= false;


}