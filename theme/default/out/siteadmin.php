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

//RESPONSIVE MOBILE
// $this->addInHead('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
// $this->addInHead('<meta name="viewport" content="width=device-width, initial-scale=1">');

$this->setBodyAttrs(array('class'=>$body_class));

//affichage si besoin des messages utilisateurs
$output .= $this->getDialog() ;

//conteneur du site
$output .=  <<<OUTPUT

{$this->output}


OUTPUT;

$this->output = $output;
