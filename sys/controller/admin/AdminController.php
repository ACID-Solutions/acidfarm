<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  Acidfarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Contrôleur d'Index
 * @package   Acidfarm\Controller
 */
class AdminController{

	/**
	 * @var array Liste des élements du menu
	 */
	public static $menu = array();

	/**
	 * @var array Liste des catégories du menu
	 */
	public static $menucat = array();

	/**
	 * @var array Liste des controllers incluant leurs droits d'accès
	 */
	public static $controller = array();

	/**
	 * @var array Liste des méthodes personnalisées
	 */
	public static $functions = array();


	/**
	 * Retourne la couleur associée à la clé en entrée
	 * @param $key
	 * @return mixed|string
	 */
	public static function color($key) {
		$color ='';

		if (!empty(AdminController::$menu[$key])) {

			$color = Lib::getIn('color',AdminController::$menu[$key]);

			if (!$color) {
				if ($parent =  Lib::getIn('parent',AdminController::$menu[$key])) {
					$color = Lib::getIn('color',AdminController::$menucat[$parent]);
				}
			}
		}

		return $color;
	}

	/**
	 * Contrôle des accès utilisateur
	 * @param $page
	 * @return bool|int|string nom de la route si accessible, false si non permis, 0 si inexistant
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
	 * Définit les accès à la page désignée part $key
	 * @param $key
	 * @param $level
	 * @param null $module
	 * @param array $config
	 */
	public static function addAccess($key,$level,$module=null,$config=array()){

		$config['level'] = $level;
		$config['module'] = $module;

		AdminController::$controller[$key] = $config;

	}

	/**
	 * Ajout la page designée par $key au menu
	 * @param $key
	 * @param $parent
	 * @param $label
	 * @param int $level
	 * @param array $config
	 */
	public static function addMenu($key,$parent, $label,$level=0,$config=array()){

		$config['label'] = $label;
		$config['level'] = $level;
		$config['parent'] = $parent;

		if ($parent) {
			AdminController::$menu[$key] = $config;
			AdminController::$menucat[$parent]['elts'][] = $key;
		}else{
			AdminController::$menucat[$key] = $config;
		}

	}

	/**
	 * Ajoute une "méthode controller" à la volée pour la page désignée par $key
	 * @param $key
	 * @param $function
	 * @param array $args
	 */
	public static function addMethod($key,$function,$args=array()){
		AdminController::$functions[$key]['func'] = $function;
		AdminController::$functions[$key]['args'] = $args;
	}

	/**
	 * Ajoute une catégorie au menu
	 * @param $key
	 * @param $label
	 * @param array $config
	 * @param int $level
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
    	Acid::set('admin_title_attr',((array)Acid::get('admin_title_attr')+array('style'=>'color:'.static::color('medias').';')));

    	$plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';
    	$dir = isset($_GET['fsb_path']) ? $_GET['fsb_path'] : '';
    	$page = isset($_GET['fsb_page']) ? $_GET['fsb_page'] : 1;
        $page = false;

    	if ($plugin=='tinymce') {
    		$GLOBALS['acid']['tinymce']['popup'] = true;
    		Acid::set('admin:content_only',true);
    	}

    	$fb = new AcidBrowser(Acid::get('path:uploads'),false,null,$plugin,null);

    	$content .= Acid::mod('User')->printAdminBody($fb->printDir($dir,$page),null);

    	Conf::addToContent($content);
    }

    /**
     * Affichage du board
     */
    public static function board(){
    	$content ='';

		$registration = '';
		$registration_path = SITE_PATH.'registration/private/registration.php';
		if (file_exists($registration_path)) {
    	ob_start();
		include(SITE_PATH.'registration/private/registration.php');
		$registration = ob_get_clean();
		}


		$expire_date = time() + Acid::get('session:expire') - (60*5);
		$stats['users'] = AcidDB::query('SELECT COUNT(*) as count FROM '.Acid::get('session:table').' WHERE `expire` > '.$expire_date)->fetch(PDO::FETCH_ASSOC);

		$last_news = null;
		if (is_callable('News::getLast')) {
			$last_news = News::getLast(1);
		}

		$vars = array('registration'=>$registration,'stats'=>$stats,'lastnews'=>$last_news);
		$tpl = 'admin/admin-board.tpl';
		$content = Acid::tpl($tpl,$vars);

		//Calling hook
		AcidHook::call('adminboard');


		Conf::addToContent(Acid::mod('User')->printAdminBody($content,null));

    }

    /**
     * Affichage de la configuration utilisateur
     */
    public static function home(){

        $content = Acid::mod('User')->printAdminBody(SiteConfig::printRemoteHomeForm(),array(AcidUrl::build(array('page'=>'home'))=>Acid::trad('admin_menu_home')));


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

			$custom_route = false;

    		$module = isset(AdminController::$controller[$check_key]['module']) ? AdminController::$controller[$check_key]['module'] : null;

    		//hook for case
    		AcidHook::call('admin_controller_route');

			//If no hack by hooks
			if (empty($custom_route)) {

				if (is_callable('static::'.$route)) {
					$content .= static::$route();
				}elseif(isset(AdminController::$functions[$check_key])) {
					call_user_func(AdminController::$functions[$check_key]['func'],AdminController::$functions[$check_key]['args']);
				}elseif($module) {
					$content .= Acid::mod($module)->printAdminInterface();
				}else{
					$content .= static::board();
				}

			}


    	}else{
    		return static::denied();
    	}

    	Conf::addToContent($content);
    }

}