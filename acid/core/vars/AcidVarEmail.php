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
 * Variante Email d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarEmail extends AcidVarString {
    /**
     * Constructeur AcidVarEmail
     * @param string $label
     * @param int $size
     */
    public function __construct($label='AcidVarEmail',$size=20) {
        parent::__construct($label,$size,100,'','`^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$`i',true);
    }
}