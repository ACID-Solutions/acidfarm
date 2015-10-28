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
		if (is_array($value) && $value) {
			$return = true;
			foreach ($value as $keyword) {
				self::addToMetaKeys($keyword);
			}
			return $return;
		}

		return  self::setMetaKeys($value,true);
	}

	/**
	 * Définit le meta image ,permettant aux applications tierces de connaitre l'image associée à la page (réseaux sociaux)
	 * @param string $value valeur
	 * @param string $add_mode si vrai, ajoute $value à la valeur actuelle
	 */
	public static function setMetaImage($value, $add_mode=false) {
		if ($add_mode && Conf::exist('seo:page_image')) {
			Conf::add('seo:page_image', $value);
		}else{
			Conf::set('seo:page_image', $value);
		}
	}

	/**
	 * Retourne la meta image associés à $key
	 * @param string $key
	 * @return mixed
	 */
	public static function defaultMetaImage($key) {
		if (Conf::exist('meta:image:'.Acid::get('lang:current').':'.$key)) {
			return Conf::get('meta:image:'.Acid::get('lang:current').':'.$key);
		}

		return Conf::get('meta:image:'.Acid::get('lang:default').':'.$key);
	}

	/**
	 * Définit les Metas par défaut en fonction de la clé en entrée
	 * @param string $seo_key
	 */
	public static function executeMetaDefault($seo_key=null) {
		if (Conf::defaultMetaKeys('default')) {
	   		Conf::setMetaKeys(Conf::defaultMetaKeys('default'));
		}

		if (Conf::defaultMetaDesc('default')) {
		    Conf::setMetaDesc(Conf::defaultMetaDesc('default'));
		}

		if (Conf::defaultPageTitle('default')) {
		    Conf::setPageTitle(Conf::defaultPageTitle('default'));
		}

		if (Conf::defaultMetaImage('default')) {
			Conf::setMetaImage(Conf::defaultMetaImage('default'));
		}

		if ($seo_key) {
			if (Conf::defaultMetaKeys($seo_key)) {
				Conf::setMetaKeys(Conf::defaultMetaKeys($seo_key));
			}

			if (Conf::defaultMetaDesc($seo_key)) {
				Conf::setMetaDesc(Conf::defaultMetaDesc($seo_key));
			}

			if (Conf::defaultPageTitle($seo_key)) {
				Conf::setPageTitle(Conf::defaultPageTitle($seo_key));
			}
		}
	}

	/**
	 * Retourne le meta image
	 */
	public static function getMetaImage() {
		return Conf::exist('seo:page_image') ? Conf::get('seo:page_image') : '';
	}

	/**
	 * Génère l'ensemble des éléments SEO de la page
	 * @param array $title : tableau contenant les titres de la page dans les langues disponibles sous forme 'fr' => 'titre'
	 * @param array $desc : tableau contenant les descriptions déclinées dans les langues disponibles sous forme 'fr' => 'desc'
	 * @param string $meta_img_url : url de la meta image qui sera partagée sur les réseau sociaux
	 * @param boolean $use_default_keywords : ajout des meta keywords configurés par défaut dans 'sys/config' ou non
	 * @param array $added_meta_keywords : ajout manuel de meta_keywords supplémentaires
	 * @param string $generate_keywords_from_text : génération de meta_keywords en analysant le texte donné en paramètre en ajoutant les mots les plus utilisés. Les mots défini dans le fichier
	 */
	public static function SEOGen($title = array(), $desc = array(), $meta_img_url = null, $use_default_keywords = true, $added_meta_keywords = null, $generate_keywords_from_text = null){
		$cur_lang = Acid::get('lang:current');
		// *** META TITLE
		if($title){
			Conf::setPageTitle($title[$cur_lang]);
		}
		// *** META DESC
		if(!empty($desc)){
			$desc = $desc[$cur_lang];
			$desc = htmlspecialchars($desc); // On rend inoffensives les balises HTML que le visiteur a pu rentrer
			$desc = html_entity_decode($desc);
			Conf::setMetaDesc(AcidVarString::split($desc, 160, '...'));
		}
		// *** META IMAGE (réseaux sociaux)
		if($meta_img_url){
			Conf::setMetaImage($meta_img_url);
		}
		// *** META KEYWORDS
		// ajout des keywords de base définis en config
		$default_keywords = Conf::get('meta:keywords:'.$cur_lang);
		if($use_default_keywords && !empty($default_keywords)){
			$base = implode(', ', Conf::get('meta:keywords:'.Acid::get('lang:current')));
			Conf::setMetaKeys($base, true);
		}
		// inclusion des mots-clés ajoutés manuellement
		if(!empty($added_meta_keywords)){
			$added_kw = $added_meta_keywords;
			$added_kw_str = implode(', ', $added_kw);
			Conf::setMetaKeys($added_kw_str, true);
		}
		// ajout de la chaine de caractère à traiter
		if($generate_keywords_from_text){
			$generated_keys = Conf::keywordGenerate($generate_keywords_from_text);
			Conf::setMetaKeys($generated_keys, true);
		}
	}

	/**
	 * Génère des mots clés pertinents à partir de la chaine de caractère passée en paramètres
	 * @param string $string : chaine de caractères à analyser
	 * @param int $nb_occurence : nombre de répétitions nécessaires pour ajouter le mot en tant que mot clé. Par défaut : 2.
	 * @param int $word_size : taille minimale nécessaire afin que le mot soit pris en compte. Par défaut : 2.
	 */
	public static function keywordGenerate($string, $nb_min_occurence = 2, $word_min_size = 2){
		$cur_lang = Acid::get('lang:current');
		// récupération des mots exclus des mots clés
		$excluded = file_get_contents(SITE_PATH.'sys/kw_excluded.txt');
		$excluded = explode("\n", $excluded);
		// suppression des accents
		$string = Lib::removeAccents($string);
		// suppression de la ponctuation
		$string = str_replace(Conf::get('seo:ponctuation'), " ", html_entity_decode($string));
		// var_dump($string); // DEBUG
		// transformation de la chaine de caractère nettoyée en tableau
		$elts = explode(' ', $string);
		// on récupère les mots clés de base pour ne pas les ajouter deux fois
		$default_keywords = Conf::get('meta:keywords:'.$cur_lang);
		if(!empty($default_keywords)){
			$selected = Conf::get('meta:keywords:'.$cur_lang);
			$selected_str = implode(',', $selected);
			$selected = explode(',', $selected_str);
			if($selected) {
				foreach ($selected as $key => $word) {
					$selected[$key] = trim($word);
				}
			}
		}
		else{
			$selected = array();
		}

		// var_dump($selected); // DEBUG
		$ii = 0;
		$added_keywords = '';
		foreach ($elts as $elt){
			$elt = trim($elt);
			$str = str_replace($elt, "", $elts, $count);
			if(($count >= $nb_min_occurence) && (strlen($elt) >= $word_min_size) && !in_array($elt, $selected)
					&& !in_array(mb_strtoupper($elt), $excluded) && ctype_print($elt) && !ctype_space($elt) && !ctype_punct($elt)){
				$ii++;
				// var_dump($elt); //DEBUG
				$added_keywords .= $ii < 2 ? $elt : ', '.$elt;
				$selected[] = $elt;
			}
		}
		// var_dump($added_keywords); exit(); // DEBUG

		return $added_keywords;
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
