<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Core
 * @version   0.1
 * @since     Version 0.6
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/*****************************************************************************
 *
 *          Acid Hook Class
 *
 *****************************************************************************/

/**
 * Utilitaire de Hook
 * @package   Acidfarm\Core
 */
class AcidHook {

	/**
	 * Valeur static
	 * @var object
	 */
	private static $_session;

	/**
	 * Dossier de hook
	 * @var string
	 */
	private $path = null;

	/**
	 * Indexation du dossier hook
	 * @var array
	 */
	private $parser = null;

	/**
	 * Gestionnaire de position auto
	 * @var array
	 */
	private $increments = array();

	/**
	 * Constructeur
	 */
	private function __construct($hook_path=null) {
		$this->path = $hook_path===null ? SITE_PATH.Acid::get('hook:path') : $hook_path;
	}

	/**
	 * Getter de l'instance
	 * @return AcidHook
	 */
	public static function getInstance() {

		if (self::$_session === null) {
			self::$_session = new AcidHook();
		}

		return self::$_session;
	}

	/**
	 * Indexe le dossier de hook
	 */
	public function parse() {
		$this->parser = array();
		if (is_dir($this->path)) {
			if ($dh = opendir($this->path)) {

		        while (($file = readdir($dh)) !== false) {

		           $moddir = $this->path.$file;
		           if ( is_dir($moddir) && (strpos($file,'.')===false) ) {

		           		if ($sdh = opendir($moddir)) {
		           			while (($sfile = readdir($sdh)) !== false) {


		           				$hookdir = $moddir.'/'.$sfile.'/';
		           				if ( is_dir($hookdir) && (strpos($sfile,'.')===false) ) {
		           					if (file_exists($hookdir.'hook.php')) {

		           						if (!isset($this->increments[$sfile])) {
			           						$this->increments[$sfile] = 0;
			           					}

			           					if (file_exists($hookdir.'pos.txt')) {
											$position = intval(trim(file_get_contents($hookdir.'pos.txt')));
			           					}else{
			           						$position = 'x'.$this->increments[$sfile];
			           						$this->increments[$sfile]++;
			           					}

										$this->parser[$sfile][$position] = $hookdir.'hook.php';
		           					}
		           				}

		           			}
		           			closedir($sdh);
		           		}
		           }
		        }
		        closedir($dh);
   			 }
		}
	}

	/**
	 * Execute un hook
	 * @param string $hook
	 */
	public function hook($hook,$params=array()) {
		if ($this->parser===null) {
			$this->parse();
		}

		if (isset($this->parser[$hook])) {
			if (is_array($this->parser[$hook])) {
				$tab = $this->parser[$hook];
				sort($tab);
				foreach ($tab as $file) {
					require($file);
				}
			}
		}
	}

	/**
	 * Alias statique de Hook::getInstance()->hook($hook);
	 * @param string $hook Nom du point d'ancrage
	 */
	public static function call($hook,$params=array()) {
		return self::getInstance()->hook($hook,$params);
	}

}