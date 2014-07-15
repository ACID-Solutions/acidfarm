<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Controller
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


$admin_page=true;
$permission_active = true;
$acid_page_type = 'admin';

$GLOBALS['acid']['session']['enable'] = true;

require 'sys/start.php';

Acid::set('out','siteadmin');

Conf::setPageTitle('Admin');

$GLOBALS['tinymce']['active'] = true;
$GLOBALS['tinymce']['all'] = true;

Conf::set('plupload:active', true);
Conf::set('plupload:all', true);

$my_onglets = null;
$content_only = false;

//Access Controller
$access_level	 = Acid::get('lvl:admin');

// Log Form
if (!User::curLevel($access_level)) {

	if  (isset($_GET['pass_oublie'])) {
		$forget = $_GET['pass_oublie'];
		$html .= Acid::mod('User')->printPasswordForgotten($forget,$_SERVER['PHP_SELF']);
	}else{
		$cur_user = User::curUser();
		$msg = $cur_user->getId() ? Acid::trad('admin_no_permission') : null;

		$html .= Acid::mod('User')->printAdminLogginForm($msg);

	}

}

// Admin Interface
else {

	//Init.
	$content = '';
	$p = isset($_GET['page']) ? $_GET['page'] : '';
	$s = ' class="selected"';

	//Color Palet
    $color = array(	1=>'#C1272D',	// Rouge bordeaux
    				2=>'#ED1C24',	// Rouge vif
    				3=>'#ED1E79',	// Fushia
    				4=>'#662D91',	// Violet
    				5=>'#335AA6',	// Bleu foncé
                    6=>'#0092D8',	// Bleu moyen
    				7=>'#6CB3FF',	// Bleu clair
    				8=>'#009245',	// Vert foncé
    				9=>'#8CC63F',	// Vert acid
    				10=>'#F7931E');	// Orange

	//Setting controller
	$def_level	 = $access_level; // $acid['lvl']['admin'];
	$dev_level	 = $acid['lvl']['dev'];
	$siteadmin_cat = array('default','configuration','user_configuration','web','tools');
	$controller  = array(
					'default'  => array('level'=>$def_level,'separator'=>true,'margin'=>0, 'label'=>Acid::trad('admin_menu_home'),'display'=>true),

					'configuration'     	=> array('level'=>$def_level,'separator'=>true,'label'=>Acid::trad('admin_menu_my_config'),'display'=>true,'unclickable'=>true,'color'=>$color[1]),
						'config'           	 => array('level'=>User::curLevel(), 'label'=>Acid::trad('admin_menu_infos'),'display'=>true,'parent'=>'configuration'),
    					'siteconfig'       	 => array('level'=>$def_level,'margin'=>0, 'label'=>Acid::trad('admin_menu_config'),'display'=>true,'parent'=>'configuration'),

    				'user_configuration'   	=> array('level'=>$dev_level,'separator'=>true,'label'=>Acid::trad('admin_menu_user_config'),'display'=>true,'unclickable'=>true,'color'=>$color[2]),
    					'user'            	 => array('level'=>$dev_level,'mod'=>'User','margin'=>0, 'label'=>Acid::trad('admin_menu_user'),'display'=>true,'parent'=>'user_configuration'),
    					'user_group'       	 => array('level'=>$dev_level,'mod'=>'UserGroup','margin'=>0, 'label'=>Acid::trad('admin_menu_user_group'),'display'=>true,'parent'=>'user_configuration'),
    					'user_permission'  	 => array('level'=>$dev_level,'mod'=>'UserPermission','margin'=>0, 'label'=>Acid::trad('admin_menu_user_permission'),'display'=>true,'parent'=>'user_configuration'),

   				    'web'     				=> array('level'=>$def_level,'separator'=>true,'label'=>Acid::trad('admin_menu_web'),'display'=>true,'unclickable'=>true,'color'=>$color[3]),
						'actu'            	 => array('level'=>$def_level,'mod'=>'Actu','label'=>Acid::trad('admin_menu_news'),'display'=>true,'parent'=>'web'),
    					'photo_home'       	 => array('level'=>$def_level,'mod'=>'PhotoHome','label'=>Acid::trad('admin_menu_photo_home'),'display'=>true,'parent'=>'web'),
    					'photo'            	 => array('level'=>$def_level,'mod'=>'Photo','label'=>Acid::trad('admin_menu_photo'),'display'=>true,'parent'=>'web'),
    					'page'             	 => array('level'=>$def_level,'mod'=>'Page','margin'=>0, 'label'=>Acid::trad('admin_menu_page'),'display'=>true,'parent'=>'web'),

    				'tools'     			=> array('level'=>$def_level,'separator'=>false,'label'=>Acid::trad('admin_menu_tools'),'display'=>true,'unclickable'=>true,'color'=>$color[4]),
    				    'medias'          	 => array('level'=>$def_level,'separator'=>false,'margin'=>0, 'label'=>Acid::trad('admin_menu_browser'),'display'=>true,'parent'=>'tools'),
				);

	//hook controller set
	AcidHook::call('admin_conroller_done');


	//Checking for User Access
	$module = null;
	$check_key = $p ? $p : 'default';

	//Controller exists
	if ( (!isset($controller[$check_key])) ) {
		$p = 'default';
		$module = null;
	}
	//Standard Access Refused
	elseif ( (!User::curLevel($controller[$check_key]['level'])) ) {

		//Permissions
		$module = isset($controller[$check_key]['mod']) ? $controller[$check_key]['mod'] : null;
		if (!Acid::mod($module)->getUserAccess()) {
			$p = 'default';
			$module = null;
		}
	}
	//Standard Access Accepted
	else{
		$module = isset($controller[$check_key]['mod']) ? $controller[$check_key]['mod'] : null;
	}


	switch($p) {
		//configuration
		case 'config' :
			Acid::set('admin_title',Acid::trad('admin_menu_infos'));
			$content .= Acid::mod('User')->printAdminBody(User::printAdminUserForms(),$my_onglets) ;
		break;

		case 'siteconfig' :
			Acid::set('admin_title',Acid::trad('admin_menu_config'));
			$GLOBALS['tinymce']['all'] = false;
			$content .= Acid::mod('SiteConfig')->printAdminInterface();
		break;

		//tools
		case 'medias' :

			Conf::set('plupload:active',true);

			Acid::set('admin_title',Acid::trad('admin_menu_browser'));
			Acid::set('admin_title_attr',array('style'=>'color:'.$color[4].';'));

			$plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';
			$dir = isset($_GET['fsb_path']) ? $_GET['fsb_path'] : '';


			if ($plugin=='tinymce') {
				$GLOBALS['tinymce']['popup'] = true;
				$content_only = true;
			}

			$fb = new AcidBrowser(Acid::get('path:uploads'),false,null,$plugin);

			$content .= Acid::mod('User')->printAdminBody($fb->printDir($dir),null);
		break;

		//general
		default:

			//hook for case
			AcidHook::call('admin_conroller_case');

			//modules
			if ($module) {
				$content .= Acid::mod($module)->printAdminInterface();
			}
			//welcome
			else{
				$default_content = 	'Bonjour'.(isset($sess['user']['username']) ? ' '.$sess['user']['username'] : '' ) . ',' . "\n" .
									'<br />Vous voici dans votre espace d\'administration';
				$content .= Acid::mod('User')->printAdminBody($default_content,$my_onglets);
			}
		break;

	}

	//generating menu
	$menu = Acid::tpl('admin/siteadmin-menu.tpl',array('siteadmin_cat'=>$siteadmin_cat,'controller'=>$controller,'page'=>$p,'def_level'=>$def_level),User::curUser());

	//generating admin body
	Acid::set('admin:contact','');
	Acid::set('admin:website','');

	$html .= $content_only ? $content : Acid::tpl('admin/siteadmin-body.tpl',array('menu'=>$menu,'content'=>$content),User::curUser());

}


require 'sys/stop.php';
?>
