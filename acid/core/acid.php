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



/*****************************************************************************
 *
 *           Acid Main Classes
 *
 *****************************************************************************/

/**
 * Acidfarm 
 * @package   Core
 */
class Acid {

	/**
	 *  Permet de charger fichier. (DEPRECATED)
	 *
	 * @param string $path repertoire pointant vers le fichier en question
	 *
	 * @return bool
	 */
	public static function load ($path)
	{
		static $files;
		if (!isset($files[$path])) {
			Acid::log('acid','Acid::load - ' . $path);
			include ACID_PATH . $path;
			return $files[$path] = true;
		} else {
			return true;
		}
	}

	/**
	 *  Permet de charger un module interne a un fichier.
	 *
	 * @param string $path  repertoire pointant vers le fichier
	 * @param string $mod nom de la classeà charger
	 *
	 * @return bool
	 */
	public static function loadMod ($path,$mod)
	{
		static $modules;
		if (!isset($modules[$path])) {
			
			Acid::log('acid','Acid::module - ' . $path);
			//include substr(ACID_PATH,0,strrpos(ACID_PATH,'/',-2)) . '/' . $path;
			//$GLOBALS['acid']['mods'][$mod] = new $mod();
			Acid::set('mods:'.$mod,new $mod());
			return $modules[$path] = true;
		} else {
			return false;
		}
	}

	/**
	 *  Méthode abrégée de chargement de module Acidfarm.
	 *
	 * @param string $mod nom de la classe à charger
	 *
	 * @return object
	 */
	public static function mod ($mod) {
		
		if (!Acid::exist('mods:'.$mod)) {		
			if (Acid::exist('includes:'.$mod)) {
				Acid::loadMod(Acid::get('includes:'.$mod),$mod);
			} else {
				trigger_error(	'Acid : Could not load ' . $mod . ', '.
                				'path must be defined by $acid[\'mods\'][\''.$mod.'\']',E_USER_ERROR);
			}
		}
		
		return Acid::get('mods:'.$mod);
	}

	/**
	 *  Chargement d'un outil Acidfarm.
	 *
	 *
	 * @param string $tool nom de l'outil
	 *
	 * @return object | boolean
	 */
	/*
	public static function tool ($tool) {
		global $acid;
		if (is_array($tool)) {
			$success = true;
			foreach ($tool as $t) {
				if (!self::tool($t)) {
					$success = false;
				}
			}
			return $success;
		} elseif (isset($acid['includes'][$tool])) {
			return Acid::load($acid['includes'][$tool]);
		} else {
			trigger_error('Acid::tool(), "'.$tool.'" does not exists',E_USER_WARNING);
		}
	}
	*/
	
	/**
	 * DEPRECATED
	 * @param mixed $tool
	 */
	public static function tool ($tool) {
		$tool = is_array($tool) ? implode(',',$tool) : $tool;
		
		Acid::log('deprecated','Using DEPRECATED method Acid::tool('.$tool.')');
	}
	
	/**
	 *  Signal un rapport d'erreur dans le registre prévu à cet effet.
	 *
	 * @param string $level niveau de gravité
	 * @param string $message
	 * @param string $path chemin vers le fichier de destination
	 */
	public static function log ($level, $message, $path=null)
	{		
		if (Acid::get('log:enable')) {
			
			$type = strtoupper($level);
			if  (is_array(Acid::get('log:keys'))) {
				$go_on = in_array($type,Acid::get('log:keys'));
			}else{
				$go_on = (Acid::get('log:keys') == '*');
			}
			
			if ($go_on) {
				
				static $uniq_code = null;
				if ($uniq_code === null) {
					$chars = '0123456789ABCDEF';
					$uniq_code = $chars{rand(0,15)}.$chars{rand(0,15)}.$chars{rand(0,15)}.$chars{rand(0,15)};
				}
				
				if ($path === null) {
					switch (Acid::get('log:type')) {
						case 'daily' :
							$f_name = Acid::get('log:filename') . '_' . date(Acid::get('log:filename_date')) . '.log';
						break;
						
						default :
							$f_name = Acid::get('log:filename') . '.log';
						break;
					}
					$path = Acid::get('log:path') . $f_name;
				}
				
				// Récupérer fichier et ligne
				$trace = debug_backtrace();
				$file   = $trace[0]['file'];
				$line   = $trace[0]['line'];
				
				$custom = Acid::get('log:custom');
				
				$color_key = 'log:colorize:'.strtoupper($type);
				$print_type = Acid::exist($color_key) ? AcidBash::shColorText($type, Acid::get($color_key)) : $type ;
				
				$line = $uniq_code . ' ' . 
						Acid::get('include:mode') . ' ' . 
						date(Acid::get('log:date_format')) . ' - ' .
						$print_type. ' - ' .
						($custom == "" ? "" : ($custom . ' - ')) .
						basename($file) . ' ' .$line. ' - ' .
						$_SERVER['REMOTE_ADDR'] . ' - ' .
						$message . "\n";

				$handle = fopen($path, 'a');
				flock($handle, LOCK_EX);
				fwrite($handle, $line);
				flock($handle, LOCK_UN);
				fclose($handle);
			}	
			
		}
	}
	
	/**
	 *  Traduit la clé en entrée
	 *
	 * @param string $val
	 * @param array $replace
	 * @return string
	*/
	public static function trad($val,$replace=array()) {
		
		if (Acid::exist('trad:'.$val,'lang')) {
			$res = Acid::get('trad:'.$val,'lang');
		}else{
			$res = $val;
		}
		
		if (count($replace)) {
			foreach ($replace as $search => $rep) {
				if (strpos($res,$search) !== false) {
					$res = str_replace($search,$rep,$res);
				}
			}
		}
		
		return $res;
	}
	
	/**
	 *  Retourne le chemin vers le fichier template désigné en entrée.
	 *  Si le fichier n'existe pas dans le template courant, on retourne le chemin vers le fichier par défaut.
	 *
	 * @param string $file
	 * @return string
	 */
	public static function outPath($file=null) {
		
		if ($file === null) {
			$file = Acid::get('out') . '.php';
		}
		
		
		$path = SITE_PATH . Acid::get('keys:theme') . '/' . Acid::get('theme') . '/out/'. $file;
		if (!is_file($path)) {
			$path = SITE_PATH . Acid::get('keys:theme') . '/' . Acid::get('def_theme') .'/out/'. $file;
		}
		if (!is_file($path)) {
			$path = self::outPath('default.php');
		}
		return $path;
	}

	/**
	 *  Retourne le chemin vers le fichier template désigné en entrée.
	 *  Si le fichier n'existe pas dans le template courant, on retourne le chemin vers le fichier par défaut.
	 *
	 * @param string $file
	 * @return string
	 */
	public static function tplPath($file) {
		$path = SITE_PATH . Acid::get('keys:theme') . '/' . Acid::get('theme') . '/tpl/'. $file;
		if (!is_file($path)) {
			$path = SITE_PATH . Acid::get('keys:theme') . '/' . Acid::get('def_theme') .'/tpl/'. $file;
				if (!is_file($path)) {
					$path = ACID_PATH . Acid::get('tpl:path') . $file;
				}
		}
		return $path;
	}

	/**
	 * Appel le fichier template désigné en entrée.
	 * Procède à un include.
	 * Dans un tpl on peut utiliser $v pour le tableau d'arguments en entrée, $o pour l'objet en entrée, et $g pour $GLOBALS
	 * @param string $file chemin vers le fichier tpl depuis le dossier de templates
	 * @param array $v les arguments à importer dans le tpl
	 * @param object $o l'objet à importer dans le tpl
	 * @return string
	 */
	public static function tpl($file,$v=array(),$o=null) {
		return self::executeTpl(self::tplPath($file),$v,$o);
	}

	/**
	 *  Execute le fichier désigné en entrée et retourne son interpretation.
	 *	Procède à un include. 
	 *  Dans un tpl on peut utiliser $v pour le tableau d'arguments en entrée, $o pour l'objet en entrée, et $g pour $GLOBALS
	 * @param string $path chemin vers le fichier tpl
	 * @param array $v les arguments à importer dans le tpl
	 * @param object $o l'objet à importer dans le tpl
	 * @return string
	 */
	public static function executeTpl($path,$v=array(),$o=null) {
		
		/*
		self::timerStart('tpl');
		
		if ($exist = ob_get_contents()) {
			$ob_exists = true;
			ob_end_clean();
		} else {
			$ob_exists = false;
		}
		
		ob_start();
		$g = &$GLOBALS;
	
		include($path);
		
		$output = 	$exist .
		ob_get_contents();
		
			
		ob_end_clean();
		
		if ($ob_exists) {
			ob_start();
		}
		
		self::timerStop('tpl');
		return $output;
		*/
		
		self::timerStart('tpl');
		ob_start();
		$g = &$GLOBALS;
	
		include($path);
	
		$output = ob_get_clean();
	
		self::timerStop('tpl');
		return $output;
		
	}
	
	/**
	 *  Génère la valeur de hachage d'une chaîne de carractère.
	 *
	 *
	 * @param string $string
	 * @return string
	 */
	public static function hash($string) {
		return hash(Acid::get('hash:type'),$string);
	}
	
	/**
	 *  Parcourt un tableau en fonction du tableau chemin en entrée
	 *
	 * @param array $array_path Chemin
	 * @param array $array
	 * 
	 * @return mixed
	 */
	public static function parse($array_path,$array) {
		$cur_tab = null;
		if (count($array_path)) {
			$cur_tab = $array;
			foreach ($array_path as $k) {
				if (isset($cur_tab[$k])) {
					$cur_tab = $cur_tab[$k];
				}else{
					return null;
				}
			}
			
			return $cur_tab;
		}
		
		return null;
	}
	
	/**
	 *  Récupère le tableau de parcours en fonction de la clé en entrée
	 *
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public static function parseArray($key) {
		return  explode(':',$key);
	}
	
	/**
	 *  Récupère une variable de configuration
	 *
	 * @param string $key
	 * @param string $array nom de la variable globale qui sera parcourue en tant que tableau 
	 * 
	 * @return mixed
	 */
	public static function get($key,$array=null) {
		$array = ($array!==null) ? $array : 'acid';
		
		$array_path = self::parseArray($key);
		return self::parse($array_path,$GLOBALS[$array]);
	}
	
	/**
	 *  Définit une variable de configuration
	 *
	 * @param array $key
	 * @param mixed $value
	 * @param string $array nom de la variable globale qui sera utilisée/altérée en tant que tableau 
	 *  
	 */
	public static function set($key,$value,$array=null) {
		$array = ($array!==null) ? $array : 'acid';
		$array_path = self::parseArray($key);
		
		if (count($array_path)) {
			$cur_tab = &$GLOBALS[$array];
			foreach ($array_path as $k) {
				if ( (!isset($cur_tab[$k])) || (!is_array($cur_tab[$k])) ) {
					$cur_tab[$k] = null;
				}
				$cur_tab = &$cur_tab[$k];
			}
			
			$cur_tab = $value;
		}
	}
	
	/**
	 *  Ajoute un élément dans une variable de configuration
	 *
	 * @param array $key
	 * @param mixed $value
	 * @param string $array nom de la variable globale qui sera utilisée/altérée en tant que tableau 
	 *  
	 */
	public static function add($key,$value,$array=null) {
		$array = ($array!==null) ? $array : 'acid';
		$array_path = self::parseArray($key);
		
		if (count($array_path)) {
			$cur_tab = &$GLOBALS[$array];
			$num = count($array_path);
			$c = 1;
			foreach ($array_path as $k) {
				if ( (!isset($cur_tab[$k])) || (!is_array($cur_tab[$k])) ) {
					if ( (!isset($cur_tab[$k])) || ($c != $num)  ) {
						$cur_tab[$k] = null;
					}
				}
				$cur_tab = &$cur_tab[$k];
				$c++;
			}
			
			if (is_string($cur_tab))  {
				$cur_tab .= $value;
			}elseif (is_numeric($cur_tab))  {
				$cur_tab += $value;
			}else{
				$cur_tab[] = $value;
			}
		}
	}

	/**
	 *  Efface une variable de configuration
	 *
	 * @param string $key
	 * @param string $array nom de la variable globale qui sera utilisée/altérée en tant que tableau 
	 * 
	 */
	public static function kill($key,$array=null) {
		$array = ($array!==null) ? $array : 'acid';
		if (self::exist($key,$array)) {
			$array_path = self::parseArray($key);
			$cur_tab = &$GLOBALS[$array];
			foreach ($array_path as $k) {
				$cur_tab = &$cur_tab[$k];
			}
			self::set($key,null,$array);
			unset($cur_tab);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 *  Test l'existence d'une variable de configuration
	 *
	 * @param string $key
	 * @param string $array nom de la variable globale qui sera parcourue en tant que tableau 
	 * @return bool
	 */
	public static function exist($key,$array=null) {
		$tab = Acid::get($key,$array);
		return isset($tab);
	}
		
	/**
	 *  Test si une variable de configuration est vide
	 *
	 * @param string $key
	 * @param string $array nom de la variable globale qui sera parcourue en tant que tableau 
	 * @return bool
	 */
	public static function isEmpty($key,$array=null) {
		$tab = Acid::get($key,$array);
		return empty($tab);
	}
	
	/**
	 *  Récupère une variable de configuration
	 *
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public static function sessGet($key) {
		return  self::get($key,'sess');
	}
	
	/**
	 *  Définit une variable de configuration
	 *
	 * @param array $key
	 * @param mixed $value
	 *  
	 */
	public static function sessSet($key,$value) {
		return  self::set($key,$value,'sess');
	}
	
	/**
	 *  Ajoute un élement dans une variable de configuration
	 *
	 * @param array $key
	 * @param mixed $value
	 *  
	 */
	public static function sessAdd($key,$value) {
		return  self::add($key,$value,'sess');
	}
	
	/**
	 *  Test l'existence d'une variable de session
	 *
	 * @param string $key
	 * 
	 * @return bool
	 */
	public static function sessExist($key) {
		return self::exist($key,'sess');
	}
		
	/**
	 *  Test si une variable de session est vide
	 *
	 * @param string $key
	 * 
	 * @return bool
	 */
	public static function sessEmpty($key) {
		return self::isEmpty($key,'sess');
	}
	
	/**
	 *  Supprime une variable de session
	 *
	 * @param string $key
	 * 
	 * @return bool
	 */
	public static function sessKill($key) {
		return self::kill($key,'sess');
	}
	
	/**
	 *  Active un timer
	 *
	 * @param string $key
	 * 
	 */
	public static function timerStart($key) {
		Acid::add('counter:'.$key,1);
		Acid::add('timer:'.$key.':opened',1);
		if (Acid::isEmpty('timer:'.$key.':current:start')) {
			Acid::set('timer:'.$key.':current:start',AcidTimer::getmicrotime());
		}
	}
	
	/**
	 *  Desactive un Timer
	 *
	 * @param string $key
	 * 
	 */
	public static function timerStop($key) {
		if (!Acid::isEmpty('timer:'.$key.':current:start')) {
			Acid::add('timer:'.$key.':opened',-1);
			if (Acid::get('timer:'.$key.':opened')===0) {
				Acid::set('timer:'.$key.':current:stop',AcidTimer::getmicrotime());
				Acid::add('timer:'.$key.':global',Acid::get('timer:'.$key.':current'));
				Acid::set('timer:'.$key.':current',array('start'=>0,'stop'=>0));
			}
		}
	}

	/**
	 * Retourne le resultat du Timer
	 * @param string $key identifiant du timer
	 * @param boolean $convert si true, retourne une valeur en ms
	 * @return number
	 */
	public static function timerSum($key,$convert=true) {
		$timer = 0;
		if (!Acid::isEmpty('timer:'.$key.':global')) {	
			foreach (Acid::get('timer:'.$key.':global') as $tab) {
				$timer += $tab['stop'] - $tab['start'];
			}
		}
		
		
		return $convert ? $timer*1000 : $timer;
	}
	
	/**
	 *  Retourne le Compteur 
	 *
	 * @param string $key
	 * 
	 */
	public static function counter($key) {
		return Acid::get('counter:'.$key);
	}
}
