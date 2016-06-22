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
 * Variante Url d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarUrl extends AcidVarString {
    /**
     * Constructeur AcidVarUrl
     * @param string $label
     * @param int $size
     */
    public function __construct($label='AcidVarUrl',$size=20) {
// 		parent::__construct($label,$size,255,'','`^(((https?|ftp)://(w{3}\.)?)(\w+-?)*\.([a-z]{2,4}))`i',true);
        parent::__construct($label,$size,255,'','`^((https?|ftp)://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)`i',true);
    }
}