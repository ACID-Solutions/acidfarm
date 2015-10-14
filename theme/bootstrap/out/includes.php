<?php


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