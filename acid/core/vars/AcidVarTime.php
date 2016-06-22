<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm/Vars
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Variante Heure d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarTime extends AcidVarString {

    /**
     * Constructeur AcidVarTime
     * @param string $label
     */
    public function __construct ($label='AcidVarTime') {
        parent::__construct($label,8,8,'00:00:00','`^[0-9]{2}:[0-9]{2}:[0-9]{2}$`');
        $this->sql['type'] = 'time';
    }

    /**
     * Retourne la valeur actuelle à l'instant T
     * @return string
     */
    public static function now() {
        return date('H:i:s');
    }

    /**
     * Retourne la valeur nulle
     * @return string
     */
    public static function zero() {
        return '00:00:00';
    }
}