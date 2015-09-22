<?php


if (Acid::get('sass:used')) {
    $this->addCSS($this->sassUrl('admin'));
    $this->addCSS($this->sassUrl('dialog'));
}else{
    $this->addCSS(Acid::themeUrl('css/admin.css'));
    $this->addCSS(Acid::themeUrl('css/dialog.css'));
}


$this->jQuery();
$this->jqueryUI();
$this->tinyMCE();
$this->plupload();


$this->dependencies();