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
 * Variante Radio d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarRadio extends AcidVar {

    /**
     * @var array tableau représentatif des éléments de la liste
     */
    protected $elts;

    /**
     * Constructeur AcidVarRadio
     *
     * @param string $label Etiquette de la variable.
     * @param array $elts Liste des Eléments de la liste.
     * @param string $def Valeur par défaut.
     * @param bool $use_index
     * @param bool $null
     */
    public function __construct($label='AcidVarRadio',$elts=array(),$def='',$use_index=false,$null=false) {

        parent::__construct($label,$def,null);

        // Infos sql
        $this->sql['type'] = $use_index ? (count($elts) < 128 ? 'tinyint(3)' : 'int(10)')
            : 'enum('.self::getEnumInstruction($elts).')';

        // Infos form
        $this->setForm('radio');

        $this->use_index = $use_index;
        if ($use_index)  $this->elts = $elts;
        else foreach ($elts as $elt) $this->elts[$elt] = $elt;
    }

    /**
     * Retourne l'ensemble des valeurs du tableau en entrée sous forme de chaîne de caractères.
     *
     * @param array $elts
     *
     * @return string
     */
    public static function getEnumInstruction($elts) {
        $output = '';
        foreach ($elts as $elt){
            $output .= '\''.addslashes($elt).'\',';
        }
        return substr($output,0,-1);
    }

    /**
     * Assigne un élément à la variable
     *
     * @param mixed $val
     */
    public function setVal($val) {
        if ($this->use_index) parent::setVal((int)$val);
        else parent::setVal($val);
    }

    /**
     * Retourne les valeurs de la liste sous forme d'un tableau
     *
     * @return array
     */
    public function getVals() {
        return $this->elts;
    }

}