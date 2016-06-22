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
 * Variante Décimale d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarFloat extends AcidVar {

    /**
     * Constructeur AcidVarFloat
     * @param string $label Etiquette de la variable.
     * @param bool $unsigned True si le décimal n'est pas signé.
     * @param int $size Taille du champs pour le formulaire.
     * @param int $maxlength Taille maximale pour le formulaire.
     * @param float $def Valeur par défaut.
     */
    public function __construct($label='AcidVarFloat',$unsigned=false,$size=20,$maxlength=10,$def=0) {
        parent::__construct($label,(float)$def,null);

        if ($maxlength === null) {
            $maxlength = 30;
        }

        $this->sql['type'] = 'float';

        $ml = $unsigned ? ((int)$maxlength+2) : ((int) $maxlength+1);
        $this->setForm('text',array('size'=>(int)$size,'maxlength'=>$ml));

        //$this->setVal($val);
    }

    /**
     *  Assigne un décimal à la variable
     * @param float $val
     */
    public function setVal($val) {
        return parent::setVal((float)$val);
    }

}
