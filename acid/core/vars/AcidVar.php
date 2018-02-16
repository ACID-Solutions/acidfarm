<?php

/**
 * AcidFarm - Yet Another Framework
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm/Vars
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Classe modèle AcidVar
 *
 * @package   Acidfarm/Vars
 */
class AcidVar
{
    /**
     * @var mixed valeur
     */
    private $_val = null;
    /**
     * @var mixed valeur par défaut
     */
    private $_def = null;
    /**
     * @var string expression régulière de validation
     */
    private $_regex = null;
    /**
     * @var string étiquette
     */
    private $_label = '';
    /**
     * @var boolean is_nullable
     */
    private $_is_nullable = false;
    /**
     * @var array paramêtres SQL
     */
    protected $sql = [];
    /**
     * @var array paramêtre formulaire
     */
    protected $form = ['print' => true];
    /**
     * @var array configuration
     */
    protected $config = [];
    /**
     * @var array éléments associés
     */
    protected $elts = [];
    
    /**
     *   Constructeur AcidVar
     *
     * @param string $label     Etiquette de la variable.
     * @param mixed  $def       Valeur par défaut.
     * @param string $regex
     * @param bool   $force_def Si true initialise la valeur à la valeur par défaut.
     * @param bool   $nullable
     */
    public function __construct($label, $def, $regex, $force_def = false, $nullable = false)
    {
        $this->setNullable($nullable);
        
        $this->setLabel($label);
        if ($regex !== null)
            $this->_regex = (string) $regex;
        
        if ($this->validEntry($def) || $force_def) {
            $this->_val = $this->_def = $def;
        }
        //else trigger_error('Acid : Unvalid default "'.$def.'" for regex "'.$regex.'" of '. get_class($this).'',E_USER_WARNING);
    }
    
    /**
     * Retourne true si la valeur est considérée vide pour ce type de variable
     * @param $val
     *
     * @return bool
     */
    public static function valIsEmpty($val)
    {
        return !$val;
    }
    
    /**
     * Force en Majuscule les valeurs en entrée
     *
     * @param string $val
     * @param string $encode
     *
     * @return string
     */
    public static function upper($val, $encode = 'UTF-8')
    {
        return mb_strtoupper($val, $encode);
    }
    
    /**
     * Force en Minuscule les valeurs en entrée
     *
     * @param string $val
     * @param string $encode
     *
     * @return string
     */
    public static function lower($val, $encode = 'UTF-8')
    {
        return mb_strtolower($val, $encode);
    }
    
    /**
     * Traite les valeurs en entrée selon la configuration de l'objet
     *
     * @param mixed $val
     * @param mixed $way
     *
     * @return mixed
     */
    public function treatVal($val, $way = null)
    {
        if (!empty($this->config['force_uppercase'])) {
            $val = self::upper($val);
        }
        
        if (!empty($this->config['force_lowercase'])) {
            $val = self::lower($val);
        }
        
        if (!empty($this->config['force_function']) && is_array($this->config['force_function'])
            && (count($this->config['force_function']) > 1)) {
            $func = $this->config['force_function'][0];
            $args = $this->config['force_function'][1];
            if ($args) {
                foreach ($args as $k => $v) {
                    if ($v == '__VAL__') {
                        $args[$k] = $val;
                    }
                }
            }
            $val = call_user_func_array($func, $args);
        }
        
        $key_way = 'force_function_' . $way;
        if (!empty($this->config[$key_way]) && is_array($this->config[$key_way])
            && (count($this->config[$key_way]) > 1)) {
            $func = $this->config[$key_way][0];
            $args = $this->config[$key_way][1];
            if ($args) {
                foreach ($args as $k => $v) {
                    if ($v == '__VAL__') {
                        $args[$k] = $val;
                    }
                }
            }
            $val = call_user_func_array($func, $args);
        }
        
        return $val;
    }
    
    /**
     *   Retourne la valeur de la variable.
     * return mixed
     */
    public function getVal()
    {
        $val = self::treatVal($this->_val, 'output');
        
        return $val;
    }
    
    /**
     *  Assigne une valeur à la variable.
     *
     * @param mixed $val
     *
     * @return bool
     */
    public function setVal($val)
    {
        if ($this->validEntry($val)) {
            $val = self::treatVal($val, 'input');
            $this->_val = $val;
            
            return true;
        } else return false;
    }
    
    /**
     *  Attribue sa valeur par défaut à la variable.
     */
    public function setDef()
    {
        $def = self::treatVal($this->_def, 'input');
        $this->_val = $def;
    }
    
    /**
     *  Retourne la valeur par défaut de la variable.
     *
     * @return mixed
     */
    public function getDef()
    {
        $def = self::treatVal($this->_def, 'output');
        
        return $def;
    }
    
    /**
     *  Assigne une étiquette à la variable.
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        if (is_string($label)) {
            $this->_label = $label;
        } else trigger_error('Acid : Label undefined', E_USER_NOTICE);
    }
    
    /**
     *  Retourne l'étiquette de la variable.
     *
     * @return  string
     */
    public function getLabel()
    {
        return $this->_label;
    }
    
    /**
     *  Assigne le paramêtre uppercase de la variable
     *
     * @param bool $value
     */
    public function setuppercase($value = true)
    {
        $this->config['force_uppercase'] = $value;
    }
    
    /**
     *  Assigne le paramêtre uppercase de la variable
     *
     * @param bool $value
     */
    public function setlowercase($value = true)
    {
        $this->config['force_lowercase'] = $value;
    }
    
    /**
     *  Assigne le paramêtre fonction de la variable
     *
     * @param bool $value
     */
    public function setfunction($value = false)
    {
        $this->config['force_function'] = $value;
    }
    
    /**
     * Attribue une nouvelle configuration à la variable.
     *
     * @param array   $config
     * @param boolean $erase_before
     */
    public function setConfig($config, $erase_before = false)
    {
        if (is_array($config)) {
            if ($erase_before) {
                $this->config = $config;
            } else {
                foreach ($config as $key => $val) {
                    $this->config[$key] = $val;
                }
            }
        }
    }
    
    /**
     * Attribue de nouveaux éléments à la variable.
     *
     * @param array   $elts
     * @param boolean $erase_before
     */
    public function setElts($elts, $erase_before = true)
    {
        if (is_array($elts)) {
            if ($erase_before) {
                $this->elts = $elts;
            } else {
                foreach ($elts as $key => $val) {
                    $this->elts[$key] = $val;
                }
            }
        }
    }
    
    /**
     *  Retourne les éléments de la variable.
     */
    public function getElts()
    {
        return $this->elts;
    }
    
    /**
     *  Teste l'éligibilité d'une valeur par la variable.
     *
     * @param mixed $val
     *
     * @return bool
     */
    public function validEntry($val)
    {
        return $this->_regex === null
            ? true
            : (
            is_array($val)
                ? false
                : (
            $val === null
                ? $this->_is_nullable
                : (
            preg_match($this->_regex, $val)
            )));
    }
    
    /**
     *  Définit si la variable est à l'état NULL ou non.
     *
     * @param bool $bool
     */
    public function setNullable($bool)
    {
        $this->_is_nullable = (bool) $bool;
    }
    
    /**
     *  retourne si la variable est nullable.
     *
     * @param bool $bool
     */
    public function isNullable()
    {
        return $this->_is_nullable;
    }
    
    /**
     * Retourne le paramètre de "configuration Formulaire" de la variable qui est renseigné en entrée.
     *
     * @param string paramêtre à traiter
     *
     * @return mixed
     */
    public function getFormValOf($key)
    {
        if (isset($this->form[$key])) {
            return $this->form[$key];
        } else trigger_error('Acid : Undefined form val "' . $key . '" for ' . get_class($this) . '::getFormValOf()',
            E_USER_WARNING);
    }
    
    /**
     * Retourne le paramètre de "configuration SQL" de la variable qui est renseigné en entrée
     * S'il n'est pas défini, renvoie false
     *
     * @param string paramêtre à traiter
     *
     * @return bool | mixed
     */
    public function getSqlValOf($key)
    {
        if (isset($this->sql[$key])) {
            return $this->sql[$key];
        } else return false;
    }
    
    /**
     *  Rajoute la variable au formulaire en entrée.
     *
     * @param object $form       AcidForm
     * @param string $key        Nom du paramétre.
     * @param bool   $print      si false, utilise la valeur par défaut
     * @param array  $params     attributs
     * @param string $start      préfixe
     * @param string $stop       suffixe
     * @param array  $body_attrs attributs à appliquer au cadre
     */
    public function getForm(&$form, $key, $print = true, $params = [], $start = '', $stop = '', $body_attrs = [])
    {
        if (!$form) {
            $form = new AcidForm('', '');
        }
        
        if (isset($this->form['override_start'])) {
            $start = $this->form['override_start'];
        }
        
        if (isset($this->form['override_stop'])) {
            $stop = $this->form['override_stop'];
        }
        
        switch ($this->form['type']) {
            case 'show' :
                $stop = $stop . '<label class="show_field">' . htmlspecialchars($this->getVal()) . '</label>';
                $form->addHidden($this->getLabel(), $key, $this->getVal(), $params, $start, $stop, $body_attrs);
                break;
            
            case 'hidden' :
                $form->addHidden('', $key, $this->getVal(), $params, $start, $stop, $body_attrs);
                break;
            
            case 'text' :
                $form->addText($this->getLabel(), $key, ($print ? $this->getVal() : ''), $this->form['size'],
                    $this->form['maxlength'], $params, $start, $stop, $body_attrs);
                break;
            
            case 'password' :
                $form->addPassword($this->getLabel(), $key, ($print ? $this->getVal() : $this->getDef()),
                    $this->form['size'], $this->form['maxlength'], $params, $start, $stop, $body_attrs);
                break;
            
            case 'textarea' :
                $form->addTextarea($this->getLabel(), $key, ($print ? $this->getVal() : ''), $this->form['cols'],
                    $this->form['rows'], $params, $start, $stop, $body_attrs);
                break;
            
            case 'file' :
                $form->addFile($this->getLabel(), $key, $this->config['max_file_size'], $params, $start, $stop,
                    $body_attrs);
                break;
            
            case 'select' :
                $form->addSelect($this->getLabel(), $key, ($print ? $this->getVal() : $this->getDef()), $this->elts,
                    $this->form['size'], $this->form['multiple'], $params, $start, $stop, $body_attrs);
                break;
            
            case 'radio' :
                $form->addRadio($this->getLabel(), $key, ($print ? $this->getVal() : $this->getDef()), $this->elts,
                    $params, $start, $stop, $body_attrs);
                break;
            
            case 'switch' :
                $body_attrs['class'] =
                    trim((!isset($body_attrs['class']) ? '' : ($body_attrs['class'])) . ' radioswitch');
                $form->addRadio($this->getLabel(), $key, ($print ? $this->getVal() : $this->getDef()), $this->elts,
                    $params, $start, $stop, $body_attrs);
                break;
            
            case 'checkbox':
                $form->addCheckbox($this->getLabel(), $key, ($print ? $this->getVal() : $this->getDef()),
                    $this->form['text'], $this->form['checked'], $params, $start, $stop);
                break;
            
            case 'free' :
                $form->addFreeText($this->getLabel(), $this->form['free_value'], [], $body_attrs, $key);
                break;
            
            case 'info' :
                return false;
                break;
            
            default :
                return false;
                break;
        }
        
        return $form->getComponent($key, 'fullhtml');
    }
    
    /**
     * Change le type de formulaire associé à la variable
     *
     * @param string $type   (hidden,text,password,textarea,file,select,radio,checkbox,free,...)
     * @param array  $config configuration
     */
    public function setForm($type, $config = [])
    {
        if (isset($config['override_start'])) {
            $this->form['override_start'] = $config['override_start'];
        }
        
        if (isset($config['override_stop'])) {
            $this->form['override_stop'] = $config['override_stop'];
        }
        
        switch ($type) {
            case 'show' :
                $this->form['type'] = 'show';
                $this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
                break;
            
            case 'hidden' :
                $this->form['type'] = 'hidden';
                $this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
                break;
            
            case 'text' :
                $this->form['type'] = 'text';
                $this->form['size'] = isset($config['size']) ? $config['size'] : 20;
                $this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
                break;
            
            case 'password' :
                $this->form['type'] = 'password';
                $this->form['size'] = isset($config['size']) ? $config['size'] : 50;
                $this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
                break;
            
            case 'textarea' :
                $this->form['type'] = 'textarea';
                $this->form['cols'] = isset($config['cols']) ? $config['cols'] : 60;
                $this->form['rows'] = isset($config['rows']) ? $config['rows'] : 20;
                break;
            
            case 'file' :
                $this->form['type'] = 'file';
                $this->form['max_file_size'] = isset($config['max_file_size']) ? $config['max_file_size'] : null;
                break;
            
            case 'select' :
                $this->form['type'] = 'select';
                $this->form['size'] = isset($config['size']) ? $config['size'] : 1;
                $this->form['multiple'] = isset($config['multiple']) ? $config['multiple'] : false;
                break;
            
            case 'radio' :
                $this->form['type'] = 'radio';
                break;
            
            case 'switch' :
                $this->form['type'] = 'switch';
                break;
            
            case 'checkbox':
                $this->form['type'] = 'checkbox';
                $this->form['checked'] = isset($config['checked']) ? $config['checked'] : false;
                $this->form['text'] = isset($config['text']) ? $config['text'] : '';
                break;
            
            case 'free' :
                $this->form['type'] = 'free';
                $this->form['free_value'] = isset($config['free_value']) ? $config['free_value'] : '';
                break;
            
            case 'info' :
                $this->form['type'] = 'info';
                break;
        }
    }
}