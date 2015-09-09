<?php

if (Acid::get('sass:used')) {
    $this->addCSS($this->sassUrl('_bootstrap'));
    $this->addCSS($this->sassUrl('_bootstrap-mincer'));
    $this->addCSS($this->sassUrl('admin'));
    $this->addCSS($this->sassUrl('admin-form'));
    $this->addCSS($this->sassUrl('dialog'));
}else{
    $this->addCSS(Acid::themeUrl('css/bootstrap.min.css'));
    $this->addCSS(Acid::themeUrl('css/bootstrap-theme.min.css'));
    $this->addCSS(Acid::themeUrl('css/admin.css'));
    $this->addCSS(Acid::themeUrl('css/admin-form.css'));
    $this->addCSS(Acid::themeUrl('css/dialog.css'));
}

$this->jQuery();
$this->jqueryUI();
$this->tinyMCE();
$this->plupload();

$this->addJs(Acid::themeUrl('js/bootstrap.min.js'));

$this->dependencies();