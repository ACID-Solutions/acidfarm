<?php

/**
 * AcidFarm - Yet Another Framework
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
 * Variante Date d'AcidVar
 *
 * @package   Acidfarm/Vars
 */
class AcidVarDate extends AcidVarString
{
    /**
     * Constructeur AcidVarDate
     *
     * @param string $label
     * @param bool $nullable
     */
    public function __construct($label = 'AcidVarDate', $nullable = true)
    {
        parent::__construct(
            $label,
            10,
            10,
            null,
            '`^[0-9]{4}-[0-9]{2}-[0-9]{2}$`',
            true,
            $nullable
        );
        $this->sql['type'] = 'date';
    }
    
    /**
     * Retourne la valeur actuelle à l'instant T
     *
     * @return string
     */
    public static function now()
    {
        return date('Y-m-d');
    }
    
    /**
     * Retourne la valeur nulle de la variable
     *
     * @return string
     */
    public function zero()
    {
        return $this->isNullable() ? null : '0000-00-00';
    }
    
    /**
     * Retourne les valeurs considérées comme nulles
     *
     * @return string
     */
    public static function zeroVals()
    {
        return [null, '0000-00-00'];
    }

    /***
    * Retourne true si la valeur est considérée vide pour ce type de variable
    * @param $val
    *
    * @return bool
    */
    public static function valIsEmpty($val)
    {
        return !in_array($val, static::zeroVals());
    }
}