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
 * Variante Liste d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarList extends AcidVar {

    /**
     * @var array tableau représentatif des éléments de la liste
     */
    protected	$elts		= array();

    /**
     * @var strinf délimiteur
     */
    protected	$limiter	= "\n";

    /**
     * @var booelan true si la valeur associée est une chaine
     */
    protected	$use_index	= null;

    /**
     * @var bool|null|true true si l'index est un entier
     */
    protected	$int_index	= null;

    /**
     * Constructeur AcidVarList
     *
     * @param string $label Etiquette de la variable.
     * @param array $elts Liste des éléments.
     * @param string $def Valeur par défaut.
     * @param bool|false $multiple True si on autorise plusieurs valeurs sélectionnées en formulaire.
     * @param bool|true $use_index On utilise l'asso
     * @param int $size Taille du select dans le formulaire.
     * @param bool|true $int_index
     */
    public function __construct($label='AcidVarList',$elts=array(),$def='',$multiple=false,$use_index=true,$size=1,$int_index=true) {

        parent::__construct($label,$def,null);

        $this->sql['type'] = $multiple ? 'text' :
            ($use_index ? (count($elts) < 128 ? 'tinyint(3)' : 'int(10)')
                : 'enum('.self::getEnumInstruction($elts).')');

        $this->setForm('select',array('size'=>$size,'multiple'=>$multiple));
        $this->use_index = $use_index;
        $this->int_index = $int_index;

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
        if ($this->use_index) {
            if ($this->int_index)  {
                parent::setVal((int)$val);
            }else{
                parent::setVal($val);
            }
        }else{
            parent::setVal($val);
        }
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