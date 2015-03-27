<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Core
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Gestionnaire de mise à jour automatisée Acidfarm
 * Nécéssite un dossier de mise à jour qui correspondra à son type ( "update" pour l'exemple ).
 * A sa racine seront présent deux fichiers :
 * cur_version.txt (svn ignore), la version (YYYY-mm-dd-x) courante de la machine
 * last_version.txt, la version (YYYY-mm-dd-x) disponible
 * Pour créer une mise à jour, il faut alors :
 * 	Créer un dossier portant le nom d'une version (YYYY-mm-dd-x)
 *	x étant un identifiant incrémentable, permettant la génération de plusieurs dossiers par jour
 * 	Ses dossiers seront traité par ordre alphabétique
 * 	Tous dossier désignant une version supérieure à celle du cur_version.txt, et une version inferieure à celle du last_version.txt seront alors parcourus.
 * 	Pour ceux-ci, on traitera les fichiers .sql, .php et .sh par ordre alphabétique.
 * 	Les fichiers SQL seront éxécutés avec remplacement des préfixes (ex : acid_[ma-table] sera remplacé en machine_[ma-table]
 * 	Les fichiers PHP seront éxécutés avec un accès aux variables globales $acid et $acidconf
 * 	Les fichiers SH interdiront laccès au site en invitant l'administrateur à effectuerune mise à jour manuelle
 * 	En mode dev, l'utilisateur du site sera invité à procéder à une mise à jour.
 *  En prod, la mise à jourse fera automatiquement
 * Exemple de mise en place d'un système d'upgrade pour votre site :
 * 	$my_upgrade = new new AcidUpgrade('upgrade');
 *	$my_upgrade->launchUpgrade();
 *
 * @package   Core
 */
class AcidUpgrade {

	/**
	 * @var string type de l'upgrade (update/upgrade)
	 */
	public $type;

	/**
	 * @var string chemin vers le dossier d'upgrade
	 */
	public $path;

	/**
	 * @var string mode de traitement (dev,prod,off)
	 */
	public $mode;

	/**
	 * @var string identifiant de traitement (post par exemple) de l'upgrade
	 */
	public $process_name;

	/**
	 * Constructeur
	 * @param string $type
	 */
	public function __construct($type='upgrade') {
		$this->type = $type;
		$this->path = Acid::get('upgrade:path:'.$this->type);
		$this->mode = Acid::get('upgrade:mode');

		$this->process_name = 'do_'.$this->type.'_process';

		$this->excluded = array('files'=>Acid::get('upgrade:excluded:files'),'folders'=>Acid::get('upgrade:excluded:folders'));
		$this->folders = array();
		$this->files = array('sql'=>array(),'php'=>array(),'sh'=>array());
	}

	/**
	 *  Récupère la version de l'utilisateur
	 */
	public function checkCurVersion() {
		$version_file = 'cur_version.txt';
		if (file_exists($this->path.$version_file)) {
			$version = file_get_contents($this->path.$version_file);
		}else{
			$version = false;
		}

		return $version;
	}

	/**
	 *  Définit la version de l'utilisateur
	 * @param string $version (YYYY-mm-dd-x)
	 */
	public function setCurVersion($version) {
		$version_file = 'cur_version.txt';
		file_put_contents($this->path.$version_file,$version);
		return $version;
	}

	/**
	 *  Récupère la version de partage  (YYYY-mm-dd-x)
	 */
	public function checkLastVersion() {
		$version_file = 'last_version.txt';
		if (file_exists($this->path.$version_file)) {
			$version = file_get_contents($this->path.$version_file);
		}else{
			$version = false;
		}

		return $version;
	}

	/**
	 *  Définit la version de partage (YYYY-mm-dd-x)
	 * @param string $version (YYYY-mm-dd-x)
	 */
	public function setLastVersion($version) {
		$version_file = 'last_version.txt';
		file_put_contents($this->path.$version_file,$version);
		return $version;
	}

	/**
	 *  Contrôle et définit si besoin la version en entrée
	 * @param string $version (YYYY-mm-dd-x)
	 */
	public function initVersion($version) {
		$version = ($version !== null) ? $version : $this->checkCurVersion();
		$version = $version ? $version : '0';

		return $version;
	}

	/**
	 * Contrôle si on doit effectuer la procédure de mise à jour
	 * @param boolean $check_mode si false, le mode d'upgrade ne sera pas pris en compte lors de la vérification
	 * @return boolean
	 */
	public function canUpgrade($check_mode=true) {
		if ( ($this->mode != 'off') || (!$check_mode) )  {
			if (is_dir($this->path)) {
				if ($this->checkLastVersion()) {
					if ($this->checkCurVersion() != $this->checkLastVersion()) {
						return (!$this->checkCurVersion()) || ($this->checkCurVersion() < $this->checkLastVersion());
					}
				}else{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 *  Applique la mise à jour à compter de la version en entrée
	 * @param string $version (YYYY-mm-dd-x)
	 */
	public function setFolders($version=null) {
		$version = self::initVersion($version);
		$last_version = $this->checkLastVersion();

		$h = opendir($this->path);
		while (($file = readdir($h)) !== false) {
			if ( ($file != '.') && ($file != '..') && (is_dir($this->path.$file)) ) {
				if (!in_array($file,$this->excluded['folders'])) {
					if ($version < $file) {
						if ( (!$last_version) || ($file <= $last_version) ) {
							$this->folders[] = $file;
						}
					}
				}
			}
		}

		sort($this->folders);

		closedir($h);
	}

	/**
	 *  Execute le fichier sql en entrée
	 * @param string $file chemin vers le fichier sql
	 */
	public function treatSQL($file) {
		$sql_request = file_get_contents($file);
		if ($sql_request) {
			if (Acid::get('upgrade:db:sample_prefix')) {
				foreach (Acid::get('upgrade:db:sample_prefix') as $to_replace) {
					$sql_request = str_replace('`'.$to_replace,'`'.Acid::get('db:prefix'), $sql_request);
				}
			}
			AcidDB::exec($sql_request);
		}
	}

	/**
	 *  Execute le fichier php en entrée
	 * @param string $file chemin vers le fichier php
	 */
	public function treatPhp($file) {
		global $acid, $acidconf;

		$_current_file = $file;
		$_current_folder = dirname($file);
		include($_current_file);
	}

	/**
	 * Applique la mise à jour à compter de la version en entrée
	 * @param string $version (YYYY-mm-dd-x)
	 */
	public function upgrade($version=null) {
		//on récupère les fichiers à traiter
		$this->setFolders($version);

		$ext = array('php','sql','sh');
		$ext_common = array('php','sql');
		foreach ($this->folders as $folder) {

			$this->files['sql'] = array();
			$this->files['php'] = array();
			$this->files['common'] = array();
			$this->files['sh'] = array();

			$cur_path = $this->path.$folder.'/';
			$h = opendir($cur_path);
			while (($file = readdir($h)) !== false) {
				$cur_ext = AcidFs::getExtension($file);
				if ( (!is_dir($cur_path.$file)) && (in_array($cur_ext,$ext)) ) {
					if (!in_array($file,$this->excluded['files'])) {
						if (in_array($cur_ext,$ext_common)) {
				 			$this->files['common'][] = $cur_path.$file;
						}
					}
				}
			}
			closedir($h);

			if (empty($this->files['sh'])) {

				if (
						($this->mode == 'dev') &&
						( (!empty($this->files['common'])) || (!empty($this->files['sql'])) || (!empty($this->files['php'])) )  &&
						(empty($_POST[$this->process_name]))
					)
				{
					$label = (($this->type =='update') || ($this->type =='upgrade')) ? $this->type : 'update of '.$this->type;
					$input_label = (($this->type =='update') || ($this->type =='upgrade')) ? $this->type : 'update '.$this->type;
					echo nl2br(
							'A new '.$label.' is available and need to be installed ( '.$folder.' )' . "\n" .
							'Please check the '.$label.' and click the button bellow.' . "\n".
							'<form action="" method="POST"><div>' . "\n" .
							'	<input type="submit" name="'.$this->process_name.'" value="'.$input_label.'" />' . "\n" .
							'</div></form>'. "\n"
					);
					exit();
				}

				//execution des fichiers
				sort($this->files['common']);
				foreach ($this->files['common'] as $common_file) {
					$cur_ext = AcidFs::getExtension($common_file);
					switch ($cur_ext) {
						case 'sql' :  $this->treatSQL($common_file); break;
						case 'php' :  $this->treatPhp($common_file); break;
					}
				}

				/*
				//execution du sql
				sort($this->files['sql']);
				foreach ($this->files['sql'] as $sql_file) {
					$this->treatSQL($sql_file);
				}

				//execution du php
				sort($this->files['php']);
				foreach ($this->files['php'] as $php_file) {
					$this->treatPhp($php_file);
				}
				*/

				//execution du sh


				//on met à jour la version courant
				Acid::log('MAJ',$this->type.' '.$folder.' done.');
				$this->setCurVersion($folder);

			}else{
				echo 'A new  version of '.$this->type.' is available and need to be installed manually ( '.$folder.' )';
				exit();
			}

		}


	}

	/**
	 *  Lance le processus de mise à jours
	 */
	public function launchUpgrade() {
		if ($this->canUpgrade()) {
			$this->upgrade();
		}
	}

}