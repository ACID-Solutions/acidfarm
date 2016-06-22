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
 * Variante Date d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarDate extends AcidVarString {

    /**
     * Constructeur AcidVarDate
     * @param string $label
     */
    public function __construct($label='AcidVarDate') {
        parent::__construct($label,10,10,'0000-00-00','`^[0-9]{4}-[0-9]{2}-[0-9]{2}$`');
        $this->sql['type'] = 'date';
    }

    /**
     * Retourne la valeur actuelle à l'instant T
     * @return string
     */
    public static function now() {
        return date('Y-m-d');
    }

    /**
     * Retourne la valeur nulle
     * @return string
     */
    public static function zero() {
        return '0000-00-00';
    }
}