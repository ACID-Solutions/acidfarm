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
 * Variante "Chaîne de caractères" d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarInfo extends AcidVar {

    /**
     * Constructeur AcidVarInfo
     * @param string $label
     * @param string $def
     */
    public function __construct ($label='AcidVarInfo',$def='') {
        parent::__construct($label,'',null);
        $this->setForm('info');
    }
}