<?php
$this->addCSS(Acid::themeUrl('css/bootstrap.min.css'));
$this->addCSS(Acid::themeUrl('css/bootstrap-theme.min.css'));
$this->addCSS(Acid::themeUrl('css/style.css'));

$this->jQuery();
$this->jQueryLightBox();
$this->jQueryUI();

$this->addCss(Acid::themeUrl('css/carousel.css'));
$this->addJs(Acid::themeUrl('js/carousel.js'));

$this->addJs(Acid::themeUrl('js/bootstrap.min.js'));

$this->dependencies();