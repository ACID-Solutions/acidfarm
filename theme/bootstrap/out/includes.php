<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model / View
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

if (Acid::get('sass:used')) {
    $this->addCSS($this->sassUrl('style'));
    //$this->addCSS(Acid::sassUrl('dialog.css'));
}else{
    $this->addCSS(Acid::themeUrl('css/style.css'));
    //$this->addCSS(Acid::themeUrl('css/dialog.css'));
}


$this->jQuery();
$this->jQueryLightBox();
$this->jQueryUI();

$this->addCss(Acid::themeUrl('js/slick/slick.css'));
$this->addCss(Acid::themeUrl('js/slick/slick-theme.css'));
$this->addJs(Acid::themeUrl('js/slick/slick.js'));

$this->addJs(Acid::themeUrl('js/bootstrap.min.js'));

$this->dependencies();