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
 * Variante Mot de Passe d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarPassword extends AcidVarString {

    /**
     * Constructeur AcidVarPassword
     * @param string $label
     */
    public function __construct($label='AcidVarPassword') {
        parent::__construct($label,50,20);
        $this->setForm('password',array('size'=>$this->form['size'],'maxlength'=>$this->form['maxlength']));
    }

    /**
     * Generate random password
     * @param int $size
     * @return string
     */
    public static function random($size=8) {
        $numbers = '0123456789';
        $chars = 'aeiouy';
        $charsb = 'bcdfghjklmnpqrstvwxz';

        $check[0] = $chars.$chars.$chars.$charsb.$numbers;
        $check[1] = $chars.$chars.$charsb.$charsb.$charsb.$numbers;
        $check[2] = $numbers.$numbers;

        $nbchars[0] = strlen($check[0]);
        $nbchars[1] = strlen($check[1]);
        $nbchars[2] = strlen($check[2]);

        $init_end = $size-rand(0,3);

        $my_pass = '';
        for ($i=0;$i<$size;$i++) {
            $indice = ($i>=$init_end) ? 2 : (($i%2===0) ? 1 : 0);
            $my_pass .= $check[$indice][rand(0,$nbchars[$indice]-1)];
        }

        return $my_pass;
    }

}
