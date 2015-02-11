<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur d'Index
 * @package   Controller
 */
class AdminController{

	public static $menu = array();
	public static $menucat = array();
	public static $controller = array();
	public static $functions = array();

	/**
	 * Contrôle des accès utilisateur
	 * @param string $routename
 	 * @return number|boolean|sring nom de la route si accessible, false si non permis, 0 si inexistant
	 */
	public static function checkAccess($page){

		//Checking for User Access
		$module = null;
		$check_key = $page ? $page : 'default';

		//Controller exists
		if ( (!isset(AdminController::$controller[$check_key])) ) {
			return 0;
		}
		//Standard Access Refused
		elseif ( (!User::curLevel(AdminController::$controller[$check_key]['level'])) ) {

			//Permissions
			$module = isset(AdminController::$controller[$check_key]['mod']) ? AdminController::$controller[$check_key]['mod'] : null;
			if ($module) {
				if (!Acid::mod($module)->getUserAccess()) {
					return false;
				}
			}

		}

		return $check_key;
	}


	/**
	 *
	 * @param unknown $routename
	 * @return number|boolean|unknown
	 */
	public static function addAccess($key,$level,$module=null,$config=array()){

		$config['level'] = $level;
		$config['module'] = $module;

		AdminController::$controller[$key] = $config;

	}

	/**
	 *
	 * @param unknown $routename
	 * @return number|boolean|unknown
	 */
	public static function addMenu($key,$parent, $label,$level=0,$config=array()){

		$config['label'] = $label;
		$config['level'] = $level;

		if ($parent) {
			AdminController::$menu[$key] = $config;
			AdminController::$menucat[$parent]['elts'][] = $key;
		}else{
			AdminController::$menucat[$key] = $config;
		}

	}

	/**
	 *
	 * @param unknown $routename
	 * @return number|boolean|unknown
	 */
	public static function addMethod($key,$function,$args=array()){
		AdminController::$functions[$key]['func'] = $function;
		AdminController::$functions[$key]['args'] = $args;
	}

	/**
	 *
	 * @param unknown $routename
	 * @return number|boolean|unknown
	 */
	public static function addMenuCat($key,$label,$config=array(),$level=0){

		static::addMenu($key,null,$label,$level,$config);

	}


	/**
	 * Affichage d'authentification
	 */
	public static function login(){
		$content = '';

		if  (isset($_GET['pass_oublie'])) {
			$forget = $_GET['pass_oublie'];
			$content .= Acid::mod('User')->printPasswordForgotten($forget,$_SERVER['PHP_SELF']);
		}else{
			$cur_user = User::curUser();
			$msg = $cur_user->getId() ? Acid::trad('admin_no_permission') : null;
			$content .= Acid::mod('User')->printAdminLogginForm($msg);
		}

		Conf::addToContent($content);
	}

	/**
	 * Affichage de l'accès refusé
	 */
	public static function denied(){
		return static::board();
	}

    /**
     * Affichage de la configuration du site
     */
    public static function siteconfig(){
    	$content ='';

    	Acid::set('admin_title',Acid::trad('admin_menu_config'));
    	$GLOBALS['acid']['tinymce']['all'] = false;
    	$content .= Acid::mod('SiteConfig')->printAdminInterface();

    	Conf::addToContent($content);
    }

    /**
     * Affichage de la configuration utilisateur
     */
    public static function config(){
    	$content ='';
    	$my_onglets = false;

    	Acid::set('admin_title',Acid::trad('admin_menu_infos'));
    	$content .= Acid::mod('User')->printAdminBody(User::printAdminUserForms(),$my_onglets) ;

    	Conf::addToContent($content);
    }

    /**
     * Affichage de la mediatheque
     */
    public static function medias(){
    	$content ='';

    	Conf::set('plupload:active',true);

    	Acid::set('admin_title',Acid::trad('admin_menu_browser'));
    	Acid::set('admin_title_attr',array('style'=>'color:'.Acid::get('admin_colors:4').';'));

    	$plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';
    	$dir = isset($_GET['fsb_path']) ? $_GET['fsb_path'] : '';


    	if ($plugin=='tinymce') {
    		$GLOBALS['acid']['tinymce']['popup'] = true;
    		$content_only = true;
    	}

    	$fb = new AcidBrowser(Acid::get('path:uploads'),false,null,$plugin);

    	$content .= Acid::mod('User')->printAdminBody($fb->printDir($dir),null);

    	Conf::addToContent($content);
    }

    /**
     * Affichage du board
     */
    public static function board(){
    	$content ='';

    	$default_content = 	'Bonjour'.(isset($sess['user']['username']) ? ' '.$sess['user']['username'] : '' ) . ',' . "\n" .
    			'<br />Vous voici dans votre espace d\'administration';
    	$content .= Acid::mod('User')->printAdminBody($default_content,null);

    	Conf::addToContent($content);
    }


    /**
     * Affichage des modules
     */
    public static function index(){
    	$content ='';

    	$route = AcidRouter::getKey('page');
    	$route = $route ? Lib::getIn('page',$_GET,'default') : $route;

    	if ($check_key = static::checkAccess($route)) {

    		$module = isset(AdminController::$controller[$check_key]['module']) ? AdminController::$controller[$check_key]['module'] : null;

    		//hook for case
    		AcidHook::call('admin_controller_route');


    		if (is_callable('static::'.$route)) {
    			$content .= static::$route();
    		}elseif(isset(AdminController::$functions[$check_key])) {
    			call_user_func(AdminController::$functions[$check_key]['func'],AdminController::$functions[$check_key]['args']);
    		}elseif($module) {
    			$content .= Acid::mod($module)->printAdminInterface();
    		}else{
    			$content .= static::board();
    		}


    	}else{
    		return static::denied();
    	}

    	Conf::addToContent($content);
    }

}