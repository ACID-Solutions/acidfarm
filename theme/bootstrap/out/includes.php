<?php
$this->addCSS(Acid::themeUrl('css/bootstrap.min.css'));
$this->addCSS(Acid::themeUrl('css/bootstrap-theme.min.css'));
$this->addCSS(Acid::themeUrl('css/style.css'));

//$this->addCSS($this->sassUrl('_bootstrap'));
//$this->addCSS($this->sassUrl('_bootstrap-mincer'));
//$this->addCSS($this->sassUrl('style'));


$this->jQuery();
$this->jQueryLightBox();
$this->jQueryUI();

$this->addCss(Acid::themeUrl('js/slick/slick.css'));
$this->addJs(Acid::themeUrl('js/slick/slick.js'));

$this->addJs(Acid::themeUrl('js/bootstrap.min.js'));

$this->dependencies();