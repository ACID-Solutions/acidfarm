<?php

//$this->addCSS(Acid::themeUrl('css/'.Acid::get('css:theme').'.css'));
//$this->addCSS(Acid::themeUrl('css/'.Acid::get('css:dialog').'.css'));

$this->addCSS($this->sassUrl(Acid::get('css:theme')));
$this->addCSS($this->sassUrl(Acid::get('css:dialog')));
//$this->addCSS($this->sassUrl('test'));

$this->jQuery();
$this->jQueryLightBox();
$this->jQueryUI();

$this->addCss(Acid::themeUrl('js/slick/slick.css'));
$this->addJs(Acid::themeUrl('js/slick/slick.min.js'));

$this->dependencies();