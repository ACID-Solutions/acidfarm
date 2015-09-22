<?php

$output = '';

//generation du menu
$menu = Acid::tpl('admin/siteadmin-menu.tpl',Acid::get('admin:menu_config'),User::curUser());

$body_class = Acid::get('admin:body_class');

//contenu seul demandé ?
if (!Acid::get('admin:content_only')) {
    //intégration du corps
    $this->output = Acid::tpl('admin/siteadmin-body.tpl',array('menu'=>$menu,'content'=>$this->output),User::curUser());
}else{
    $body_class .= ' content_only';
}

$this->setBodyAttrs(array('class'=>$body_class));

//RESPONSIVE MOBILE
$this->addInHead('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
$this->addInHead('<meta name="viewport" content="width=device-width, initial-scale=1">');

//affichage si besoin des messages utilisateurs
$output .= $this->getDialog() ;
$output .= $this->getBwin() ;

//conteneur du site
$output .=  <<<OUTPUT
<div id="site" class="admin">
{$this->output}
</div>

OUTPUT;

$this->output = $output;
