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
 * Autre Variante BigInt d'AcidVar ( Clonée Base de Données )
 * @package   Acidfarm/Vars
 */
class AcidVarBigInt extends AcidVarInteger {

    /**
     * Constructeur AcidVarBigInt
     * @param string $label
     * @param booelan $unsigned
     * @param int $def
     */
    public function __construct($label='AcidVarBigInt',$unsigned=false,$def=0) {
        parent::__construct($label,$unsigned,20,20,$def,'bigint');
    }

}