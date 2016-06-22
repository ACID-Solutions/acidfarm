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
 * Variante TextArea d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarTextarea extends AcidVar {

    /**
     * Constructeur AcidVarTextarea
     *
     * @param string $label
     * @param int $cols
     * @param int $rows
     * @param string $def
     * @param string $sql_type
     */
    public function __construct($label='AcidVarTextarea',$cols=80,$rows=5,$def='',$sql_type='text') {
        parent::__construct($label,(string)$def,null);

        $this->sql['type'] = $sql_type;

        $this->setForm('textarea',array('cols'=>(int)$cols, 'rows'=>(int)$rows));

        //$this->setVal($val);
    }

    /**
     * Assigne une chaîne de caractères à la variable
     * @param string $val
     */
    public function setVal($val) {
        return parent::setVal((string)$val);
    }


}