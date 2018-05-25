<?php
/**
 * AcidFarm - Yet Another Framework
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\User Module
 * @version   0.1
 * @since     Version 0.3
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Classe de configuration du site
 *
 * @package   Acidfarm\User Module
 */
class SiteConfig extends AcidModule
{
    const TBL_NAME = 'config';
    const TBL_PRIMARY = 'id_config';
    /**
     * @var array liste des clés de variables "obligatoires" (elles apparaitront dans le formulaire de configuration)
     */
    protected $controlled_keys = [];
    /**
     * @var array liste des clés de variables "libres" (ces variables pourront être employées dans un formulaire dont
     *      le config_do vaut remote_update)
     */
    protected $controlled_remote_keys = [];
    /**
     * @var array liste des clés à ne pas considérer comme des variables
     */
    protected $excluded_keys = [];
    /**
     * @var bool si vrai, alors on permet à l'utilisateur de créer ses propres variables
     */
    protected $free_mode;
    /**
     * @var array les instances
     */
    protected $instance = [];
    
    /**
     * Constructeur
     *
     * @param mixed   $init_id
     * @param boolean $free_mode
     *
     * @return boolean
     */
    public function __construct($init_id = null, $free_mode = false)
    {
        $this->vars['id_config'] = new AcidVarInt($this->modTrad('id_config'), true);
        $this->vars['name'] = new AcidVarString($this->modTrad('name'));
        $this->vars['value'] = new AcidVarString($this->modTrad('value'));
        
        $success = parent::__construct($init_id);
        
        $home_content = [];
        foreach (Page::build()->langKeyDecline('content') as $k) {
            $home_content[] = 'home_' . $k;
        }
        
        $policy_content = [];
        foreach (array_merge(Page::build()->langKeyDecline('title'), Page::build()->langKeyDecline('content')) as $k) {
            $policy_content[] = 'policy_' . $k;
        }
        
        $this->free_mode = $free_mode;
        
        $this->addControl([
            'email', 'contact', 'address', 'cp', 'city', 'coords', 'zoom', 'gmapapikey', 'phone', 'fax', 'website'
        ]);
        $this->addRemoteControl($home_content);
        $this->addRemoteControl($policy_content);
        
        $this->addExcluded([self::preKey('do'), 'submit', 'next_page']);
        
        $this->setConfig('assoc:index', 'name');
        $this->setConfig('assoc:value', 'id_config');
        
        return $success;
    }
    
    /**
     * Ajoute des clés à la liste des variables associées à l'objet
     *
     * @param array $tab
     */
    public function addControl($tab)
    {
        foreach ($tab as $key) {
            if (!in_array($key, $this->controlled_keys)) {
                $this->controlled_keys[] = $key;
            }
        }
    }
    
    /**
     * Ajoute des clés à la liste des variables libres associées à l'objet
     *
     * @param array $tab
     */
    public function addRemoteControl($tab)
    {
        foreach ($tab as $key) {
            if (!in_array($key, $this->controlled_remote_keys)) {
                $this->controlled_remote_keys[] = $key;
            }
        }
    }
    
    /**
     * Ajoute des clés d'exclusion associées à l'objet
     *
     * @param array $tab
     */
    public function addExcluded($tab)
    {
        foreach ($tab as $key) {
            if (!in_array($key, $this->excluded_keys)) {
                $this->excluded_keys[] = $key;
            }
        }
    }
    
    /**
     * Récupère la liste des variables associées à l'objet
     *
     * @return array
     */
    public function controlledKeys()
    {
        return $this->controlled_keys;
    }
    
    /**
     * Récupère la liste des variables libres associées à l'objet
     *
     * @return array
     */
    public function controlledRemoteKeys()
    {
        return $this->controlled_remote_keys;
    }
    
    /**
     * Récupère la liste des variables et variables libres associées à l'objet
     *
     * @return array
     */
    public function allControlledKeys()
    {
        return array_merge($this->controlledRemoteKeys(), $this->controlledKeys());
    }
    
    /**
     * Récupère la liste des exclusions associées à l'objet
     *
     * @return array
     */
    public function excludedKeys()
    {
        return $this->excluded_keys;
    }
    
    /**
     * retourne l'instance
     *
     * @return array
     */
    public function getInstance()
    {
        return $this->instance = $this->getTab();
    }
    
    /**
     * Assigne l'instance dans une variable globale si elle n'est pas déjà créée puis retourne cette dernière
     *
     * @return array
     */
    public static function getCurrent()
    {
        if (!isset($GLOBALS['site_config'])) {
            $site_config = new SiteConfig();
            $site_config->getInstance();
            $GLOBALS['site_config'] = $site_config;
        }
        
        return $GLOBALS['site_config'];
    }
    
    /**
     * Retourne la valeur d'une variable, null si elle n'existe pas
     *
     * @param string $name nom de la variable
     *
     * @return mixed
     */
    public function getConf($name)
    {
        return isset($this->instance[$name]) ? $this->instance[$name] : null;
    }
    
    /**
     * Retourne la valeur d'une variable après lui avoir affecté htmlspecialchars
     *
     * @param string $name nom d ela variable
     *
     * @return string
     */
    public function hscConf($name)
    {
        return htmlspecialchars($this->getConf($name));
    }
    
    /**
     * Retourne un tableau contenant tous les variables de configuration
     *
     * @return array
     */
    public function getTab()
    {
        $res = $this->dbList();
        
        $tab = [];
        foreach ($res as $elt) {
            $tab[$elt['name']] = $elt['value'];
        }
        
        return $tab;
    }
    
    /**
     * (non-PHPdoc)
     * @see AcidModuleCore::debug()
     */
    public function debug()
    {
        $tab = $this->getTab();
        
        $vars = [];
        foreach ($tab as $k => $v) {
            $field = new AcidVarText();
            $field->setVal($v);
            $vars[$k] = $field;
        }
        
        return Acid::tpl('core/debug.tpl', ['vars' => $vars], $this);
    }
    
    /**
     * (non-PHPdoc)
     * @param array $conf
     *
     * @see AcidModuleCore::printAdminInterface()
     */
    public function printAdminInterface($conf = [])
    {
        return $this->printAdminBody($this->printAdminUpdateForm(), null);
    }
    
    /**
     * (non-PHPdoc)
     * @param string $do
     *
     * @see AcidModuleCore::printAdminForm()
     */
    public function printAdminForm($do)
    {
        $tab = $this->getTab();
        $controlled = $this->controlledKeys();
        
        $add = '';
        $js = '';
        $rem_form = '';
        if ($this->free_mode) {
            $assoc = $this->getAssoc();
            $add = '<hr /><h4>' . Acid::trad('config_add_conf') . '</h4>' . parent::printAdminForm('add');
            foreach ($assoc as $name => $id) {
                $rem_form .= '<form method="post" action="" id="remove_form_' . $id . '" class="remove_form">' .
                             '<input type="hidden" name="' . self::preKey('do') . '" value="del" />' .
                             '<input type="hidden" name="module_do" value="' . self::getClass() . '" />' .
                             '<input type="hidden" name="id_config" value="' . $id . '" />' .
                             '<input type="submit" value="' . Acid::trad('config_delete_conf',
                        ['__NAME__' => htmlspecialchars($name)]) . '" />' .
                             '</form>';
            }
            
            $js = Lib::getJsCaller('$(".remove_form").hide();');
        }
        
        $form = new AcidForm('post', '');
        $form->setFormParams(['class' => $this::TBL_NAME . ' ' . $this->preKey('do') . ' admin_form']);
        
        $form->addhidden('', self::preKey('do'), 'update');
        $form->addhidden('', 'module_do', self::getClass());
        
        $form->tableStart();
        
        if ($this->free_mode) {
            foreach ($tab as $name => $value) {
                $jsr = 'if (confirm(\'' . Acid::trad('config_ask_delete_conf',
                        ['__NAME__' => htmlspecialchars($name), "\\" => "\\\\", "'" => "\\'"])
                       . '\')) { $(\'#remove_form_' . $assoc[$name] . '\').submit(); }';
                $form->addText($this->modTrad($name), $name, $value, null, null, [], '',
                    '<a href="#" onclick="' . $jsr . ' return false;" style="margin:0px 3px;"  >X</a>');
            }
        } else {
            foreach ($controlled as $key) {
                $value = isset($tab[$key]) ? $tab[$key] : null;
                $form->addText($this->modTrad($key), $key, $value);
            }
        }
        
        $form->tableStop();
        
        $form->addsubmit('', Acid::trad('config_btn_validate'));
        
        return $form->html() . $rem_form . $add . $js;
    }
    
    /**
     * (non-PHPdoc)
     * @see AcidModuleCore::exePost()
     */
    public function exePost()
    {
        $do = $_POST[self::preKey('do')];
        $acl = $this->getACL($do);
        
        if (User::curLevel($acl)) {
            switch ($do) {
                case 'update' :
                    return $this->postUpdate($_POST);
                    break;
                
                case 'add' :
                    if ($this->free_mode) {
                        $this->postAdd($_POST);
                    }
                    break;
                
                case 'remote_update' :
                    return $this->postRemoteUpdate($_POST);
                    break;
                
                default :
                    return parent::exePost();
                    break;
            }
        }
    }
    
    /**
     * Processus de mise à jour des variables libres
     *
     * @param array $vals
     */
    public function postRemoteUpdate($vals)
    {
        $assoc = self::getAssoc();
        $treat = [];
        
        $controlled_key = $this->allControlledKeys();
        $excluded_key = $this->excludedKeys();
        
        foreach ($vals as $key => $val) {
            if (!in_array($key, $excluded_key)) {
                if (($this->free_mode) || (in_array($key, $controlled_key))) {
                    if (isset($assoc[$key])) {
                        AcidDb::exec("UPDATE " . $this->tbl() . " SET `value`='" . addslashes($val)
                                     . "' WHERE `id_config`='" . $assoc[$key] . "'");
                    } else {
                        if ($key) {
                            $new = new SiteConfig();
                            $new->initVars(['name' => $key, 'value' => $val]);
                            $new->dbAdd();
                            $assoc[$key] = $new->getId();
                        }
                    }
                }
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @param array $vals les données à traiter
     * @param mixed $dialog
     *
     * @see AcidModuleCore::postUpdate()
     */
    public function postUpdate($vals, $dialog = null)
    {
        $assoc = self::getAssoc();
        $treat = [];
        
        $controlled_key = $this->controlledKeys();
        $excluded_key = $this->excludedKeys();
        
        foreach ($vals as $key => $val) {
            if (!in_array($key, $excluded_key)) {
                if (($this->free_mode) || (in_array($key, $controlled_key))) {
                    $treat[$key] = $key;
                    
                    if (isset($assoc[$key])) {
                        AcidDb::exec("UPDATE " . $this->tbl() . " SET `value`='" . addslashes($val)
                                     . "' WHERE `id_config`='" . $assoc[$key] . "'");
                    } else {
                        if ($key) {
                            $new = new SiteConfig();
                            $new->initVars(['name' => $key, 'value' => $val]);
                            $new->dbAdd();
                            $assoc[$key] = $new->getId();
                        }
                    }
                }
            }
        }
        
        foreach ($assoc as $key => $id) {
            if (!isset($treat[$key])) {
                if (($this->free_mode) || in_array($key, $controlled_key)) {
                    AcidDb::exec("DELETE FROM " . $this->tbl() . " WHERE `id_config`='" . $id . "'");
                }
            }
        }
    }
    
    /**
     * Formulaire d'administration du contenu de la home page
     *
     * @return string
     */
    public static function printRemoteHomeForm()
    {
        $form = new AcidForm('post', '');
        $form->tableStart();
        $form->addHidden('', Acid::mod('SiteConfig')->preKey('do'), 'remote_update');
        $form->addHidden('', 'module_do', Acid::mod('SiteConfig')->getClass());
        
        $lang_keys = Page::build()->langKeyDecline('content');
        if ($langs = Acid::get('lang:available')) {
            $have_lang_keys = (count($langs) > 1) || Acid::get('lang:use_nav_0');
            foreach ($lang_keys as $k) {
                $class = '';
                if ($have_lang_keys) {
                    $l = explode('_', $k);
                    $l = $l[(count($l) - 1)];
                    $class = 'lang ' . $l;
                }
                
                $form->addTextarea(Page::build()->getLabel($k), 'home_' . $k, self::getCurrent()->getConf('home_' . $k),
                    120, 20, ['class' => $class], '', '', ['class' => $class]);
            }
        }
        
        $form->addSubmit('', Acid::trad('config_btn_validate'));
        $form->tableStop();
        
        $flags = $have_lang_keys ? Page::build()->printAdminFlags('remote_update', $lang_keys) : '';
        
        return $flags . $form->html();
    }
    
    /**
     * Formulaire d'administration sur la politique sur les données
     *
     * @return string
     */
    public static function printRemotePolicyForm()
    {
        $form = new AcidForm('post', '');
        $form->tableStart();
        $form->addHidden('', Acid::mod('SiteConfig')->preKey('do'), 'remote_update');
        $form->addHidden('', 'module_do', Acid::mod('SiteConfig')->getClass());
        
        $lang_keys = array_merge(Page::build()->langKeyDecline('title'), Page::build()->langKeyDecline('content'));
        if ($langs = Acid::get('lang:available')) {
            $have_lang_keys = (count($langs) > 1) || Acid::get('lang:use_nav_0');
            foreach ($lang_keys as $k) {
                $class = '';
                if ($have_lang_keys) {
                    $l = explode('_', $k);
                    $l = $l[(count($l) - 1)];
                    $class = 'lang ' . $l;
                }
                
                if (strpos($k, 'content') !== 0) {
                    $form->addText(
                        Page::build()->getLabel($k), 'policy_' . $k,
                        self::getCurrent()->getConf('policy_' . $k),
                        120, 255, ['class' => $class], '', '', ['class' => $class]);
                } else {
                    $form->addTextarea(
                        Page::build()->getLabel($k), 'policy_' . $k,
                        self::getCurrent()->getConf('policy_' . $k),
                        120, 20, ['class' => $class], '', '', ['class' => $class]);
                }
            }
        }
        
        $form->addSubmit('', Acid::trad('config_btn_validate'));
        $form->tableStop();
        
        $flags = $have_lang_keys ? Page::build()->printAdminFlags('remote_update', $lang_keys) : '';
        
        return $flags . $form->html();
    }
    
    /**
     * Exemple de formulaire de mise à jour de variables libres
     */
    public function printRemoteForm()
    {
        /*
        $form = new AcidForm('post','');
        $form->tableStart();
        $form->addHidden('',Acid::mod('SiteConfig')->preKey('do'),'remote_update');
        $form->addHidden('','module_do',Acid::mod('SiteConfig')->getClass());
        $form->addTextarea('My Remote ','remote_key',$GLOBALS['site_config']->getConf('remote_key'),40,5);
        $form->tableStop();
        $form->addSubmit('',Acid::trad('config_btn_validate'));
        return $form->html();
        */
    }
}