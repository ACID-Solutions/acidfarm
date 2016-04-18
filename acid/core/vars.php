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
 * Classe modèle AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVar {

	/**
	 * @var mixed valeur
	 */
	private 	$_val		= null;

	/**
	 * @var mixed valeur par défaut
	 */
	private		$_def		= null;

	/**
	 * @var string expression régulière de validation
	 */
	private 	$_regex		= null;

	/**
	 * @var string étiquette
	 */
	private 	$_label		= '';

	/**
	 * @var boolean not null
	 */
	private		$_is_null	= false;

	/**
	 * @var array paramêtres SQL
	 */
	protected	$sql		= array();

	/**
	 * @var array paramêtre formulaire
	 */
	protected	$form		= array('print'=>true);

	/**
	 * @var array configuration
	 */
	protected	$config 	= array();

	/**
	 * @var array éléments associés
	 */
	protected	$elts		= array();

	/**
	 *   Constructeur AcidVar
	 *
	 * @param string $label Etiquette de la variable.
	 * @param mixed $def Valeur par défaut.
	 * @param string $regex
	 * @param bool $force_def Si true initialise la valeur à la valeur par défaut.
	 */
	public function __construct($label,$def,$regex,$force_def=false) {

		$this->setLabel($label);
		if ($regex !== null) $this->_regex = (string)$regex;

		if ($this->validEntry($def) || $force_def) {
			$this->_val = $this->_def = $def;
		}

		//else trigger_error('Acid : Unvalid default "'.$def.'" for regex "'.$regex.'" of '. get_class($this).'',E_USER_WARNING);
	}

	/**
	 * Force en Majuscule les valeurs en entrée
	 * @param string $val
	 * @param string $encode
	 * @return string
	 */
	public static function upper($val,$encode = 'UTF-8'){
		return mb_strtoupper($val,$encode);
	}

	/**
	 * Force en Minuscule les valeurs en entrée
	 * @param string $val
	 * @param string $encode
	 * @return string
	 */
	public static function lower($val,$encode = 'UTF-8'){
		return mb_strtolower($val,$encode);
	}

	/**
	 * Traite les valeurs en entrée selon la configuration de l'objet
	 * @param mixed $val
	 * @param mixed $way
	 * @return mixed
	 */
	public function treatVal($val,$way=null){
		if (!empty($this->config['force_uppercase'])) {
			$val =  self::upper($val);
		}

		if (!empty($this->config['force_lowercase'])) {
			$val =  self::lower($val);
		}

		if (!empty($this->config['force_function']) && is_array($this->config['force_function']) && (count($this->config['force_function'])>1)) {
			$func = $this->config['force_function'][0];
			$args = $this->config['force_function'][1];
			if ($args) {
				foreach ($args as $k=>$v) {
					if ($v=='__VAL__') {
						$args[$k] = $val;
					}
				}
			}
			$val =  call_user_func_array($func,$args);
		}

		$key_way = 'force_function_'.$way;
		if (!empty($this->config[$key_way]) && is_array($this->config[$key_way]) && (count($this->config[$key_way])>1)) {
			$func = $this->config[$key_way][0];
			$args = $this->config[$key_way][1];
			if ($args) {
				foreach ($args as $k=>$v) {
					if ($v=='__VAL__') {
						$args[$k] = $val;
					}
				}
			}
			$val =  call_user_func_array($func,$args);
		}

		return $val;
	}

	/**
	 *   Retourne la valeur de la variable.
	 *
	 * return mixed
	 */
	public function getVal() {
		$val = self::treatVal($this->_val,'output');
		return $val;
	}

	/**
	 *  Assigne une valeur à la variable.
	 *
	 *
	 * @param mixed $val
	 * @return bool
	 */
	public function setVal($val) {
		if ($this->validEntry($val)) {
			$val =  self::treatVal($val,'input');
			$this->_val = $val;
			return true;
		}else return false;
	}

	/**
	 *  Attribue sa valeur par défaut à la variable.
	 */
	public function setDef() {
		$def =  self::treatVal($this->_def,'input');
		$this->_val = $def;
	}

	/**
	 *  Retourne la valeur par défaut de la variable.
	 *
	 * @return mixed
	 */
	public function getDef() {
		$def =  self::treatVal($this->_def,'output');
		return $def;
	}


	/**
	 *  Assigne une étiquette à la variable.
	 *
	 * @param string $label
	 */
	public function setLabel ($label) {
		if (is_string($label)) $this->_label = $label;
		else trigger_error('Acid : Label undefined', E_USER_NOTICE );
	}

	/**
	 *  Retourne l'étiquette de la variable.
	 *
	 * @return  string
	 */
	public function getLabel () {
		return $this->_label;
	}

	/**
	 *  Assigne le paramêtre uppercase de la variable
	 *
	 * @param bool $value
	 */
	public function setuppercase ($value=true) {
		$this->config['force_uppercase'] = $value;
	}

	/**
	 *  Assigne le paramêtre uppercase de la variable
	 *
	 * @param bool $value
	 */
	public function setlowercase ($value=true) {
		$this->config['force_lowercase'] = $value;
	}

	/**
	 *  Assigne le paramêtre fonction de la variable
	 *
	 * @param bool $value
	 */
	public function setfunction ($value=false) {
		$this->config['force_function'] = $value;
	}

	/**
	 * Attribue une nouvelle configuration à la variable.
	 * @param array $config
	 * @param boolean $erase_before
	 */
	public function setConfig($config,$erase_before=false) {
		if (is_array($config)) {
			if ($erase_before) {
				$this->config = $config;
			}else{
				foreach ($config as $key=>$val) {
					$this->config[$key] = $val;
				}
			}
		}
	}

	/**
	 * Attribue de nouveaux éléments à la variable.
	 * @param array $elts
	 * @param boolean $erase_before
	 */
	public function setElts($elts,$erase_before=true) {
		if (is_array($elts)) {
			if ($erase_before) {
				$this->elts = $elts;
			}else{
				foreach ($elts as $key=>$val) {
					$this->elts[$key] = $val;
				}
			}
		}
	}

	/**
	 *  Retourne les éléments de la variable.
	 */
	public function getElts() {
		return $this->elts;
	}

	/**
	 *  Teste l'éligibilité d'une valeur par la variable.
	 *
	 *
	 * @param mixed $val
	 * @return bool
	 */
	public function validEntry($val) {
		return	$this->_regex === null ? true : (
		is_array($val) ? false : (
		$val === null ? $this->_is_null : (
		preg_match($this->_regex,$val)
		)));
	}

	/**
	 *  Définit si la variable est à l'état NULL ou non.
	 *
	 * @param bool $bool
	 */
	public function isNull($bool) {
		$this->_is_null = (bool)$bool;
	}

	/**
	 * Retourne le paramètre de "configuration Formulaire" de la variable qui est renseigné en entrée.
	 * @param string paramêtre à traiter
	 * @return mixed
	 */
	public function getFormValOf($key) {
		if (isset($this->form[$key])) return $this->form[$key];
		else trigger_error('Acid : Undefined form val "'.$key.'" for '.get_class($this).'::getFormValOf()',E_USER_WARNING);
	}

	/**
	 * Retourne le paramètre de "configuration SQL" de la variable qui est renseigné en entrée
	 * S'il n'est pas défini, renvoie false
	 * @param string paramêtre à traiter
	 * @return bool | mixed
	 */
	public function getSqlValOf($key) {
		if (isset($this->sql[$key])) return $this->sql[$key];
		else return false;
	}

	/**
	 *  Rajoute la variable au formulaire en entrée.
	 *
	 * @param object $form AcidForm
	 * @param string $key Nom du paramétre.
	 * @param bool $print si false, utilise la valeur par défaut
	 * @param array $params attributs
	 * @param string $start préfixe
	 * @param string $stop suffixe
	 * @param array $body_attrs attributs à appliquer au cadre
	 */
	public function getForm(&$form,$key,$print=true,$params=array(),$start='',$stop='',$body_attrs=array()) {
		if (!$form) {
			$form = new AcidForm('','');
		}

		if (isset($this->form['override_start'])) {
			$start = $this->form['override_start'];
		}

		if (isset($this->form['override_stop'])) {
			$stop = $this->form['override_stop'];
		}

		switch ($this->form['type']) {

			case 'show' :
				$stop = $stop . '<label class="show_field">' . htmlspecialchars($this->getVal()) . '</label>';
				$form->addHidden($this->getLabel(), $key, $this->getVal(), $params, $start, $stop,$body_attrs);
				break;

			case 'hidden' :
				$form->addHidden('', $key, $this->getVal(), $params, $start, $stop,$body_attrs);
				break;

			case 'text' :
				$form->addText($this->getLabel(),$key,($print?$this->getVal():''),$this->form['size'],$this->form['maxlength'],$params,$start,$stop,$body_attrs);
				break;

			case 'password' :
				$form->addPassword($this->getLabel(),$key,($print?$this->getVal():$this->getDef()),$this->form['size'],$this->form['maxlength'],$params,$start,$stop,$body_attrs);
				break;

			case 'textarea' :
				$form->addTextarea($this->getLabel(), $key, ($print?$this->getVal():''), $this->form['cols'], $this->form['rows'],$params, $start, $stop,$body_attrs);
				break;

			case 'file' :
				$form->addFile($this->getLabel(), $key, $this->config['max_file_size'], $params, $start, $stop,$body_attrs);
				break;

			case 'select' :
				$form->addSelect($this->getLabel(), $key, ($print?$this->getVal():$this->getDef()),$this->elts, $this->form['size'], $this->form['multiple'],$params,$start,$stop,$body_attrs);
				break;

			case 'radio' :
				$form->addRadio($this->getLabel(),$key,($print?$this->getVal():$this->getDef()),$this->elts, $params,$start,$stop,$body_attrs);
				break;

			case 'switch' :
				$body_attrs['class'] = trim((!isset($body_attrs['class']) ? '' : ($body_attrs['class'])).' radioswitch');
				$form->addRadio($this->getLabel(),$key,($print?$this->getVal():$this->getDef()),$this->elts, $params,$start,$stop,$body_attrs);
				break;

			case 'checkbox':
				$form->addCheckbox($this->getLabel(),$key,($print?$this->getVal():$this->getDef()),$this->form['text'],$this->form['checked'],$params,$start,$stop);
				break;

			case 'free' :
				$form->addFreeText($this->getLabel(),$this->form['free_value'],array(),$body_attrs,$key);
				break;

			case 'info' :
				return false;
				break;

			default :
				return false;
				break;
		}

		return $form->getComponent($key, 'fullhtml');

	}

	/**
	 * Change le type de formulaire associé à la variable
	 * @param string $type (hidden,text,password,textarea,file,select,radio,checkbox,free,...)
	 * @param array $config  configuration
	 */
	public function setForm($type, $config=array()) {

		if (isset($config['override_start'])) {
			$this->form['override_start'] = $config['override_start'];
		}

		if (isset($config['override_stop'])) {
			$this->form['override_stop'] = $config['override_stop'];
		}

		switch ($type) {

			case 'show' :
				$this->form['type'] = 'show';
				$this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
				break;

			case 'hidden' :
				$this->form['type'] = 'hidden';
				$this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
				break;

			case 'text' :
				$this->form['type'] = 'text';
				$this->form['size'] = isset($config['size']) ? $config['size'] : 20;
				$this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
				break;

			case 'password' :
				$this->form['type'] = 'password';
				$this->form['size'] = isset($config['size']) ? $config['size'] : 50;
				$this->form['maxlength'] = isset($config['maxlength']) ? $config['maxlength'] : null;
				break;

			case 'textarea' :
				$this->form['type'] = 'textarea';
				$this->form['cols'] = isset($config['cols']) ? $config['cols'] : 60;
				$this->form['rows'] = isset($config['rows']) ? $config['rows'] : 20;
				break;

			case 'file' :
				$this->form['type'] = 'file';
				$this->form['max_file_size'] = isset($config['max_file_size']) ? $config['max_file_size'] : null;
				break;

			case 'select' :
				$this->form['type'] = 'select';
				$this->form['size'] = isset($config['size']) ? $config['size'] : 1;
				$this->form['multiple'] = isset($config['multiple']) ? $config['multiple'] : false;
				break;

			case 'radio' :
				$this->form['type'] = 'radio';
				break;

			case 'switch' :
				$this->form['type'] = 'switch';
				break;

			case 'checkbox':
				$this->form['type'] = 'checkbox';
				$this->form['checked'] = isset($config['checked']) ? $config['checked'] : false;
				$this->form['text'] = isset($config['text']) ? $config['text'] : '';
				break;

			case 'free' :
				$this->form['type'] = 'free';
				$this->form['free_value'] = isset($config['free_value']) ? $config['free_value'] : '';
				break;

			case 'info' :
				$this->form['type'] = 'info';
				break;

		}


	}
}

/**
 * Variante "Chaîne de caractères" d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarString extends AcidVar {

	/**
	 * Constructeur AcidVarString
	 *
	 * @param string $label
	 * @param int $size
	 * @param int $maxlength
	 * @param string $def
	 * @param string $regex
	 * @param bool $force_def
	 */
	public function __construct($label='AcidVarString',$size=20,$maxlength=255,$def='',$regex=null,$force_def=false) {

		parent::__construct($label,(string)$def,$regex,$force_def);

		// Infos sql
		$this->sql['type'] = 'varchar('.((int)$maxlength).')';

		// Infos form
		$this->setForm('text',array('size'=>(int)$size,'maxlength'=>(int)$maxlength));
		// Value
		//$this->setVal($val);
	}

	/**
	 *  Assigne une chaîne de caractères à la variable
	 * @param string $val
	 */
	public function setVal($val) {
		return parent::setVal((string)$val);
	}

	/**
	 *  Convertit une chaîne de caractères typée bbcode au format html.
	 *
	 *
	 * @param string $text
	 * @return string
	 */
	public static function bbcode($text) {

		$text = preg_replace('`\[img\](.+?)\[/img\]`', '<img src="$1" alt="img" />', $text);
		$text = preg_replace('`\[url\](.+?)\[/url\]`', '<a href="$1">$1</a>', $text);
		$text = preg_replace('`\[url=(.+?)\](.+?)\[\/url\]`', '<a href=$1>$2</a>', $text);
		$text = preg_replace('`\[b\](.+?)\[\/b\]`', '<b>$1</b>', $text);
		$text = preg_replace('`\[i\](.+?)\[\/i\]`', '<i>$1</i>', $text);
		$text = preg_replace('`\[u\](.+?)\[\/u\]`', '<u>$1</u>', $text);

		$text = preg_replace('`\[code\](.+?)\[\/code\]`s', '<code>$1</code>', $text);
		$text = preg_replace('`\[quote\](.+?)\[\/quote\]`s', '<blockquote>$1</blockquote>', $text);
		$text = preg_replace('`\[quote=(.+?)\](.+?)\[\/quote\]`s', '<blockquote cite="$1">$2</blockquote>', $text);
		$text = preg_replace('`\[color=(.+?)\](.+?)\[\/color\]`s', '<span style="color:$1">$2</span>', $text);
		$text = nl2br($text);
		return $text;
	}

	/**
	 *  Convertit une chaîne de caractères typée bbcode au format texte.
	 *
	 *
	 * @param string $text
	 * @return string
	 */
	public static function stripbbcode($text) {

		$text = preg_replace('`\[img\](.+?)\[/img\]`', '$1', $text);
		$text = preg_replace('`\[url\](.+?)\[/url\]`', '$1', $text);
		$text = preg_replace('`\[url=(.+?)\](.+?)\[\/url\]`', '$2', $text);
		$text = preg_replace('`\[b\](.+?)\[\/b\]`', '$1', $text);
		$text = preg_replace('`\[i\](.+?)\[\/i\]`', '$1', $text);
		$text = preg_replace('`\[u\](.+?)\[\/u\]`', '$1', $text);

		$text = preg_replace('`\[code\](.+?)\[\/code\]`s', '$1', $text);
		$text = preg_replace('`\[quote\](.+?)\[\/quote\]`s', '$1', $text);
		$text = preg_replace('`\[quote=(.+?)\](.+?)\[\/quote\]`s', '$2', $text);
		$text = preg_replace('`\[color=(.+?)\](.+?)\[\/color\]`s', '$2', $text);

		return $text;
	}

	/**
	 *  Abrège une chaîne de caractères.
	 *
	 *
	 * @param string $string Chaîne en entrée.
	 * @param int $length Longueur de la chaîne en sortie sans son suffixe.
	 * @param string $end Suffixe de la chaîne en sortie.
	 * @return string
	 */
	public static function split($string, $length, $end=' ...'){

		$translate = self::entityTranslator();
		$string = str_replace(array_keys($translate), array_values($translate), $string);

		$string = utf8_decode($string);
		$string = strip_tags($string);
		$string = str_replace("\r\n",' ',$string);


		$string = html_entity_decode($string,null,'ISO-8859-1');
		$string = utf8_encode($string);

		while (strpos($string,'  ') !== false) {
			$string = str_replace('  ',' ',$string);
		}

		if ($length != 0 && strlen($string) > $length) {
			$length_backup = $length;
			while ($length > 0 && $string[$length] != ' ') {
				$length --;
			}
			if ($length == 0) {
				$length = $length_backup;
			}

			// $string = substr($string,0,$length);
			// $string = mb_substr($string,0,$length);

			/**
			 * @see http://php.net/manual/fr/function.mb-substr.php#107698
			 */
			$string = 	join("", array_slice(
					preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY), 0, $length)
			);

			$string .= $end;
		}

		return $string;
	}

	/**
	 *  Retourne un tableau de conversion de caractères spéciaux
	 *
	 * @return array()
	 */
	public static function entityTranslator(){
		return array(

			"‚" => ",",
			"ƒ" => "f",
			"„" => ",,",
			"…" => "...",
			"†" => "T",
			"‡" => "I",
			"ˆ" => "^",
			"‰" => "L",
			"Š" => "Ŝ",
			"‹" => "‹",
			"Œ" => "OE",
			"‘" => "'",
			"’" => "'",
			"“" => '"',
			"”" => '"',
			"•" => "°",
			"–" => "_",
			"—" => "_",
			"˜" => "~",
			"™" => "TM",
			"š" => "ŝ",
			"›" => ">",
			"œ" => "oe",
			"Ÿ" => "Ÿ"

		);
	}

}

/**
 * Variante paramètre invisible d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarHidden extends AcidVar {

	/**
	 * Constructeur AcidVarHidden
	 *
	 * @param string $label
	 * @param int $maxlength
	 * @param string $def
	 * @param string $regex
	 * @param bool $force_def
	 */
	public function __construct($label='AcidVarString',$maxlength=255,$def='',$regex=null,$force_def=false) {

		parent::__construct($label,(string)$def,$regex,$force_def);

		// Infos sql
		$this->sql['type'] = 'varchar('.((int)$maxlength).')';

		// Infos form
		$this->setForm('hidden',array('maxlength'=>$maxlength));

		// Value
		//$this->setVal($val);
	}

	/**
	 *  Assigne une chaîne de caractères à la variable
	 * @param string $val
	 */
	public function setVal($val) {
		return parent::setVal((string)$val);
	}


}

/**
 * Variante Décimale d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarFloat extends AcidVar {

	/**
	 * Constructeur AcidVarFloat
	 * @param string $label Etiquette de la variable.
	 * @param bool $unsigned True si le décimal n'est pas signé.
	 * @param int $size Taille du champs pour le formulaire.
	 * @param int $maxlength Taille maximale pour le formulaire.
	 * @param float $def Valeur par défaut.
	 */
	public function __construct($label='AcidVarFloat',$unsigned=false,$size=20,$maxlength=10,$def=0) {
		parent::__construct($label,(float)$def,null);

		if ($maxlength === null) {
			$maxlength = 30;
		}

		$this->sql['type'] = 'float';

		$ml = $unsigned ? ((int)$maxlength+2) : ((int) $maxlength+1);
		$this->setForm('text',array('size'=>(int)$size,'maxlength'=>$ml));

		//$this->setVal($val);
	}

	/**
	 *  Assigne un décimal à la variable
	 * @param float $val
	 */
	public function setVal($val) {
		return parent::setVal((float)$val);
	}

}

/**
 * Variante Nombre Entier d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarInteger extends AcidVar {

	/**
	 * Constructeur AcidVarInteger
	 *
	 * @param string $label
	 * @param bool $unsigned
	 * @param int $size
	 * @param int $maxlength
	 * @param int $def
	 * @param string $sql_type
	 */
	public function __construct($label='AcidVarInteger',$unsigned=false,$size=20,$maxlength=10,$def=0,$sql_type='int') {
		parent::__construct($label,(int)$def,null);

		$this->sql['type'] = $sql_type.'('.($unsigned?$maxlength:($maxlength+1)).')';

		$ml = $unsigned ? ((int)$maxlength+1) : (int) $maxlength;
		$this->setForm('text',array('size'=>(int)$size,'maxlength'=>$ml));

		//$this->setVal($val);
	}

	/**
	 *  Assigne un entier à la variable
	 * @param int $val
	 */
	public function setVal($val) {
		return parent::setVal((int)$val);
	}

}

/**
 * Variante TextArea d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarTextarea extends AcidVar {

	/**
	 * Constructeur AcidVarTextarea
	 *
	 * @param string $label
	 * @param int $cols
	 * @param int $rows
	 * @param string $def
	 * @param string $sql_type
	 */
	public function __construct($label='AcidVarTextarea',$cols=80,$rows=5,$def='',$sql_type='text') {
		parent::__construct($label,(string)$def,null);

		$this->sql['type'] = $sql_type;

		$this->setForm('textarea',array('cols'=>(int)$cols, 'rows'=>(int)$rows));

		//$this->setVal($val);
	}

	/**
	 * Assigne une chaîne de caractères à la variable
	 * @param string $val
	 */
	public function setVal($val) {
		return parent::setVal((string)$val);
	}


}

/**
 * Variante Texte d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarText extends AcidVarTextarea {
	/**
	 * Constructeur AcidVarText
	 * @param string $label
	 * @param int $cols
	 * @param int $rows
	 * @param string $def
	 */
	public function __construct ($label='AcidVarText',$cols=80,$rows=5,$def='') {
		parent::__construct($label,$cols,$rows,$def,'text');
	}
}

/**
 * Variante Medium Texte d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarMediumText extends AcidVarTextarea {
	/**
	 * Constructeur AcidVarMediumText
	 * @param string $label
	 * @param int $cols
	 * @param int $rows
	 * @param string $def
	 */
	public function __construct ($label='AcidVarText',$cols=80,$rows=5,$def='') {
		parent::__construct($label,$cols,$rows,$def,'mediumtext');
	}
}

/**
 * Variante Long Texte d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarLongText extends AcidVarTextarea {
	/**
	 * Constructeur AcidVarLongText
	 * @param string $label
	 * @param int $cols
	 * @param int $rows
	 * @param string $def
	 */
	public function __construct ($label='AcidVarText',$cols=80,$rows=5,$def='') {
		parent::__construct($label,$cols,$rows,$def,'longtext');
	}
}

/**
 * Variante "Chaîne de caractères" d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarInfo extends AcidVar {

	/**
	 * Constructeur AcidVarInfo
	 * @param string $label
	 * @param string $def
	 */
	public function __construct ($label='AcidVarInfo',$def='') {
		parent::__construct($label,'',null);
		$this->setForm('info');
	}
}

/**
 * Variante Heure d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarTime extends AcidVarString {

	/**
	 * Constructeur AcidVarTime
	 * @param string $label
	 */
	public function __construct ($label='AcidVarTime') {
		parent::__construct($label,8,8,'00:00:00','`^[0-9]{2}:[0-9]{2}:[0-9]{2}$`');
		$this->sql['type'] = 'time';
	}

	/**
	 * Retourne la valeur actuelle à l'instant T
	 * @return string
	 */
	public static function now() {
		return date('H:i:s');
	}

	/**
	 * Retourne la valeur nulle
	 * @return string
	 */
	public static function zero() {
		return '00:00:00';
	}
}

/**
 * Variante Date d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarDate extends AcidVarString {

	/**
	 * Constructeur AcidVarDate
	 * @param string $label
	 */
	public function __construct($label='AcidVarDate') {
		parent::__construct($label,10,10,'0000-00-00','`^[0-9]{4}-[0-9]{2}-[0-9]{2}$`');
		$this->sql['type'] = 'date';
	}

	/**
	 * Retourne la valeur actuelle à l'instant T
	 * @return string
	 */
	public static function now() {
		return date('Y-m-d');
	}

	/**
	 * Retourne la valeur nulle
	 * @return string
	 */
	public static function zero() {
		return '0000-00-00';
	}
}

/**
 * Variante Date Time d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarDateTime extends AcidVarString {

	/**
	 * Constructeur AcidVarDateTime
	 * @param string $label
	 */
	public function __construct ($label='AcidVarDateTime') {
		parent::__construct($label,20,19,'0000-00-00 00:00:00','`^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$`',true);
		$this->sql['type'] = 'datetime';
	}

	/**
	 * Retourne la valeur actuelle à l'instant T
	 * @return string
	 */
	public static function now() {
		return date('Y-m-d H:i:s');
	}

	/**
	 * Retourne la valeur nulle
	 * @return string
	 */
	public static function zero() {
		return '0000-00-00 00:00:00';
	}
}

/**
 * Variante Email d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarEmail extends AcidVarString {
	/**
	 * Constructeur AcidVarEmail
	 * @param string $label
	 * @param int $size
	 */
	public function __construct($label='AcidVarEmail',$size=20) {
		parent::__construct($label,$size,100,'','`^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$`i',true);
	}
}

/**
 * Variante Url d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarUrl extends AcidVarString {
	/**
	 * Constructeur AcidVarUrl
	 * @param string $label
	 * @param int $size
	 */
	public function __construct($label='AcidVarUrl',$size=20) {
// 		parent::__construct($label,$size,255,'','`^(((https?|ftp)://(w{3}\.)?)(\w+-?)*\.([a-z]{2,4}))`i',true);
		parent::__construct($label,$size,255,'','`^((https?|ftp)://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)`i',true);
	}
}


/**
 * Variante RVB d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarRVB extends AcidVarString {
	/**
	 * Constructeur AcidVarRVB
	 * @param string $label
	 * @param int $size
	 */
	public function __construct($label='AcidVarRVB',$size=20) {
		parent::__construct($label,$size,7,'','`^\#[0-9a-fA-F]{6}$`');
	}
}

/**
 * Variante Mot de Passe d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarPassword extends AcidVarString {

	/**
	 * Constructeur AcidVarPassword
	 * @param string $label
	 */
	public function __construct($label='AcidVarPassword') {
		parent::__construct($label,50,20);
		$this->setForm('password',array('size'=>$this->form['size'],'maxlength'=>$this->form['maxlength']));
	}

	/**
	 * Generate random password
	 * @param int $size
	 * @return string
	 */
	public static function random($size=8) {
		$numbers = '0123456789';
		$chars = 'aeiouy';
		$charsb = 'bcdfghjklmnpqrstvwxz';

		$check[0] = $chars.$chars.$chars.$charsb.$numbers;
		$check[1] = $chars.$chars.$charsb.$charsb.$charsb.$numbers;
		$check[2] = $numbers.$numbers;

		$nbchars[0] = strlen($check[0]);
		$nbchars[1] = strlen($check[1]);
		$nbchars[2] = strlen($check[2]);

		$init_end = $size-rand(0,3);

		$my_pass = '';
		for ($i=0;$i<$size;$i++) {
			$indice = ($i>=$init_end) ? 2 : (($i%2===0) ? 1 : 0);
			$my_pass .= $check[$indice][rand(0,$nbchars[$indice]-1)];
		}

		return $my_pass;
	}

}

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

/**
 * Variante Image d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarImage extends AcidVarFile {

	/**
	 * Constructeur AcidVarImage
	 * @param string $label étiquette
	 * @param string $folder chemin vers le repertoire de stockage du fichier
	 * @param array $config configuration
	 * @param string $name nom du fichier (variables magiques : __NAME__, __ID__ )
	 */
	public function __construct($label='AcidVarImage',$folder=null,$config=array(),$name='') {

		$this->config['format'] = array();
		$this->config['format']['src'] = array('size'=>array(0,0,false), 'effect'=>array(), 'suffix'=>'');

		if (isset($config['format']) && is_array($config['format'])) {
			foreach ($config['format'] as $key=>$val) {
				$this->config['format'][$key] = $val;
			}
			unset($config['format']);
		}

		if ($this->config['ext'] === null) {
			$this->config['ext'] = Acid::get('ext:varimage');
		}

		parent::__construct($label,$folder,$config,$name);

	}

	/**
	 * Retourne le chemin d'accès au fichier.
	 *
	 * @param string $format Dérivé du fichier cible. Valeur par Défaut : NULL
	 *
	 * @return string
	 */
	public function getPath($format='src') {
		return SITE_PATH . $this->dir_path . pathinfo($this->getUrl($format),PATHINFO_BASENAME);
	}

	/**
	 * Retourne le chemin d'accès au fichier.
	 *
	 * @param string $format Dérivé du fichier cible. Valeur par Défaut : NULL
	 *
	 * @return string
	 */
	public function getValPath($format='src') {
		$val = SITE_PATH.$this->getVal();
		if ($suffix =  $this->getSuffix($format)) {
			return  self::applySuffix($val,$suffix);
		}

		return $val;
	}

	/**
	 * Retourne le nom de fichier en entrée après lui avoir appliqué le suffixe
	 *
	 * @param string $str le fichier
	 * @param string $suffix le suffixe
	 * @return string
	 */
	public static function applySuffix($str,$suffix) {
		$ext = AcidFs::getExtension($str);
		$str = substr($str,0,-strlen($ext)-1) . $suffix . '.' . $ext;
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
	public function getUrl($format='src') {
		$format = isset($this->config['format'][$format]) ? $format : 'src';

		$suffix =  $this->getSuffix($format);
		$url = $this->getVal();

		if ($suffix) {
			//$ext = AcidFs::getExtension($url);
			//$url = substr($url,0,-strlen($ext)-1) . $suffix . '.' . $ext;
			$url = self::applySuffix($url,$suffix);
		}



		if (empty($this->config['get_url_func'])) {
			return 	Acid::get('url:folder').$url;
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
						$args[$k] = $url;
					}elseif ($args[$k] === '__ABSVAL__') {
						$args[$k] = $this->getVal();
					}elseif ($args[$k] === '__FORMAT__') {
						$args[$k] = $format;
					}
				}
			}

			return ($name=='value') ? $url : call_user_func_array($name,$args);
		}

	}

	/**
	 * Retourne le suffixe à intégrer aux images en fonction du format en entrée
	 *
	 * @param string $format Derivé de l'image. Valeur par défaut : NULL
	 */
	public function getSuffix($format='src') {

		$suffix = null;

		if (isset($this->config['format'][$format]['suffix'])) {
			$suffix = $this->config['format'][$format]['suffix'];
		}

		if ($suffix===null) {
			$suffix = '_'.$format;
		}

		return $suffix;
	}

	/**
	 * Génère un dérivé $format de l'image du module renseignée par $img.
	 *
	 * @param string $format
	 */
	public function imgResize($format) {
		global $acid;

		Acid::log('debug','Resizing ' . $this->getFileName() .' in '.$format.' format');

		if (isset($this->config['format'][$format])) {

			list($max_img_w, $max_img_h, $crop) = $this->config['format'][$format]['size'];

			$img_big_path = $this->getPath();
			$img_small_path = $this->getPath($format);

			if (file_exists($img_big_path)) {
				if (($max_img_w) && ($max_img_h)) {

					if ($crop) {
						list($img_big_w,$img_big_h,$img_big_type) = getimagesize($img_big_path);
						list($img_big_w,$img_big_h,$src_x,$src_y) = AcidFs::getImgSrcSizeCroped($img_big_w,$img_big_h,$max_img_w,$max_img_h);
						$img_small_w = $max_img_w;
						$img_small_h = $max_img_h;
					} else {
						list($img_big_w,$img_big_h,$img_big_type) = getimagesize($img_big_path);
						list($img_small_w,$img_small_h) = AcidFs::getImgSmallerSize($img_big_w,$img_big_h,$max_img_w,$max_img_h);
						$src_x = $src_y = 0;
					}

					AcidFs::imgResize($img_big_path,$img_small_path,$img_small_w,$img_small_h,$img_big_w,$img_big_h,$img_big_type,$src_x,$src_y);

					chmod($img_small_path,$acid['files']['file_mode']);

				}else{
					if ($img_big_path != $img_small_path) {
						copy($img_big_path,$img_small_path);
					}
				}
			}
		}
	}

	/**
	 * Supprime le fichier associé au module.
	 *
	 *
	 * @return bool
	 */
	public function fsRemove() {
		$tab_format = $this->config['format'];
		unset($tab_format['src']);

		$success = true;

		foreach ($tab_format as $format => $img) {
			if (is_file($path = $this->getValPath($format))) {
				if (!unlink($path)) {
					$success = false;
					Acid::log('error','AcidImage::fsRemove can\'t delete ' . $path);
				}
			}
		}

		if (is_file($path = $this->getValPath())) {
			if (!unlink($path)) {
				$success =  false;
				Acid::log('error','AcidImage::fsRemove can\'t delete ' . $path);
			}else{
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
	protected function formatProcess($format_filter=null) {
		$format_filter = (($format_filter !== null) && is_array($format_filter)) ? $format_filter : array_keys($this->config['format']);

		foreach ($this->config['format'] as $format => $elt) {
			if (in_array($format,$format_filter)) {
				$this->imgResize($format);
				if (!empty($elt['effect'])) {
					foreach ($elt['effect'] as $effect) {
						$this->effectProcess($effect,$format);
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
	public function regen($format_filter=null) {
		$this->formatProcess($format_filter);
	}

	/**
	 * Applique un effet choisi sur la format renseigné en entrée
	 *
	 * @param string $effect
	 * @param string $format
	 */
	public function effectProcess($effect,$format) {

		if (isset($this->config['effects'][$effect])) {
			$my_effect = $this->config['effects'][$effect];
			if ( (is_array($my_effect)) && (count($my_effect)==2) ) {


				foreach ($my_effect[1] as $key => $val) {
					switch ($val) {
						case '__VAR__' : $my_effect[1][$key] = $this;	break;
						case '__FORMAT__' :	$my_effect[1][$key] = $format;	break;
						case '__WIDTH__' : $my_effect[1][$key] = $this->config['format'][$format]['size'][0];	break;
						case '__HEIGHT__' : $my_effect[1][$key] = $this->config['format'][$format]['size'][1];	break;
						case '__CROP__' : $my_effect[1][$key] = $this->config['format'][$format]['size'][2]; break;
						case '__SRCPATH__' : $my_effect[1][$key] = $this->getPath(); break;
						case '__PATH__' : $my_effect[1][$key] = $this->getPath($format); break;
					}
				}


				call_user_func_array($my_effect[0],$my_effect[1]);

			}
		}else{

			switch ($effect) {
				case 'gray' :
					AcidFs::imgGray($this->getPath($format),$this->getPath($format));
					break;

				case 'fill_white' :
					AcidFs::fill(
						$this->getPath($format),
						$this->getPath($format),
						$this->config['format'][$format]['size'][0],
						$this->config['format'][$format]['size'][1],
						array(255,255,255)
					);
					break;

				case 'fill_transparent' :
					AcidFs::fill(
						$this->getPath($format),
						$this->getPath($format),
						$this->config['format'][$format]['size'][0],
						$this->config['format'][$format]['size'][1],
						array(false,false,false)
					);
					break;

				case 'fill_black' :
					AcidFs::fill(
						$this->getPath($format),
						$this->getPath($format),
						$this->config['format'][$format]['size'][0],
						$this->config['format'][$format]['size'][1],
						array(0,0,0)
					);
					break;
			}

		}
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

		//Images par upload direct
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

				$this->formatProcess();

			}else{
				Acid::log('file','$_FILES[\''.$key.'\'], file extension is not in array('.implode(',',$this->config['ext']).')');
				$success = false;
			}

		}elseif( isset($tfiles[$key]) && ($tfiles[$key]['size'] <= 0) ) {
			Acid::log('file','$_FILES[\''.$key.'\'][\'size\'] = 0');
			Acid::log('file','$_FILES[\''.$key.'\'][\'error\'] = ' . $tfiles[$key]['error']);
			$success = $r_success;
		}

		return $success;
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
	public function getForm(&$form,$key,$print=true,$params=array(),$start='',$stop='',$body_attrs=array()) {
		if ($this->getVal()) {
			if (isset($this->config['admin_format']) && isset($this->config['format'][$this->config['admin_format']])) {
				$start .=	'<a href="'.$this->getUrl().'">'. "\n" .
					'	<img src="'.$this->getUrl($this->config['admin_format']).'" alt="'.$this->getFileName().'" />'. "\n" .
					'</a>';
				$stop .= ''.$form->checkbox ($key.'_remove', '1', false,'Supprimer');

				$bstart =  '<div class="src_container">';
				$bstop = '</div>';

				return $this->getParentForm($form,$key,$print,$params,$start.$bstart,$bstop.$stop,$body_attrs);
			}

			return parent::getForm($form,$key,$print,$params,$start,$stop,$body_attrs);
		}else{

			return parent::getForm($form,$key,$print,$params,$start,$stop,$body_attrs);

		}
	}

}

/**
 * Autre Variante Int d'AcidVar ( Clonée Base de Données )
 * @package   Acidfarm/Vars
 */
class AcidVarInt extends AcidVarInteger {

	/**
	 * Constructeur AcidVarInt
	 * @param string $label
	 * @param booelan $unsigned
	 * @param int $def
	 */
	public function __construct($label='AcidVarInt',$unsigned=false,$def=0) {
		parent::__construct($label,$unsigned,20,10,$def,'int');
	}

}

/**
 * Autre Variante TinyInt d'AcidVar ( Clonée Base de Données )
 * @package   Acidfarm/Vars
 */
class AcidVarTinyInt extends AcidVarInteger {
	/**
	 * Constructeur AcidVarTinyInt
	 * @param string $label
	 * @param booelan $unsigned
	 * @param int $def
	 */
	public function __construct($label='AcidVarTinyInt',$unsigned=false,$def=0) {
		parent::__construct($label,$unsigned,20,3,$def,'tinyint');
	}

}

/**
 * Autre Variante SmallInt d'AcidVar ( Clonée Base de Données )
 * @package   Acidfarm/Vars
 */
class AcidVarSmallInt extends AcidVarInteger {

	/**
	 * Constructeur AcidVarSmallInt
	 * @param string $label
	 * @param booelan $unsigned
	 * @param int $def
	 */
	public function __construct($label='AcidVarSmallInt',$unsigned=false,$def=0) {
		parent::__construct($label,$unsigned,20,5,$def,'smallint');
	}

}

/**
 * Autre Variante MediumInt d'AcidVar ( Clonée Base de Données )
 * @package   Acidfarm/Vars
 */
class AcidVarMediumInt extends AcidVarInteger {

	/**
	 * Constructeur AcidVarMediumInt
	 * @param string $label
	 * @param booelan $unsigned
	 * @param int $def
	 */
	public function __construct($label='AcidVarMediumInt',$unsigned=false,$def=0) {
		parent::__construct($label,$unsigned,20,8,$def,'mediumint');
	}

}

/**
 * Autre Variante BigInt d'AcidVar ( Clonée Base de Données )
 * @package   Acidfarm/Vars
 */
class AcidVarBigInt extends AcidVarInteger {

	/**
	 * Constructeur AcidVarBigInt
	 * @param string $label
	 * @param booelan $unsigned
	 * @param int $def
	 */
	public function __construct($label='AcidVarBigInt',$unsigned=false,$def=0) {
		parent::__construct($label,$unsigned,20,20,$def,'bigint');
	}

}

/**
 * Autre Variante Booléenne d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarBool extends AcidVarInteger {

	/**
	 * Constructeur AcidVarBool
	 * @param string $label
	 * @param int $def
	 */
	public function __construct($label='AcidVarBool',$def=0) {
		parent::__construct($label,true,20,1,$def,'tinyint');
		$this->setDef($def);
		$this->elts = self::assoc();
		$this->setForm('switch');
	}

	/**
	 * Retourne le tableau associatif interne de la variable
	 * @return array
	 */
	public static function assoc() {
		return array(1=>Acid::trad('yes'),0=>Acid::trad('no'));
	}

}

/**
 * Variante Liste d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarList extends AcidVar {

	/**
	 * @var array tableau représentatif des éléments de la liste
	 */
	protected	$elts		= array();

	/**
	 * @var strinf délimiteur
	 */
	protected	$limiter	= "\n";

	/**
	 * @var booelan true si la valeur associée est une chaine
	 */
	protected	$use_index	= null;
	protected	$int_index	= null;

	/**
	 * Constructeur AcidVarList
	 *
	 * @param string $label Etiquette de la variable.
	 * @param array $elts Liste des éléments.
	 * @param string $def Valeur par défaut.
	 * @param bool $multiple True si on autorise plusieurs valeurs sélectionnées en formulaire.
	 * @param bool $use_index
	 * @param int $size Taille du select dans le formulaire.
	 */
	public function __construct($label='AcidVarList',$elts=array(),$def='',$multiple=false,$use_index=true,$size=1,$int_index=true) {

		parent::__construct($label,$def,null);

		$this->sql['type'] = $multiple ? 'text' :
			($use_index ? (count($elts) < 128 ? 'tinyint(3)' : 'int(10)')
				: 'enum('.self::getEnumInstruction($elts).')');

		$this->setForm('select',array('size'=>$size,'multiple'=>$multiple));
		$this->use_index = $use_index;
		$this->int_index = $int_index;

		if ($use_index)  $this->elts = $elts;
		else foreach ($elts as $elt) $this->elts[$elt] = $elt;
	}

	/**
	 * Retourne l'ensemble des valeurs du tableau en entrée sous forme de chaîne de caractères.
	 *
	 * @param array $elts
	 *
	 * @return string
	 */
	public static function getEnumInstruction($elts) {
		$output = '';
		foreach ($elts as $elt){
			$output .= '\''.addslashes($elt).'\',';
		}
		return substr($output,0,-1);
	}

	/**
	 * Assigne un élément à la variable
	 *
	 * @param mixed $val
	 */
	public function setVal($val) {
		if ($this->use_index) {
			if ($this->int_index)  {
				parent::setVal((int)$val);
			}else{
				parent::setVal($val);
			}
		}else{
			parent::setVal($val);
		}
	}

	/**
	 * Retourne les valeurs de la liste sous forme d'un tableau
	 *
	 * @return array
	 */
	public function getVals() {
		return $this->elts;
	}

}


/**
 * Variante Radio d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarRadio extends AcidVar {

	/**
	 * @var array tableau représentatif des éléments de la liste
	 */
	protected $elts;

	/**
	 * Constructeur AcidVarRadio
	 *
	 * @param string $label Etiquette de la variable.
	 * @param array $elts Liste des Eléments de la liste.
	 * @param string $def Valeur par défaut.
	 * @param bool $use_index
	 * @param bool $null
	 */
	public function __construct($label='AcidVarRadio',$elts=array(),$def='',$use_index=false,$null=false) {

		parent::__construct($label,$def,null);

		// Infos sql
		$this->sql['type'] = $use_index ? (count($elts) < 128 ? 'tinyint(3)' : 'int(10)')
			: 'enum('.self::getEnumInstruction($elts).')';

		// Infos form
		$this->setForm('radio');

		$this->use_index = $use_index;
		if ($use_index)  $this->elts = $elts;
		else foreach ($elts as $elt) $this->elts[$elt] = $elt;
	}

	/**
	 * Retourne l'ensemble des valeurs du tableau en entrée sous forme de chaîne de caractères.
	 *
	 * @param array $elts
	 *
	 * @return string
	 */
	public static function getEnumInstruction($elts) {
		$output = '';
		foreach ($elts as $elt){
			$output .= '\''.addslashes($elt).'\',';
		}
		return substr($output,0,-1);
	}

	/**
	 * Assigne un élément à la variable
	 *
	 * @param mixed $val
	 */
	public function setVal($val) {
		if ($this->use_index) parent::setVal((int)$val);
		else parent::setVal($val);
	}

	/**
	 * Retourne les valeurs de la liste sous forme d'un tableau
	 *
	 * @return array
	 */
	public function getVals() {
		return $this->elts;
	}

}

?>