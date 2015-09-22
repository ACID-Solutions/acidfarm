<?php

if (Acid::get('sass:used')) {
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