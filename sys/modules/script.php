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
    
    /**
     * Constructeur
     *
     * @param mixed $init_id
     */
    public function __construct($init_id = null)
    {
        $this->vars['id_script'] = new AcidVarInt(self::modTrad('id_script'), true);
        
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
        
        $this->vars['script'] = new AcidVarText(self::modTrad('script'), 80,10);
        
        $this->vars['pos'] = new AcidVarInt(self::modTrad('position'), true);
        
        $this->vars['optionnal'] = new AcidVarBool($this->modTrad('optionnal'), true);
        
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
        $this->config['admin']['list']['order'] = ['pos' => 'ASC'];
        $this->config['admin']['list']['keys'] = [
            'id_script_category',
            $this->langKey('name'), $this->langKey('description'),
            'optionnal', 'show', 'active', 'pos'
        ];
        $this->config['print']['pos'] =
            ['type' => 'quickchange', 'ajax' => false, 'params' => ['style' => 'width:30px; text-align:center;']];
        
        $this->config['print']['active'] = ['type' => 'toggle', 'ajax' => true];
        $this->config['print']['optionnal'] = ['type' => 'toggle', 'ajax' => true];
        $this->config['print']['show'] = ['type' => 'toggle', 'ajax' => true];

        return parent::printAdminConfigure($do, $conf);
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
}