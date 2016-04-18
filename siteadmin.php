<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Controller
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

//préconfiguration de l'appel d'acidfarm
$admin_page=true;
$permission_active = true;
$acid_page_type = 'admin';

//activation forcée des sessions
$GLOBALS['acid']['session']['enable'] = true;

//$acid['server_theme'] = 'bootstrap';

//appel d'acidfarm
require 'sys/start.php';


//on utilisera le controller comme librairie
$acid['includes']['AdminController'] 	 	  = 'sys/controller/admin/AdminController.php';

//l'affichage siteadmin est choisi
Acid::set('out','siteadmin');

//on définit le page Title
Conf::setPageTitle('Admin');


//on active tinymce pour toutes
Acid::set('tinymce:active', true);
Acid::set('tinymce:all', true);

Conf::set('plupload:active', true);
Conf::set('plupload:all', true);

$my_onglets = null;
Acid::set('admin:content_only',false);
Acid::set('admin_title_attr',array('class'=>'h2'));

//Access Controller
$access_level	 = Acid::get('lvl:admin');

// Log Form
if (!User::curLevel($access_level)) {

	Acid::set('admin:content_only',true);

	//Si pas de page définie, affichage de la home
	AcidRouter::addDefaultRoute('index',new AcidRoute('default',array('controller'=>'AdminController','action'=>'login','module'=>'admin')));

	//Lancement du Router
	AcidRouter::run();
}

// Admin Interface
else {

	//Init.
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
	Acid::set('admin_colors',$color);

	//Setting controller
	$def_level	 = $access_level; // $acid['lvl']['admin'];
	$dev_level	 = $acid['lvl']['dev'];

	//les catégories


	//MENUS ET CONTROLLEURS

	//Accueil
	AdminController::addMenuCat('default',Acid::trad('admin_menu_home'),array(),$def_level);

	//Configuration

	//-configuration
	AdminController::addMenuCat('configuration',Acid::trad('admin_menu_my_config'),array('unclickable'=>true,'color'=>$color[1]),$def_level);

	//--mes infos
	AdminController::addMenu('config','configuration',Acid::trad('admin_menu_infos'),User::curLevel());
	AdminController::addAccess('config',User::curLevel());

	//--configuration
	AdminController::addMenu('siteconfig','configuration',Acid::trad('admin_menu_config'),$def_level);
	AdminController::addAccess('siteconfig',$def_level);

	//--seo
	AdminController::addMenu('seo','configuration',Acid::trad('admin_menu_seo'),$dev_level);
	AdminController::addAccess('seo',$dev_level,'Seo');


	//User Configuration

	//-utilisateurs
	AdminController::addMenuCat('user_configuration',Acid::trad('admin_menu_user_config'),array('unclickable'=>true,'color'=>$color[2]),$dev_level);

	//--utilisateurs
	AdminController::addMenu('user','user_configuration',Acid::trad('admin_menu_user'),$dev_level);
	AdminController::addAccess('user',$dev_level,'User');

	//--groupes utilisateurs
	AdminController::addMenu('user_group','user_configuration',Acid::trad('admin_menu_user_group'),$dev_level);
	AdminController::addAccess('user_group',$dev_level,'UserGroup');

	//--permissions utilisateurs
	AdminController::addMenu('user_permission','user_configuration',Acid::trad('admin_menu_user_permission'),$dev_level);
	AdminController::addAccess('user_permission',$dev_level,'UserPermission');


	//Web

	//-site web
	AdminController::addMenuCat('web',Acid::trad('admin_menu_web'),array('unclickable'=>true,'color'=>$color[3]),$def_level);

	//--actualites
	AdminController::addMenu('actu','web',Acid::trad('admin_menu_news'),$def_level);
	AdminController::addAccess('actu',$def_level,'Actu');

	//--photos accueil
	AdminController::addMenu('photo_home','web',Acid::trad('admin_menu_photo_home'),$def_level);
	AdminController::addAccess('photo_home',$def_level,'PhotoHome');

	//--photos
	AdminController::addMenu('photo','web',Acid::trad('admin_menu_photo'),$def_level);
	AdminController::addAccess('photo',$def_level,'Photo');

	//--pages
	AdminController::addMenu('page','web',Acid::trad('admin_menu_page'),$def_level);
	AdminController::addAccess('page',$def_level,'Page');

	//--sample
	//AdminController::addMenu(Sample::TBL_NAME,'web',Sample::modTrad('__NAME__'),$def_level);
	//AdminController::addAccess(Sample::TBL_NAME,$def_level,'Sample');

	//Tools

	//-outils
	AdminController::addMenuCat('tools',Acid::trad('admin_menu_tools'),array('unclickable'=>true,'color'=>$color[4]),$def_level);

	//--mediatheque
	AdminController::addMenu('medias','tools',Acid::trad('admin_menu_browser'),$def_level);
	AdminController::addAccess('medias',$def_level);



	//Ajout d'un hook une fois les controlleurs définis
	AcidHook::call('admin_controller_config_done');

	//Définition du routeur principal
	AcidRouter::addDefaultRoute('index',new AcidRoute('default',array('controller'=>'AdminController','action'=>'index','module'=>'admin')));

	//Lancement du Router
	AcidRouter::run();

	//Generation du corps de l'admin
	Acid::set('admin:contact','');
	Acid::set('admin:website','');
	Acid::set('admin:menu_config',array('siteadmin_cat'=>AdminController::$menucat,'controller'=>AdminController::$menu,'page'=>$p,'def_level'=>$def_level));

	//Acid::set('admin:content_only',true);

}

require 'sys/stop.php';

?>
