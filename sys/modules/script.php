<?php

/**
 * AcidFarm - Yet Another Framework
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\User Module
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Gestion des de scripts personnalisés
 *
 * @package   Acidfarm\User Module
 */
class Script extends AcidModule
{
    const TBL_NAME = 'script';
    const TBL_PRIMARY = 'id_script';
    
    private $_category = null;
    
    public static $_cookie_prefix = 'acid_consent_';
    
    /**
     * Constructeur
     *
     * @param mixed $init_id
     */
    public function __construct($init_id = null)
    {
        $this->vars['id_script'] = new AcidVarInt(self::modTrad('id_script'), true);
        $this->vars['id_script_category'] = new AcidVarInt(self::modTrad('id_script_category'), true);
        
        $this->vars['key'] = new AcidVarString(self::modTrad('key'), 30);
        
        if ($langs = Acid::get('lang:available')) {
            /*AUTODETECTION DU MULTILINGUE*/
            //commenter cette ligne pour desactiver le multilingue auto
            $have_lang_keys = (count($langs) > 1) || Acid::get('lang:use_nav_0');
            //POUR CHAQUE LANGUE
            foreach ($langs as $l) {
                //AUTODETECT
                $ks = !empty($have_lang_keys) ? ('_' . $l) : '';
                $ds = !empty($have_lang_keys) ? (' ' . $l) : '';
                //DEFINITION DE LA VARIABLE
                $this->vars['name' . $ks] = new AcidVarString(self::modTrad('name') . $ds, 80);
                $this->vars['description' . $ks] = new AcidVarText(self::modTrad('description') . $ds, 80);
                $this->config['admin']['add']['params']['description' . $ks]['class'] = 'acidtinymce';
                $this->config['admin']['update']['params']['description' . $ks]['class'] = 'acidtinymce';
            }
            //CONFIGURATION DU MULTILINGUE DANS LES FORMULAIRES ADMIN
            $this->config['multilingual']['flags']['default'] = !empty($have_lang_keys);
        }
        
        $this->vars['script'] = new AcidVarText(self::modTrad('script'), 80, 10);
        
        $this->vars['pos'] = new AcidVarInt(self::modTrad('position'), true);
        
        $this->vars['optional'] = new AcidVarBool($this->modTrad('optional'), true);
        
        $this->vars['show'] = new AcidVarBool($this->modTrad('show'), true);
        
        $this->vars['active'] = new AcidVarBool($this->modTrad('active'), true);
        
        parent::__construct($init_id);
        
        /*--- CONFIGURATION ---*/
        $this->config['acl']['default'] = Acid::get('lvl:dev');
    }
    
    /**
     * Override de la configuration de l'interface d'administration
     *
     * @see AcidModuleCore::printAdminConfigure()
     *
     * @param string $do
     * @param array  $conf
     */
    public function printAdminConfigure($do = 'default', $conf = [])
    {
    
        $this->config['admin']['list']['mods'] = ['ScriptCategory'=>['id_script_category']];
        $this->config['admin']['list']['order'] = [ScriptCategory::dbPref('pos') => 'ASC'];
        $this->config['admin']['list']['keys'] = [
            'id_script',
            ScriptCategory::dbPref('name'),
            $this->langKey('name'), $this->langKey('description'),
            'optional', 'show', 'active', 'pos'
        ];
        $this->config['print']['pos'] =
            ['type' => 'quickchange', 'ajax' => false, 'params' => ['style' => 'width:30px; text-align:center;']];
        
        $this->config['print']['active'] = ['type' => 'toggle', 'ajax' => true];
        $this->config['print']['optional'] = ['type' => 'toggle', 'ajax' => true];
        $this->config['print']['show'] = ['type' => 'toggle', 'ajax' => true];
        
        $this->config['admin']['head'][ScriptCategory::dbPref('name')] = $this->getLabel('id_script_category');
        
        $categories = [0 => '-'] + ScriptCategory::getAssoc($this->langKey('name'), null, true, ['pos' => 'asc']);
        $this->vars['id_script_category']->setElts($categories);
        $this->vars['id_script_category']->setForm('select');
        
        return parent::printAdminConfigure($do, $conf);
    }
    
    /**
     * @see AcidModuleCore::checkVals()
     *
     * @param string $tab
     * @param string $do
     *
     * @return array
     */
    protected function checkVals($tab, $do)
    {
        if (isset($tab['key'])) {
            $tab['key'] = AcidUrl::normalize($tab['key']);
        }
        
        return parent::checkVals($tab, $do);
    }
    
    /**
     * (non-PHPdoc)
     * @see AcidModuleCore::printAdminAddForm()
     */
    public function printAdminAddForm()
    {
        $res = AcidDB::query('SELECT MAX(`pos`) as max_pos FROM ' . $this->tbl())->fetch(PDO::FETCH_ASSOC);
        $plus = ($res['max_pos'] + 1);
        $this->initVars(['pos' => $plus]);
        
        return parent::printAdminAddForm();
    }
    
    /**
     * @param string $do
     *
     * @return string
     */
    public function printAdminForm($do)
    {
        Acid::set('tinymce:all', false);
        
        return parent::printAdminForm($do);
    }
    
    /**
     * @return string
     */
    public function cookiename()
    {
        return static::$_cookie_prefix.AcidUrl::normalize($this->get('key'));
    }
    
    /**
     * @return string
     */
    public function category()
    {
        if ($this->_category === null) {
            $this->_category = new ScriptCategory($this->get('id_script_category'));
        }
        
        return $this->_category;
    }
    
    /**
     * @param string $value
     */
    public function setCookie($value = 'accept')
    {
        if ($value === false) {
            AcidCookie::unsetcookie($this->cookiename());
        }
        
        AcidCookie::setcookie($this->cookiename(), $value, time()+(365 * 24 * 60 * 60));
    }
    
    /**
     * Retourne true si on peut utiliser le script
     *
     * @return bool
     */
    public function hasConsent()
    {
        if ($this->category()->get('use_cookie')) {
            if (!$this->category()->hasConsent()) {
                return false;
            }
        }
        
        return AcidCookie::getValue($this->cookiename()) == 'accept';
    }
    
    public static function getAll() {
        return static::arrayToObjects(static::dbList([['active','=',1]],['pos'=>'ASC']));
    }
}