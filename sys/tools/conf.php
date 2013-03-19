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
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Outil de Configuration
 * @package   Model
 */
class Conf {
	
	// Site Configuration
	
	/**
	 * Retourne une variable de configuration
	 * @param string $key acheminement vers la variable  - exemple : 'macat:mavariable' pour $acidconf['macat']['mavariable'] 
	 * @return mixed
	 */
	public static function get($key) {
		return  Acid::get($key,'acidconf');
	}
	
	/**
	 * Définit une variable de configuration
	 * @param string $key acheminement vers la variable - exemple : 'macat:mavariable' pour $acidconf['macat']['mavariable'] 
	 * @param mixed $value valeur
	 */
	public static function set($key,$value) {
		return  Acid::set($key,$value,'acidconf');
	}
	
	
	/**
	 * Teste l'existence d'une variable de configuration
	 * @param string $key acheminement vers la variable - exemple : 'macat:mavariable' pour $acidconf['macat']['mavariable']
	 * @return bool
	 */
	public static function exist($key) {
		return Acid::exist($key,'acidconf');
	}
		
	/**
	 * Teste l'existence et l'état vrai d'une variable de configuration
	 * @param string $key acheminement vers la variable - exemple : 'macat:mavariable' pour $acidconf['macat']['mavariable']
	 * @return bool
	 */
	public static function isEmpty($key) {
		return Acid::isEmpty($key,'acidconf');
	}
	
	/**
	 * Efface une variable de configuration
	 * @param string $key acheminement vers la variable - exemple : 'macat:mavariable' pour $acidconf['macat']['mavariable']
	 */
	public static function kill($key) {
		return Acid::kill($key,'acidconf');
	}
	
	/**
	 * Ajoute $value à une variable de configuration
	 * @param string $key acheminement vers la variable - exemple : 'macat:mavariable' pour $acidconf['macat']['mavariable']
	 * @param mixed $value valeur à ajouter
	 */
	public static function add($key,$value) {
		return  Acid::add($key,$value,'acidconf');
	}
	
	
	
	// Metas
	//page title
	
	/**
	 * Définit le meta title
	 * @param string $value valeur
	 * @param string $add_mode si vrai, ajoute $value à la valeur actuelle
	 */
	public static function setPageTitle($value,$add_mode=false) {
		if ($add_mode && Conf::exist('seo:page_title')) {
			Conf::add('seo:page_title', $value);
		}else{
			Conf::set('seo:page_title', $value);
		}
	}
	
	/**
	 * Retourne le meta title
	 */
	public static function getPageTitle() {
		return Conf::exist('seo:page_title') ? Conf::get('seo:page_title') : '';
	}
	
	/**
	 * Retourne le meta title par défault associé à $key
	 * @param string $key identifiant de page
	 */
	public static function defaultPageTitle($key) {
		if (Conf::exist('meta:title:'.Acid::get('lang:current').':'.$key)) {
			return Conf::get('meta:title:'.Acid::get('lang:current').':'.$key);
		}
		
		return Conf::get('meta:title:'.Acid::get('lang:default').':'.$key);
	}
	
	/**
	 * Définit si le titre sera accompagné ou non des titres de droite et gauche
	 * @param bool $alone 
	 */
	public static function setPageTitleAlone($alone=true) {
		Conf::set('seo:page_title_alone', $alone);
	}
	
	/**
	 * Retourne si le titre sera accompagné ou non des titres de droite et gauche
	 * @return bool 
	 */
	public static function getPageTitleAlone() {
		return Conf::exist('seo:page_title_alone') ? Conf::get('seo:page_title_alone') : '';
	}
	
	
	//meta description
	
	/**
	 * Définit le meta desc
	 * @param string $value valeur
	 * @param string $add_mode si vrai, ajoute $value à la valeur actuelle
	 */
	public static function setMetaDesc($value,$add_mode=false) {
		if ($add_mode && Conf::exist('seo:meta_desc')) {
			Conf::add('seo:meta_desc', $value);
		}else{
			Conf::set('seo:meta_desc', $value);
		}
	}
	
	/**
	 * Définit le meta desc de base
	 * @param string $value valeur
	 * @param string $add_mode si vrai, ajoute $value à la valeur actuelle
	 */
	public static function setMetaDescBase($value,$add_mode=false) {
		if ($add_mode && Conf::exist('seo:meta_desc_base')) {
			Conf::add('seo:meta_desc_base', $value);
		}else{
			Conf::set('seo:meta_desc_base', $value);
		}
	}
	
	/**
	* Définit le meta desc en préfixe
	* @param string $value valeur
	* @param string $add_mode si vrai, ajoute $value à la valeur actuelle
	*/
	public static function setMetaDescStart($value,$add_mode=false) {
		if ($add_mode && Conf::exist('seo:meta_desc_start')) {
			Conf::add('seo:meta_desc_start', $value);
		}else{
			Conf::set('seo:meta_desc_start', $value);
		}
	}
	
	/**
	 * Retourne le meta desc
	 * @return string
	 */
	public static function getMetaDesc() {
		return Conf::exist('seo:meta_desc') ? Conf::get('seo:meta_desc') : '';
	}

	/**
	 * Retourne le meta desc de base
	 * @return string
	 */
	public static function getMetaDescBase() {
		return Conf::exist('seo:meta_desc_base') ? Conf::get('seo:meta_desc_base') : '';
	}
	
	/**
	 * Retourne le meta desc en prefixe
	 * @return string
	 */
	public static function getMetaDescStart() {
		return Conf::exist('seo:meta_desc_start') ? Conf::get('seo:meta_desc_start') : '';
	}
	
	/**
	 * Retourne le meta desc par défaut associé à $key
	 * @param string $key identifiant de page
	 * @return string
	 */
	public static function defaultMetaDesc($key) {
		if (Conf::exist('meta:description:'.Acid::get('lang:current').':'.$key)) {
			return Conf::get('meta:description:'.Acid::get('lang:current').':'.$key);
		}
	
		return Conf::get('meta:description:'.Acid::get('lang:default').':'.$key);
	}
	
	/**
	 * Ajoute au meta desc
	 * @param string $value valeur
	 */
	public static function addToMetaDesc($value) {
		return  self::setMetaDesc($value,true);
	}
	
	
	//meta Keywords
	
	/**
	 * Définit les meta keywords
	 * @param array $value tableau contenant les mots-clés
	 * @param string $add_mode si vrai, ajoute $value à la valeur actuelle
	 */
	public static function setMetaKeys($value,$add_mode=false) {
		if (! Conf::exist('seo:meta_keys') ) {
			Conf::set('seo:meta_keys', array());
		}
		
		if ($add_mode) {
			Conf::add('seo:meta_keys', $value);
		}else{
			Conf::set('seo:meta_keys', $value);
		}
	}
	
	/**
	 * Retourne les meta keywords
	 * @return array
	 */
	public static function getMetaKeys() {
		return Conf::exist('seo:meta_keys') ? Conf::get('seo:meta_keys') : array();
	}
	

	/**
	 * Retourne les meta keywords associés à $key
	 * @param string $key identifiant de page
	 * @return array
	 */
	public static function defaultMetaKeys($key) {
		if (Conf::exist('meta:keywords:'.Acid::get('lang:current').':'.$key)) {
			return Conf::get('meta:keywords:'.Acid::get('lang:current').':'.$key);
		}
	
		return Conf::get('meta:keywords:'.Acid::get('lang:default').':'.$key);
	}
	
	/**
	 * Ajoute $value aux meta keywords
	 * @param mixed $value valeur
	 */
	public static function addToMetaKeys($value) {
		return  self::setMetaKeys($value,true);
	}

	
	// Content
	/**
	 * Définit le contenu du site
	 * @param string $value valeur
	 * @param string $add_mode si vrai, ajoute $value à la valeur actuelle
	 */
	public static function setContent($value,$add_mode=false) {
		if ($add_mode) {
			$GLOBALS['html'] = $GLOBALS['html'] . $value;
		}else{
			$GLOBALS['html'] = $value;
		}
	}
	
	/**
	 * Ajoute au contenu du site
	 * @param string $value valeur
	 */
	public static function addToContent($value) {
		return  self::setContent($value,true);
	}
	
	/**
	 * Retourne le contenu du site
	 * @return string
	 */
	public static function getContent() {
		return  isset($GLOBALS['html']) ? $GLOBALS['html'] : '';
	}
	
	
	// Ariane
	
	/**
	 * Definit le tableau representatif du fil d'Ariane
	 * @param string $ariane tableau representatif du fil d'ariane - exemple : array('label'=>url,'labelfils'=>urlfils)
	 * @return string
	 */
	public static function setAriane($ariane) {
		 Acid::set('ariane',$ariane);
	}
	
	/**
	 * Ajoute au fil d'Ariane
	 * @param string $name Nom lié à $url
	 * @param string $url Url
	 */
	public static function addToAriane($name,$url) {
		 Acid::add('ariane',array('name'=>$name,'url'=>$url));
	}
	
	/**
	 * Retourne le tableau representatif du fil d'Ariane
	 * @return array
	 */
	public static function getAriane() {
		return Acid::get('ariane');
	}

}
