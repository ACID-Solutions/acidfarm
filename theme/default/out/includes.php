<?php
$this->addCSS(Acid::themeUrl('css/'.Acid::get('css:theme').'.css'));
$this->addCSS(Acid::themeUrl('css/'.Acid::get('css:dialog').'.css'));

$this->jQuery();
$this->jQueryLightBox();
$this->jQueryUI();

$this->addCss(Acid::themeUrl('css/carousel.css'));
$this->addJs(Acid::themeUrl('js/carousel.js'));

$this->dependencies();