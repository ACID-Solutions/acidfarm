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
 * Variante Image d'AcidVar
 *
 * @package   Acidfarm/Vars
 */
class AcidVarImage extends AcidVarFile
{
    /**
     * Constructeur AcidVarImage
     *
     * @param string $label  étiquette
     * @param string $folder chemin vers le repertoire de stockage du fichier
     * @param array  $config configuration
     * @param string $name   nom du fichier (variables magiques : __NAME__, __ID__ )
     */
    public function __construct($label = 'AcidVarImage', $folder = null, $config = [], $name = '')
    {
        $this->config['format'] = [];
        $this->config['format']['src'] = ['size' => [0, 0, false], 'effect' => [], 'suffix' => ''];
        
        if (isset($config['format']) && is_array($config['format'])) {
            foreach ($config['format'] as $key => $val) {
                $this->config['format'][$key] = $val;
            }
            unset($config['format']);
        }
        
        if ($this->config['ext'] === null) {
            $this->config['ext'] = Acid::get('ext:varimage');
        }
        
        parent::__construct($label, $folder, $config, $name);
    }
    
    /**
     * Retourne le chemin d'accès au fichier.
     *
     * @param string $format Dérivé du fichier cible. Valeur par Défaut : NULL
     *
     * @return string
     */
    public function getPath($format = 'src')
    {
        return SITE_PATH . $this->dir_path . pathinfo($this->getUrl($format), PATHINFO_BASENAME);
    }
    
    /**
     * Retourne le chemin d'accès au fichier.
     *
     * @param string $format Dérivé du fichier cible. Valeur par Défaut : NULL
     *
     * @return string
     */
    public function getValPath($format = 'src')
    {
        $val = SITE_PATH . $this->getVal();
        if ($suffix = $this->getSuffix($format)) {
            return self::applySuffix($val, $suffix);
        }
        
        return $val;
    }
    
    /**
     * Retourne le nom de fichier en entrée après lui avoir appliqué le suffixe
     *
     * @param string $str    le fichier
     * @param string $suffix le suffixe
     *
     * @return string
     */
    public static function applySuffix($str, $suffix)
    {
        $ext = AcidFs::getExtension($str);
        $str = substr($str, 0, -strlen($ext) - 1) . $suffix . '.' . $ext;
        
        return $str;
    }
    
    /*
    public function getUrl($format='src') {
        $format = isset($this->config['format'][$format]) ? $format : 'src';

        $suffix =  $this->getSuffix($format);
        $url = $this->getVal();

        if ($suffix) {
            //$ext = AcidFs::getExtension($url);
            //$url = substr($url,0,-strlen($ext)-1) . $suffix . '.' . $ext;
            $url = self::applySuffix($url,$suffix);
        }

        return $GLOBALS['acid']['url']['folder'].$url;
    }
    */
    
    /**
     * Retourne l'url d'une image du module ou de son derivé.
     *
     * @param string $format Derivé de l'image. Valeur par défaut : NULL
     */
    public function getUrl($format = 'src')
    {
        $format = isset($this->config['format'][$format]) ? $format : 'src';
        
        $suffix = $this->getSuffix($format);
        $url = $this->getVal();
        
        if ($suffix) {
            //$ext = AcidFs::getExtension($url);
            //$url = substr($url,0,-strlen($ext)-1) . $suffix . '.' . $ext;
            $url = self::applySuffix($url, $suffix);
        }
        
        if (empty($this->config['get_url_func'])) {
            return Acid::get('url:folder') . $url;
        } else {
            if (is_array($this->config['get_url_func'])) {
                $name = $this->config['get_url_func'][0];
                $args = $this->config['get_url_func'][1];
            } else {
                $name = $this->config['get_url_func'];
            }
            
            if (!empty($args)) {
                foreach ($args as $k => $v) {
                    if ($args[$k] === '__OBJ__') {
                        $args[$k] = $this;
                    } elseif ($args[$k] === '__VAL__') {
                        $args[$k] = $url;
                    } elseif ($args[$k] === '__ABSVAL__') {
                        $args[$k] = $this->getVal();
                    } elseif ($args[$k] === '__FORMAT__') {
                        $args[$k] = $format;
                    }
                }
            }
            
            return ($name == 'value') ? $url : call_user_func_array($name, $args);
        }
    }
    
    /**
     * Retourne le suffixe à intégrer aux images en fonction du format en entrée
     *
     * @param string $format Derivé de l'image. Valeur par défaut : NULL
     */
    public function getSuffix($format = 'src')
    {
        $suffix = null;
        
        if (isset($this->config['format'][$format]['suffix'])) {
            $suffix = $this->config['format'][$format]['suffix'];
        }
        
        if ($suffix === null) {
            $suffix = '_' . $format;
        }
        
        return $suffix;
    }
    
    /**
     * Génère un dérivé $format de l'image du module renseignée par $img.
     *
     * @param string $format
     */
    public function imgResize($format)
    {
        global $acid;
        
        Acid::log('debug', 'Resizing ' . $this->getFileName() . ' in ' . $format . ' format');
    
        if (isset($this->config['format'][$format])) {
            list($max_img_w, $max_img_h, $crop) = $this->config['format'][$format]['size'];
            
            $img_big_path = $this->getPath();
            $img_small_path = $this->getPath($format);
     
            if (file_exists($img_big_path)) {
                if (($max_img_w) && ($max_img_h)) {
                    if ($crop) {
                        list($img_big_w, $img_big_h, $img_big_type) = getimagesize($img_big_path);
                        list($img_big_w, $img_big_h, $src_x, $src_y) = AcidFs::getImgSrcSizeCroped(
                            $img_big_w,
                            $img_big_h,
                            $max_img_w,
                            $max_img_h,
                            $crop
                        );
                        $img_small_w = $max_img_w;
                        $img_small_h = $max_img_h;
                       
                    } else {
                        list($img_big_w, $img_big_h, $img_big_type) = getimagesize($img_big_path);
                        list($img_small_w, $img_small_h) =
                            AcidFs::getImgSmallerSize($img_big_w, $img_big_h, $max_img_w, $max_img_h);
                        $src_x = $src_y = 0;
                    }
                    
                    AcidFs::imgResize($img_big_path, $img_small_path, $img_small_w, $img_small_h, $img_big_w,
                        $img_big_h, $img_big_type, $src_x, $src_y);
                    
                    chmod($img_small_path, $acid['files']['file_mode']);
                } else {
                    if ($img_big_path != $img_small_path) {
                        copy($img_big_path, $img_small_path);
                    }
                }
            }
          
        }
    }
    
    /**
     * Supprime le fichier associé au module.
     *
     * @return bool
     */
    public function fsRemove()
    {
        $tab_format = $this->config['format'];
        unset($tab_format['src']);
        
        $success = true;
        
        foreach ($tab_format as $format => $img) {
            if (is_file($path = $this->getValPath($format))) {
                if (!unlink($path)) {
                    $success = false;
                    Acid::log('error', 'AcidImage::fsRemove can\'t delete ' . $path);
                }
            }
        }
        
        if (is_file($path = $this->getValPath())) {
            if (!unlink($path)) {
                $success = false;
                Acid::log('error', 'AcidImage::fsRemove can\'t delete ' . $path);
            } else {
                $this->setVal('');
            }
        }
        
        return $success;
    }
    
    /**
     * Traite le processus de création/mise à jour des différents formats.
     *
     * @param array $format_filter
     */
    protected function formatProcess($format_filter = null)
    {
        $format_filter = (($format_filter !== null) && is_array($format_filter)) ? $format_filter
            : array_keys($this->config['format']);
        
        foreach ($this->config['format'] as $format => $elt) {
            if (in_array($format, $format_filter)) {
                $this->imgResize($format);
                if (!empty($elt['effect'])) {
                    foreach ($elt['effect'] as $effect) {
                        $this->effectProcess($effect, $format);
                    }
                }
            }
        }
    }
    
    /**
     * regénère les formats donnés
     *
     * @param array $format_filter
     */
    public function regen($format_filter = null)
    {
        $this->formatProcess($format_filter);
    }
    
    /**
     * Applique un effet choisi sur la format renseigné en entrée
     *
     * @param string $effect
     * @param string $format
     */
    public function effectProcess($effect, $format)
    {
        if (isset($this->config['effects'][$effect])) {
            $my_effect = $this->config['effects'][$effect];
            if ((is_array($my_effect)) && (count($my_effect) == 2)) {
                foreach ($my_effect[1] as $key => $val) {
                    switch ($val) {
                        case '__VAR__' :
                            $my_effect[1][$key] = $this;
                            break;
                        case '__FORMAT__' :
                            $my_effect[1][$key] = $format;
                            break;
                        case '__WIDTH__' :
                            $my_effect[1][$key] = $this->config['format'][$format]['size'][0];
                            break;
                        case '__HEIGHT__' :
                            $my_effect[1][$key] = $this->config['format'][$format]['size'][1];
                            break;
                        case '__CROP__' :
                            $my_effect[1][$key] = $this->config['format'][$format]['size'][2];
                            break;
                        case '__SRCPATH__' :
                            $my_effect[1][$key] = $this->getPath();
                            break;
                        case '__PATH__' :
                            $my_effect[1][$key] = $this->getPath($format);
                            break;
                    }
                }
                
                call_user_func_array($my_effect[0], $my_effect[1]);
            }
        } else {
            switch ($effect) {
                case 'gray' :
                    AcidFs::imgGray($this->getPath($format), $this->getPath($format));
                    break;
                
                case 'fill_white' :
                    AcidFs::fill(
                        $this->getPath($format),
                        $this->getPath($format),
                        $this->config['format'][$format]['size'][0],
                        $this->config['format'][$format]['size'][1],
                        [255, 255, 255]
                    );
                    break;
                
                case 'fill_transparent' :
                    AcidFs::fill(
                        $this->getPath($format),
                        $this->getPath($format),
                        $this->config['format'][$format]['size'][0],
                        $this->config['format'][$format]['size'][1],
                        [false, false, false]
                    );
                    break;
                
                case 'fill_black' :
                    AcidFs::fill(
                        $this->getPath($format),
                        $this->getPath($format),
                        $this->config['format'][$format]['size'][0],
                        $this->config['format'][$format]['size'][1],
                        [0, 0, 0]
                    );
                    break;
            }
        }
    }
    
    /**
     * Traite la procédure de chargement d'un fichier.
     *
     * @param int    $id       identifiant
     * @param string $key      paramêtre
     * @param string $filename variable de récupération du nom de fichier
     * @param array  $tfiles   Equivalent $_FILES
     * @param array  $tpost    $_POST
     *
     * @return boolean
     */
    public function uploadProcess($id, $key, &$filename = null, $tfiles = null, $tpost = null)
    {
        $success = true;
        $r_success = false;
        
        //$_FILES
        $tfiles = $tfiles === null ? $_FILES : $tfiles;
        
        //$_POST
        $tpost = $tpost === null ? $_POST : $tpost;
        
        if (!empty($tpost[$key . '_remove'])) {
            if (!$this->fsRemove()) {
                $success = false;
            } else {
                $filename = '';
                $r_success = true;
            }
        }
        
        $upload_proccess = false;
        $tmp_proccess = false;
        
        //Images par upload direct
        if (isset($tfiles[$key]) && ($tfiles[$key]['size'] > 0)) {
            $file_path = $tfiles[$key]['tmp_name'];
            $file_name = $tfiles[$key]['name'];
            $upload_proccess = true;
        } elseif (isset($tpost['tmp_' . $key])) {
            $file_path = $tpost['tmp_' . $key];
            $file_name =
                isset($tpost['tmp_name_' . $key]) ? $tpost['tmp_name_' . $key] : basename($tpost['tmp_' . $key]);
            if (file_exists($tpost['tmp_' . $key])) {
                $tmp_proccess = true;
            } else {
                Acid::log('file', 'wrong path ' . $tpost['tmp_' . $key] . ' for tmp_' . $key);
            }
        }
        
        if ($upload_proccess || $tmp_proccess) {
            if ($this->isAValidFile($file_name)) {
                $this->fsRemove();
                
                $this->setUrl($id, AcidFs::getExtension($file_name), $file_name);
                
                $this->fsAdd($file_path, $this->getPath(), $upload_proccess);
                
                $filename = $file_name;
                
                $this->formatProcess();
            } else {
                Acid::log('file',
                    '$_FILES[\'' . $key . '\'], file extension is not in array(' . implode(',', $this->config['ext'])
                    . ')');
                $success = false;
            }
        } elseif (isset($tfiles[$key]) && ($tfiles[$key]['size'] <= 0)) {
            Acid::log('file', '$_FILES[\'' . $key . '\'][\'size\'] = 0');
            Acid::log('file', '$_FILES[\'' . $key . '\'][\'error\'] = ' . $tfiles[$key]['error']);
            $success = $r_success;
        }
        
        return $success;
    }
    
    /**
     * Rajoute la variable au formulaire en entrée.
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
        if ($this->getVal()) {
            if (isset($this->config['admin_format']) && isset($this->config['format'][$this->config['admin_format']])) {
                $start .= '<a href="' . $this->getUrl() . '">' . "\n" .
                          '	<img src="' . $this->getUrl($this->config['admin_format']) . '" alt="'
                          . $this->getFileName() . '" />' . "\n" .
                          '</a>';
                $stop .= '' . $form->checkbox($key . '_remove', '1', false, 'Supprimer');
                
                $bstart = '<div class="src_container">';
                $bstop = '</div>';
                
                return $this->getParentForm($form, $key, $print, $params, $start . $bstart, $bstop . $stop,
                    $body_attrs);
            }
            
            return parent::getForm($form, $key, $print, $params, $start, $stop, $body_attrs);
        } else {
            return parent::getForm($form, $key, $print, $params, $start, $stop, $body_attrs);
        }
    }
}