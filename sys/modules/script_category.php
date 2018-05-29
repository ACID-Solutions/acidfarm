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
 * Gestion des categories de scripts personnalisés
 *
 * @package   Acidfarm\User Module
 */
class ScriptCategory extends AcidModule
{
    const TBL_NAME = 'script_category';
    const TBL_PRIMARY = 'id_script_category';
    public static $_cookie_prefix = 'acid_consent_category_';
    
    /**
     * Constructeur
     *
     * @param mixed $init_id
     */
    public function __construct($init_id = null)
    {
        $this->vars['id_script_category'] = new AcidVarInt(self::modTrad('script_category'), true);
        
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
                $this->vars['name' . $ks] = new AcidVarString(self::modTrad('name') . $ds, 60);
                $this->vars['description' . $ks] = new AcidVarText(self::modTrad('description') . $ds, 60);
            }
            //CONFIGURATION DU MULTILINGUE DANS LES FORMULAIRES ADMIN
            $this->config['multilingual']['flags']['default'] = !empty($have_lang_keys);
        }
        
        $this->vars['pos'] = new AcidVarInt(self::modTrad('position'), true);
        
        $this->vars['use_cookie'] = new AcidVarBool($this->modTrad('use_cookie'), false);
        
        $this->vars['default'] = new AcidVarList(self::modTrad('default'), ['accept','deny'],'deny',false,false);
        
        $this->vars['show'] = new AcidVarBool($this->modTrad('show'), true);
        
        $this->vars['active'] = new AcidVarBool($this->modTrad('active'), true);
        
        parent::__construct($init_id);
        
        /*--- CONFIGURATION ---*/
        $this->config['acl']['default'] = Acid::get('lvl:dev');
    }
    
    /**
     * Override de l'exePost pour le controle des cookies
     *
     * @return array|bool
     */
    public function exePost()
    {
        if ($this->getPostDo() == 'policy') {
            return $this->postPolicy();
        } else {
            return parent::exePost();
        }
    }
    
    public function postPolicy($post = null)
    {
        $post = $post === null ? $_POST : $post;
        $has_consent = false;
        
        //On définit les cookies de consentement des catégories de scripts
        $categories = static::arrayToObjects(
            static::dbList([['active', '=', 1], ['use_cookie', '=', 1]], ['pos' => 'ASC'])
        );
    
        foreach ($categories as $category) {
            //Si on agit pour tous on pré-rempli la valeur
            if (isset($post[Script::$_cookie_prefix.'_set_all'])) {
                $post[$category->cookiename()] = $post[Script::$_cookie_prefix.'_set_all'];
            }
            
            //Si une valeur pour le cookie existe
            if (isset($post[$category->cookiename()])) {
                $category->setcookie($post[$category->cookiename()]);
    
                if ($category->hasConsent()) {
                    $has_consent = true;
                }
            }
        }
        
        //On définit les cookies de consentement des scripts
        $scripts = Script::arrayToObjects(
            Script::dbList([['active', '=', 1], ['optional', '=', 1]], ['pos' => 'ASC'])
        );
        
        foreach ($scripts as $script) {
    
            //Si on agit pour tous on pré-rempli la valeur
            if (isset($post[Script::$_cookie_prefix.'_set_all'])) {
                $post[$script->cookiename()] = $post[Script::$_cookie_prefix.'_set_all'];
            }
    
            //Si une valeur pour le cookie existe
            if (isset($post[$script->cookiename()])) {
                $script->setcookie($post[$script->cookiename()]);
                
                if ($script->hasConsent()) {
                    $has_consent = true;
                }
            }
        }
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
        $this->config['admin']['list']['order'] = ['pos' => 'ASC'];
        $this->config['admin']['list']['keys'] = [
            'id_script_category',
            $this->langKey('name'), $this->langKey('description'),
            'use_cookie', 'default', 'show', 'active', 'pos'
        ];
        $this->config['print']['pos'] =
            ['type' => 'quickchange', 'ajax' => false, 'params' => ['style' => 'width:30px; text-align:center;']];
        
        $this->config['print']['use_cookie'] = ['type' => 'toggle', 'ajax' => true];
        $this->config['print']['active'] = ['type' => 'toggle', 'ajax' => true];
        $this->config['print']['show'] = ['type' => 'toggle', 'ajax' => true];
        
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
     * @return array|int
     */
    public function scripts()
    {
        return Script::arrayToObjects(
            Script::dbList(
                [['active', '=', 1], ['id_script_category', '=', $this->getId()]],
                ['pos' => 'ASC']
            )
        );
    }
    
    /**
     * Retourne la valeur associé au cookie
     *
     * @return bool
     */
    public function value()
    {
        return AcidCookie::getValue($this->cookiename(),$this->get('default'));
    }
    
    
    /**
     * @return string
     */
    public function cookiename()
    {
        return static::$_cookie_prefix . AcidUrl::normalize($this->get('key'));
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
        return $this->value() == 'accept';
    }
}