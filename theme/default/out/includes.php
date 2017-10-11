<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\ModelView
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

if (Acid::get('sass:enable')) {
    $this->addCSS($this->sassUrl(Acid::get('css:theme')));
    $this->addCSS($this->sassUrl(Acid::get('css:dialog')));
}else{
    $this->addCSS(Acid::themeUrl('css/'.Acid::get('css:theme').'.css'));
    $this->addCSS(Acid::themeUrl('css/'.Acid::get('css:dialog').'.css'));
}

$this->jQuery();
$this->jQueryLightBox();
$this->jQueryUI();

$this->addCss(Acid::themeUrl('js/slick/slick.css'));
$this->addCss(Acid::themeUrl('js/slick/slick-theme.css'));
$this->addJs(Acid::themeUrl('js/slick/slick.min.js'));
$this->addJs(Acid::themeUrl('js/stellar/jquery.stellar.min.js'));

$this->dependencies();