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
 * Variante Date Time d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarDateTime extends AcidVarString {

    /**
     * Constructeur AcidVarDateTime
     * @param string $label
     */
    public function __construct ($label='AcidVarDateTime') {
        parent::__construct($label,20,19,'0000-00-00 00:00:00','`^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$`',true);
        $this->sql['type'] = 'datetime';
    }

    /**
     * Retourne la valeur actuelle à l'instant T
     * @return string
     */
    public static function now() {
        return date('Y-m-d H:i:s');
    }

    /**
     * Retourne la valeur nulle
     * @return string
     */
    public static function zero() {
        return '0000-00-00 00:00:00';
    }
}