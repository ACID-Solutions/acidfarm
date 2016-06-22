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
 * Variante paramètre invisible d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarHidden extends AcidVar {

    /**
     * Constructeur AcidVarHidden
     *
     * @param string $label
     * @param int $maxlength
     * @param string $def
     * @param string $regex
     * @param bool $force_def
     */
    public function __construct($label='AcidVarString',$maxlength=255,$def='',$regex=null,$force_def=false) {

        parent::__construct($label,(string)$def,$regex,$force_def);

        // Infos sql
        $this->sql['type'] = 'varchar('.((int)$maxlength).')';

        // Infos form
        $this->setForm('hidden',array('maxlength'=>$maxlength));

        // Value
        //$this->setVal($val);
    }

    /**
     *  Assigne une chaîne de caractères à la variable
     * @param string $val
     */
    public function setVal($val) {
        return parent::setVal((string)$val);
    }


}