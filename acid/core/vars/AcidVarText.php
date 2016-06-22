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
 * Variante Texte d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarText extends AcidVarTextarea {
    /**
     * Constructeur AcidVarText
     * @param string $label
     * @param int $cols
     * @param int $rows
     * @param string $def
     */
    public function __construct ($label='AcidVarText',$cols=80,$rows=5,$def='') {
        parent::__construct($label,$cols,$rows,$def,'text');
    }
}