<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Librairie de fonctions diverses
 * @package   Model
 */
class Lib {

	/**
	 * Retourne une valeur stockée dans $_POST en s'affranchissant des contrôles d'existence, return $def si n'existe pas
	 * @param string $key la clé
	 * @param mixed $def la valeur par défaut
	 * @return mixed
	 */
	public static function getInPost($key,$def=null) {
		return self::getIn($key,$_POST,$def);
	}

	/**
	 * Retourne une valeur stockée dans $tab en s'affranchissant des contrôles d'existence, return $def si n'existe pas
	 * @param string $key la clé
	 * @param array $tab le tableau à parcourir
	 * @param mixed $def la valeur par défaut
	 * @return mixed
	 */
	public static function getIn($key,$tab,$def=null) {
		return isset($tab[$key]) ? $tab[$key] : $def;
	}


	/**
	 * Retourne le type de materiel mobile utilisé par l'utilisateur ou $def s'il n'est pas trouvé
	 * @param string $def valeur de retour si pas de materiel mobile identifié
	 * @return mixed
	 */
	public static function mobileDevice($def=false) {
		$searches = array('iPad','Android','BlackBerry','iPhone','Palm');

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			foreach ($searches as $search) {

				if ((stripos($_SERVER['HTTP_USER_AGENT'],$search) !== false)) {
					return $search;
				}
			}
		}

		return $def;
	}

	/**
	 * Retourne le type de navigateur utilisé par l'utilisateur ou $def s'il n'est pas trouvé
	 * @param string $def valeur de retour si pas de navigateur identifié
	 * @return mixed
	 */
	public static function navDevice($def=false) {
		$searches = array('MSIE','Firefox','Chrome','Safari','Opera');

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			foreach ($searches as $search) {

				if ((stripos($_SERVER['HTTP_USER_AGENT'],$search) !== false)) {
					return $search;
				}
			}
		}

		return $def;
	}

	/**
	 * Parse une url sous forme de tableau
	 * @param string $url url a parser
	 * @return mixed
	 */
	public static function parseUrl($url) {
		return explode('/',substr(parse_url($url, PHP_URL_PATH),strlen(Acid::get('url:folder'))));
	}


	/**
	 * Retourne $js encadré par les balises javascript
	 * @param string $js votre script
	 * @return string
	 */
	public static function getJsCaller($js) {
		return '<script type="text/javascript">' . "\n" . '<!--' . "\n" . $js . "\n" . '-->' . "\n" ."</script>";
	}


	/**
	 * Echappe une $text pour l'intégrer dans une chaine javascript
	 * @param string $text votre texte
	 * @param string $echap echappement utilisé
	 * @return string
	 */
	public static function getJsFormat($text, $echap='"') {
		$text = str_replace('\\','\\\\',$text);
		$text = str_replace($echap,'\\'.$echap,$text);
		$text = str_replace("\n",'',$text);

		return $text;
	}


	/**
	 * Retourne un tableau associatif ( clé primaire => intitulé ) du module en entrée
	 * @param string $module Nom de la classe de l'AcidModule
	 * @param string $field champs utilisé pour l'intitulé
	 * @param string $field2 champs secondaire utilisé pour l'intitulé
	 * @param string $start separation de $field1 et $field2
	 * @param string $stop suffix de l'initulé
	 * @return array
	 */
	public static function getCorrespondance($module,$field,$field2=null,$start=' (',$stop=') ') {
		$obj=Acid::mod($module);
		$res = $obj->dbList();

		$tab = array();
		$id = $obj->tblId();
		foreach ($res as $k=>$v) {
			$val = $v[$field];
			if ($field2) {

				$val .= $start.$v[$field2].$stop;

			}

			$tab[$v[$id]] =  $val;
		}

		return $tab;
	}


	/**
	 * Retourne une liste de caractères spéciaux
	 * @return string
	 */
	public static function specialchars() {
		$mytab = get_html_translation_table(HTML_ENTITIES);
		$treat = array();
		foreach ($mytab as $k=>$val) {
			$treat[utf8_encode($k)] = $val;
		}
		return implode(array_keys($treat));
	}


	/**
	 * Retourne un texte UTF-8 à partir du contenu html saisi en entrée
	 * @param string $txt contenu html
	 * @return string
	 */
	public static function htmlToUtf8($txt) {

		if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
			$mytab = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT | ENT_HTML401 ,'ISO-8859-1');
		}else{
			$mytab = get_html_translation_table(HTML_ENTITIES);
		}

		$treat = array();
		foreach ($mytab as $k=>$val) {
			$treat[utf8_encode($k)] = $val;
		}
		$my_str = str_replace($mytab,array_keys($treat),$txt);
		return strip_tags($my_str);
	}


	/**
	 * Fait l'enumération de chaque mot présent dans le texte en entrée
	 * @param string $txt texte
	 * @param string $get_above si une valeur est précisée, ne retourne que les mots ayant au moins le même nombre d'occurrences que la valeur saisie
	 * @param string $min_lenght si une valeur est précisée, ne retourne que les mots ayant au moins le même nombre de caractères que la valeur saisie
	 * @return string
	 */
	public static function getWordsCount($txt,$get_above=null,$min_lenght=4)
	{
		$words = array();

		$txt = self::htmlToUtf8($txt); //$txt = strip_tags(utf8_decode(utf8_encode($txt)));

		$tab = str_word_count ($txt ,2 ,self::specialchars());

		if ($tab) {
			$words = array_count_values($tab);
		}

		if (count($words)) {
			if (($min_lenght) || ($get_above)) {
				$new_words = array();
				foreach ($words as $word => $count) {
					$get = true;

					if ($get_above) {
						if ($count < $get_above) {
							$get = false;
						}
					}

					if ($min_lenght) {
						if (strlen($word) < $min_lenght) {
							$get = false;
						}
					}

					if (strpos($word,'&')!==false) {
						$get = false;
					}

					if ($get) {
						$new_words[$word] = $count;
					}
				}

				if (count($new_words)) {
					asort($new_words);
					return array_reverse($new_words);
				}

			}else{
				asort($words);
				return array_reverse($words);
			}
		}


		return array();
	}
}