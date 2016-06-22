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
 * Autre Variante Booléenne d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarBool extends AcidVarInteger {

    /**
     * Constructeur AcidVarBool
     * @param string $label
     * @param int $def
     */
    public function __construct($label='AcidVarBool',$def=0) {
        parent::__construct($label,true,20,1,$def,'tinyint');
        $this->setDef($def);
        $this->elts = self::assoc();
        $this->setForm('switch');
    }

    /**
     * Retourne le tableau associatif interne de la variable
     * @return array
     */
    public static function assoc() {
        return array(1=>Acid::trad('yes'),0=>Acid::trad('no'));
    }

}