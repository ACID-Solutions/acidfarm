<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\User Module
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Gestion des menus du site
 * @package   Acidfarm\User Module
 */
class Menu extends AcidModule {
    const TBL_NAME = 'menu';
    const TBL_PRIMARY = 'id_menu';

    /**
     * Cache pour les pages liées
     * @var null
     */
    public static $_pages = null;

    /**
     * Cache pour les associations d'ident
     * @var null
     */
    public static $_assoc = null;

    /**
     * Constructeur
     * @param mixed $init_id initialisateur
     */
    public function __construct($init_id=null) {

        $this->vars['id_menu']	=	new AcidVarInt($this->modTrad('id_menu'),true);
        $this->vars['ident']	=	new AcidVarString($this->modTrad('ident'),25,255);

        if ($langs = Acid::get('lang:available')) {
            /*AUTODETECTION DU MULTILINGUE*/
            //commenter cette ligne pour desactiver le multilingue auto
            $have_lang_keys = (count($langs)>1) || Acid::get('lang:use_nav_0');
            //POUR CHAQUE LANGUE
            foreach ($langs as $l) {
                //AUTODETECT
                $ks = !empty($have_lang_keys) ? ('_'.$l) : '';
                $ds = !empty($have_lang_keys) ? (' '.$l) : '';
                $this->vars['url'.$ks] =  new AcidVarString($this->modTrad('url').$ds,50);
                $this->vars['name'.$ks] =  new AcidVarString($this->modTrad('name').$ds,50);
            }
            //CONFIGURATION DU MULTILINGUE DANS LES FORMULAIRES ADMIN
            $this->config['multilingual']['flags']['default']  = !empty($have_lang_keys);
        }

        $this->vars['pos']	=	new AcidVarInt($this->modTrad('pos'),false);
        $this->vars['active']	=	new AcidVarBool($this->modTrad('active'));

        parent::__construct($init_id);
    }

    /**
     * Récupère la page associé au menu
     */
    public function page() {

        if(is_numeric($this->get('ident'))) {
            if (!isset(static::$_pages[$this->get('ident')])) {
                static::$_pages[$this->get('ident')] = new Page($this->get('ident'));
            }

            return static::$_pages[$this->get('ident')];
        }

        return null;
    }

    /**
     * Override de la configuration de l'interface d'administration
     * @see AcidModuleCore::printAdminConfigure()
     * @param string $do
     * @param array $conf
     */
    public function printAdminConfigure($do='default',$conf=array()) {

        $this->config['admin']['list']['order']= array('pos'=>'ASC');

        $this->config['print']['pos']= array('type'=>'quickchange','ajax'=>false,'params'=>array('style'=>'width:30px; text-align:center;'));
        $this->config['print']['active']= array('type'=>'toggle','ajax'=>true);

        if (static::$_assoc===null) {
            $assoc = Page::getAssoc(Page::build()->langKey('title'), null, true, array(Page::build()->langKey('title') => 'ASC'), array(array(Page::build()->langKey('ident','default'), '!=', 'home')));
            $other_assoc = array('index'=>AcidRouter::getName('index'));
            if ($keys = Conf::get('site_keys')) {
                foreach ($keys as $key) {
                    $other_assoc[$key] = AcidRouter::getName($key);
                }
            }

            static::$_assoc = $other_assoc + $assoc;
        }
        $this->vars['ident']->setElts(static::$_assoc);
        $this->vars['ident']->setForm('select');
        $this->config['print']['ident']= array('type'=>'quickchange','ajax'=>false);

        foreach (array_merge($this->langKeyDecline('name'), $this->langKeyDecline('url')) as $lkey) {
            $this->config['print'][$lkey]= array('type'=>'quickchange','ajax'=>false);
        }

        return parent::printAdminConfigure($do,$conf);
    }

    /**
     * @param $key
     * @param null $obj
     * @param bool $ajax
     * @param array $params
     * @return string
     */
    public function printFormQuickChange($key,$obj=null,$ajax=false,$params=array()) {
        $this->printAdminConfigure('quickchange');
        return parent::printFormQuickChange($key,$obj,$ajax,$params);
    }

    /**
     * (non-PHPdoc)
     * @see AcidModuleCore::printAdminAddForm()
     */
    public function printAdminAddForm() {
        $res = AcidDB::query('SELECT MAX(`pos`) as max_pos FROM '.$this->tbl())->fetch(PDO::FETCH_ASSOC);
        $plus = ($res['max_pos']+1);
        $this->initVars(array('pos'=>$plus));

        return parent::printAdminAddForm();
    }

    /**
     * Retourne le lien du menu
     * Si l'ident est un entier alors c'est l'id de la page
     * @return string
     */
    public function link() {

        if ($this->trad('url')) {
            return $this->trad('url');
        }

        if ($this->get('ident')) {
            if ($p = $this->page()) {
                return $p->url();
            }

            return Route::buildUrl($this->get('ident'));
        }
    }

    /**
     * Retourne le titre du menu
     * Si non renseigné c'est celui de la page cible
     * @return string
     */
    public function name() {
        if ($this->trad('name')) {
            return $this->hscTrad('name');
        }

        if ($p = $this->page()) {
            return $p->hscTrad('title');
        }

        return htmlspecialchars(AcidRouter::getName($this->hscTrad('ident')));
    }

}