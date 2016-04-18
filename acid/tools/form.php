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
 * Outil AcidForm, Gestionnaire de formulaire
 * @package   Acidfarm\Tool
 */
class AcidForm {

	/*
	 * <form> properties
	 */

	/**
	 * @var float version html
	 */
	private $_html=5;

	/**
	 * @var string méthode du formulaire (post, get)
	 */
	private $_method;

	/**
	 * @var string url d'action du formulaire
	 */
	private $_action;

	/**
	 * @var string id du formulaire
	 */
	private $_id=null;

	/**
	 * @var string class du formulaire
	 */
	private $_classname=null;

	/**
	 * @var boolean true si le formulaire gère les fichiers
	 */
	private $_file_transfer=false;

	/**
	 *
	 * @var array attributs du formulaire
	 */
	private $_form_params=array();


	/**
	 * Form components
	 * @var array éléments du formulaire
	 */
	private $_components=array();


	/**
	 * Constructeur AcidForm
	 *
	 * @param string $method Type de formulaire. (Get/Post)
	 * @param string $action Cible du formulaire.
	 * @param float $version version html.
	 *
	 */
	function __construct ($method, $action, $version=null) {
		$this->_method = $method;
		$this->_action = $action;

		if ($version===null) {
			if (Acid::get('tpl:html:version')) {
				$this->_html = Acid::get('tpl:html:version');
			}
		}else{
			$this->_html = $version;
		}

	}

	/**
	 * Définit l'identifiant DOM du formulaire.
	 *
	 * @param string $id
	 */
	public function setFormId($id) {
		$this->_id = $id;
	}

	/**
	 * Définit la classe CSS du formulaire.
	 *
	 * @param string $classname
	 */
	public function setFormClass($classname) {
		$this->_classname = $classname;
	}

	/**
	 * Assigne des attributs au formulaire
	 * Si un attribut est déjà défini, écrase l'ancienne valeur.
	 *
	 * @param array $params Liste des attributs. ([paramètres]=>[valeurs])
	 */
	public function setFormParams($params) {
		foreach ($params as $key=>$val) {
			$this->_form_params[$key] = $val;
		}
	}

	// TOOL

	/**
	 * Retourne les paramètres du tableau en entrée sous forme d'une chaîne de caractères au format "Attribut HTML".
	 *
	 * @param array $params Liste des paramètres.
	 *
	 * @return string
	 */
	public static function getParams($params) {
		$str = '';
		$params = (array) $params;
		foreach ($params as $key=>$val) $str .= ' ' . $key . '="' . $val . '"';
		return $str;
	}

	// HIDDEN
	/**
	 * Retourne une entrée formulaire de type hidden.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : arrray()
	 *
	 * @return string
	 */
	public static function hidden ($name, $value, $params=array()) {
		return 	'<input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value).'"' .
				self::getParams($params).' />';
	}

	// TEXT
	/**
	 * Retourne une entrée formulaire de type text.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param string $size Taille du champ. - Défaut : NULL
	 * @param string $maxlength Nombre de caractères maximum. - Défaut : NULL
	 * @param array $params Liste des paramètes. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @return string
	 */
	public static function text ($name, $value, $size=null, $maxlength=null, $params=array()) {
		return 	'<input type="text" name="'.$name.'" value="'.htmlspecialchars($value).'"' .
				($size === null ? '' : ' size="'.$size.'"') .
				($maxlength === null ? '' : ' maxlength="'.$maxlength.'"') .
				self::getParams($params).' />';
	}

	// PASSWORD
	/**
	 * Retourne une entrée formulaire de type password.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param string $size Taille du champ. - Défaut : NULL
	 * @param string $maxlength Nombre de caractères maximum. - Défaut : NULL
	 * @param array $params Liste des paramètes. ([paramètres]=>[valeurs]) - Défaut : array()
	 *
	 * @return string
	 */
	public static function password ($name, $value, $size=null, $maxlength=null, $params=array()) {
		return 	'<input type="password" name="'.$name.'" value="'.htmlspecialchars($value).'"' .
				($size === null ? '' : ' size="'.$size.'"') .
				($maxlength === null ? '' : ' maxlength="'.$maxlength.'"') .
				self::getParams($params).' />';
	}

	// CHECKBOX
	/**
	 * Retourne une entrée de type checkbox.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param bool $checked True si selectionné, false sinon.
	 * @param string $label intitulé de la checkbox
	 * @param array $params  - Défaut : array()
	 *
	 * @return string
	 */
	public static function checkbox ($name, $value, $checked, $label=null, $params=array()) {
		$label = ($label!==null) ? ('<span class="checkbox_label">'.$label.'</span>') : '';
		return	'<label><span class="checkbox_input"><input type="checkbox" name="'.$name.'" value="'.htmlspecialchars($value).'"'.
					($checked ? ' checked="checked"' : '').self::getParams($params).' /></span>'.$label.'</label>';
	}

	// RADIO
	/**
	 * Retourne une entrée formulaire de type radio.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur selectionnée.
	 * @param array $elts  Liste des éléments du radio. ([noms]=>[valeurs])
	 * @param array $params  - Défaut : array()
	 *
	 * @return string
	 */
	public static function radio ($name, $value, $elts, $params=array()) {

		$params_str = self::getParams($params);

		$output = '';
		foreach ($elts as $val=>$label) {
			$output .= '<label><span class="radio_input"><input type="radio" name="'.$name.'" value="'.htmlspecialchars($val).'" '.
							($value == $val ? ' checked="checked"' : '').
							$params_str.' /><span class="radio_image"></span></span><span class="radio_label">' . $label . ' </span></label>';
		}
		return $output;
	}

	// TEXTAREA
	/**
	 * Retourne une entrée formulaire de type textarea.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param int $cols  Largeur. - Défaut : 80
	 * @param int $rows Hauteur. - Défaut : 5
	 * @param array $params  - Défaut : array()
	 *
	 * @return string
	 */
	public static function textarea ($name, $value, $cols=80, $rows=5, $params=array()) {
		return	'<textarea name="'.$name.'" rows="'.$rows.'" cols="'.$cols.'"'.self::getParams($params).'>' .
				htmlspecialchars($value) . '</textarea>';
	}

	// SELECT
	/**
	 * Retourne une entrée formulaire de type select.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param array $elts Liste des options du select.
	 * @param array $params Liste des paramètres.  - Défaut : array()
	 * @param int $size Taille de la liste. Si 0, on prend le nombre d'élément. - Défaut : 0
	 * @param bool $multiple Si true, on autorise la selection multiple. - Défaut : false
	 *
	 * @return string
	 */
	public static function select ($name, $value, $elts, $params=array(), $size=0, $multiple=false) {

		if ($size === 0) $size = count($elts);

		$selecteds = explode("\n",$value);

		$options = '';
		foreach ($elts as $val=>$label) {
			$options .= '	<option value="'.htmlspecialchars($val).'"' .
							(in_array($val,$selecteds) ? ' selected="selected"' : '') .
						'>'.htmlspecialchars($label).'</option>' . "\n";
		}
		return	'<select name="'.$name.($multiple ? '[]' : '').'"'.
					($size > 1 ? ' size="'.$size.'"' : '').
					($multiple ? ' multiple="multiple"' : '').
					self::getParams($params) . '>' . "\n" .
						$options .
				'</select>';
	}


	/**
	 * Retourne une entrée formulaire de type file.
	 *
	 * @param string $name Nom.
	 * @param array $params Liste des paramètres. - Défaut : array()
	 * @return string
	 */
	public static function file ($name, $params=array()) {
		return 	'<input type="file" name="'.$name.'" value=""' .
				self::getParams($params).' />';
	}

	// BUTTON
	/**
	 * Retourne une entrée formulaire de type button.
	 *
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param array $params Liste des paramètres.  - Défaut : array()
	 * @return string
	 */
	public static function button ($name, $value, $params=array()) {
		return	'<input type="button" name="'.$name.'" value="'.$value.'"'.self::getParams($params).' />';
	}

	// SUBMIT
	/**
	 * Retourne une entrée formulaire de type submit.
	 *
	 * @param string $value Valeur.
	 * @param string $params Liste des paramètres.  - Défaut : array()
	 *
	 * @return string
	 */
	public static function submit ($value, $params=array()) {
		return	'<input type="submit" name="submit" value="'.$value.'"'.self::getParams($params).' />';
	}


	// Image
	/**
	 * Retourne une entrée formulaire de type image.
	 *
	 * @param string $src url de l'image.
	 * @param string $value Valeur.
	 * @param string $params Liste des paramètres.  - Défaut : array()
	 *
	 * @return string
	 */
	public static function image ($src,$value,$params=array()) {

		$name = !empty($params['name']) ? $params['name'] : 'submit';

		return	'<input type="image" name="'.$name.'" src="'.$src.'"  alt="'.$value.'"'.self::getParams($params).' />';
	}

	/**
	 * Retourne un couple d'entrées select, permettant de renseigner une heure.
	 *
	 * @param string $key Prefixe pour les noms. ([$key]_h, [$key]_i)
	 * @param string $atime Heure selectionnée. - Défaut : 00:00
	 * @param int $step Pas pour l'incrémentation des minutes.  - Défaut : 1
	 *
	 * @return string
	 */
	public static function hourHI($key,$atime='00:00',$step=1) {
		if (!preg_match('`[0-9]{2}:[0-9]{2}`',$atime)) {
			$atime = '00:00';
		}
		list($hour,$minute) = explode(':',$atime);

		if ($step < 1) {
			$step = 1;
		}

		$fh = $fm = '';

		for ($i=0;$i<24;$i++) {
			$s = $i == $hour ? ' selected="selected"' : '';
			$fh .= '	<option value="'.sprintf('%02d',$i).'"'.$s.'>'.sprintf('%02d',$i).'</option>' . "\n";
		}

		for ($i=0;$i<60;$i=$i+$step) {
			$s = $i == $minute ? ' selected="selected"' : '';
			$fm .= '	<option value="'.sprintf('%02d',$i).'"'.$s.'>'.sprintf('%02d',$i).'</option>' . "\n";
		}


		return	'<select name="'.$key.'_h">' . "\n" .
				$fh .
				'</select>' . "\n" .
				'<select name="'.$key.'_i">' . "\n" .
				$fm . "\n" .
				'</select>' . "\n";
	}

	/**
	 * Retourne un triplet d'entrées select, permettant de renseigner une date.
	 *
	 * @param string $key Prefixe pour les noms. ([$key]_d, [$key]_m,[$key]_y)
	 * @param string $adate Date selectionnée. - Défaut : 0000-00-00
	 * @param int min_year Année de départ. Si NULL, devient 1900  - Défaut :NULL
	 * @param int min_year Année de fin. Si NULL, devient l'année courante  - Défaut :NULL
	 *
	 * @return string
	 */
	public static function dateYMD($key,$adate='0000-00-00',$min_year=null,$max_year=null) {
		global $lang;

		if (!preg_match('`[0-9]{4}-[0-9]{2}-[0-9]{2}`',$adate))
			$adate = '0000-00-00';
		list($y,$m,$d) = explode('-',$adate);

		$fd = $fm = $fy = '';

		for ($i=1;$i<=31;$i++) {
			$s = $d == $i ? ' selected="selected"' : '';
			$fd .= '	<option value="'.sprintf('%02d',$i).'"'.$s.'>'.$i.'</option>' . "\n";
		}

		for ($i=1;$i<=12;$i++) {
			$s = $m == $i ? ' selected="selected"' : '';
			$fm .= '	<option value="'.sprintf('%02d',$i).'"'.$s.'>'.$lang['date']['month'][$i].'</option>' . "\n";
		}

		if ($min_year === null) {
			$min_year = 1900;
		}
		if ($max_year === null) {
			$max_year = date('Y');
		}

		for ($i=$max_year;$i>=$min_year;$i--) {
			$s = $y == $i ? ' selected="selected"' : '';
			$fy .= '	<option value="'.sprintf('%04d',$i).'"'.$s.'>'.$i.'</option>' . "\n";
		}

		return	'<select name="'.$key.'_d">' . "\n" .
				$fd .
				'</select>' . "\n" .
				'<select name="'.$key.'_m">' . "\n" .
				$fm . "\n" .
				'</select>' . "\n" .
				'<select name="'.$key.'_y">' . "\n" .
				$fy . "\n" .
				'</select>' . "\n";

	}


	// Add a component to the form
	/**
	 * Ajoute une entrée au formulaire.
	 *
	 * @param string $type Type.
	 * @param string $name Nom.
	 * @param string $label Etiquette.
	 * @param string $html Milieu.
	 * @param string $start Préfixe.
	 * @param string $stop Suffixe.
	 * @param array $body_attrs attributs du cadre
	 */
	private function addComponent ($type, $name, $label, $html, $start, $stop, $body_attrs=array()) {

		$component = array(	'type'=>$type,
							//'name'=>$name,
							'label'=>$label,
							'html'=>$html,
							'start'=>$start,
							'stop'=>$stop,
							'body_attrs'=>$body_attrs
										);
		if ($name === null) $this->_components[] = $component;
		else $this->_components[$name] = $component;

	}

	// Table start
	/**
	 * Ajoute l'ouverture d'un tableau au formulaire.
	 *
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs])
	 *
	 */
	public function tableStart ($params=array()) {
		$this->_components[] = array('type'=>'table_start', 'params'=>$params);
	}

	// Table stop
	/**
	 * Ajoute la fermeture d'un tableau au formulaire.
	 */
	public function tableStop () {
		$this->_components[] = array('type'=>'table_stop');
	}

	/**
	 * Définit si le formulaire est multipart
	 * @param boolean $val
	 */
	public function setFileTranfer ($val) {
		$this->_file_transfer = $val;
	}

	// Body attributes
	/**
	 * Ajoute des attributs HTML à la ligne contenant
	 * @param string $name identifiant du champs
	 * @param array $attr les attributs
	 */
	public function addBodyAttributes ($name,$attr=array()) {
		if (isset($this->_components[$name])) {

			if (!isset($this->_components[$name]['body_attrs'])) {
				$this->_components[$name]['body_attrs'] = array();
			}

			foreach ($attr as $k => $val) {
				$this->_components[$name]['body_attrs'][$k] = $val;
			}

		}
	}

	// Adding free text
	/**
	 * Ajoute du texte libre au formaulire.
	 * @param string $label Etiquette.
	 * @param string $text Texte.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs])
	 * @param array $body_attrs attributs du cadre
	 * @param string $name identifiant
	 */
	public function addFreeText ($label,$text,$params=array(),$body_attrs=array(),$name=null) {
		$this->addComponent('free_text',$name,$label,$text,'','',$body_attrs);
	}

	/**
	 * Ajoute une entrée hidden au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addHidden ($label, $name, $value, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::hidden($name,$value,$params),$start,$stop,$body_attrs);
	}

	/**
	 * Ajoute une entrée texte au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param string $size Taille du champ. - Défaut : NULL
	 * @param string $maxlength Nombre de caractères maximum. - Défaut : NULL
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addText ($label, $name, $value, $size=null, $maxlength=null, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::text($name,$value,$size,$maxlength,$params),$start,$stop,$body_attrs);
	}

	/**
	 * Ajoute une entrée password au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param string $size Taille du champ. - Défaut : NULL
	 * @param string $maxlength Nombre de caractères maximum. - Défaut : NULL
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addPassword ($label, $name, $value, $size=null, $maxlength=null, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::password($name,$value,$size,$maxlength,$params),$start,$stop,$body_attrs);
	}


	/**
	 * Ajoute une entrée checkbox au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur selectionnée.
	 * @param string $text Texte Associé.
	 * @param bool $checked True si selectionné. Défaut : false
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addCheckbox ($label, $name, $value, $text, $checked=false, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::checkbox($name,$value,$checked,$text,$params),$start,$stop,$body_attrs);
	}

	/**
	 * Ajoute une entrée radio au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur selectionnée.
	 * @param array $elts Liste des éléments. ([noms]=>[valeurs])
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addRadio ($label, $name, $value, $elts, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::radio($name,$value,$elts,$params),$start,$stop,$body_attrs);
	}

	/**
	 * Ajoute une entrée textarea au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param int $cols Largeur.
	 * @param int $rows Hauteur.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addTextarea ($label, $name, $value, $cols, $rows, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::textarea($name,$value,$cols,$rows,$params),$start,$stop,$body_attrs);
	}

	/**
	 * Ajoute une entrée select au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur selectionnée.
	 * @param array $elts Liste des options. ([initulés]=>[valeurs])
	 * @param int $size Taille de la liste. Si 0, on prend le nombre d'élément. - Défaut : 0
	 * @param bool $multiple Si true, on autorise la selection multiple. - Défaut : false
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addSelect ($label, $name, $value, $elts, $size, $multiple=false, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::select($name,$value,$elts,$params,$size,$multiple),$start,$stop,$body_attrs);
	}

	/**
	 * Ajoute une entrée file au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param int $max_file_size Taille maximum du fichier.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addFile ($label, $name, $max_file_size, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->_file_transfer = true;
		$body_attrs_mfs = isset($body_attrs['MAX_FILE_SIZE']) ? $body_attrs['MAX_FILE_SIZE'] : $body_attrs;
		$this->addComponent('field',$name.'_max_file_size','',self::hidden('MAX_FILE_SIZE',$max_file_size),'','',$body_attrs_mfs);
		$this->addComponent('field',$name,$label,self::file($name,$params),$start,$stop,$body_attrs);
	}

	/**
	 *  Ajoute une entrée button au formulaire.
	 *
	 * @param string $label Etiquette.
	 * @param string $name Nom.
	 * @param string $value Valeur.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addButton ($label, $name, $value, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field',$name,$label,self::button($name,$value,$params),$start,$stop,$body_attrs);
	}

	/**
	 *  Ajoute une entrée submit au formulaire.
	 * @param string $label Etiquette.
	 * @param string $value Valeur.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addSubmit ($label, $value, $params=array(), $start='', $stop='', $body_attrs=array()) {
		$this->addComponent('field','submit',$label,self::submit($value,$params),$start,$stop,$body_attrs);
	}


	/**
	 *  Ajoute une entrée submit au formulaire.
	 * @param string $label Etiquette.
	 * @param string $src url de l'image.
	 * @param string $value Valeur.
	 * @param array $params Liste des paramètres. ([paramètres]=>[valeurs]) - Défaut : array()
	 * @param string $start Préfixe. - Défaut : Chaîne vide
	 * @param string $stop Suffixe. - Défaut : Chaîne vide
	 * @param array $body_attrs attributs du cadre
	 */
	public function addImage ($label, $src, $value, $params=array(), $start='', $stop='',$body_attrs=array()) {
		$this->addComponent('field','image',$label,self::image($src,$value,$params),$start,$stop,$body_attrs);
	}


	/**
	 * Retourne un composant du formulaire une fois mis en forme.
	 *
	 * @param string $name Nom du composant.
	 * @param string $out Format désiré. - Défaut : 'html'
	 *
	 * @return string
	 */
	public function getComponent ($name, $out='html') {
		if (isset($this->_components[$name])) {
			return $out === 'html' ? $this->_components[$name]['html'] :(
						$out === 'fullhtml' ? $this->_components[$name]['start'].$this->_components[$name]['html'].$this->_components[$name]['stop'] :(
							$out === 'label' ? $this->_components[$name]['label'] : 'AcidForm::ERROR'
			));
		}
	}

	/**
	 * Retourne le formulaire sous forme d'une chaîne de caractères formatée en HTML.
	 *
	 * @return string
	 */
	public function html () {

		$enctype = $this->_file_transfer ? ' enctype="multipart/form-data"' : '';

		$bcontainer = isset($this->_form_params['body_container']) ? $this->_form_params['body_container'] : 'span';
		unset($this->_form_params['body_container']);

		$output = '';
		$table = false;
		if ($this->_components) {
			foreach ($this->_components as $name => $elts) {
				switch ($elts['type']) {
					case 'field' :
					case 'free_text' :

						$b_attr = empty($elts['body_attrs']) ? array() : $elts['body_attrs'];
						$container = !isset($b_attr['body_container']) ? $bcontainer : $b_attr['body_container'];
						unset($b_attr['body_container']);

						$attrs = $b_attr ? self::getParams($b_attr) : '';

						$attrs_start = $table ? '' : ($container ? '<' . $container . ' ' . $attrs . '>' : '');
						$attrs_stop = $table ? '' : ($container ? '</' . $container . '>' : '');

						$output .= $table ?
							'			<tr ' . $attrs . '>' . "\n" .
							'				<td class="form_label" >' . $elts['label'] . '</td>' . "\n" .
							'				<td class="form_value" >' . $elts['start'] . $elts['html'] . $elts['stop'] . '</td>' . "\n" .
							'			</tr>' . "\n" .
							''
							: '		' . $attrs_start . $elts['label'] . ' ' . $elts['start'] . $elts['html'] . $elts['stop'] . $attrs_stop . "\n";
						break;

					case 'table_start' :
						$table = true;
						$output .= '		<table' . self::getParams($elts['params']) . '>' . "\n";
						break;


					case 'table_stop' :
						$table = false;
						$output .= '		</table>' . "\n";
						break;
				}
			}
		}
		$action_attr = (($this->_action==='') && ($this->_html>=5)) ? '' : 'action="'.$this->_action.'"';
		return 	'<form method="'.$this->_method.'" '.$action_attr.$enctype.self::getParams($this->_form_params).'>' . "\n" .
				'	<div>' . "\n" .
						$output .
				'	</div>' . "\n" .
				'</form>' . "\n" .
				'' ;
	}
}


?>
