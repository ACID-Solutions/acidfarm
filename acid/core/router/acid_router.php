<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Core
 * @version   0.4
 * @since     Version 0.4
 * @copyright 2012 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

require_once 'interface/acid_router_interface.php';

/**
 * Classe de Routage : contrôleur de route
 * @package   Core
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
        self::getInstance()->_folder = Acid::get('url:folder');
        $formated_path = substr($_uri_total, 1, strlen($_uri_total)-1);
        $formated_path = explode(AcidRouter::URI_DELIMITER, $formated_path);
        $root_keys = false;
        $indexstep = strpos(self::getInstance()->_folder,AcidRouter::URI_DELIMITER)!==false ? (count(explode(AcidRouter::URI_DELIMITER, self::getInstance()->_folder)) - 2) : 0;

        if(Acid::get('lang:use_nav_0')){
        	
        	if(self::getInstance()->_folder!=''&&self::getInstance()->_folder!='/'){
                self::getInstance()->_currentLang = $formated_path[$indexstep];
            }else{
            	self::getInstance()->_currentLang = $formated_path[0];
            }
            
            if(isset($formated_path[$indexstep]) && in_array($formated_path[$indexstep], Acid::get('root_keys','acidconf'))){
                self::getInstance()->_currentLang = '';
                $root_keys = true;
            }
            
        }else{
            self::getInstance()->_currentLang = '';
        }
        
        $_uri = substr($_uri_total, (strlen(self::getInstance()->_folder)+strlen(self::getInstance()->_currentLang)));
        $_uri = ($root_keys)? $formated_path[$indexstep] : $_uri;
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
    	Acid::log('ROUTER', 'DISPATCH DEFAULT URL...');
    	self::getInstance()->dispatcher();
    }

    /**
      * Renvoi vers le controller/action si match
      * Sinon Renvoi une erreur 404
      * @param Request = URI de requete soumis.
      */
     public static function route($request){
        try {
            
        	if($request{0}==="/"){
                $request = substr($request, 1);
            }
            
            if($request==''){
                if(self::getInstance()->_defaultRoute){
                	/*
                    self::getInstance()->_currentRoute = self::getInstance()->_defaultRoute;
                    Acid::log('ROUTER', '----- DISPATCH DEFAULT URL -----');
                    self::getInstance()->dispatcher();
                    */
                	self::getInstance()->runDefault();
                    return true;
                }else{
                   throw new Exception('No Default route call'); 
                }
            }

            foreach (self::getInstance()->_routes as $key => $route) {   
                if($route->match($request)){
                    self::getInstance()->_currentRoute = $route;
                    Acid::log('ROUTER', 'DISPATCH FOUNDED URL : '.$route->build());
                    self::getInstance()->dispatcher();
                    return true;
                }                      
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
         $params = $route->getParams();
         foreach($params as $key=>$value){
             if($name===$key){
                 return $value;
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
             if($value{0}===':'){
                 return $params[substr($value,1)];
             }
             if($value{0}==='@'){
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
            	if($try_patch{0}=="/"){
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
        if (Acid::exist('router:'.$key.':'.$lang.':key','lang')) {
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
     	
        if (Acid::exist('router:'.$key.':'.$lang.':name','lang')) {
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
	         if($value{0}==='@'){
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
