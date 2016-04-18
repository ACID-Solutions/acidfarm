<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/**
 * Outil AcidBrowser, Gestionnaire de navigateur multimedia
 * @package   Acidfarm\Tool
 */
class AcidBrowser {

	/**
	 * Racine de la mediathèque
	 * @var unknown_type
	 */
	protected $base_path;

	/**
	 * Chemin vers le dossier des ressources fichier
	 * @var unknown_type
	 */
	protected $files_path;

	/**
	 * Chemin du dossier courant
	 * @var unknown_type
	 */
	protected $cur_path = '';

	/**
	 * Chemin vers le dossier des ressources images
	 * @var unknown_type
	 */
	protected $img;

	/**
	 * Identifiant de la mediathèque
	 * @var unknown_type
	 */
	protected $key;

	/**
	 * Droits de la médiathèque
	 * @var unknown_type
	 */
	protected $acl;

	/**
	 * Gestion de plugin
	 * @var unknown_type
	 */
	protected $plugin;

	/**
	 * Js courant
	 * @var unknown_type
	 */
	protected $js = "";

	const UNAUTHORIZED_PATH = '`(^\.\./)|(^\.\.$)|(/\.\./)|(\.\.$)`';
	const UNAUTHORIZED_NAME = '`[\\\\/:*?"<>|]`';


	/**
	 * Constructeur AcidBrowser
	 * @param string $path chemin associé au navigateur.
	 * @param bool|false $absolute si true, le chemin est absolu. - Défaut : false
	 * @param null $acl acl personnalisé
	 * @param null string $plugin plugin associé
	 * @param null $key clé dom
	 * @param null $img_path chemin vers le dossier d'images
	 */
	public function __construct($path,$absolute=false,$acl=null,$plugin=null,$key=null,$img_path=null) {
		global $acid,$css_theme;
		if(substr($path,-1) != '/') {
			$path .= '/';
		}

		$this->acl = ($acl === null) ? Acid::get('browser:acl') : $acl;
		$this->base_path = ($absolute?'':SITE_PATH) . $path;
		$this->files_path = $path;
		$this->img = $img_path===null ? Acid::themeFolder(	'img/admin/fsb/' ) : $img_path;
		$this->key = $key===null ? 'fsb' : $key;
		$this->setJS();
		$this->setPlugin($plugin);
	}

	/**
	 * Définit la valeur du JS
	 * @param string $content
	 */
	public function setJs($content=null) {
		if ($content===null) {
			$this->js = Acid::tpl('tools/browser/js.tpl');
		}else{
			$this->js = $content;
		}
	}

	/**
	 * Définit la valeur du plugin
	 * @param string $name
	 */
	public function setPlugin($name) {
		$this->plugin = $name;
	}

	/**
	 * Récupère la valeur du plugin
	 */
	public function getPlugin() {
		return $this->plugin;
	}

	/**
	 * Controlleur d'execution depuis un formulaire.
	 */
	public function exePost() {
		if (User::curLevel($this->acl) && isset($_POST[$this->key.'_do'])) {
			switch($_POST[$this->key.'_do']) {
				case 'multi_add' :	$this->postMultiAdd($_POST); 		break;
				case 'add' :		$this->postAdd($_POST);				break;
				case 'del' :		$this->fsRemove($_POST['path']);	break;
				case 'new_dir' :	$this->createNewDir($_POST);		break;
				case 'new_name' :	$this->fsRename($_POST);			break;
			}
		}
	}

	/**
	 * Supprime un dossier et son contenu, et retourne true en cas de succès, false sinon.
	 *
	 * @param string $directory Chemin vers le dossier.
	 *
	 * @return boolean
	 */
	protected function recursiveRemoveDir($directory) {
		if(substr($directory,-1) == '/') {
			$directory = substr($directory,0,-1);
		}

		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$handle = opendir($directory);
			while (false !== ($item = readdir($handle))) {
				if($item != '.' && $item != '..') {
					$path = $directory.'/'.$item;
					if(is_dir($path)) {
						$this->recursiveRemoveDir($path);
					} else {
						unlink($path);
					}
				}
			}
			closedir($handle);
			return rmdir($directory);
		}
	}

	/**
	 * Deplace un fichier uploadé en $tmp_path vers $final_path.
	 *
	 * @param string $tmp_path Chemin de la source.
	 * @param string $final_path Chemin de destination.
	 *
	 * @return boolean
	 */
	protected function fsAdd ($tmp_path,$final_path) {
		Acid::log('debug','Uploading to : '.$final_path);
		return move_uploaded_file($tmp_path,$final_path);
	}

	/**
	 * Renomme un fichier ou un dossier.
	 *
	 * @param array $attrs Attributs de la cible.
	 */
	protected function fsRename($attrs){

		if (isset($attrs['current_dir']) && isset($attrs['current_name']) && isset($attrs['name']) && isset($attrs['type'])) {

			$type=$attrs['type'];

			if (	!preg_match(self::UNAUTHORIZED_PATH,$attrs['current_dir'])
				&&	!preg_match(self::UNAUTHORIZED_PATH,$attrs['current_name'])
				&&	!preg_match(self::UNAUTHORIZED_PATH,$attrs['name'])
				&&	!preg_match(self::UNAUTHORIZED_NAME,$attrs['name'])
				)
			{

				$new_name = $this->getNameBasedOn($attrs['name'],$attrs['current_dir'],$type);
				$old_path = $this->base_path . $this->cur_path . $attrs['current_dir'] . $attrs['current_name'];
				$new_path = $this->base_path . $this->cur_path . $attrs['current_dir'] . $new_name;

				$file_ok = false;
				if ($type == 'file'){
					if(is_file($old_path) && AcidFs::getExtension($attrs['current_name']) == AcidFs::getExtension($new_name)) {
						$file_ok = true;
					}
				}
				elseif ($type == 'dir') {
					if(is_dir($old_path)){
						$file_ok = true;
					}
				}


				if(is_file($old_path) && AcidFs::getExtension($attrs['current_name']) != AcidFs::getExtension($new_name))
					Acid::log('hack','AcidBrowser::fsRename Trying to change extension file : <' . $old_path . '> <'.$new_name.'>');

				elseif ($file_ok) {
					rename($old_path,$new_path);
				}
			}
		}

	}

	/**
	 * Supprime un fichier ou un dossier du navigateur.
	 *
	 * @param string $path Chemin de l'élément.
	 */
	protected function fsRemove($path) {

		$file_path = $this->base_path.$this->cur_path.$path;

		if (is_file($file_path)) {
			return unlink($file_path);
		}

		elseif (is_dir($file_path)) {
			return $this->recursiveRemoveDir($file_path);
		}

		return false;
	}

	/**
	 * Gère l'ajout delayés de fichiers.
	 *
	 * @param array $attrs Attributs du fichier.
	 */
	protected function postMultiAdd($attrs) {
		if (!empty($_POST['files'])) {

			if (empty($_POST['names'])) {
				$_POST['names'] = array();
			}

			$dest = isset($_POST['path']) ? $_POST['path'] : '';
			$files = is_array($_POST['files']) ? $_POST['files'] : array($_POST['files']);
			$names = is_array($_POST['names']) ? $_POST['names'] : array($_POST['names']);
			$ajax = !empty($_POST['ajax']);
			$error = false;

			Acid::log('BROWSER',json_encode($files));
			$treat_log = array();
			foreach ($files as $kfile => $file) {
				if ($this->fileExtAllowed(AcidFs::getExtension($file)) && !preg_match(self::UNAUTHORIZED_PATH,$dest)) {

					$success = true;
					$name = isset($names[$kfile]) ? $names[$kfile] : basename($file);
					$dest_file = $this->base_path.$dest.$name;

					if (file_exists($file) && rename($file,$dest_file)) {
						Acid::log('BROWSER','File moved from '.$file.' to '.$dest_file);
					}else{
						$success = false;
						Acid::log('BROWSER','Error when moving file from '.$file.' to '.$dest_file);
					}

					$treat_log[] = array('dest_name'=>$name,'$dest_file'=>$dest_file,'success'=>$success);

					$error = $error && (!$success);

				}else{
					$error = true;
				}
			}

			if (($error) && (!$ajax) ) {
				AcidDialog::add('error',Acid::trad('browser_bad_file'));
			}

			if ($ajax) {
				$result = $_POST;
				$result['treatment'] = $treat_log;
				$result['success'] = !$error;
				echo json_encode($result);
				exit();
			}
		}
	}

	/**
	 * Gère l'ajout d'un fichier depuis un formulaire.
	 *
	 * @param array $attrs Attributs du fichier.
	 */
	protected function postAdd($attrs) {
		if (isset($_FILES['fichier'])) {
			$file = $_FILES['fichier'];
			if ($this->fileExtAllowed(AcidFs::getExtension($file['name'])) && !preg_match(self::UNAUTHORIZED_PATH,$attrs['path'])) {
				$name = $this->getNameBasedOn($file['name'],$attrs['path'],'file');
				$this->fsAdd($file['tmp_name'],$this->base_path.$this->cur_path.$attrs['path'].$name);
			} else {
				AcidDialog::add('error',Acid::trad('browser_bad_file'));
			}
		}
	}

	/**
	 * Traitement de mise à jour
	 * @param array $attrs
	 */
	protected function postUpdate($attrs) {

	}

	/**
	 * Traitement de suppression
	 * @param string $path
	 */
	protected function postRemove($path) {

	}

	/**
	 * Retourne le contenu d'un dossier sous forme d'un tableau.
	 *
	 * @param string $path Chemin du dossier.
	 *
	 * @return array
	 */
	protected function getContent($path) {
		$files_list = array();
		if ($handle = opendir($path)) {
			while ($file = readdir($handle)){
				if ($file != "." && $file != "..") {
					$file_to_list = array();
					if (is_dir($path.$file)) {
						$file_to_list['type'] = 'dir';
					} elseif (is_link($path.$file)) {
						$file_to_list['type'] = 'link';
					} else {
						$file_to_list['type'] = 'file';
					}
					$file_to_list['name'] = $file;
					$files_list[] = $file_to_list;
				}
			}
			closedir($handle);
		}

		return $files_list;
	}

	/**
	 * Retourne les éléments du repertoire courant du navigateur.
	 *
	 * @return array (array : dir , array : link , array : file)
	 */
	protected function getDirElements() {
		$dirs = array();
		$files = array();
		$links = array();

		$elts = $this->getContent($this->base_path.$this->cur_path);

		foreach ($elts as $file) {
			switch ($file['type']) {
				case 'dir'	:
					$dirs[] = array(	'name'=>$file['name'],
										'path'=>$this->cur_path . $file['name']
									);
				break;

				case 'link'	:
					$links[] = array(	'name'=>$file['name'],
										'path'=>$this->cur_path . $file['name'],
										'size'=>filesize($this->base_path.$this->cur_path.$file['name']),
										'ext'=>$this->getExtType(AcidFs::getExtension($file['name']))
									);

				break;

				case
					'file'	:
					$files[] = array(	'name'=>$file['name'],
										'path'=>$this->cur_path . $file['name'],
										'size'=>filesize($this->base_path.$this->cur_path.$file['name']),
										'ext'=>$this->getExtType(AcidFs::getExtension($file['name']))
									);
				break;
			}
		}

		return array($dirs,$links,$files);
	}

	/**
	 * Définit le répertoire courant du navigateur, et retourne true en case de succès, false sinon.
	 *
	 * @param string $path
	 *
	 * @return bool
	 */
	public function setCurPath($path) {
		if (!preg_match(self::UNAUTHORIZED_PATH,$path)) {
			$this->cur_path = rawurldecode($path);
			if(substr($this->cur_path,-1) != '/') {
				$this->cur_path .= '/';
			}
			if(substr($this->cur_path,0,1) == '/') {
				$this->cur_path = substr($this->cur_path,1);
			}
			return true;
		}
		return false;
	}

	/**
	 * Retourne true si l'extension est reconnue par le navigateur, false sinon.
	 *
	 * @param string $file_name Nom du fichier.
	 *
	 * @return bool
	 */
	protected function fileExtAllowed($file_name) {
		$ext = AcidFs::getExtension($file_name);
		foreach (Acid::get('files:ext') as $id=>$list) {
			if (in_array($ext,$list)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Retourne un nom d'élément basé sur les paramètres en entrée ainsi que le contenu du dossier référant.
	 *
	 * @param string $name Nom de base.
	 * @param string $dir Dossier référent.
	 * @param string $type Type de fichier.	- Défaut : 'file'
	 * @param string $i Numéro d'indexation. (si 0, non affiché) - Défaut : 0
	 * @param string $ext Extension Forcée. (si définie)
	 */
	protected function getNameBasedOn($name,$dir,$type='file',$i=0,$ext='') {

		$post = ($i>0) ? '_' . $i : '';

		if ($type == 'dir') {

			$dir_path = $this->base_path.$this->cur_path.$dir.$name.$post;
			if (is_dir($dir_path)) {
				return $this->getNameBasedOn($name,$dir,$type,$i+1);
			} else {
				return $name.$post;
			}

		}else {

			if (empty($ext)) {
				$ext = '.' . AcidFs::getExtension($name);
				$name = substr($name,0,-(strlen($ext)));
			}

			$file_path = $this->base_path.$this->cur_path.$dir.$name.$post.$ext;

			if (is_file($file_path)) {
				return $this->getNameBasedOn($name,$dir,$type,$i+1,$ext);
			}
			else {
				return $name.$post.$ext;
			}
		}
	}

	/**
	 * Créer un nouveau dossier dans le répertoire courant du navigateur, et retourne true en cas de succès, false sinon.
	 *
	 * @param array $attrs Attributs du dossier.
	 *
	 * @return boolean
	 */
	protected function createNewDir($attrs) {
		$name = $this->getNameBasedOn($attrs['name'],$attrs['current_dir'],'dir');
		return mkdir($this->base_path.$this->cur_path.$attrs['current_dir'].$name);
	}

	/**
	 * Retourne le type d'élément associé à l'extension en entrée
	 * Retourne false si non reconnu.
	 *
	 * @param string  $ext
	 * @return bool | string
	 */
	public function getExtType($ext) {
		foreach (Acid::get('files:ext') as $type=>$tab){
			if (in_array($ext,$tab)) {
				return $type;
			}
		}
		return false;
	}

	/**
	 * Retourne le chemin vers le repertoire courant du navigateur.
	 *
	 * @return string
	 */
	public function printPath() {
		$dirs = explode('/',$this->cur_path);

		return Acid::tpl('tools/browser/print-path.tpl',array('dirs'=>$dirs,'img_path'=>$this->img),$this);
	}


	/**
	 * Retourne un formulaire de renommage.
	 *
	 * @return string
	 */
	public function printChangeNameForm() {
		return Acid::tpl('tools/browser/change-name-form.tpl',array('key'=>$this->key,'cur_path'=>$this->cur_path),$this);
	}

	/**
	 * Retourne un formulaire caché
	 *
	 *
	 * @return string
	 */
	public function printNewDirHiddenForm() {
		return Acid::tpl('tools/browser/new-dir-form.tpl',array('key'=>$this->key,'cur_path'=>$this->cur_path),$this);
	}

	/**
	 * Retourne un formulaire d'upload ves le repertoire $dst_dir.
	 *
	 * @param string $dst_dir
	 *
	 * @return string
	 */
	public function printUploadForm($dst_dir) {
		return Acid::tpl('tools/browser/upload-form.tpl',array('key'=>$this->key,'cur_path'=>$this->cur_path,'dst_dir'=>$dst_dir),$this);
	}

	/**
	 * Retourne un formulaire de suppression d'élement.
	 *
	 *
	 * @return string
	 */
	public function printRemoveForm() {
		return Acid::tpl('tools/browser/remove-form.tpl',array('key'=>$this->key,'cur_path'=>$this->cur_path),$this);
	}

	/**
	 * Retourne un bloc Dossier mis en forme pour le navigateur en fonction des attributs en entrée.
	 *
	 * @param array $attrs
	 *
	 * @return string
	 */
	public function printEltDir($attrs) {
		$link = AcidUrl::build(array('fsb_path'=>rawurlencode($attrs['path'] . '/')));
		return Acid::tpl('tools/browser/print-elt-dir.tpl',array('img_path'=>$this->img,'attrs'=>$attrs,'link'=>$link,'key'=>$this->key,'cur_path'=>$this->cur_path),$this);
	}

	/**
	 * Retourne un bloc Raccourci mis en forme pour le navigateur en fonction des attributs en entrée.
	 *
	 * @param array $attrs
	 *
	 * @return string
	 */
	public function printEltLink($attrs) {
		return $this->printEltFile($attrs);
	}

	/**
	 * Retourne un bloc Fichier mis en forme pour le navigateur en fonction des attributs en entrée.
	 *
	 * @param array $attrs
	 *
	 * @return string
	 */
	public function printEltFile($attrs) {
		if ($attrs['ext'] == 0) {
			$img_path = Acid::get('url:folder').$this->files_path.$attrs['path'];
		}
		else {
			if ($attrs['ext'] == false) $icone_name = 'inconnu.png';
			else $icone_name = Acid::get('files:icons:'.$attrs['ext']);
			$img_path = $this->img.$icone_name;
		}

		$link = Acid::get('url:folder').$this->files_path.$attrs['path'];

		return Acid::tpl('tools/browser/print-elt-file.tpl',array('img_path'=>$this->img,'img_file'=>$img_path,'attrs'=>$attrs,'link'=>$link,'key'=>$this->key,'cur_path'=>$this->cur_path),$this);
	}

	/**
	 * Retourne le contenu du repertoire $path depuis le navigateur.
	 *
	 * @param string $path Chemin vers le répertoire à afficher.
	 *
	 * @return string
	 */
	public function printDir($path) {

		$this->setCurPath($path);

		list($dirs,$files,$links) = self::getDirElements();

		$params = array(
						'print_path'=>$this->printPath(), 'new_dir_form'=>$this->printNewDirHiddenForm(),
						'upload_form' => $this->printUploadForm($this->cur_path), 'remove_form' => $this->printRemoveForm(),
						'change_form' => $this->printChangeNameForm(),
						'key'=>$this->key, 'cur_path'=>$this->cur_path, 'img_path' => $this->img,
						'dirs' => $dirs, 'links' => $links, 'files' => $files, 'js' => $this->js,
						'base_path' => $this->base_path.$this->cur_path
				);

		return Acid::tpl('tools/browser/print-dir.tpl',$params,$this);
	}

}
