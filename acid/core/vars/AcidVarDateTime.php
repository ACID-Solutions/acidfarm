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
 * Variante Date Time d'AcidVar
 *
 * @package   Acidfarm/Vars
 */
class AcidVarDateTime extends AcidVarString
{
    /**
     * Constructeur AcidVarDateTime
     *
     * @param string $label
     * @param bool $nullable
     */
    public function __construct($label = 'AcidVarDateTime',$nullable=true)
    {
        parent::__construct(
            $label,
            20,
            19,
            null,
            '`^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$`',
            true,
            $nullable
        );
        $this->sql['type'] = 'datetime';
    }
    
    /**
     * Retourne la valeur actuelle à l'instant T
     *
     * @return string
     */
    public static function now()
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Retourne la valeur nulle
     *
     * @return string
     */
    public static function zero()
    {
        return null;
        //return '0000-00-00 00:00:00';
    }
    
    /**
     * Retourne les valeurs considérées comme nulles
     *
     * @return string
     */
    public static function zeroVals()
    {
        return [null, '0000-00-00 00:00:00', ''];
    }
    
    /**
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