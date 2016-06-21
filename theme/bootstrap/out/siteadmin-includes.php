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

if (Acid::get('sass:used')) {
    $this->addCSS($this->sassUrl('admin'));
    $this->addCSS($this->sassUrl('dialog'));
}else{
    $this->addCSS(Acid::themeUrl('css/admin.css'));
    $this->addCSS(Acid::themeUrl('css/dialog.css'));
}

$this->jQuery();
$this->jqueryUI();
$this->jQueryImgAreaSelect();
$this->tinyMCE();
$this->plupload();

$this->addJs(Acid::themeUrl('js/bootstrap.min.js'));

$this->dependencies();