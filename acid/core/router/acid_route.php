<?php

/**
 * AcidFarm - Yet Another Framework
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

/**
 * Classe de Routage : configuration de route
 *
 * @package   Acidfarm\Core
 */
class AcidRoute
{
    /**
     * @var string URI
     */
    protected $URI = null;
    /**
     * @var string URI partielle
     */
    protected $_partitional_URI = null;
    /**
     * @var string URI générée
     */
    protected $generatedURI = null;
    /**
     * @var string Module associé à la route
     */
    protected $module = null;
    /**
     * @var string Controller associé à la route
     */
    protected $controller = null;
    /**
     * @var string Méthode liée à la route
     */
    protected $action = null;
    /**
     * @var string HTTP Methods de liaison
     * NULL / GET / POST / PUT / DELETE / HEAD / PATCH / OPTIONS
     */
    protected $method = null;
    /**
     * @var array Paramètres obligatoires
     */
    protected $params = [];
    /**
     * @var array Cette route contient des paramètres partiels
     */
    protected $partial = null;
    /**
     * @var array  Paramètres partiels
     */
    protected $partialParams = [];
    /**
     * @var string Nom de la route
     */
    protected $name = null;
    /**
     * @var boolean Empêche les autres routes d'être appelées si rencontré
     */
    public $unique_match = true;
    
    /**
     * Constructeur AcidRoute
     *
     * @param       $URI           alias de la route
     * @param array $controller    configuration du controller
     * @param null  $partial_start si défini, index de la premère clé des paramètres partiels
     * @param null  $partial_stop  si défini, index de la dernière clé des paramètres partiels
     * @param null  $params        les paramètres
     * @param null  $method        la méthode associée
     */
    public function __construct(
        $URI,
        $controller = [],
        $partial_start = null,
        $partial_stop = null,
        $params = null,
        $method = null
    ) {
        $this->params = $params;
        $this->URI = $URI;
        $this->_partitional_URI = explode(AcidRouter::URI_DELIMITER, $URI);
        $this->module = (isset($controller['module'])) ? $controller['module'] : AcidRouter::DEFAULT_MODULE;
        $this->controller =
            (isset($controller['controller'])) ? $controller['controller'] : AcidRouter::DEFAULT_CONTROLLER;
        $this->action = (isset($controller['action'])) ? $controller['action'] : AcidRouter::DEFAULT_ACTION;
        $this->method = $method;
        if (isset($partial_start)) {
            $this->partial['start'] = (is_int($partial_start)) ? $partial_start : null;
            if (isset($partial_stop)) {
                $this->partial['stop'] = (is_int($partial_stop)) ? $partial_stop : null;
            }
        }
    }
    
    /**
     * Définit le nom de la route
     *
     * @param string $val
     *
     * @return string
     */
    public function setName($val)
    {
        return $this->name = $val;
    }
    
    /**
     * Retourne le nom de la route
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Définit la méthode de la route
     *
     * @param mixed $val : NULL / GET / POST / PUT / DELETE / HEAD / PATCH / OPTIONS / ARRAY(GET,POST,..)
     *
     * @return string
     */
    public function setMethod($val)
    {
        return $this->method = $val;
    }
    
    /**
     * Retourne le nom de la route
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Retourn l'URI de la route
     *
     * @return string
     */
    public function getURI()
    {
        return $this->URI;
    }
    
    /**
     * Retourne l'URI partielle de la route
     *
     * @return string
     */
    public function getPartitionalUri()
    {
        return $this->_partitional_URI;
    }
    
    /**
     * Retourne si le chemin en entré coïncide avec la route
     *
     * @param string       $path
     * @param unknown_type $partial_on_fail
     *
     * @return boolean
     */
    public function match($path, $partial_on_fail = null)
    {
        if ($this->URI === '*') {
            return true;
        }
        
        if (($path === false) && ($this->URI !== '')) {
            return false;
        }
        
        if ($path !== '') {
            $path = explode(AcidRouter::URI_DELIMITER, $path);
        } elseif (($this->URI === '') && (!$this->_partitional_URI)) {
            return true;
        } else {
            return false;
        }
        
        $this->generatedURI = '';
        $param = '';
        $matched = false;
        $currentPartial = 0;
        
        if (count($path) < count($this->_partitional_URI)) {
            return false;
        }
        
        if ($this->method) {
            if ($http_method = $_SERVER['REQUEST_METHOD']) {
                if (is_array($this->method)) {
                    if (!in_array($http_method, $this->method)) {
                        return false;
                    }
                } elseif ($this->method != $http_method) {
                    return false;
                }
            }
        }
        
        if ($path) {
            foreach ($path as $key => $value) {
                $value = urldecode($value);
                
                if (!array_key_exists($key, $this->_partitional_URI)) {
                    if (isset($this->partial['start'])) {
                        if (isset($this->partial['stop']) && $this->partial['stop'] === $currentPartial) {
                            break;
                        }
                        $this->partialParams[] = $value;
                        ++$currentPartial;
                        continue;
                    }
                    $matched = false;
                    
                    return false;
                }
                
                // is a var
                if ($this->_partitional_URI[$key]{0} === ':') {
                    $this->params[substr($this->_partitional_URI[$key], 1)] = $value;
                    continue;
                }
                // IS A TRANSLATE
                if ($this->_partitional_URI[$key]{0} === '@') {
                    if ($this->proceedToTranslate($key, $value)) {
                        continue;
                    }
                    
                    return false;
                    break;
                }
                if ($value === $this->_partitional_URI[$key]) {
                    $this->generatedURI .= $value . '|';
                } else {
                    if ($partial_on_fail) {
                        if (!is_array($partial_on_fail)) {
                            $partial_on_fail = $path;
                        }
                        $this->setPartial($partial_on_fail);
                    }
                    
                    $matched = false;
                    
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Traduit une route
     *
     * @param string $key
     * @param string $value
     *
     * @return boolean
     */
    public function proceedToTranslate($key, $value)
    {
        $langRout = $GLOBALS['lang']['router'];
        $lang = (Acidrouter::getCurrentLang() !== '') ? AcidRouter::getCurrentLang() : Acid::get('lang:current');
        
        if (array_key_exists(substr($this->_partitional_URI[$key], 1), $langRout)) {
            if (array_key_exists($lang, $langRout[substr($this->_partitional_URI[$key], 1)])) {
                if ($value === $langRout[substr($this->_partitional_URI[$key], 1)][$lang]['key']) {
                    $this->generatedURI .= $value;
                    $this->params[substr($this->_partitional_URI[$key], 1)] =
                        $langRout[substr($this->_partitional_URI[$key], 1)][$lang]['key'];
                    
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Retourne les paramètres obligatoires
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Retourne les paramètres partiels
     *
     * @return array
     */
    public function getPartials()
    {
        return $this->partialParams;
    }
    
    /**
     * Definit les paramètres obligatoires
     *
     * @param array $new_params
     */
    public function setParams($new_params = null)
    {
        if (isset($new_params)) {
            foreach ($new_params as $key => $value) {
                $this->params[$key] = $value;
            }
        }
    }
    
    /**
     * Definit les paramètres partiels
     *
     * @param mixed   $value
     * @param boolean $clear
     */
    public function setPartial($value, $clear = false)
    {
        if ($clear) {
            $this->cleanPartialParams();
        }
        
        if (($value) && (is_array($value))) {
            foreach ($value as $val) {
                $this->partialParams[] = $val;
            }
        } else {
            $this->partialParams[] = $value;
        }
    }
    
    /**
     * Traitement la route
     *
     * @throws Exception
     * @return boolean
     */
    public function callDispatch()
    {
        $include_url =
            SITE_PATH . AcidRouter::MODULES_PATH . $this->module . AcidRouter::URI_DELIMITER . $this->controller
            . '.php';
        if (file_exists($include_url)) {
            include_once($include_url);
            $className = $this->controller;
            Acid::log('ROUTER', 'Calling ' . $className);
            if (class_exists($className)) {
                $instance = new $className();
                $action = $this->action;
                Acid::log('ROUTER', 'Launching ' . $action);
                if (method_exists($instance, $action)) {
                    $instance->$action();
                    
                    return true;
                } else {
                    throw new Exception('Bad Method call. ' . $action . ' is undefined in ' . $className . ' Class.');
                    
                    return false;
                }
            } else {
                throw new Exception('Bad Controller call.');
                
                return false;
            }
        } else {
            throw new Exception('Bad Module call.');
            
            return false;
        }
    }
    
    /**
     * Supprimes les paramètres associés à la route
     *
     * @param string $name
     */
    public function cleanParam($name = null)
    {
        if (isset($name)) {
            $this->params[$name] = null;
        } else {
            $this->params = [];
        }
    }
    
    /**
     * Supprimes les paramètres partiels associés à la route
     */
    public function cleanPartialParams()
    {
        $this->partialParams = [];
    }
    
    /**
     * Builder
     *
     * @return boolean|Ambigous <string, unknown>
     */
    public function build()
    {
        $final_url = '';
        $langRout = $GLOBALS['lang']['router'];
        if (AcidRouter::getInstance()->getDefaultRoute()->getURI() !== $this->URI) {
            $lang = ((AcidRouter::getCurrentLang() !== '') && (AcidRouter::getCurrentLang() !== null)) ?
                AcidRouter::getCurrentLang() : Acid::get('lang:current');
            foreach ($this->_partitional_URI as $key => $value) {
                if ($value{0} === ':') {
                    if (array_key_exists(substr($value, 1), $this->params)) {
                        $final_url .= $this->params[substr($value, 1)] . AcidRouter::URI_DELIMITER;
                        continue;
                    }
                    
                    return false;
                }
                if ($value{0} === '@') {
                    if (array_key_exists(substr($value, 1), $langRout)) {
                        if (array_key_exists($lang, $langRout[substr($value, 1)])) {
                            $final_url .= $langRout[substr($value, 1)][$lang]['key'] . AcidRouter::URI_DELIMITER;
                            continue;
                        }
                    }
                    
                    return false;
                }
                $final_url .= $value . AcidRouter::URI_DELIMITER;
            }
            foreach ($this->partialParams as $key => $value) {
                $final_url .= $value;
            }
        }
        
        return $final_url;
    }
}