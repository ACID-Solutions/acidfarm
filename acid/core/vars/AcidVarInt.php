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
 * Autre Variante Int d'AcidVar ( Clonée Base de Données )
 * @package   Acidfarm/Vars
 */
class AcidVarInt extends AcidVarInteger {
    
    /**
     * Constructeur AcidVarInt
     * @param string $label
     * @param booelan $unsigned
     * @param int $def
     * @param bool   $nullable
     */
    public function __construct($label='AcidVarInt',$unsigned=false,$def=0,$nullable=false) {
        parent::__construct($label,$unsigned,20,10,$def,'int', $nullable);
    }

}