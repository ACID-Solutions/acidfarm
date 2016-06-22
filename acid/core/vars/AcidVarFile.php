<?php

/**
 * AcidFarm - Yet Another Framework
 *
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
 * Variante Fichier d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarFile extends AcidVarString {

    /**
     * @var string chemin vers le repertoire de stockage du fichier
     */
    var $dir_path 	= null;

    /**
     * @var array configuration
     */
    var $config 	= array(
        'max_file_size'=>null,
        'name'=>'',
        'print_id'=>'%d',
        'ext'=>null
    );

    /**
     * Constructeur AcidVarFile
     * @param string $label étiquette
     * @param string $folder chemin vers le repertoire de stockage du fichier
     * @param array $config configuration
     * @param string $name nom du fichier (variables magiques : __NAME__, __ID__ )
     */
    public function __construct($label='AcidVarFile',$folder=null,$config=array(),$name='') {
        $folder = ($folder!==null) ? $folder : Acid::get('path:files');
        $max_file_size = isset($config['max_file_size'])? $config['max_file_size'] : null;

        $this->dir_path = $folder;
        if (!is_dir(SITE_PATH.$this->dir_path)) {
            trigger_error(get_called_class().' : unable to find path '.SITE_PATH.$this->dir_path,E_USER_WARNING);
        }

        $config['name'] = isset($config['name']) ? $config['name'].$name : $name;

        foreach ($config as $key=>$val) {
            $this->config[$key] = $val;
        }


        if ($this->config['ext'] === null) {

            $this->config['ext'] = Acid::get('ext:files');
        }

        parent::__construct($label,255,20);

        $this->setForm('file',array('max_file_size'=>$max_file_size));

    }

    /**
     * Retourne le chemin d'accès au fichier.
     *
     *
     * @return string
     */
    public function getPath() {
        return SITE_PATH.$this->dir_path.pathinfo($this->getUrl(),PATHINFO_BASENAME);
    }

    /**
     * Retourne le chemin d'accès au fichier désigné par la valeur.
     *
     * @return string
     */
    public function getValPath() {
        return SITE_PATH.$this->getVal();
    }

    /**
     * Retourne le chemin d'accès au fichier.
     *
     *
     * @return string
     */
    public function getFileName() {
        return basename($this->getUrl());
    }

    /*
    public function getUrl() {
        return 	Acid::get('url:folder').$this->getVal();
    }
    */

    /**
     * Retourne l'URL du fichier.
     *
     *
     * @return string
     */
    public function getUrl() {
        if (empty($this->config['get_url_func'])) {
            return 	Acid::get('url:folder').$this->getVal();
        }else{

            if (is_array($this->config['get_url_func'])) {
                $name = $this->config['get_url_func'][0];
                $args = $this->config['get_url_func'][1];
            }else{
                $name = $this->config['get_url_func'];
            }

            if (!empty($args)) {
                foreach ($args as $k=>$v) {
                    if ($args[$k] === '__OBJ__') {
                        $args[$k] = $this;
                    }elseif ($args[$k] === '__VAL__') {
                        $args[$k] = $this->getVal();
                    }
                }
            }


            return ($name=='value') ? $this->getVal() : call_user_func_array($name,$args);
        }
    }

    /**
     * Attribue son URL au fichier.
     *
     * @param string $ext
     */
    /*
    protected function setUrl($id,$ext) {
        $print_id = !empty($this->config['print_id']) ? sprintf($this->config['print_id'],$id) : $id;

        if (substr_count($this->config['name'],'__ID__')) {
            $name = str_replace('__ID__',$print_id,$this->config['name']);
        }else{
            $name = $print_id . $this->config['name'];
        }

        $name .=  '.'.$ext;

        $this->setVal($this->dir_path.$name);
    }
    */

    /**
     * Attribue son URL au fichier.
     * @param int $id
     * @param string $ext
     * @param string $filename
     */
    protected function setUrl($id,$ext,$filename=null) {
        $url = $this->generateUrl($id,$ext,$filename);
        $this->setVal($url);
    }

    /**
     * Génère l'URL du fichier.
     *
     * @param string $id
     * @param string $ext
     * @param string $filename
     */
    public function buildUrl($id,$ext,$filename=null) {
        $print_id = !empty($this->config['print_id']) ? sprintf($this->config['print_id'],$id) : $id;

        if (substr_count($this->config['name'],'__ID__')) {
            $name = str_replace('__ID__',$print_id,$this->config['name']);
        }else{
            $name = $print_id . $this->config['name'];
        }

        if ($filename) {
            $name = str_replace('__NAME__',AcidFs::removeExtension($filename),$name);
        }

        $name .=  '.'.$ext;

        return $this->dir_path.$name;
    }

    /**
     * Retourne l'URL du fichier.
     *
     * @param string $id
     * @param string $ext
     * @param string $filename
     */
    public function generateUrl($id,$ext,$filename=null) {

        if (empty($this->config['url_func'])) {
            return $this->buildUrl($id,$ext,$filename);
        }else{
            $name = $this->config['url_func'][0];
            $args = $this->config['url_func'][1];
            foreach ($args as $k=>$v) {
                if ($args[$k] === '__ID__') {
                    $args[$k] = $id;
                }elseif ($args[$k] === '__EXT__') {
                    $args[$k] = $ext;
                }elseif ($args[$k] === '__NAME__') {
                    $args[$k] = $filename;
                }
            }
            return call_user_func_array($name,$args);
        }

    }

    /**
     * Initialise l'url du fichier
     * @param int $id
     * @param string $ext
     * @param string $filename
     */
    public function initVal($id,$ext,$filename=null) {
        $this->setUrl($id,$ext,$filename);
    }

    /**
     * Retourne true si le fichier renseigné en entrée est interprété comme valide, retourne false sinon.
     *
     * @param string $file_path
     *
     * @return bool
     */
    public function isAValidFile($file_path) {
        if ($this->isAValidExt(AcidFs::getExtension($file_path))) {
            return true;
        } elseif (!empty($file_path)) {
            AcidDialog::add('error',Acid::trad('vars_bad_file'));
        }
        return false;
    }

    /**
     * Retourne true si l'exention renseignée en entrée est interprétée comme valide, retourne false sinon.
     *
     * @param string $ext
     * @param array $t_exts tableau d'extensions, par défaut $this->config['ext'], si false alors on accepte tout
     * @return bool
     */
    public  function isAValidExt($ext,$t_exts=null) {
        if ($t_exts === null || !is_array($t_exts)) {
            $t_exts = $this->config['ext'];
        }

        if($t_exts === false){
            return true;
        }

        return (in_array($ext,$t_exts));
    }

    /**
     *Mets à jour les extensions autorisées
     * @param unknown_type $exts
     */
    public  function setExt($exts) {

        if ($exts === null) {
            $this->config['ext'] = Acid::get('ext:files');
        }else{
            $this->config['ext'] = $exts;
        }


    }

    /**
     * Déplace le fichier en $tmp_path vers le $final_path et configure ses droits d'accès.
     * @param string $tmp_path
     * @param string $final_path
     * @param boolean $uploaded_file
     * @return boolean
     */
    protected function fsAdd ($tmp_path,$final_path,$uploaded_file=true) {

        if ($uploaded_file) {
            if (move_uploaded_file($tmp_path,$final_path)) {
                chmod($final_path,Acid::get('files:file_mode'));
                return true;
            }
        }else{
            if (rename($tmp_path,$final_path)) {
                chmod($final_path,Acid::get('files:file_mode'));
                return true;
            }
        }

        return false;
    }

    /**
     * Supprime le fichier associé au module.
     *
     *
     * @return bool
     */
    public function fsRemove() {

        if (is_file($path = $this->getValPath())) {
            if (unlink($path)) {
                $this->setVal('');
            }else{
                Acid::log('error',get_called_class().'::fsRemove can\'t delete ' . $path);
                return false;
            }
        }
        return true;
    }

    /**
     * Traite la procédure de chargement d'un fichier.
     * @param int $id identifiant
     * @param string $key paramêtre
     * @param string $filename variable de récupération du nom de fichier
     * @param array $tfiles Equivalent $_FILES
     * @param array $tpost $_POST
     * @return boolean
     */
    public function uploadProcess($id,$key,&$filename=null,$tfiles=null,$tpost=null) {
        $success = true;
        $r_success = false;

        //$_FILES
        $tfiles = $tfiles===null ? $_FILES : $tfiles;

        //$_POST
        $tpost = $tpost===null ? $_POST : $tpost;

        if (!empty($tpost[$key.'_remove'])) {
            if (!$this->fsRemove()){
                $success = false;
            }else{
                $filename = '';
                $r_success = true;
            }
        }

        $upload_proccess = false;
        $tmp_proccess = false;

        //Fichier par upload direct
        if (isset($tfiles[$key]) && ($tfiles[$key]['size'] > 0)) {
            $file_path = $tfiles[$key]['tmp_name'];
            $file_name = $tfiles[$key]['name'];
            $upload_proccess = true;
        }elseif(isset($tpost['tmp_'.$key])) {
            $file_path = $tpost['tmp_'.$key];
            $file_name = isset( $tpost['tmp_name_'.$key] ) ? $tpost['tmp_name_'.$key] : basename($tpost['tmp_'.$key]);

            if (file_exists($tpost['tmp_'.$key])) {
                $tmp_proccess = true;
            }else{
                Acid::log('file','wrong path '.$tpost['tmp_'.$key].' for tmp_'.$key);
            }
        }

        if ($upload_proccess || $tmp_proccess) {

            if ($this->isAValidFile($file_name)) {

                $this->fsRemove();

                $this->setUrl($id,AcidFs::getExtension($file_name),$file_name);

                $this->fsAdd($file_path,$this->getPath(),$upload_proccess);

                $filename = $file_name;

            }else{
                Acid::log('file','$_FILES[\''.$key.'\'], file extension is not in array('.implode(',',$this->config['ext']).')');
                $success = false;
            }

        }elseif( isset($tfiles[$key]) && ($tfiles[$key]['size'] <= 0) ) {
            Acid::log('file','$_FILES[\''.$key.'\'][\'size\'] = 0');
            Acid::log('file','$_FILES[\''.$key.'\'][\'error\'] = ' . $tfiles[$key]['error']);
            $success = $r_success;
        }

        return true;
    }

    /**
     * Rajoute la variable au formulaire en entrée.
     *
     * @param object $form AcidForm
     * @param string $key Nom du paramétre.
     * @param bool $print si false, utilise la valeur par défaut
     * @param array $params attributs
     * @param string $start préfixe
     * @param string $stop suffixe
     * @param array $body_attrs attributs à appliquer au cadre
     */
    public function getParentForm(&$form,$key,$print=true,$params=array(),$start='',$stop='',$body_attrs=array()) {
        return parent::getForm($form,$key,$print,$params,$start,$stop,$body_attrs);
    }

    /**
     * Formulaire HTML
     *
     * @param object $form AcidForm
     * @param string $key Nom du paramétre.
     * @param bool $print si false, utilise la valeur par défaut
     * @param array $params attributs
     * @param string $start préfixe
     * @param string $stop suffixe
     * @param array $body_attrs attributs à appliquer au cadre
     *
     * @return string
     */
    public function getForm(&$form,$key,$print=true,$params=array(),$start='',$stop='',$body_attrs=array()) {
        if ($this->getVal()) {
            $start .= '<a href="'.$this->getUrl().'">'.$this->getFileName().'</a>';
            $stop .= ''.$form->checkbox ($key.'_remove', '1', false,'Supprimer');
        }

        $bstart =  '<div class="src_container">';
        $bstop = '</div>';

        return $this->getParentForm($form,$key,$print,$params,$start.$bstart,$bstop.$stop,$body_attrs);
    }

    /**
     * Recupère la configuration de l'objet
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig($key=null) {
        if ($key!==null) {
            return isset($this->config[$key]) ? $this->config[$key] : null;
        }

        return $this->config;
    }

    /**
     * Modifie la configuration de l'objet
     *
     * @param mixed $val
     * @param string $key
     *
     * @return mixed
     */
    public function setConfig($val,$key=null) {
        if ($key!==null) {
            $this->config[$key] = $val;
        }else{
            $this->config = $val;
        }
    }

    /**
     * Recupère le dir_path de l'objet
     *
     *
     * @return mixed
     */
    public function getDirPath() {
        return $this->dir_path;
    }

    /**
     * Modifie la dir_path de l'objet
     *
     * @param string $path
     *
     * @return mixed
     */
    public function setDirPath($path) {
        $this->dir_path = $path;
    }

}