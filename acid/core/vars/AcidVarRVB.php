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
 * Variante RVB d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarRVB extends AcidVarString {
    /**
     * Constructeur AcidVarRVB
     * @param string $label
     * @param int $size
     */
    public function __construct($label='AcidVarRVB',$size=20) {
        parent::__construct($label,$size,7,'','`^\#[0-9a-fA-F]{6}$`');
    }
}