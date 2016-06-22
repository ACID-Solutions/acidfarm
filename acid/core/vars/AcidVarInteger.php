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
 * Variante Nombre Entier d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarInteger extends AcidVar {

    /**
     * Constructeur AcidVarInteger
     *
     * @param string $label
     * @param bool $unsigned
     * @param int $size
     * @param int $maxlength
     * @param int $def
     * @param string $sql_type
     */
    public function __construct($label='AcidVarInteger',$unsigned=false,$size=20,$maxlength=10,$def=0,$sql_type='int') {
        parent::__construct($label,(int)$def,null);

        $this->sql['type'] = $sql_type.'('.($unsigned?$maxlength:($maxlength+1)).')';

        $ml = $unsigned ? ((int)$maxlength+1) : (int) $maxlength;
        $this->setForm('text',array('size'=>(int)$size,'maxlength'=>$ml));

        //$this->setVal($val);
    }

    /**
     *  Assigne un entier à la variable
     * @param int $val
     */
    public function setVal($val) {
        return parent::setVal((int)$val);
    }

}