<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Core
 * @version   0.4
 * @since     Version 0.4
 * @copyright 2012 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

require_once 'interface/acid_router_interface.php';

/**
 * Classe de Routage : contrôleur de route
 * @package   Acidfarm\Core
 */
class AcidRouter implements Acid_Router_Interface{

    const URI_DELIMITER         = "/";
    const MODULES_PATH          = 'sys/controller/';
    const DEFAULT_MODULE        = 'acid';
    const DEFAULT_CONTROLLER    = 'IndexController';
    const DEFAULT_ACTION        = 'index';

    /**
     * @var array Tableau des routes Défini
     */
    protected  $_routes = array();

    /**
     * @var object Route courante
     */
    private $_currentRoute  = null;

    /**
     * @var object Route par défaut
     */
    private $_defaultRoute  = null;

    /**
     * @var string Langue courante
     */
    private $_currentLang   = null;

    /**
     * @var string url folder
     */
    private $_folder        = null;

    /**
     * @var string url site
     */
    private $_site          = null;

    /**
     * @var array liste des callbacks avant dispach
     */
    private $_before          = array();

    /**
     * @var array liste des callbacks après dispach
     */
    private $_after          = array();
    
    /**
     * @var string get_param_used_as_route
     */
    private $_use_get_param          = null;
    
    /**
     * @var array Paramètres qui seront transmis à toutes les routes
     */
    private static $_globalParams = array();

    /**
     * @var objet Instance
     */
    private static $instance;

    /**
     * Retourne l'instance
     * @return AcidRouter
     */
    public static function getInstance(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    /**
     * Lance une détection d'URI
     */
    public static function run(){

      
        Acid::log('ROUTER', 'START RUN...');
        $_server = $_SERVER["SERVER_NAME"];
        $_uri_total= parse_url($_SERVER["REQUEST_URI"],PHP_URL_PATH);
        self::getInstance()->_site = Acid::get('url:system');
        self::getInstance()->_folder = Acid::get('url:folder').Acid::get('router:folder');
        $formated_path = substr($_uri_total, 1, strlen($_uri_total)-1);
        $formated_path = explode(AcidRouter::URI_DELIMITER, $formated_path);
        $root_keys = false;
        $indexstep = strpos(self::getInstance()->_folder,AcidRouter::URI_DELIMITER)!==false ? (count(explode(AcidRouter::URI_DELIMITER, self::getInstance()->_folder)) - 2) : 0;

        $lang_use_nav_0 = Acid::get('router:use_lang')===null ? Acid::get('lang:use_nav_0') : Acid::get('router:use_lang');
        if ($lang_use_nav_0){

        	if(self::getInstance()->_folder!=''&&self::getInstance()->_folder!='/'){
                self::getInstance()->_currentLang = $formated_path[$indexstep];
            }else{
            	self::getInstance()->_currentLang = $formated_path[0];
            }

            if(isset($formated_path[$indexstep]) && in_array($formated_path[$indexstep], Acid::get('conf:root_keys'))){
                self::getInstance()->_currentLang = '';
                $root_keys = true;
            }

        }else{
            self::getInstance()->_currentLang = '';
        }

        if (!self::getInstance()->_use_get_param) {
            $_uri =
                substr($_uri_total, (strlen(self::getInstance()->_folder) + strlen(self::getInstance()->_currentLang)));
            $_uri = ($root_keys) ? $formated_path[$indexstep] : $_uri;
        }else{
            $get_param_value = isset($_GET[self::getInstance()->_use_get_param]) ?
                $_GET[self::getInstance()->_use_get_param] : false;
            $_uri = $get_param_value ? $get_param_value : '/';
        }

        try{
            if(isset(self::getInstance()->_routes)&&count(self::getInstance()->_routes)>0){
                self::getInstance()->route($_uri);
                return true;
            }else{
           		if (self::getInstance()->_defaultRoute) {
                	self::getInstance()->runDefault();
                	return true;
            	}else{
               		throw new Exception('No route defined');
                	return false;
            	}
            }
        }catch(Exception $e){
            echo 'Router Exception (on router run() ) : '.$e->getMessage()."\n";
            die();
        }
    }

 	/**
     * Lance la route par défaut
     */
    public static function runDefault(){
   		self::getInstance()->_currentRoute = self::getInstance()->_defaultRoute;

    	self::runCallBack(self::getInstance()->_before);
    	Acid::log('ROUTER', 'DISPATCH DEFAULT URL...');
    	self::getInstance()->dispatcher();

    	self::runCallBack(self::getInstance()->_after);
    }

    /**
     * Renvoi vers le controller/action si match parmis les routes existantes
     * Sinon Renvoi une erreur 404
     * @param Request = URI de requete soumis.
     */
    public static function proceed($request){

		if (self::getInstance()->_routes) {
			foreach (self::getInstance()->_routes as $key => $route) {
				if($route->match($request)){
					self::getInstance()->_currentRoute = $route;

					self::runCallBack(self::getInstance()->_before);
					Acid::log('ROUTER', 'DISPATCH FOUNDED URL : '.$route->build());
					self::getInstance()->dispatcher();
					self::runCallBack(self::getInstance()->_after);

					if ($route->unique_match) {
						return true;
					}
				}
			}
		}

		return false;
    }

    /**
      * Renvoi vers le controller/action si match
      * Sinon Renvoi une erreur 404
      * @param Request = URI de requete soumis.
      */
     public static function route($request){
        try {

        	if(strlen($request) && $request[0]==="/"){
                $request = substr($request, 1);
            }

            if($request==''){
                if(self::getInstance()->_defaultRoute){

					if (self::proceed($request)) {
	                	return true;
					}

                	self::getInstance()->runDefault();
                    return true;
                }else{
                   throw new Exception('No Default route call');
                }
            }

            if (self::proceed($request)) {
            	return true;
            }


            Acid::log('ROUTER', 'No matching for '.$request);
            if(self::getInstance()->_defaultRoute){
            	self::getInstance()->_defaultRoute->match($request,true);
        	    self::getInstance()->runDefault();
        	    return true;
            }


            AcidUrl::error404();

         }catch(Exception $e){
             echo 'Router Exception (on route match) : '.$e->getMessage()."\n";
             die();
         }
     }

      /**
      * Ajoute un callback before
      * @param string $routename
      * @param function $callback
      */
     public static function before($routename,$callback){
     	$routes = is_array($routename) ? $routename : array($routename);
     	foreach ($routes as $r) {
     		self::getInstance()->_before[$r][] = $callback;
     	}
     	return self::getInstance();
     }

     /**
      * Ajoute un callback before
      * @param string $routename
      * @param function $callback
      */
     public static function after($routename,$callback){
     	$routes = is_array($routename) ? $routename : array($routename);
     	foreach ($routes as $r) {
     		self::getInstance()->_after[$r][] = $callback;
     	}
     	return self::getInstance();
     }

     /**
      * Execute un callback
      * @param string $routename
      * @param function $callback
      */
     public static function runCallback($tab) {
		$execute = array();

		$to_call = array('*','events','routes',self::getCurrentRouteName());
		foreach ($to_call as $call) {

	     	if (isset($tab[$call])) {

	     		$accept = true;
	     		if (($call=='events') && (self::getInstance()->_currentRoute->unique_match)) {
	     			$accept = false;
	     		}

	     		if (($call=='routes') && (!self::getInstance()->_currentRoute->unique_match)) {
	     			$accept = false;
	     		}

				if ($accept) {
					$execute =  array_merge($execute,$tab[$call]);
				}
	     	}
		}

		if ($execute) {
			foreach ($execute as $function) {
				Acid::log('ROUTER', 'RUNNING CALLBACK');
				call_user_func_array($function,array(self::getInstance()));
			}
		}

		return self::getInstance();
     }

     /**
      * Ajoute une route
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addRoute($name,AcidRoute $route){
         //Acid::log('ROUTER', 'ADD ROUTE : '.$name);
     	 $route->setName($name);
         self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Ajoute une route GET
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addGet($name,AcidRoute $route){
     	$route->setName($name);
     	$route->setMethod('GET');
     	self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Ajoute une route POST
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addPost($name,AcidRoute $route){
     	$route->setName($name);
     	$route->setMethod('POST');
     	self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Ajoute une route PUT
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addPut($name,AcidRoute $route){
     	$route->setName($name);
     	$route->setMethod('PUT');
     	self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Ajoute une route DELETE
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addDelete($name,AcidRoute $route){
     	$route->setName($name);
     	$route->setMethod('DELETE');
     	self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Ajoute une route Head
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addHead($name,AcidRoute $route){
     	$route->setName($name);
     	$route->setMethod('HEAD');
     	self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Ajoute une route PATCH
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addPatch($name,AcidRoute $route){
     	$route->setName($name);
     	$route->setMethod('PATCH');
     	self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Ajoute une route OPTION
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addOption($name,AcidRoute $route){
     	$route->setName($name);
     	$route->setMethod('OPTION');
     	self::getInstance()->_routes[$name] = $route;
     }

     /**
      * Définit la route par défaut
      * @param string $name
      * @param AcidRoute $route
      */
     public static function addDefaultRoute($name, AcidRoute $route){
         //Acid::log('ROUTER', 'ADD DEFAULT ROUTE : '.$name);
     	 $route->setName($name);
         self::getInstance()->_defaultRoute = $route;
     }
    
    /**
     * Definit si la route est enregistrée dans un paramêtre GET
     * @param $name Nom de l'attribut GET
     */
    public static function useGetParam($name){
        self::getInstance()->_use_get_param = $name;
        return self::getInstance();
    }
    
     /**
      * Definit des paramètres
      * @param string $name = nom du parametre
      * @param $value = valeur du parametre
      */
     public static function setParam($name,$value){
          self::getInstance()->_currentRoute->setParams(array($name=>$value));
          return self::getInstance();
     }

     /**
      * Récupère la valeur d'un paramètre
      * @param string $name = nom du parametre
      */
     public static function getParam($name){
         $instance = self::getInstance();
         $route = self::getInstance()->_currentRoute;
         if ($params = $route->getParams()) {
             foreach ($params as $key => $value) {
                 if ($name === $key) {
                     return $value;
                 }
             }
         }
         return false;
     }

     /**
      * Défini les parametres à injecté lors de l'appel
      * @param array $params Array(name=>value)
      *
      */
     public static function setParams($params=array()){
         self::getInstance()->_currentRoute->setParams($params);
         return self::getInstance();
     }

     /**
      * Renvoi un tableau des parametres URI
      * array(key=>value)
      */
     public static function getParams(){
         return self::getInstance()->_currentRoute->getParams();
     }

     /**
      * Retourne la valeur sinon une Exception
      * @param $id = position du parametre voulu
      */
     public static function getParamById($id){
     	if (self::getInstance()->_currentRoute) {
         $uri = self::getInstance()->_currentRoute->getURI();
         $url = explode(AcidRouter::URI_DELIMITER, $uri);
         $params = self::getInstance()->_currentRoute->getParams();
         if(array_key_exists($id, $url)){
            $value = $url[$id];
             if($value[0]===':'){
                 return $params[substr($value,1)];
             }
             if($value[0]==='@'){
                 return $params[substr($value,1)];
             }
             return $value;
         }else{
             return false;
         }
     	}else{
     		return false;
     	}
     }

     /**
      * Définit les paramètres partiels
      * @param string $value
      * @param boolean $clear
      * @return mixed
      */
     public static function setPartialParams($value,$clear=false){
         self::getInstance()->_currentRoute->setPartial($value,$clear);
         return self::getInstance();
     }

     /**
      * All partial
      */
     public static function getPartialParams(){
         return self::getInstance()->_currentRoute->getPartials();
     }

     /**
      * Partial
      * @param srting $id ident
      */
     public static function getPartialParamById($id){
         $params = self::getInstance()->_currentRoute->getPartials();
         if(array_key_exists($id, $params)){
            return $params[$id];
         }else{
             return false;
         }
     }

     /**
      * Retourne une url partielle au moyen de l'identifiant en entrée
      * @param mixed $id
      * @return mixed
      */
     public static function getPartitionalURIbyId($id){
         $array = self::getInstance()->_currentRoute->getPartitionalURI();
         return (!empty($array[$id]))?$array[$id]:false;
     }

     /**
      * Si un name est entré, suppression et clear du parametre URI
      * Sinon Clear de tous les parametres
      * @param $name
      */
     public static function clearParams($name=null){
         self::getInstance()->_currentRoute->cleanParam($name);
     }

     /**
      * Retourne la langue courante
      * @return string
      */
     public static function getCurrentLang(){
         return self::getInstance()->_currentLang;
     }

     /**
      * Retourne la route par défaut
      * @return string
      */
     public static function getDefaultRoute(){
         return self::getInstance()->_defaultRoute;
     }

     /**
      * Distribution de la route
      */
     public static function dispatcher(){
         self::getInstance()->_currentRoute->callDispatch();
     }

     /**
      * Accède à la route
      * @param string $routeName
      * @param boolean $clean si true, supprime les paramètres liés à la route avant de la retourner
      * @return Ambigous <AcidRouter, objet>
      */
     public static function accessTo($routeName,$clean=true){
         $routes = self::getInstance()->_routes;
         if(array_key_exists($routeName, $routes)){
             $route = $routes[$routeName];
             self::getInstance()->_currentRoute = $route;
             if($clean){
                 self::getInstance()->_currentRoute->cleanPartialParams();
             }
         }
         return self::getInstance();
     }

     /**
      * Retourne la route courante
      */
     public static function getCurrentRouteName(){
     	if (self::getInstance()->_currentRoute) {
     		return self::getInstance()->_currentRoute->getName();
     	}
     	return false;
     }

     /**
      * Génère une URL en fonction de la route désignée par les paramètres en entrée
      * @param string $routename
      * @param array $params paramètres obligatoires
      * @param array $partial_params paramètres partiels
      * @param boolean $no_slash  si true, enlève le dernier slash
      * @param boolean $no_lang si true, ne prend pas en compte la langue
      * @param boolean $no_param DEPRECATED/TODO
      * @param boolean $http si true, retourne l'url absolue, sinon retourne l'url relative
      * @return mixed
      */
     public static function buildURL($routename,$params=null,$partial_params=null,$no_slash=false,$no_lang=false,$no_param=false,$http=false){

         $lang = (AcidRouter::getCurrentLang()!=='')?self::getInstance()->_currentLang.AcidRouter::URI_DELIMITER:'';
         if($no_lang){
             $lang='';
         }

         $rroute = (!empty(self::getInstance()->_routes[$routename]))?self::getInstance()->_routes[$routename]:null;
         if(!empty($rroute)){
         	$route = new AcidRoute($rroute->getURI());
         	$route->setParams($params);
          	$route->setPartial($partial_params);

        	$path = (($http)?self::getInstance()->_site:self::getInstance()->_folder).$lang.$route->build($no_param);

        	if($no_slash){
           		$try_patch = strrev($path);
            	if($try_patch[0]=="/"){
            		$path = substr($path, 0,strlen($path)-1);
           		}
           	}

          	return $path;
          }

         return false;
     }

     /**
      * Dispatch current URL
      */
     public static function dispatch(){
         $lang = (AcidRouter::getCurrentLang()!=='')?self::getInstance()->_currentLang.AcidRouter::URI_DELIMITER:'';
         $path = self::getInstance()->_site.$lang.self::getInstance()->_currentRoute->build();
         Acid::log('ROUTER', 'REDIRECT TO : '.$path);
         AcidUrl::redirection301($path);
     }

     /**
      * Retourne la traduction de la clé de routage pour la langue soumise en entrée, retourne false si non trouvée
      * @param string $key
      * @param string $lang
      * @return mixed
      */
     public static function getKey($key,$lang=null){
     	$lang = $lang===null ? Acid::get('lang:current') : $lang;
        if (Acid::exists('router:'.$key.':'.$lang.':key','lang')) {
            return Acid::get('router:'.$key.':'.$lang.':key','lang');
        }
        return false;
     }

     /**
      * Retourne le nom associé à la clé de routage pour la langue soumise en entrée, retourne false si non trouvé
      * @param string $key
      * @param string $lang
      * @return mixed
      */
     public static function getName($key,$lang=null){
     	$lang = $lang===null ? Acid::get('lang:current') : $lang;

        if (Acid::exists('router:'.$key.':'.$lang.':name','lang')) {
            return Acid::get('router:'.$key.':'.$lang.':name','lang');
        }
        return false;
     }

     /**
      * Cherche la clé de routage correspondant à $value
      * @param string $value
      * @param string $current langue
      * @return mixed
      */
     public static function searchKey($value,$current=null){
         $current = (!empty($current))?$current:Acid::get('lang:current');
         if ($value) {
	         if($value[0]==='@'){
	             return substr($value, 1);
	         }
	         if($value!==''){
	             $lang = Acid::get('router','lang');
	             foreach ($lang as $key => $array) {
	                 if($array[$current]['key']===$value){
	                     return $key;
	                 }
	             }
	         }
         }
         return false;
     }

     /**
      * Definit une route à la volée et y accès directement
      * @param string $name
      * @param AcidRoute $route
      * @param array $params
      * @param array $partials
      */
     public static function directlyRun($name,AcidRoute $route,$params=array(),$partials=array()){
     	$route->setName($name);
        self::getInstance()->addRoute($name,$route);
        self::getInstance()->run();
        self::getInstance()->accessTo($name)->setParams($params)->setPartialParams($partials)->dispatch();
     }
}