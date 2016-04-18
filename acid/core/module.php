<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Core
 * @version   0.2
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Module Acidfarm
 * @package   Acidfarm\Core
 *
 */
abstract class AcidModuleCore {

	/**
	* @var object les différents champs sous forme d'AcidVar
	*/
	protected $vars = array();

	/**
	* @var array tableau de configuration du module
	*/
	protected $config = array();

	const TBL_NAME = null;
	const TBL_PRIMARY = null;

	/**
	* Constructeur du module Acidfarm
	* Si un identifiant est renseigné en entrée depuis $init_id, on initialise l'objet en fonction des valeurs stockées en base de données.
	*
	* @param mixed $init_id
	*
	* @return bool
	*/
	public function __construct($init_id=null) {

		if (is_array($init_id)) {
			$this->initVars($init_id,true);
		}else{
			$this->dbInit($init_id);
		}

	}

	/**
	* Appel static
	* @param unknown_type $chrMethod
	* @param unknown_type $arrArguments
	* @return mixed
	*/
	final public static function __callStatic( $chrMethod, $arrArguments ) {
		 static::checkTbl();
		 return call_user_func_array($chrMethod,$arrArguments);
	 }

	//abstract public static function className();

	/**
	  * Verifie que le module dispose bien d'une constante TBL_NAME et retourne le nom de la table SQL dont il fait référence
	  * @return boolean|string
	  */
	 public static function checkTbl() {
	 	if (static::TBL_NAME === null) {
			trigger_error(get_called_class().': you must define TBL_NAME constant', E_USER_ERROR);
			return false;
		}else{
			return static::TBL_NAME;
		}
	 }

	/**
	* Retourne le nom de la table SQL associé au module.
	*
	*
	* @return string
	*/
	public static function tbl() {
		if ($tbl_name = static::checkTbl()) {
			return Acid::get('db:prefix') .$tbl_name;
		}
	}

	/**
	* Contrôle puis retourne le nom de la table SQL associé au module sans préfixe.
	*
	*
	* @return string
	*/
 	public static function checkTblId() {
	 	if (static::TBL_PRIMARY === null) {
			trigger_error(get_called_class().': you must define TBL_PRIMARY constant', E_USER_ERROR);
			return false;
		}else{
			return static::TBL_PRIMARY;
		}
	 }

	/**
	* Retourne le nom du champs identifiant en base de données.
	*
	*
	* @return string
	*/

	public static function tblId() {
		return static::checkTblId();
	}

	/**
	* Retourne la classe courante.
	*
	*
	* @return string
	*/

	public static function getClass() {
		return get_called_class();
	}

	/**
	* Retourne un nouvel objet module
	*
	* @param mixed $elts initialisateur de module
	*
	* @return string
	*/
	public static function build($elts=null) {
		$mod_name = get_called_class();
		$mod = new $mod_name();
		if ( ($elts) && (is_array($elts)) ) {
			$mod->initVars($elts);
		}elseif(is_numeric($elts)) {
			$mod = new $mod_name($elts);
		}
		return $mod;
	}

	/**
	* Retourne le module en session
	*
	*
	* @return string
	*/
	public static function current() {
		$sess = AcidSession::get(static::checkTbl());
		$my_module = static::build($sess);
		return $my_module;
	}

	/**
	* Sauvegarde le module en session
	*
	* @param $values tableau contenant les nouvelles valeurs du module
	*
	* @return string
	*/
	public static function currentChange($values=array()) {
		$current = self::current();
		$current->initVars($values);
		$current->sessionMake();
	}

	/**
	* Retourne une valeur du module en session
	* @param string $key identifiant de la variable
	* @param string $def valeur à retourner en cas d'échec
	* @return mixed
	*/
	public static function curValue($key,$def=null) {
		$sess = AcidSession::get(static::checkTbl());
		return isset($sess[$key]) ?  $sess[$key] : $def;
	}

	/**
	* Compare l'id soumis avec celui en session
	*
	* @param int $id
	*
	* @return string
	*/
	public static function isSame($id) {
		$cur_id = self::curValue(static::tblId());
		return ($cur_id &&  ($cur_id == $id));
	}

	/**
	* Retourne le nom de la table en base de données.
	*
	* @param string $next suffixe à intégrer à la valeur de retour
	*
	* @return string
	*/
	public static function preKey($next='') {
		return static::TBL_NAME.'_'.$next;
	}


	/**
	 * Retourne le nom d'un champ avec le suffixe de langue s'il existe
	 *
	 * @param string $key la clé
	 * @param string $lang la langue à utiliser
	 *
	 * @return string
	 */
	public function langKey($key,$lang=null) {
		$lang = $lang===null ? Acid::get('lang:current') : $lang;
		$lang = $lang=='default' ? Acid::get('lang:default') : $lang;
		$check_key = $key.'_'.$lang;
		return isset($this->vars[$check_key]) ? $check_key : $key;
	}

	/**
	 * Retourne le nom d'un champ avec le suffixe de langue s'il existe
	 *
	 * @param string $key la clé
	 * @param string $langs les langues à utiliser
	 *
	 * @return string
	 */
	public function langKeyDecline($key,$langs=null) {
		$langs= $langs===null ? Acid::get('lang:available') : $langs;

		$tab = array();
		foreach ($langs as $lang) {

			$check_key = $key.'_'.$lang;
			if (isset($this->vars[$check_key])) {
				$tab[] = $check_key;
			}

		}

		return $tab ? $tab : array($key);
	}

	/**
	* Attribut une valeur aux paramètre de l'objet s'ils sont renseignés dans le tableau en entrée
	* Retourne un tableau reccueillant les paramètres réellement modifiés lors de la procédure
	* Si $eraseIfUndefined est à true, alors les champs non renseignés dans le tableau en entrée seront initialisés à leur valeur par défaut.
	*
	* @param array $tab ([paramètre] => [valeurs])
	* @param bool $eraseIfUndefined
	* @param bool $allow_tbl_pref
	*
	* @return array ([paramètre])
	*/
	public function initVars ($tab,$eraseIfUndefined=false,$allow_tbl_pref=true) {
		$changes = array();
		if (is_array($tab)) {
			if ($eraseIfUndefined) {
				foreach ($this->vars as $key => $var) {
					$var->setDef();
				}
			}

			//without prefix
			foreach ($this->vars as $key => $var) {
				if (isset($tab[$key])) {
					if ($tab[$key] != $var->getVal()) {
						$var->setVal($tab[$key]);
						$changes[] = $key;
					}
				}
			}

			//with prefix
			if ($allow_tbl_pref) {
				foreach ($this->getKeys(true) as $pkey) {
					if (isset($tab[$pkey])) {
						$key = substr($pkey,strlen($this->tbl().'.'));
						if ($tab[$pkey] != $this->vars[$key]->getVal()) {
							$this->vars[$key]->setVal($tab[$pkey]);
							$changes[] = $key;
						}
					}
				}
			}

		} else {
			trigger_error('Acid : an array is expected for AcidModule::initVars', E_USER_WARNING);
		}

		return $changes;
	}

	/**
	* Retourne un tableau accueillant les paramètres de l'objet associés à leurs valeurs.
	*
	*
	* @return array ([paramètre] => [valeurs])
	*/
	public function getVals () {
		$tab = array();
		foreach ($this->vars as $key => $var) {
			$tab[$key] = $var->getVal();
		}
		return $tab;
	}

	/**
	* Liste les paramètres de l'objet ainsi que leurs valeurs sous forme d'une chaîne de caractères mise en forme.
	*
	*
	* @return string
	*/
	public function debug(){
		return Acid::tpl('core/debug.tpl',array('vars'=>$this->vars),$this);
	}

	/**
	* Retourne la traduction dans la langue courante de la clé renseignée en entrée.
	* @param string $val Nom du paramétre.
	*
	* @return mixed
	*/
	public static function modTrad($val) {
		if (Acid::exists('mod:'.static::TBL_NAME.':'.$val,'lang')) {
			return Acid::get('mod:'.static::TBL_NAME.':'.$val,'lang');
		}else{
			return $val;
		}
	}

	/**
	* Retourne la valeur du paramétre renseigné en entrée.
	* @param string $key Nom du paramétre.
	*
	* @return mixed
	*/
	public function get($key) {
		if (isset($this->vars[$key])) {
			return $this->vars[$key]->getVal();
		} else {
			trigger_error('' . get_called_class() . '::get("' . htmlspecialchars($key) . '") val "' . htmlspecialchars($key) . '" doesn\'t exists !', E_USER_WARNING);
		}
	}

	/**
	* Retourne la valeur associée à la clé renségnée et relative à la langue courante.
	* @param string $key Nom du paramétre.
	*
	* @return mixed
	*/
	public function trad($key) {

		$trad_key = $key.'_'.Acid::get('lang:current');
		if (isset($this->vars[$trad_key])) {
			return $this->get($trad_key);
		}else{
			$trad_key = $key.'_'.Acid::get('lang:default');
			if (isset($this->vars[$trad_key])) {
				return $this->get($trad_key);
			}
		}

		return $this->get($key);
	}

	/**
	* Retourne la valeur associée à la clé en entrée tronquée
	* @param string $key Nom du paramétre.
	* @param string length Longueur max de la chaîne.
	* @param string end Fin de la chaîne.
	* @param boolean $trad si true, alors split la valeur traduite
	*
	* @return string
	*/
	public function split($key,$length=100,$end='...',$trad=false) {
		$val = $trad ? $this->trad($key) : $this->get($key);
		return AcidVarString::split($val,$length,$end);
	}

	/**
	* Retourne la valeur traduite dans la langue courante associée à la clé en entrée tronquée
	* @param string $key Nom du paramétre.
	* @param string $length Longueur max de la chaîne.
	* @param string $end Fin de la chaîne.
	*
	* @return Ambigous <string, string, mixed>
	*/
	public function splitTrad($key,$length=100,$end='...') {
		return $this->split($key,$length,$end,true);
	}

	/**
	* Alias : htmlspecialchars de la fonction getTrad($key)
	* @param string $key Nom du paramétre.
	*
	* @return mixed
	*/
	public function hscTrad($key) {
		return htmlspecialchars($this->trad($key));
	}

	/**
	* Retourne un tableau accueillant le nom de chaque paramétre de l'objet.
	*
	* @param boolean $add_tbl si true, les champs seront prefixés par le nom de table SQL du module
	*
	* @return array [paramètre]
	*/
	public function getKeys($add_tbl=false) {
		$keys = array();
		foreach ($this->vars as $key => $var) {
			$keys[] = $add_tbl ? $this->tbl().'.'.$key : $key;
		}
		return $keys;
	}

	/**
	* Retourne un tableau de clés reconnues comme clés de traduction
	* @return multitype:unknown
	*/
	public function getMultiLingualKeys() {
		$keys = array();
		foreach ($this->vars as $key => $var) {
			$l = explode('_',$key);
			$l = count($l) ? $l[count($l)-1] : false;

			if ($l) {
				if (in_array($l,Acid::get('lang:available'))) {
					$keys[] = $key;
				}
			}
		}

		return $keys;
	}

	/**
	* Retourne un tableau accueillant les différentes valeurs du paramétre multi-valeurs (select, radio) renseigné en entrée.
	*
	* @param string $key Nom du paramétre.
	*
	* @return mixed
	*/
	public function getVarOptions($key) {
		if (isset($this->vars[$key])) {
			if (in_array(array('select','radio'), $this->vars[$key]->getFormValOf('type'))) {
				return $this->vars[$key]->getVals();
			}
		} else {
			trigger_error('Acid : ' . htmlspecialchars($key) . ' does not exist', E_USER_WARNING);
		}
	}

	/**
	*  Retourne le formulaire associé à la clé en entrée.
	* @param string $key Nom du paramétre.
	* @param boolean $print si false, utilise la valeur par défaut
	* @param array $attr attributs
	* @param string $start préfixe
	* @param string $stop suffixe
	*
	* @return mixed
	*/
	public function getVarForm($key,$print=true,$attr=array(),$start='',$stop='') {
		if (isset($this->vars[$key])) {
				$name = !empty($attr['name']) ? $attr['name']:$key;

				$form = new AcidForm('','');
				return $this->vars[$key]->getForm($form,$name,$print,$attr,$start,$stop);
		} else {
			trigger_error('Acid : ' . htmlspecialchars($key) . ' does not exist', E_USER_WARNING);
		}
	}

	/**
     * Retourne le type de form associé à la clé en entrée.
     *
     * @param string $key Nom du paramétre.
     *
     * @return string
     */
    public function getVarFormType($key) {
        if (isset($this->vars[$key])) {
                return $this->vars[$key]->getFormValOf('type');
        } else {
            trigger_error('Acid : ' . htmlspecialchars($key) . ' does not exist', E_USER_WARNING);
        }
    }

	/**
	* Retourne la valeur de l'identifiant de l'objet.
	*
	* @return array
	*/
	public function getId() {
		return $this->get(static::tblId());
	}

	/**
	* Retourne la valeur du paramétre renseigné en entrée, aprés l'avoir soumise à la fonction htmlspecialchars.
	*
	* @param string $key
	*
	* @return string
	*/
	public function hsc($key) {
		if (isset($this->vars[$key])) {
			return htmlspecialchars($this->vars[$key]->getVal());
		}
	}

	/**
	* Retourne l'étiquette du paramétre renseigné en entrée.
	*
	* @param string $key
	*
	* @return string
	*/
	public function getLabel($key) {
		$def_label = null;
		if (isset($this->vars[$key])) {
			$def_label = $this->vars[$key]->getLabel();
		}elseif (isset($this->vars[$this->dbPref($key)]) ) {
			$def_label = $this->vars[$this->dbPref($key)]->getLabel();
		}

		if (isset($this->config['admin']['head'][$key])){

			if (is_array($this->config['admin']['head'][$key])) {
				return isset($this->config['admin']['head'][$key]['label']) ? $this->config['admin']['head'][$key]['label'] : $def_label;
			}

			return $this->config['admin']['head'][$key];

		}else{
			return $def_label;
		}
	}

	/**
	*  Attribue leur valeur par défaut à tous les paramètres de l'objet.
	*/
	public function cleanVars() {
		foreach ($this->vars as $key => $var) {
			$var->setDef();
		}
	}

	/**
	* Initialise la valeur des paramètre de l'objet en fonction des valeurs stockées en base de données
	* Renvoie true en cas de réussite
	* Si $id n'est pas trouvé en base de données, alors l'objet est initialisé avec ses valeurs par défaut, on renvoie alors false.
	*
	* @param mixed $id Valeur de l'identifiant.
	*
	* @return bool
	*/
	public function dbInit($id) {
		if ($id) {

			$req =	"SELECT * " .
					"FROM ".static::tbl()." " .
					"WHERE `".static::tblId()."`='".((int)$id)."';";

			if ($res = AcidDB::query($req)->fetch(PDO::FETCH_ASSOC)) {
				$this->initVars($res, true);
				return true;
			} else {
				$this->cleanVars();
				return false;
			}

		}
	}

	/**
	* Enregistre l'objet dans la table SQL qui lui est associée
	* Renvoie la valeur de son identifiant en cas de réussite, renvoie false sinon.
	*
	* @return int | bool
	*/
	public function dbAdd() {

		$keys = $values = '';
		$assoc = array();
		foreach ($this->vars as $key => $var) {
			if ($key === static::tblId()) {
				$values .= "'',";
			} else {
				$values .= ':'.$key . ',';
				$assoc[':'.$key] = $var->getVal();
			}
			$keys .= '`'.$key . '`,';
		}

		$req =	"INSERT INTO ".static::tbl()." " .
				"(".substr($keys,0,-1).") " .
				"VALUES(".substr($values,0,-1).");";

		$sth = AcidDB::prepare($req,array(),true);

		$log = $sth->queryString;
		if ($assoc) {
			foreach ($assoc as $k => $v) {
				$log = str_replace($k, "'$v'", $log);
			}
		}

		// Log contenant les valeurs bindées
		Acid::log('sql', 'DB values : ' . $log);

		if ($sth->execute($assoc)) {
			$id = AcidDB::lastInsertId();
			if ($this->vars[static::tblId()]->setVal($id)) {
				return $id;
			}
		}
		return false;
	}

	/**
	* Met à jour la base de données avec les valeurs de l'objet
	* Si $updates est la chaîne de carractères "all", met à jour tous les champs
	* Sinon, met à jour uniquement les champs renseignés par le tableau $updates
	*
	*
	* @param { string | array } $updates
	*
	* @return bool
	*/
	public function dbUpdate($updates) {

		if ($updates === 'all') {
			$updates = $this->getKeys();
		} else {
			$updates = (array) $updates;
		}

		$sets = '';
		$assoc = array();
		foreach ($updates as $key) {
			if ($key !== static::tblId()) {
				$sets .= "`".$key."`=:".$key.",";
				$assoc[':'.$key] = $this->vars[$key]->getVal();
			}
		}
		$sets = substr($sets,0,-1);

		if (!empty($sets)) {
			$req =	"UPDATE " . static::tbl() . " " .
    				"SET ".$sets." " .
    				"WHERE ".static::tblId()."='".$this->getId()."';";

			$sth = AcidDB::prepare($req,array(),true);

			$log = $sth->queryString;
			if ($assoc) {
				foreach ($assoc as $k => $v) {
					$log = str_replace($k, "'$v'", $log);
				}
			}

			// Log contenant les valeurs bindées
			Acid::log('sql', 'DB values : ' . $log);

			if ($sth->execute($assoc)) {
				return $this->getId();
			}
		}
		/* else {
		 trigger_error('Acid : AcidModule::dbUpdate($updates), you must specify field updates',E_USER_WARNING);
		 }*/
	}

	/**
	 * Met à jour la base de données avec les valeurs de l'objet
	 * Si pas encore présent en base, on ajoute l'élément sinon on le met à jour
	 * Si $updates est la chaîne de carractères "all", met à jour tous les champs
	 * Sinon, met à jour uniquement les champs renseignés par le tableau $updates
	 *
	 *
	 * @param { string | array } $updates
	 *
	 * @return bool
	 */
	public function dbSave($updates='all') {
		return $this->getId() ? $this->dbUpdate($updates) : $this->dbAdd();
	}

	/**
	* Supprime l'enregistrement de l'objet d'identifiant $id de la base de données.
	*
	* @param mixed $id
	*/
	public function dbRemove($id=null) {

		if ($id === null) {
			$id = $this->getId();
		}

		$req = "DELETE FROM ".static::tbl()." WHERE ".static::tblId()."='".((int)$id)."';";
		if (AcidDB::exec($req)) {
			return $id;
		}

	}

	/**
	* Retourne le champ SQL entré prefixé du nom de table associé au module
	* @param string $value
	* @param string $prefix si défini, la valeur sera utilisée comme préfixe
	* @return string|unknown
	*/
	public static function dbPref($value,$prefix=null) {
		$prefix = $prefix===null ? static::tbl() : $prefix;

		if ($prefix!='') {
			return $prefix.'.'.$value;
		}

		return $value;
	}

	/**
	* Supprime le nom de table du nom de champs SQL saisi en entré
	* @param string $key nom du champs SQL
	* @return string|unknown
	*/
	public static function dbPrefRemove($key) {
		if (strpos($key,self::dbPref(''))!==false) {
			return substr($key,strlen(self::dbPref('')));
		}

		return $key;
	}

	/**
	* Retourne le module associé au nom de champs SQL saisi en entré si un résultat est trouvé
	* @param string $key nom du champs
	* @param array $mods liste des modules à inspecter
	* @return string|Ambigous <boolean, unknown>
	*/
	public static function dbPrefSearchModule($key,$mods=null) {
		$mods = ($mods===null) ? array(self::getCLass()) : $mods;
		$current = self::build();

		if (in_array($key,$current->getKeys())) {
			return $current;
		}

		$module_found = false;
		foreach ($mods as $module) {
			if (!$module_found) {
				$m = new $module();
				$check = substr($key,strlen($m->dbPref('')));
				$module_found = in_array($check,$m->getKeys()) ? $m : false;
			}
		}

		return $module_found;
	}

	/**
	* Génère une portion de code SQL (portion WHERE) en fonction des paramêtres en entrée
	* @param mixed $filter [ code SQL | array(array('field1','operator','value'),array('field2','operator2','value2'),'codeSQL3') ]
	* @param string $combo opérateur à utiliser pour lier les différents filtres
	* @param boolean $add_aquote si true, ajoute les antiquote au nom des champs
	* @return string
	*/
	public static function dbGenerateFilter($filter,$combo='AND',$add_aquote=true) {
		$filter_string = '';

		//String filter
		if (!is_array($filter)) {
			$filter_string = $filter;
		}
		//Array filter
		else {

			foreach ($filter as $f_val) {

				//Array valide
				if (is_array($f_val) && count($f_val) >= 3) {

					list($key,$method,$value) = $f_val;
					$start = isset($f_val[3]) ? $f_val[3] : '';
					$stop = isset($f_val[4]) ? $f_val[4] : '';

					//between
					if (strtolower($method) === 'between') {
						$akey = $add_aquote ? "`".$key."`" : $key;
						if (is_array($value)) {
							$bvalue = array('','');
							if (count($value)>1) {
								$bvalue[0] = $value[0];
								$bvalue[1] = $value[1];
							}else{
								$bvalue[0] = $value[0];
								$bvalue[1] = $value[0];
							}
							$between_val = "'".$bvalue[0]."' AND '".$bvalue[1]."'";
						}else{
							$between_val = $value;
						}
						$filter_string .= $akey." ".$method." ".$between_val." ".$combo." ";
					}
					//in (not)
					elseif (strtolower($method) === 'in' || strtolower($method) === 'not in') {

						$in_vals = array();
						if (count($value)) {
							foreach ($value as $val) {
								$in_vals[] = "'".addslashes($val)."'";
							}
						}

						if (count($in_vals)) {
							$akey = $add_aquote ? "`".$key."`" : $key;
							$filter_string .= $akey." ".$method." (".implode(',',$in_vals).") ".$combo." ";
						}else{
							$filter_string .= ((strtolower($method) === 'in') ? " 0 " : " 1 ").$combo." ";
						}

					}
					//default
					else {
						$akey = $add_aquote ? "`".$key."`" : $key;
						$filter_string .= $akey." ".$method." '".($start!==null?$start:'').addslashes($value).($stop!==null?$stop:'')."' ".$combo." ";
					}

				}
				//String filter
				else if(is_string($f_val)) {
					$filter_string .= " ".$f_val." ".$combo." ";
				}
				//Bad format
				else {
					trigger_error('Acid : dbList filter error, invalid param : ' . var_export($f_val,true),E_USER_WARNING);
				}

			}

			$filter_string = substr($filter_string,0,-strlen($combo)-1);

		}

		$filter_string = empty($filter_string) ? '' : "WHERE " . $filter_string . " ";

		return $filter_string;
	}

	/**
	* Génère une portion de code SQL (portion ORDER BY) en fonction des paramêtres en entrée
	* @param mixed $order [SQL CODE | array('field1'=>'ASC|DESC','field2'=>'ASC|DESC')]
	* @return string
	*/
	public static function dbGenerateOrder($order) {
		$order_string = '';

		//requête en chaine de caractères
		if (!is_array($order)){
			$order_string = $order;
		}

		//tri par valeur
		else {
			foreach ($order as $key=>$val) {

				if ($key === "()") {
					$order_string .= $val . ", ";
				}
				//tri selon un ordre défini par un tableau de valeurs
				elseif ($val && is_array($val) && is_array($val[0]) && $val[0]) {

					$way = isset($val[1]) ? $val[1] : 0;

					if (is_bool($val)) {
						$way = $way ? 'DESC':'ASC';
					}

					$order_string .= " FIELD(" . addslashes($key) . ",".addslashes(implode(',',$val[0])). ") ". $way . ", ";

				} else {
					if (is_bool($val)) {
						$val = $val ? 'DESC':'ASC';
					}
					$order_string .= "`".addslashes($key)."` " . $val . ", ";
				}

			}
			$order_string = substr($order_string,0,-2);
		}

		$order_string = empty($order_string) ? '' : "ORDER BY " . $order_string . " ";

		return $order_string;
	}

	/**
	* Génère une portion de code SQL (portion LIMIT ) en fonction des paramêtres en entrée
	* @param string $limit la limite
	* @return string
	*/
	public static function dbGenerateLimit($limit) {
		$limit_string = "";

		if (!empty($limit)) {
			if (strpos($limit,',') !== false) {
				list ($offset,$max) = explode(',',$limit);
				$limit_string = (int) $offset . ',' . (int)$max;
			}
			else {
				$limit_string = (int) $limit;
			}
			$limit_string = " LIMIT " . $limit_string;
		}

		return $limit_string;
	}

	/**
	* Génère une portion de code SQL (portion GROUP BY ) en fonction des paramêtres en entrée
	* @param array $fields liste des champss
	* @return string
	*/
	public static function dbGenerateGroupBy($fields=array()) {
		$group_by_string = "";

		if (!empty($fields)) {
			if (is_array($fields)) {
				$group_by_string = 'GROUP BY '.implode(',',$fields);
			}else{
				$group_by_string = 'GROUP BY '.$fields;
			}
		}

		return $group_by_string;
	}

	/**
	* Génère une portion de code SQL (portion SELECT ) en fonction des paramêtres en entrée
	* @param mixed $select [SQL Code | array(array(key,func,pref,as),array(key2,func2,pref2,as2))]
	* @return string
	*/
	public static function dbGenerateSelect($select='') {
		$select_string = "";

		if (!empty($select)) {
			if (is_array($select)) {
				$select_tab = array();
				foreach ($select as $sel) {

					if (is_array($sel)) {
						$key = isset($sel[0]) ? $sel[0] : '';
						$func = isset($sel[1]) ? $sel[1] : false;
						$prefix = isset($sel[2]) ? $sel[2] : '';
						$prefix = ($prefix===true) ? static::tbl() : $prefix;
						$as = isset($sel[3]) ? $sel[3] : '';

					}else{
						$key = $sel;
						$prefix =  '';
						$as =  '';
						$func = false;
					}

					$func = $func===true ? 'COUNT' : $func;

					if ($key) {
						$as_str = $as ? " AS '".$as."'" : '';
						$key_str = $func ? $func.'('.static::dbPref($key,$prefix).')' : ''.static::dbPref($key,$prefix).'';
						$select_tab[] = $key_str.$as_str;
					}
				}

				if ($select_tab) {
					$select_string =  'SELECT '.implode(', ',$select_tab);
				}

			}else{
				$select_string = 'SELECT '.$select;
			}
		}else{
			$select_string = 'SELECT *';
		}

		return $select_string;
	}

	/**
	* Generère un filtre FROM
	*
	* @param {array} $tables array( array('Table1',array('clé commune')) , array('Table2',array(array('clé tbl1','clé tbl2'))) )
	* @param string $join Méthode de jointure
	* @param string $first_as Prefixe pour la table principale
	*
	* @return array | int
	*/
	public static function dbGenerateFrom($tables=array(),$join='LEFT JOIN',$first_as='') {
		$first_tbl = static::tbl();
		$first_prefix = $first_as ? $first_as : $first_tbl;

		$from_string = 'FROM `'.$first_tbl.'`'.($first_as ? ' AS '.$first_as : '');

		if (!empty($tables)) {
			if (is_array($tables)) {
				$from_table = array();
				foreach ($tables as $data) {
					if (is_array($data)) {
						$as = '';
						$tbl = isset($data[0]) ? $data[0] : '';

						if (is_array($tbl)) {
							$as = isset($tbl[1]) ? $tbl[1] : '';
							$tbl = isset($tbl[0]) ? $tbl[0] : '';
						}

						$prefix = $as ? $as : $tbl;

						$on = isset($data[1]) ? $data[1] : '';
						if (is_array($on)) {
							if ($on) {
								$join_table = array();
								foreach ($on as $exp) {
									if (is_array($exp)) {
										if (count($exp)>1) {
											$a = $exp[0];
											$b = $exp[1];
											$join_table[] = static::dbPref($a,$first_prefix).' = '.static::dbPref($b,$prefix);
										}else{
											trigger_error('dbGenerateFrom() : wrong parameters for '.$tbl.' join',E_USER_ERROR);
										}
									}else{
										$join_table[] = static::dbPref($exp,$first_prefix).' = '.static::dbPref($exp,$prefix);
									}
								}
								$on = implode(' AND ',$join_table);
							}
						}


						$from_table[] = $join.' `'.$tbl.'`'.($as ? ' AS '.$as : '').($on ? ' ON '.$on : '');

					}else{
						$from_table[] = $join.' '.$data;
					}

				}

				$from_string .= ' '.implode(' ',$from_table);
			}else{
				trigger_error('dbGenerateFrom() : $tables must be an array',E_USER_ERROR);
			}
		}

		return $from_string;
	}

	/**
	* Retourne tous les éléments de la table SQL associée à l'objet en fonction de la configuration renseignée par les paramètress en entrée
	* Si $count a la valeur true, retourne seulement le nombre d'élements.
	*
	* @param mixed $select Commandes SQL de selection, array(array([Champs],[Fonction],[Prefixe],[Alias]))
	* @param {array | string} $filter Commandes SQL de filtrage, array(arrays([Champs],[Opérateurs],[Valeurs]))
	* @param {array | string} $order Commandes d'indexation SQL, array(arrays( [Champs]=>{true : DESC | false : ASC} ))
	* @param {int | string} $limit Commande de limite SQL
	* @param bool $count Valeur par défaut : false ( ASC )
	* @param string $combo Opérateur logique associant les éléments de $filter Valeur par défaut 'AND'
	* @param mixed $group_by Commandes SQL de group by
	*
	* @return array | int
	*/
	public static function dbSelList($select='', $filter='', $order='', $limit='', $count=false, $combo='AND',$group_by='') {

		// Building WHERE query
		$filter_string = static::dbGenerateFilter($filter,$combo);
		$filter_group_by = static::dbGenerateGroupBy($group_by);
		$filter_group_by = $filter_group_by ? ' '.$filter_group_by : $filter_group_by;

		// Return COUNT()
		if ($count) {
			$req = 	static::dbGenerateSelect(array(array('*',true,false,'nb')))." " .static::dbGenerateFrom()." " . $filter_string . $filter_group_by;
			$res = AcidDB::query($req)->fetch(PDO::FETCH_ASSOC);
			return (int)$res['nb'];


			// Return array()
		} else {

			// Building ORDER BY query
			$order_string = static::dbGenerateOrder($order);
			$order_string = $order_string ? ' '.$order_string : $order_string;


			// Building LIMIT query
			$limit_string = static::dbGenerateLimit($limit);
			$limit_string = $limit_string ? ' '.$limit_string : $limit_string;

			$req = 	static::dbGenerateSelect($select)." " .static::dbGenerateFrom()." ". $filter_string . $filter_group_by .  $order_string . $limit_string;
			return AcidDB::query($req)->fetchAll(PDO::FETCH_ASSOC);
		}
	}


	/**
	* Retourne tous les éléments de la table SQL associée à l'objet en fonction de la configuration renseignée par les paramètress en entrée
	* Si $count a la valeur true, retourne seulement le nombre d'élements.
	*
	* @param {array | string} $filter Commandes SQL de filtrage, array(arrays([Champs],[Opérateurs],[Valeurs]))
	* @param {array | string} $order Commandes d'indexation SQL, array(arrays( [Champs]=>{true : DESC | false : ASC} ))
	* @param {int | string} $limit Commande de limite SQL
	* @param bool $count Valeur par défaut : false ( ASC )
	* @param string $combo Opérateur logique associant les éléments de $filter Valeur par défaut 'AND'
	* @param mixed $group_by Commandes SQL de group by
	*
	* @return array | int
	*/
	public static function dbList($filter='', $order='', $limit='', $count=false, $combo='AND',$group_by='') {
		return static::dbSelList('',$filter, $order, $limit, $count, $combo ,$group_by);
	}

	/**
	* Retourne tous les éléments de la table SQL associée à l'objet en fonction de la configuration renseignée par les paramètress en entrée
	* Si $count a la valeur true, retourne seulement le nombre d'élements.
	* @param array	$mods array('Module1'=>array('key1'),'Module2'=>array(array('keymod','keymod2')),'Module3'=>'str')
	* @param {array | string} $filter Commandes SQL de filtrage, array(arrays([Champs],[Opérateurs],[Valeurs]))
	* @param {array | string} $order Commandes d'indexation SQL, array(arrays( [Champs]=>{true : DESC | false : ASC} ))
	* @param {int | string} $limit Commande de limite SQL
	* @param bool $count Valeur par défaut : false ( ASC )
	* @param string $combo Opérateur logique associant les éléments de $filter Valeur par défaut 'AND'
	* @param mixed $group_by Commandes SQL de group by
	*
	* @return array | int
	*/
	public static function dbListMods($mods=array(),$filter='', $order='', $limit='', $count=false, $combo='AND',$group_by='') {

		// Building WHERE query
		$filter_string = static::dbGenerateFilter($filter,$combo,false);
		$filter_group_by = static::dbGenerateGroupBy($group_by);
		$filter_group_by = $filter_group_by ? ' '.$filter_group_by : $filter_group_by;

		$select = array();
		$from = array();

		if ($mods) {

			$mods_tab[static::getClass()] = false;
			foreach ($mods as $k=>$m) {
				$mods_tab[$k] = $m;
			}

			foreach ($mods_tab as $module => $keys) {
				foreach (Acid::mod($module)->getKeys() as $key) {
					$select[] = array($key,false,Acid::mod($module)->tbl(),Acid::mod($module)->dbPref($key));
				}

				if ($keys) {
					$my_from = array();
					$my_from[] = Acid::mod($module)->tbl();
					$my_from[] = $keys;
					$from[] = $my_from;
				}
			}
		}

		// Return COUNT()
		if ($count) {
			$req = 	static::dbGenerateSelect(array(array('*',true,false,'nb')))." " .static::dbGenerateFrom($from)." " . $filter_string . " " . $filter_group_by ;
			$res = AcidDB::query($req)->fetch(PDO::FETCH_ASSOC);
			return (int)$res['nb'];


			// Return array()
		} else {

			// Building ORDER BY query
			$order_string = static::dbGenerateOrder($order);
			$order_string = $order_string ? ' '.$order_string : $order_string;


			// Building LIMIT query
			$limit_string = static::dbGenerateLimit($limit);
			$limit_string = $limit_string ? ' '.$limit_string : $limit_string;

			$req = 	static::dbGenerateSelect($select)." " .static::dbGenerateFrom($from)." ". $filter_string . $filter_group_by . $order_string . $limit_string;
			//return $req;
			return AcidDB::query($req)->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	/**
	* Generère une portion de code SQL (DELETE FROM) en fonction des paramètres en entrée
	* @param mixed $filter Commandes SQL de filtrage, array(arrays([Champs],[Opérateurs],[Valeurs]))
	* @param mixed $combo Opérateur logique associant les éléments de $filter Valeur par défaut 'AND'
	* @return number
	*/
	public static function dbExecuteRemove($filter='',$combo='AND') {
		$filter = ($filter===true) ? 1 : $filter;
		$filter = ($filter===false) ? 0 : $filter;
		$req = '';

		$filter_string = static::dbGenerateFilter($filter,$combo);
		if ($filter_string) {
			$req = 	"DELETE " .static::dbGenerateFrom()." ". $filter_string;
		}

		return AcidDB::exec($req);
	}

	/**
	* Retourne le nombre d'élements de la table SQL associée à l'objet en fonction de la configuration renseignée par les paramètress en entrée.
	*
	* @param {array | string} $filter Commandes SQL de filtrage, array(arrays([Champs],[Opérateurs],[Valeurs]))
	* @param string $combo Opérateur logique associant les éléments de $filter Valeur par défaut 'AND'
	*
	* @return int
	*/
	public static function dbCount($filter='',$combo='AND') {
		return static::dbList($filter,'','',true,$combo);
	}

	/**
	* Joint plusieures tables
	* Retourne tous les éléments de la table SQL associée à l'objet en fonction de la configuration renseignée par les paramètresss en entrée
	* Si $count a la valeur true, retourne seulement le nombre d'élements.
	*
	* @param array $mod_elts Liste des champs prélevés array( string{ [nom table] | [nom table/alias] } => string|array [champs prélevés] )
	* @param array $mod_rel Liste des relations array( string [alias table] => array( [alias1]=>[champ1],[alias2]=>[champs2] ) )
	* @param {array | string} $filter Commandes SQL de filtrage, array(arrays([Champs],[Opérateurs],[Valeurs]))
	* @param {array | string} $order Commandes d'indexation SQL, array(arrays( [Champs]=>{true : DESC | false : ASC} ))
	* @param {int | string} $limit Commande de limite SQL
	* @param string $combo Opérateur logique associant les éléments de $filter Valeur par défaut 'AND'
	* @param boolean $count Valeur par défaut : false ( ASC )
	*
	* @return array | int
	*/
	public static function dbJoinedList($mod_elts,$mod_rel,$filter='',$order='',$limit='',$combo="AND",$count=false) {

		if (!is_array($mod_elts)) {
			trigger_error('sqlJoinedList() : $mod_elts must be an array',E_USER_ERROR);
		}

		if (!is_array($mod_rel)) {
			trigger_error('sqlJoinedList() : $mod_rel must be an array',E_USER_ERROR);
		}


		$knew_keys = array();
		$select = '';
		$from = '';

		$mods = array();
		foreach ($mod_elts as $mod_name=>$values) {

			if (strpos($mod_name,'/') !== false) {
				list ($mod_name,$mod_alias) = explode('/',$mod_name);
			} else {
				$mod_alias = $mod_name;
			}

			$mods[$mod_alias] = $mod_name;

			$keys = array();
			if ($count) {
				$select = ' COUNT(*) AS nb  ';
			}elseif ($values == '*') {
				$values = array();
				$keys = Acid::mod($mod_name)->getVals();
				foreach ($keys as $key=>$val) {
					$values[$key] = $key;
				}
			}elseif ($values == '.*') {
				$values = array();
				$keys = Acid::mod($mod_name)->getVals();
				foreach ($keys as $key=>$val) {
					$values[$key] = $mod_alias.'.'.$key;
				}
			}

			if (is_array($values)) {
				foreach ($values as $key=>$alias) {
					if (is_int($key)) {
						$key = $alias;
					}
					if (!in_array($alias,$knew_keys)) {
						array_push($knew_keys,$alias);
					} else {
						trigger_error('dbJoinedList : Key "'.$alias.'" already exists',E_USER_ERROR);
					}

					$select .= ' `'.$mod_alias.'`.`'.$key.'` AS `'.$alias.'`,';
				}
			}
			else{
				trigger_error('dbJoinedList : Need to define elements array for module ' . $mod_name,E_USER_ERROR);
			}
		}
		if (!empty($select)) {
			$select = substr($select,0,-1);
		}


		foreach ($mod_rel as $mod_alias => $tbl) {
			if (is_int($mod_alias)) {
				$from .= Acid::mod($mods[$tbl])->tbl(). " AS ".$tbl." " . '    ';
			}else {
				$mod_name = $mods[$mod_alias];

				$from .= ' LEFT JOIN '.Acid::mod($mod_name)->tbl().' AS '.$mod_alias.' ON ';

				$mods_keys = array_keys($tbl);
				list ($mod1,$mod2) = array_keys($tbl);
				$from .= ' '. $mod1.'.'.$tbl[$mod1].'='.$mod2.'.'.$tbl[$mod2] . ' ';

			}
		}



		$filter_string = '';
		if (!is_array($filter)) {
			$filter_string = empty($filter) ? '' : "WHERE " . $filter . " ";
		}
		else {
			foreach ($filter as $f_val) {
				if (is_array($f_val) && count($f_val)>=4) {
					list($mod,$key,$method,$value) = $f_val;
					$start = isset($f_val[4]) ? $f_val[4] : '';
					$stop = isset($f_val[5]) ? $f_val[5] : '';
					if (strtolower($method) === 'in' || strtolower($method) === 'not in') {

						$in_vals = array();
						foreach ($value as $val) {
							$in_vals[] = "'".addslashes($val)."'";
						}

						if (count($in_vals)) {
							$filter_string .= "`".$mod."`.`".$key."` ".$method." (".implode(',',$in_vals).") ".$combo." ";
						}else{
							$filter_string .= ((strtolower($method) === 'in') ? " 0 " : " 1 ").$combo." ";
						}

					} else {
						$filter_string .= "`".$mod."`.`".$key."` ".$method." '".$start.addslashes($value).$stop."' ".$combo." ";
					}
				}else if(is_string($f_val)) {
					$filter_string .= " ".$f_val." ".$combo;
				}
			}
			$filter_string = empty($filter_string) ? '' : "WHERE " . substr($filter_string,0,-strlen($combo)-1) . " ";
		}


		if (!is_array($order)) {
			$order = array();
		}


		$order_string = '';
		if (!is_array($order)){
			$order_string = $order;
		} else {
			foreach ($order as $key=>$val) {
				if ($key === "()") {
					$order_string .= $val . ", ";
				} else {
					if (is_bool($val)) {
							$val = $val ? 'DESC':'ASC';
					}
					$order_string .= "".addslashes($key)." " . $val . ", ";
				}
			}
			$order_string = substr($order_string,0,-1);
		}
		$order_string = empty($order_string) ? '' : "ORDER BY " . $order_string . " ";


		$order_string = substr($order_string,0,-2) . " ";

		if (!empty($limit)) {
			if (strpos($limit,',') !== false) {
				list ($offset,$max) = explode(',',$limit);
				$limit_string = (int) $offset . ',' . (int)$max;
			}
			else {
				$limit_string = (int) $limit;
			}
		}




		$req =	"SELECT " . $select . " " .
				"FROM " . $from . " " .
		$filter_string ." ";

		if (!empty($order_string)) $req .= $order_string . " ";
		if (!empty($limit_string)) $req .= "LIMIT " . $limit_string;

		Acid::log('sql','MYSQL - dbJoinedList - ' . $req);

		if ($count) {
			if ($res = AcidDB::query($req)->fetch(PDO::FETCH_ASSOC)) {
				return $res['nb'];
			}
		}

		return AcidDB::query($req)->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
     * Joint plusieures tables
	* Retourne le nombre d'éléments de la table SQL associée à l'objet en fonction de la configuration renseignée par les paramètress en entrée
	*
	* @param unknown_type $mods
	* @param unknown_type $join
	* @param unknown_type $filter
	* @param unknown_type $combo
	* @return Ambigous <multitype:, mixed>
	*/
	public static function dbJoinedCount($mods,$join,$filter='',$combo='AND') {
		return static::dbJoinedList($mods,$join,$filter,'','',true,$combo);
	}

	/**
	* Simplifie le tableau en entrée
	* @param array $filter
	* @return array
	*/
	public static function convJoinedFilter($filter) {
		$simple = array();
		foreach ($filter as $t) {
			$s = array();
			$i = 0;
			foreach ($t as $v) {
				if ($i++) $s[] = $v;
			}
			$simple[] = $s;
		}
		return $simple;
	}

	/**
	* Initialise l'objet unique correspondant aux contraintes  renseignées par le tableau en entrée
	* Renvoi true en cas de succés, false sinon.
	*
	* @param array $tab
	* @param boolean $limit si vrai, retourne le premier élément parmis ceux trouvés, sinon retourne une valeur que si cette dernière est unique
	* @param array $order
	* @return boolean
	*/
	public function dbInitSearch($tab,$limit=false,$order=array()) {
		$filter = array();
		foreach ($tab as $key => $val) {
			if (isset($this->vars[$key])) {
				$filter[] = array($key,'=',$val);
			}
		}
		if (!empty($filter)) {
			$my_limit = ($limit===false) ? '' : $limit;
			$res = $this->dbList($filter,$order,$my_limit);

			$go_on = ($limit===false) ? (count($res) === 1) : (count($res) > 0);
			if ($go_on) {
				$this->initVars($res[0],true);
				return true;
			}
		}
		return false;
	}

	/**
	* 	Recupére la valeur du prochain identifiant de la table SQL associé à l'objet.
	*
	*
	* @return string
	*/
	public function dbNextId() {
		$req =		"SELECT AUTO_INCREMENT FROM information_schema.TABLES " .
					"WHERE TABLE_SCHEMA='" .Acid::get('db:base'). "' " .
					"AND TABLE_NAME='" .Acid::get('db:prefix').static::tbl(). "'";

		$res = AcidDB::query($req)->fetch(PDO::FETCH_ASSOC);

		return ($res) ? $res['AUTO_INCREMENT'] : '';
	}

	/**
	 * Retourne le nom de la variable correspondant au cache time
	 */
	public static function cacheTimeKey() {
		return Acid::get('modcache:key_time') ? Acid::get('modcache:key_time') : 'cache_time';
	}

	/**
	 * Retourne un timestamp de référence pour le cache
	 * @param string $force force une valeur à time() si aucune configuration
	 */
	public function getCacheTime($force=null) {

		$force = $force !==null ? $force : (Acid::exists('modcache:force_time') ? Acid::get('modcache:force_time') : false);
		$key = static::cacheTimeKey();

		if (isset($this->vars[$key])) {
			return $this->get($key);
		}

		return $force ? time() : null;
	}

	/**
	 * Met à jour la variable cache time
	 * @param string $value si non définie, retroune time()
	 */
	public function updateCacheTime($value=null) {

		if (isset($this->vars[static::cacheTimeKey()])) {
			$value = $value===null ? time() : $value;
			$changes = $this->initVars(array(static::cacheTimeKey()=>$value));
			$this->dbUpdate($changes);
			return $value;
		}

	}

	/**
	* Formate l'url en fonction de l'AcidVar de la clé saisi en entrée
	* @param string $key nom du paramètre
	* @param string $url pour forcer l'url
	* @param string $format format à appliquer à l'url
	*/
	public static function genUrlKey($key,$url=null,$format=null,$cache_time=null) {

		$class = get_called_class();
		$module = new $class();
		if (isset($module->vars[$key])) {

			$module->initVars(array($key=>$url));

			if ($format===null) {
				$resurl = $module->vars[$key]->getUrl();
			}else{
				$resurl = $module->vars[$key]->getUrl($format);
			}

			if ($cache_time) {
				$cachetimekey = Acid::get('modcache:key_time_name') ? Acid::get('modcache:key_time_name') : 'cache_time';
				$resurl .= (strpos($resurl, '?')===false) ? ('?'.$cachetimekey.'='.$cache_time) : '';
			}

			return $resurl;

		}else{
			trigger_error('genUrlKey : unable to find module\'s key : '. $key . ' in ' . $class,E_USER_ERROR);
		}

	}

	/**
	* Retourne l'url associé à l'AcidVar de la clé saisi en entrée
	* @param string $key
	* @param string $format
	*/
	public function getUrlKey($key,$format=null) {
		return $this->genUrlKey($key,$this->get($key),$format,$this->getCacheTime());
	}

	/**
	* Regénère les images associées à l'AcidVar de la clé saisi en entrée
	* @param string $key nom du paramètre
	* @param array $format_filter liste des formats à traiter
	* @return boolean
	*/
	public static function regenImagesKey($key,$format_filter=null) {
		$class = get_called_class();
		$module = new $class();

		if (isset($module->vars[$key])) {

			$res = $module->dbList();

			Acid::log('maintenance','Regenerating all formats of '.$class.'::'.$key.'...');

			foreach ($res as $mod) {
				$t_module = new $class();
				$t_module->initVars($mod);
				if ($t_module->get($key)) {
					$t_module->vars[$key]->regen($format_filter);
				}
				Acid::log('maintenance',$t_module->getId().' done.');
			}

			return true;
		}else{
			trigger_error('genUrlKey : unable to find module\'s key : '. $key . ' in ' . $class,E_USER_ERROR);
		}
	}

	/**
	* Regénère toutes les images associées au module
	* @param array $keys liste des paramètres à traiter
	* @return multitype:unknown
	*/
	public static function regenAll($keys=null) {
		$mod = static::build();

		$keys = ($keys===null)  ? array_keys($mod->getVarsImages()) : $keys;

		$done = array();
		if (count($keys)) {
			foreach ($keys as $key) {
				if ($mod->regenImagesKey($key)) {
					$done[] = $key;
				}
			}
		}

		return $done;
	}

	/**
	 * Lance le processus d'upload pour la clé en entrée
	 * @param string $key clé du fichier
	 * @param string $val chemin vers le fichier
	 * @param string $name nom du fichier
	 */
	public function exeUploadKey($key,$val,$name=null) {
		$vals['tmp_'.$key] = $val;
		if ($name) {
			$vals['tmp_name_'.$key] = $name;
		}

		$filename = '';
		if ($this->vars[$key]->uploadProcess($this->getId(),$key,$filename,array(),$vals)) {
			$this->dbUpdate(array($key));
		}
	}

	/**
	* Retourne la liste des clés du modules étant du type saisi en entrée
	* @param array $types liste des types acceptés
	* @return multitype:unknown
	*/
	public function getVarsByType($types) {
		$tab = array();
		foreach ($this->vars as $key =>$val) {
			if (in_array(get_class($val),$types)) {
				$tab[$key]=$val;
			}
		}

		return $tab;
	}

	/**
	* Retourne la liste des clés "fichier" du modules
	* @return Ambigous <multitype:unknown, multitype:unknown >
	*/
	public function getVarsFiles() {
		return $this->getVarsByType(array('AcidVarFile'));
	}

	/**
	* Retourne la liste des clés "images" du modules
	* @return Ambigous <multitype:unknown, multitype:unknown >
	*/
	public function getVarsImages() {
		return $this->getVarsByType(array('AcidVarImage'));
	}

	/**
	* Retourne la liste des clés "nécéssitant un upload" du modules
	* @return Ambigous <multitype:unknown, multitype:unknown >
	*/
	public function getUploadVars() {
		return $this->getVarsByType(array('AcidVarFile','AcidVarImage'));
	}

	/**
	* Préparation du module pour le processus POST
	* @param array $vals les valeurs à traiter
	* @param string $do le type d'action effectué
	* @param array $config
	*/
	public function postConfigure($vals=array(),$do=null,$config=null) {
		if ($config!==null) {
			$this->setConfig(null,$config);
		}
	}

	/**
	* Traitement lors du succès d'un processus POST
	* @param array $vals les valeurs à traiter
	* @param string $do le type d'action effectué
	* @return boolean
	*/
	public function postSuccess($vals=array(),$do=null) {

		//cache time
		if (in_array($do, array('add','update'))) {
			$this->updateCacheTime();
		}

		return true;
	}

	/**
	* Traite la procédure d'ajout d'élément depuis un formulaire.
	  *
	* @param array $vals
	* @param mixed $dialog
	*
	* @return object | bool
	*/
	public function postAdd($vals,$dialog=null) {
		$class = $this->getClass();
		$obj = new $class();
		$obj->postConfigure($vals,'add',$this->getConfig());


		$obj->initVars($vals);
		if ($obj->dbAdd()) {
			$upload_success = true;
			$success = array();
			foreach ($obj->getUploadVars() as $key=>$val) {
				if ( (!empty($_FILES[$key])) || (!empty($vals['tmp_'.$key])) ) {
					if ($val->uploadProcess($obj->getId(),$key)) {
						$success[] = $key;
					}else{
						$upload_success = false;
					}
				}
			}

			$obj->dbUpdate($success);

			$dialog = $dialog===null ? $this->getDialogDo('add') : $dialog;
			if ($dialog) {
				AcidDialog::add('banner', $this->getDialogMessage('add',$dialog));
			}

			//return (!$upload_success) ? false : $obj;
			if ($obj->postSuccess($vals,'add')) {
				return  $obj;
			}else{
				return false;
			}

		} else {
			return false;
		}
	}

	/**
	* Traite la procédure de mise à jour d'un élément depuis un formulaire.
	  *
	* @param array $vals
	* @param mixed $dialog
	*
	* @return object | bool
	*/
	public function postUpdate($vals,$dialog=null) {
		$class =  $this->getClass();
		$obj = new $class($vals[static::tblId()]);
		$obj->postConfigure($vals,'update',$this->getConfig());

		$old = $obj;

		if ($obj->getId()) {

			$changes = $obj->initVars($vals);

			foreach ($obj->getUploadVars() as $key=>$file) {
				if ($file->uploadProcess($obj->getId(),$key)) {
					$changes[]=$key;
				}
			}

			$obj->dbUpdate($changes);

			$dialog = $dialog===null ? $this->getDialogDo('update') : $dialog;
			if ($dialog) {
				AcidDialog::add('banner', $this->getDialogMessage('update',$dialog));
			}

			$vals['obj_before'] = $old;
			if ($obj->postSuccess($vals,'update')) {
				return  $obj;
			}else{
				return false;
			}

		}
		return false;
	}

	/**
	* Traite la procédure de suppression d'un élément depuis un formulaire.
	*
	* @param mixed $id Identifiant de l'élément
	* @param mixed $dialog
	*
	* @return bool
	*/
	public function postRemove($id=null,$dialog=null) {
		$id = ($id===null) ? $this->getId() : $id;
		if ($this->dbCount(array(array(static::tblId(),'=',$id)))) {
			$class = get_called_class();
			$obj = new $class($id);

			$success = true;
			foreach ($obj->getUploadVars() as $key => $val) {
				if (!$val->fsRemove()) {
		    		$success = false;
		    	}
			}

			if ($success) {
				$obj->dbRemove();

				$dialog = $dialog===null ? $this->getDialogDo('remove') : $dialog;
				if ($dialog) {
					AcidDialog::add('banner', $this->getDialogMessage('remove',$dialog));
				}

				if ($obj->postSuccess(array(static::tblId()=>$id),'remove')) {
					return  $obj;
				}else{
					return false;
				}
			}
		}
		return false;
	}

	/**
	* Retourne le niveau d'accès à l'action en entrée
	*
	* @param string $do
	*
	* @return int
	*/
	public function getUserAccess($do=null) {
		if ($this->getUserPermission($do)) {
			return true;
		}elseif (User::curLevel($this->getACL($do))) {
			return true;
		}else{
			$acl_key_comp = $this->getACLKeys($do);
			if ($acl_key_comp) {
				foreach ($acl_key_comp as $key => $min_level) {
					if (User::curLevel($min_level)) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	* Retourne le niveau d'accès à l'action en entrée
	*
	* @param string $do
	*
	* @return int
	*/
	public function getACL($do=null) {

		$acl_def = isset($this->config['acl']['default']) ? $this->config['acl']['default'] : Acid::get('lvl_def');
		if ($do==null) {
			return $acl_def;
		}

		$acl_comp = isset($this->config['acl'][$do]) ? $this->config['acl'][$do] : $acl_def;

		return $acl_comp;
	}

	/**
	* Retourne le tableau associatif clé=>niveau correspondant à l'action en entrée
	*
	* @param string $do
	*
	* @return int
	*/
	public function getACLKeys($do=null) {
		$acl_key_def = isset($this->config['acl']['keys']) ? $this->config['acl']['keys'] : false;

		if ($do==null) {
			return $acl_key_def;
		}

		$acl_key_comp = isset($this->config['acl'][$do]['keys']) ? $this->config['acl'][$do]['keys'] : $acl_key_def;

		if ($acl_key_comp) {
			if ($upkeys = $this->getUploadVars())  {
				foreach (array_keys($upkeys) as $uk) {
					if (isset($acl_key_comp[$uk])) {
						$acl_key_comp['tmp_'.$uk] = isset($acl_key_comp['tmp_'.$uk]) ?  : $acl_key_comp[$uk];
						$acl_key_comp['tmp_name_'.$uk] = isset($acl_key_comp['tmp_name_'.$uk]) ?  : $acl_key_comp[$uk];
					}
				}
			}
		}

		return $acl_key_comp;
	}

	/**
	* Définit les droits d'accés de l'utilisateur sur les éléments renseignés en entrée
	* Retourne un tableau avec uniquement les éléments dont l'accés est autorisé, ou false si l'utilisateur n'a aucun droit.
	*
	* @param string $do
	* @param array $vals
	*
	* @return array|bool
	*/
	public function postACL($do,$vals) {
			$acl_comp = $this->getACL($do);
			$acl_key_comp = $this->getACLKeys($do);

			$level = User::curLevel();

			$permission = $this->getUserPermission($do);


				// Global mod authorization
				if (($permission) || ($level >= $acl_comp)) {
					Acid::log('debug',	get_called_class().'::exePost() ' . $do . ' globaly authorized');
					return $vals;
				} else {
					$tab = array();

					// Global key authorization
					if ($acl_key_comp) {
						foreach ($acl_key_comp as $key => $min_level) {
							if (isset($vals[$key]) && $level >= $min_level) {
								$tab[$key] = $vals[$key];
							}
						}
					}

					return $tab;
				}

		Acid::log('debug',	get_called_class().'::exePost() ' . $do . ' permission denied');
		return false;
	}

	/**
	* Définit une permission pour le module
	*
	* @param string $do l'action associée; default : tous
	* @param int $id identifiant
	* @param string $type type de permission (id_group, id_user, level)
	*
	* @return string
	*/
	public function setPermission($do,$id,$type='id_user') {
		Acid::log('permission',$this::getClass().' '.$do.' : adding permission for '.$type.' '.$id);
		//$GLOBALS['acid']['permission'][$this::TBL_NAME][$do][$type][]=$id;
		Acid::add('permission:'.$this::TBL_NAME.':'.$do.':'.$type,$id);
	}

	/**
	* Recupère les permissions du module
	*
	* @param string $do l'action associée; default : tous
	* @param string $type type de permission (id_group, id_user, level)
	*
	* @return string
	*/
	public function getPermissions($do=null,$type=null) {

		if (Acid::get('permission_active')) {

			if (Acid::exists('permission:'.static::TBL_NAME)) {

				if ($do) {
					if (Acid::exists('permission:'.static::TBL_NAME.':'.$do)) {
						if ($type) {
							if (Acid::exists('permission:'.static::TBL_NAME.':'.$do.':'.$type)) {
								return Acid::get('permission:'.static::TBL_NAME.':'.$do.':'.$type);
							}
						}else{
							return Acid::get('permission:'.static::TBL_NAME.':'.$do);
						}
					}
				}else{
					return Acid::get('permission:'.static::TBL_NAME);
				}

			}

		}

		return array();
	}

	/**
	* Permet de récupérer un $do de ce module qui aurait été transmis en $_POST.
	* Ex : Pour le module Actu, vérifie la présence de "actu_do"
	*
	* @return La valeur du $do en question s'il existe, FALSE sinon.
	*/
	public static function getPostDo() {
		return static::getValsDo($_POST);
	}

	/**
	* Permet de récupérer un $do de ce module qui aurait été transmis en $_GET.
	* Ex : Pour le module Actu, vérifie la présence de "actu_do"
	*
	* @return La valeur du $do en question s'il existe, FALSE sinon.
	*/
	public static function getGetDo() {
		return static::getValsDo($_GET);
	}

	/**
	* Permet de récupérer un $do de ce module qui aurait été transmis en $vals.
	* Ex : Pour le module Actu, vérifie la présence de "actu_do"
	* @param array $vals tableau à inspecter
	*
	* @return La valeur du $do en question s'il existe, FALSE sinon.
	*/
	public static function getValsDo($vals) {
		return ( isset($vals[static::preKey('do')]) ? $vals[static::preKey('do')] : false );
	}

 	/**
	* Recupère les permissions de l'utilisateur sur le module
	*
	* @param string $do l'action associée; default : tous
	* @param string $type type de permission (id_group, id_user, level)
	* @param int $id_user identifiant utilisateur
	*
	* @return string
	*/
	public function getUserPermission($do=null,$type=null,$id_user=null) {
		$user = ($id_user === null) ? User::curUser() : new User($id_user);
		$types = $type ? array($type) : Acid::get('permission_groups');


		$def_do = Acid::exists('permission:'.static::TBL_NAME) ? Acid::get('permission:'.static::TBL_NAME) : array();
		$to_do = $do ? array($do) : array_keys($def_do);

		foreach ($to_do as $d) {
			foreach ($types as $t) {

				switch ($t) {
					case 'id_group' :
						$permission = $this->getPermissions($d,$t);
						$permission_def = $this->getPermissions('default',$t);
						foreach ($user->getGroups() as $idg) {
							if (in_array($idg,$permission) || in_array($idg,$permission_def)) {
								Acid::log('permission',	get_called_class().'::getUserPermission() ' . $d . '=> ' . $t .' permission');
								return true;
							}
						}
					break;

					default:
						if (in_array($user->get($t),$this->getPermissions($d,$t))) {
							Acid::log('permission',	get_called_class().'::getUserPermission() ' . $d . '=> ' . $t .' permission');
							return true;
						}elseif (in_array($user->get($t),$this->getPermissions('default',$t))) {
							Acid::log('permission',	get_called_class().'::getUserPermission() ' . $d . '=> ' . $t .' permission');
							return true;
						}
					break;
				}
			}
		}

		Acid::log('permission',	get_called_class().'::getUserPermission() ' . $do . ' permission denied');
		return false;
	}

	/**
	* Définit le nom des clés contrôlées
	*
	* @param string $key
	*
	* @return string
	*/
	public function checkLabel($key) {
		if (isset($this->vars[$key])) {
			return $this->getLabel($key);
		}
		return  '';
	}

	/**
	* Retourne un tableau contenant les clés à contrôler
	*
	* @param string $do
	*
	* @return array()
	*/
	protected function getControlledKeys($do) {
		switch ($do) {
			default :
				return  array();
			break;
		}

	}

	/**
	* Retourne un tableau contenant les clés à contrôler
	*
	* @param string $tab tableau à controller
	* @param string $do action effectuée
	*
	* @return array()
	*/
	protected function checkVals($tab,$do) {

		//récupération des clés controllées
		$controlled_keys[$do] =  $this->getControlledKeys($do);

		//creation des sessions
		$session_time[$do]  = 100;
		AcidSession::tmpSet(static::preKey($do),$tab,$session_time[$do]);

		//initialisation
		$missing = array();

		$missing_error = '';
		$text_error = '';
		foreach ($controlled_keys[$do] as $key) {
			switch ($key) {
				default :
					if (empty($tab[$key])) {
						$missing[] = $key;
						$missing_error .= $this->checkLabel($key) . '<br />';
					}
				break;
			}
		}

		//s'il n'y a pas d'erreurs
		if (!$missing) {
			AcidSession::tmpKill(static::preKey($do));
			return $tab;
		}
		//en cas d'erreurs
		else{
			$error = ($missing_error? Acid::trad('checkvals_error_plur').'<br />' . $missing_error : '') .
					 '<br />' . $text_error;
			AcidDialog::add('banner',$error);
			return false;
		}
	}

	/**
	* Controlleur d'execution d'un formulaire.
	*
	* @return {object, bool}
	*/
	public function exePost() {

		if (static::getPostDo()) {

			Acid::log('debug',get_called_class().'::exePost() ' . static::getPostDo());

			switch (static::getPostDo())
			{
				case 'add' :
					if ($vals = $this->postACL('add',$_POST)) {
						if ($vals = $this->checkVals($vals,'add')) {
 							return $this->postAdd($vals);
						}
						unset($_POST['next_page']);
					}
					break;


				case 'update' :
					if ($vals = $this->postACL('update',$_POST)) {
						if ($vals = $this->checkVals($vals,'update')) {
							if (isset($_POST[static::tblId()])) {
								$vals[static::tblId()] = $_POST[static::tblId()];
								return $this->postUpdate($vals);
							}
						}
					}
					break;


				case 'del' :
					if ($vals = $this->postACL('del',$_POST)) {
						if (isset($vals[static::tblId()])) {
							//if ($vals = $this->checkVals($vals,'del')) {
								return $this->postRemove($vals[static::tblId()]);
							//}
						}
					}
					break;
			}

		}

		return false;
	}

	/**
	* Execution d'un formulaire.
	*
	*
	* @return {object, bool}
	*/
	public function exePostProcess() {
		$res = $this->exePost();
		$this->treatAjax($res);
		return $res;
	}

	/**
	* Gestion Ajax d'un formulaire.
	*
	* @param mixed $res
	* @param array $vals
	* @param array $config
	*
	* @return {object, bool}
	*/
	public function treatAjax($res,$vals=null,$config=array()) {
		$vals = ($vals===null) ? (Acid::get('ajax:tmp_datas') ? Acid::get('ajax:tmp_datas') : $_POST) : $vals;
		$custom_js  = (Acid::get('ajax:tmp_js') ? Acid::get('ajax:tmp_js') : '');

		if (!empty($vals[Acid::get('post:ajax:key')])) {

			//dialog
			AcidDialog::initDialog();
			$sess = AcidSession::getInstance();
			$dialog = $sess->data['dialog'];


			//config
			$config['dialog'] = $dialog;
			$config['obj'] = $res ? $res->getVals() : array();
			$config['datas'] = isset($vals['datas']) ? $vals['datas'] : array();
			$config['success'] = !empty($res);
			$config['content'] = isset($config['content']) ? $config['content'] : '';
			$config['js'] = isset($config['js']) ? $config['js'] : $custom_js;

			echo json_encode($config);
			exit();

		}
	}

	/**
	 * Listing à retourner lors d'un GET REST
	 * @param array $config
	 */
	public function restGet($config=array()) {
		if (!empty($this->config['rest']['active'])) {
			return $this->getVals();
		}else{
			AcidUrl::error403();
		}
	}

	/**
	 * Listing à retourner lors d'un GET REST
	 * @param array $config
	 */
	public function restList($config=array()) {
		if (!empty($this->config['rest']['active'])) {
			$filter = isset($config['filter']) ?  $config['filter'] : array();
			$order = isset($config['order']) ?  $config['order'] : array();
			$limit = isset($config['limit']) ?  $config['limit'] : array();
			$count =  isset($config['count']) ?  $config['count'] :false;
			return $this->dbList($filter,$order,$limit,$count);
		}else{
			AcidUrl::error403();
		}
	}


	/**
	* Définit une session relative à la table SQL associée à l'objet
	* Si $id est renseigné, définit une session propre à l'identifiant correspondant
	*
	* @param mixed $id
	*/
	public function sessionMake($id=null) {
		$sess = &AcidSession::getInstance()->data;
		if ($id === null) {
			$sess[static::TBL_NAME] = $this->getVals();
		} else {
			$sess[static::TBL_NAME][$id] = $this->getVals();
		}
	}

	/**
	* Retourne la valeur de configuration de l'objet qui renseignée en entrée lorsque celle-ci existe, renvoie null sinon.
	*
	* @param string $key Nom de la configuration.
	*
	* @return mixed
	*/
	public function getConfig($key=null) {
		if ($key!=null) {
			$array_path = Acid::parseArray($key);
			return Acid::parse($array_path,$this->config);
		}else{
			return $this->config;
		}
	}

	/**
	* Attribut une valeur de configuration de l'objet
	*
	* @param string $key Nom/Chemin de la configuration.
	* @param mixed $value Valeur de la configuration.
	*
	* @return mixed
	*/
	public function setConfig($key,$value) {
		if ($key!==null) {
			$ident = $this->preKey('tmp').time();
			$GLOBALS[$ident] = $this->config;

			Acid::set($key,$value,$ident);
			$this->config = $GLOBALS[$ident];
			unset($GLOBALS[$ident]);
		}else{
			$this->config = $value;
		}
	}

	/**
	* Attribut la même configuration de l'objet à l'objet en entrée
	*
	* @param object $obj
	*
	*/
	public function cloneConfig(&$obj) {
		$obj->setConfig($this->getConfig());
	}

	/**
	* Modifie une valeur de configuration de l'objet
	*
	* @param string $key Nom/Chemin de la configuration.
	* @param mixed $value Valeur de la configuration.
	*
	* @return mixed
	*/
	public function addToConfig($key,$value) {
		$ident = $this->preKey('tmp').time();
		$GLOBALS[$ident] = $this->config;

		Acid::add($key,$value,$ident);
		$this->config = $GLOBALS[$ident];
		unset($GLOBALS[$ident]);
	}

	/**
	* Regarde si une configuration existe
	*
	* @param string $key Nom/Chemin de la configuration.
	* @param boolean $check_if_empty
	*
	* @return mixed
	*/
	public function hasConfig($key,$check_if_empty=false) {
		$ident = $this->preKey('tmp').time();
		$GLOBALS[$ident] = $this->config;
		if ($check_if_empty) {
			$res = Acid::isEmpty($key,$ident);
		}else{
			$res = Acid::exists($key,$ident);
		}
		unset($GLOBALS[$ident]);

		return $res;
	}

	/**
	* Instancit un formulaire ( post ) AcidForm propre à l'objet.
	*
	* @param string $prefix Prefixe pour le nom des éléments.
	* @param bool $print_vals
	*
	* @return object
	*/
	public function initForm($prefix='',$print_vals=true) {
		Acid::load('tools/form.php');
		$form = new AcidForm('post','');
		foreach ($this->vars as $key=>$var) {
			$var->getForm($form,$prefix.$key,$print_vals);
		}
		return $form;
	}

	/**
	* Stocke l'élément renseigné en entrée puis la retire de la variable PHP $_GET.
	*
	* @param string $name
	*
	* @return bool
	*/
	protected function removeGet ($name) {
		$get_key = static::preKey($name);
		$get_key = str_replace('.','_', $get_key);
		if (isset($_GET[$get_key])) {
			$this->admin_nav[$name] = $_GET[$get_key];
			unset($_GET[$get_key]);
			return true;
		}
		return false;
	}

	/**
	* Gére l'URL de l'administration.
	*
	* @return void
	*/
	public function adminNav () {
		if (!isset($this->admin_nav)) {
			$this->admin_nav = array();

			$keys = array(	'do',	// Action
							'id',	// ID Elt
							'lo',	// List Order
							'ld',	// List Desc
							'll',	// List limit
							'lp'	// List page
			);
			foreach($keys as $key) {
				$this->removeGet($key);
			}

			foreach ($this->vars as $key=>$var) {
				$this->removeGet('fm_'.$key);	// Filter method
				$this->removeGet('fv_'.$key);	// Filter val
			}
			if (isset($_GET['submit'])) {
				unset($_GET['submit']);
			}
		}
	}

	/**
	 * Etend le champ d'action de AdminNav avec le dbPref des champs issus des modules en entrée
	 * @param string $mods
	 */
	public function extendAdminNav ($mods=null) {
		if ($mods ===null) {
			$mods = array();
			if ($listmods = $this->getConfig('admin:list:mods')) {
				$mods = array_keys($listmods);
			}
		}

		if ($mods) {
			foreach ($mods as $mod) {
				foreach ($mod::build()->getVals() as $mkey=>$val) {
					$key = urlencode($mod::dbPref($mkey));
					$this->removeGet('fm_'.$key);	// Filter method
					$this->removeGet('fv_'.$key);	// Filter val
					$this->config['admin']['curnav']['extended_keys'][] = $key;
				}
			}
		}

		return $this->admin_nav;
	}

	/**
	* Retourne l'url courante, gérée par acidfarm, de l'adminsitration.
	*
	*
	* @return string
	*/
	public function getAdminCurNav() {
		$gets = array();
		$fv = static::preKey('fv_');
		$fm = static::preKey('fm_');

		$gkeys = array_keys($this->vars);
		if ($extend = $this->getConfig('admin:curnav:extended_keys')) {
			$gkeys = array_merge($gkeys,$extend);
		}

		foreach ($gkeys as $key) {
			if (	isset($this->admin_nav['fv_'.$key]) && isset($this->admin_nav['fm_'.$key])) {
				if ($this->admin_nav['fm_'.$key] != 'unused' && strlen($this->admin_nav['fv_'.$key])) {
					$gets[$fv.$key] = $this->admin_nav['fv_'.$key];
					$gets[$fm.$key] = $this->admin_nav['fm_'.$key];
				}
			}
		}

		$keys = array(	'do',	// Action
						'id',	// ID Elt
						'lo',	// List Order
						'ld',	// List Desc
						'll',	// List limit
						'lp'	// List page
		);

		foreach ($keys as $key) {
			if (isset($this->admin_nav[$key])) {
				$gets[static::preKey($key)] = $this->admin_nav[$key];
			}
		}

		return $gets;
	}

	/**
	* Retourne le titre de l'administration
	*
	* @param array $do
	*
	* @return array
	*/
	public function getAdminTitle($do='default') {
		$def = isset($this->config['admin']['title']['default']) ?
					$this->config['admin']['title']['default'] : Acid::get('admin_title');
		return isset($this->config['admin']['title'][$do]) ? $this->config['admin']['title'][$do] : $def;
	}

	/**
	* Retourne les attributs du titre de l'administration
	*
	* @param array $do
	*
	* @return array
	*/
	public function getAdminTitleAttr($do='default') {
		$def = isset($this->config['admin']['title_attr']['default']) ?
					$this->config['admin']['title_attr']['default'] : Acid::get('admin_title_attr');
		return isset($this->config['admin']['title_attr'][$do]) ? $this->config['admin']['title_attr'][$do] : $def;
	}

	/**
	* Retourne une portion HTML correspondant aux boutons de modération de l'administration d'AcidFarm
	*
	* @param string $link Url du bouton.
	* @param string $image Url de l'icone.
	* @param string $title Titre / Alt de l'image.
	* @param string $click Evènment Onclick.
	* @param string $tpl Chemin vers le fichier tpl.
	*
	* @return string
	*/
	public function getIconLink($link=null,$image=null,$title='bouton',$click=null,$tpl=null) {
		$tpl = $tpl ? $tpl : 'admin/admin-icone-link.tpl';
		return Acid::tpl($tpl,array('link'=>$link,'image'=>$image,'title'=>$title,'click'=>$click),$this);
	}

	/**
	* Retourne un tableau représentant un onglet
	*
	* @param array $name Intitulé de l'onglet
	* @param array $builder Composantes qui définissent l'url
	* @param array $excluder Clés exclues de l'url
	* @param string $key Clés associée à l'onglet
	* @param string $src Url source
	*
	* @return array
	*/
	public function buildAdminOnglets($name, $builder=array(),$excluder=array(),$key=null,$src=null) {
		$url = AcidUrl::build($builder,$excluder,$src);
		$sel = $builder;

		if ((empty($builder)) && in_array(static::preKey('do'),$excluder)) {
			$sel = array(static::preKey('do')=>$this->getDefaultDo());
		}

		$onglet =  array('url'=>$url,'name'=>$name,'selector'=>$sel);

		if ($key) {
			$onglet['key'] = $key;
		}

		return $onglet;
	}

	/**
	* Retourne un tableau d'onglets "standard" de l'administration d'AcidFarm
	*
	* @param array $config Tableau de filtrage / rangement des onglets
	*
	* @return array
	*/
	public function getStandardOnglets($config=array('list','add','search')) {

			$tab=array();

			foreach ($config as $key) {
				switch ($key) {
					case 'list' :
						$tab[] = $this->buildAdminOnglets(Acid::trad('admin_onglet_list'),array(static::preKey('do')=>'list'),array(static::preKey('id')),'list');
					break;
					case 'add' :
						$tab[] = $this->buildAdminOnglets(Acid::trad('admin_onglet_add'),array(static::preKey('do')=>'add'),array(),'add');
					break;
					case 'search' :
						$builder = array_merge($this->getAdminCurNav(),array(static::preKey('do')=>'search'));
						$tab[] = $this->buildAdminOnglets(Acid::trad('admin_onglet_search'),$builder,array(),'search');
					break;
				}
			}

			return $tab;
	}

	/**
	* Retourne un tableau d'onglets personnalisés
	*
	* @param array $do
	*
	* @return array
	*/
	public function getOnglets($do='default') {
		if ( isset($this->config['onglets'][$do]) ) {
			return $this->config['onglets'][$do];
		}elseif ( isset($this->config['onglets']['default']) ) {
			return $this->config['onglets']['default'];
		}else{
			return $this->getStandardOnglets();
		}
	}

	/**
	* Retourne le controller par défaut
	*
	* @return array
	*/
	public function getDefaultDo() {
		return isset($this->config['default_do']) ? $this->config['default_do'] : 'list';
	}

	/**
	* Retourne si la colonne d'action estactivée ou non dans l'AdminList
	*
	* @return boolean
	*/
	public function getDisableAction() {
		return isset($this->config['admin']['list']['disable_actions']) ? $this->config['admin']['list']['disable_actions'] : false;
	}

	/**
	* Retourne le controller dialog par défaut
	*
	* @param array $do
	*
	* @return array
	*/
	public function getDialogDo($do) {
		return isset($this->config['admin'][$do]['dialog']) ? $this->config['admin'][$do]['dialog'] : true;
	}

	/**
	* Retourne le texte dialog par défaut
	*
	* @param array $do
	* @param mixed $dialog texte à afficher en case de succès si défini
	*
	* @return array
	*/
	public function getDialogMessage($do,$dialog=null) {

		if ($dialog===null) {
			$dialog =  $this->getDialogDo($do);
		}

		if ($dialog === true) {
			switch($do) {
				case 'add' :
					return Acid::trad('admin_add_succeed');
				break;

				case 'remove' :
					return Acid::trad('admin_delete_succeed');;
				break;

				case 'update' :
				default :
					return 	Acid::trad('admin_update_succeed');
				break;
			}
		}

		return $dialog;
	}

	/**
	* Retourne un tableau de configuration "standard" du bouton d'affichage
	*
	* @param mixed $elt Tableau representatif de l'élement
	*
	* @return array
	*/
	public function getStandardActionPrint($elt) {
		$ident = isset($elt[static::tblId()]) ? $elt[static::tblId()] : $elt[self::dbPref(static::tblId())];

		return  array(
					'link'=>AcidUrl::build(array(static::preKey('do')=>'print',static::preKey('id')=>$ident)),
					'image'=>Acid::themeUrl('img/admin/btn_afficher.png'),
					'title'=>Acid::trad('admin_action_print'),
					'click'=>null
				);
	}

	/**
	* Retourne un tableau de configuration "standard" du bouton de mise à jour
	*
	* @param mixed $elt Tableau representatif de l'élement
	*
	* @return array
	*/
	public function getStandardActionUpdate($elt) {
		$ident = isset($elt[static::tblId()]) ? $elt[static::tblId()] : $elt[self::dbPref(static::tblId())];

		return  array(
				'link'=>AcidUrl::build(array(static::preKey('do')=>'update',static::preKey('id')=>$ident)),
				'image'=>Acid::themeUrl('img/admin/btn_modifier.png'),
				'title'=>Acid::trad('admin_action_update'),
				'click'=>null
				);
	}

	/**
	* Retourne un tableau de configuration "standard" du bouton de suppression
	*
	* @param mixed $elt Tableau representatif de l'élement
	*
	* @return array
	*/
	public function getStandardActionDelete($elt) {
		$ident = isset($elt[static::tblId()]) ? $elt[static::tblId()] : $elt[self::dbPref(static::tblId())];

		$del_form = Acid::tpl('admin/admin-form-delete.tpl',array('id'=>$ident,'next'=>AcidUrl::build($this->getAdminCurNav())),$this);

		return array(
				'link'=>'#',
				'image'=>Acid::themeUrl('img/admin/btn_supprimer.png'),
				'title'=>Acid::trad('admin_action_remove'),
				'click'=>'if (confirm(\'Supprimer ?\')){window.document.getElementById(\''.static::preKey($ident).'_delform\').submit()};return false;',
				'script'=>$del_form
				);
	}

	/**
	* Retourne le resultat de la fonction conditionnelle d'affichage du bouton, si inexistante, retourne true
	*
	* @param string $do L'action associée
	* @param array $elt L'élement à contrôler
	*
	* @return bool
	*/
	public function checkActionTabCondition($do,$elt) {

		if (isset($this->config['admin']['list']['actions_func'][$do])) {
			$func = $this->config['admin']['list']['actions_func'][$do];

			if (isset($func['name']) && isset($func['args'])) {

				foreach ($func['args'] as $k=>$v) {
					if ($v === '__ELT__') {
						$func['args'][$k] = $elt;
					}
				}

				return call_user_func_array($func['name'],$func['args']);

			}
		}

		return true;
	}

	/**
	* Retourne un tableau de configuration "standard" des boutons de modérations de l'administration AcidFarm
	*
	* @param mixed $elt L'élement à contrôler
	* @param array $config Tableau de filtrage / rangement des boutons de modérations
	*
	* @return string
	*/
	public function getStandardActionTab($elt,$config=array('print','update','delete')) {
		$ident = isset($elt[static::tblId()]) ? $elt[static::tblId()] : $elt[self::dbPref(static::tblId())];

		$tab_btn = array();
		$tab_script = array();
		foreach ($config as $key) {

			$custom_key = ( is_array($key) ? (isset($key['key']) ? $key['key'] : 'custom')  : $key );
			if ($this->checkActionTabCondition($custom_key,$elt)) {
				switch ($key) {
					case 'print' :
						$tab_btn[] = $this->getStandardActionPrint($elt);
					break;

					case 'update' :
						$tab_btn[] = $this->getStandardActionUpdate($elt);
					break;

					case 'delete' :
						$tab_btn[] = $this->getStandardActionDelete($elt);
					break;

					default :
						if ( is_array($key) ) {
							$tab = $key;
							//$tab['link'] = str_replace('__ID__',$ident,$tab['link']);
							foreach ($tab as $k => $v) {
								$tab[$k] = str_replace('__ID__',$ident,$v);
							}
							$tab_btn[] = $tab;
							if (isset($tab['ext_script'])) {
								$tab_script[] = $tab['ext_script'];
							}
						}
					break;
				}
			}
		}

		return array($tab_btn,$tab_script);
	}

	/**
	* Retourne une portion HTML representant des boutons de modérations
	*
	* @param array $links
	* @param array $forms
	* @param array $elt
	* @param array $conf
	* @return string
	*/
	public function printAdminActionTab($links=array(),$forms = array(), $elt=array() , $conf=array()) {
		$tpl_icone = isset($conf['tpl']['icone']) ? $conf['tpl']['icone'] : null;
		$tpl = isset($conf['tpl']['action']) ? $conf['tpl']['action'] : 'admin/admin-action-tab.tpl';

		$actions =	'' ;

		foreach ($links as $l) {
				$script = isset($l['script']) ? $l['script'] : '';
				$actions .= $this->getIconLink($l['link'],$l['image'],$l['title'],$l['click'],$tpl_icone). $script . "\n";
		}

		foreach ($forms as $f) {
				$actions .= $f. "\n";
		}


		return Acid::tpl($tpl,array('actions'=>$actions),$this);
	}

	/**
	* Retourne une portion HTML representant un Th du tableau de l'administration AcidFarm
	* @param string $cont
	* @param array $attr
	* @param array $conf
	* @return string
	*/
	public function getAdminTh($cont,$attr=array(),$conf=array()) {
		$tpl = isset($conf['tpl']['th']) ? $conf['tpl']['th'] : 'core/tab/admin-th.tpl';
		return Acid::tpl($tpl,array('cont'=>$cont,'attr'=>$attr),$this);
	}

	/**
	* Retourne une portion HTML representant un Td du tableau de l'administration AcidFarm
	*
	* @param string $cont
	* @param array $attr
	* @param array $conf
	* @return string
	*/
	public function getAdminTd($cont,$attr=array(),$conf=array()) {
		$tpl = isset($conf['tpl']['td']) ? $conf['tpl']['td'] : 'core/tab/admin-td.tpl';
		return Acid::tpl($tpl,array('cont'=>$cont,'attr'=>$attr),$this);
	}

	/**
	* Retourne une portion HTML representant un Tr du tableau de l'administration AcidFarm
	* @param int $line
	* @param string $class
	* @param array $attr
	* @param array $conf
	* @return string
	*/
	public function getAdminTr($line,$class=null,$attr=array(),$conf=array()) {
		if ($class) {
			$attr['class'] = isset($attr['class']) ? $attr['class'].' '.$class : $class;
		}
		$tpl = isset($conf['tpl']['tr']) ? $conf['tpl']['tr'] : 'core/tab/admin-tr.tpl';
		return Acid::tpl($tpl,array('line'=>$line,'attr'=>$attr),$this);
	}

	/**
	* Renvoie un tableau associatif des données en basse poru ce module avec pour couple clé => valeur, le nom de champs du module
	* passé en argument. Si les deux champs sont vides, cette méthode ira voir la présence de
	* config['assoc:index'] && config['assoc:value'] qui devront être renseigné dans le module correspondant.
	*
	* @param string $key_val Le nom du champ du module qui doit servir de valeur pour le tableau associatif (ex: title)
	* @param string $key_index Le nom du champ du module qui doit servir de clé pour le tableau associatif (ex: id_module)
	* @param string $hsc_if_value
	* @param string $order
	* @param string $filter
	*
	* @return array Le tableau associatif
	*/
	public static function getAssoc($key_val=null,$key_index=null, $hsc_if_value=true,$order=array(),$filter=array()) {
		$elts = static::dbList($filter,$order);
		$mod = static::build();

		$def_key_index = $mod->getConfig('assoc:index') ? $mod->getConfig('assoc:index') : static::tblId();
		$key_index = $key_index!==null ? $key_index : $def_key_index;

		$key_val = $key_val!==null ? $key_val : $mod->getConfig('assoc:value');
		if(!$key_val) throw new Exception(get_called_class() . "::getAssoc() - config['assoc:value'] == null, association impossible.");


		$assoc = static::sortByKey($elts,$key_index,$key_val,true,$hsc_if_value);

		return $assoc;
	}

	/**
	* Tri un tableau associatif des données avec pour clé la valeur en entrée
	*
	* @param array $tab tableau
	* @param string $key_index Le nom du champ du module qui doit servir de clé pour le tableau associatif (ex: id_module)
	* @param string $key_value Le nom du champ du module qui doit servir de valeur pour le tableau associatif (ex: title)
	* @param boolean $force_unique
	* @param boolean $hsc_if_value
	* @return Ambigous <multitype:multitype: unknown , unknown, string>
	*/
	public static function sortByKey($tab, $key_index=null, $key_value=null, $force_unique=true, $hsc_if_value=true) {
		if ($key_index===null) {
			$key_index = static::tblId();
		}

		$assoc = array();

		if ($tab) {
			foreach($tab as $elt) {

				$value= ($key_value===null) ? $elt : ($hsc_if_value ? htmlspecialchars($elt[$key_value]) : $elt[$key_value]);

				if ($force_unique) {
					$assoc[$elt[$key_index]] =  $value;
				}else{
					if (!isset($assoc[$elt[$key_index]])) {
						$assoc[$elt[$key_index]] = array();
					}
					$assoc[$elt[$key_index]][] = $value;
				}
			}
		}

		return $assoc;
	}

	/**
	* Retourne une portion HTML representant un tableau de l'administration AcidFarm
	*
	* @param array $cols les entêtes
	* @param array $rows les lignes
	* @param array $conf configuration
	* @return string
	*/
	public function printAdminTab($cols = array(),$rows=null,$conf=array()) {

		$my_rows = array();
		foreach ($rows as $l_tab) {
			if (is_array($l_tab)) {

				//on remplit les colonnes
				$line='';
				foreach ($l_tab as $k => $td) {
					$line .= $this->getAdminTd($td,array('class'=>str_replace('.','_','col_'.$k)),$conf) . "\n" ;
				}

				//on prépare la ligne
				$my_rows[] = $line;
			}
		}

		$tpl = isset($conf['tpl']['tab']) ? $conf['tpl']['tab'] : 'core/tab/admin-tab.tpl';
		return Acid::tpl($tpl,array('cols'=>$cols,'rows'=>$my_rows,'config'=>$conf),$this);
	}

	/**
	* Retourne true si l'onglet designé par $check correspond à l'url en cours
	*
	* @param array $check Composantes de l'onglet
	*
	* @return string
	*/
	public function isSelectedOnglet($check) {
	 	if (is_array($check)) {
	 		$gets = array();

	 		$url_parsed = parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY);
	 		if ($url_parsed) {
	 			$parsed =  explode('&',$url_parsed);
	 			foreach ($parsed as $mp) {
	 				$exp = explode('=',$mp);
	 				if (count($exp)>1) {
	 					$gets[$exp[0]] = $exp[1];
	 				}
	 			}
	 		}

	 		if (isset($check[static::preKey('do')]) && empty($gets[static::preKey('do')])) {
	 			$gets[static::preKey('do')] = $this->getDefaultDo();
	 		}

 			$selected = true;
 			foreach ($check as $k => $v) {
 				$get = isset($gets[$k]) ? $gets[$k] : null;
 				$selected = $selected && ($get==$v);
 			}

 			return $selected;

	 	}else{
	 		return ($_SERVER['REQUEST_URI'] === html_entity_decode($check));
	 	}
	}

	/**
	* Retourne une portion HTML representant les onglets de l'administration AcidFarm
	*
	* @param array $onglets Les onglets
	* @param array $config Tableau de configuration pour les onglets
	*
	* @return string
	*/
	 public function printAdminOnglets($onglets=array(), $config=array()) {

	 	$tpl = isset($config['tpl']) ? $config['tpl'] : 'admin/admin-body-onglets.tpl';

		if (empty($onglets)) {
			$no_onglets = (($onglets===null) || ($onglets===false));
			$onglets = $no_onglets ?  array() : $this->getOnglets() ;
		}

		$vars = array();
		$vars['onglets'] = array();

		 if ($onglets) {
			 foreach ($onglets as $url_onglet => $conf_onglet) {
				 $_onglet = new stdClass();
				 $_onglet->url = !is_numeric($url_onglet) ? $url_onglet : $conf_onglet['url'];
				 $_onglet->name = is_array($conf_onglet) ? $conf_onglet['name'] : $conf_onglet;
				 $_onglet->selector = is_array($conf_onglet) ? (isset($conf_onglet['selector']) ? $conf_onglet['selector'] : $_onglet->url) : $url_onglet;
				 $_onglet->isSelected = $this->isSelectedOnglet($_onglet->selector);

				 $vars['onglets'][] = $_onglet;

			 }
		 }

		return Acid::tpl($tpl,$vars,$this);
	 }

	/**
	* Retourne une portion HTML representant le corps de l'administration AcidFarm
	*
	* @param string $content Le contenu à insérer
	* @param array $onglets La configuration des onglets
	* @param string $title Le titre
	* @param array $title_attr La configuration du titre
	*
	* @return string
	*/
	 public function printAdminBody($content,$onglets=array(),$title=null,$title_attr=null) {

		$this->adminNav();

		$do = isset($this->admin_nav['do']) ? $this->admin_nav['do'] : $this->getDefaultDo();
		$title = $title!==null ? $title : $this->getAdminTitle($do);
		$title_attr = $title_attr!==null ? $title_attr : $this->getAdminTitleAttr($do);
		$title_attr = $title_attr ? AcidForm::getParams($title_attr) : '';

		$selected = ' class="selected"';

		$menu = $this->printAdminOnglets($onglets);

		return Acid::tpl('admin/admin-body.tpl',array('menu'=>$menu,'title'=>$title,'title_attr'=>$title_attr,'content'=>$content));

	}

	/**
	* Exécute les méthodes standards d'administration en fonction du paramètre d'entrée $do
	*
	* @param string $do L'identifiant de la methode à effectuer
	* @param array $config Configuration
	*
	* @return string
	*/
	public function exeAdminController($do,$config=array()) {
		$class = get_called_class();
		$keys = array('add','search','update','print','list');
		$do = in_array($do,$keys) ? $do : $this->getDefaultDo();

		$content = '';
		switch($do) {

			case 'add' :
				$content .= $this->printAdminAdd();
				break;

			case 'search' :
				$content .= $this->printAdminSearch();
				break;

			case 'update' :
				if (isset($this->admin_nav['id'])) {
 					$obj = new $class($this->admin_nav['id']);
					$content .= $obj->printAdminUpdate();
				}
				break;

			case 'print' :
				if (isset($this->admin_nav['id'])) {
					$obj = new $class($this->admin_nav['id']);
					$content .= $obj->printAdminElt();
				}
				break;

			default :
				$config_list = isset($config['list']) ? $config['list'] : array();
				$content .= $this->printAdminList($config_list);
			break;
		}

		return $content;

	}

	/**
	* Ajoute un onglet à l'administration
	* @param string $do alias de l'onglet
	*/
	public function addAdminOnglets($do) {
		if (empty($this->config['no_more_onglets'])) {

			switch ($do) {
				case 'update' :
					$this->config['onglets'][$do] = $this->getOnglets($do);
					$name = Acid::trad('admin_action_update');
					$this->config['onglets'][$do][] = $this->buildAdminOnglets($name,array($this->preKey('do')=>$do));
				break;

				case 'print' :
					$this->config['onglets'][$do] = $this->getOnglets($do);
					$name = Acid::trad('admin_action_print');
					$this->config['onglets'][$do][] = $this->buildAdminOnglets($name,array($this->preKey('do')=>$do));
				break;
			}

		}
	}

	/**
	* Controlleur de l'interface d'administration.
	*
	*
	* @param array $config Configuration
	*
	* @return string
	*/
	public function printAdminInterface($config=array()) {

		$onglets= (isset($config['onglets'])) ? $config['onglets'] : array();
		$controller = isset($config['controller']) ? $config['controller'] : null;
		$default_do = isset($config['default_do']) ? $config['default_do'] : $this->getDefaultDo();
		$no_permission = isset($config['no_permission']) ? $config['no_permission']: false;
		$this->adminNav();

		$do = isset($this->admin_nav['do']) ? $this->admin_nav['do'] : $default_do;

		// Content
		$content = '';
		if (($this->getUserAccess($do)) || ($no_permission)) {

			if ($controller === null) {
				$content .= $this->exeAdminController($do,$config);
			}else{

				//Génération du controller : tab_controller[key] = true pour une fonction personnalisée, false sinon
				$tab_controller = array();
				foreach ($controller as $key => $fun_do) {
					if (is_int($key)) {
						$tab_controller[$fun_do] = false;
					}else{
						$tab_controller[$key] = true;
					}
				}

				if ( in_array($do,array_keys($tab_controller)) ) {
					if ($tab_controller[$do]) {
						list($a,$b) = $controller[$do];

						if (isset($config[$do])) {
							foreach ($b as $bk => $bv) {
								if ($bv == '__CONFIG__') {
									$b[$bk] = $config[$do];
								}
							}
						}

						$content .= $this->callFunction($a,$b);
					}else{
						$content .= $this->exeAdminController($do,$config);
					}
				}

			}

		}else{
			$content .= Acid::trad('admin_no_permission');
		}

		$this->addAdminOnglets($do);
		$no_onglets = (($onglets===null) || ($onglets===false));
		$onglets= $onglets? $onglets : ( $no_onglets ? null : $this->getOnglets($do) );

		if (!empty($config['content_only'])) {
			return $content;
		}else{
			return $this->printAdminBody($content,$onglets);
		}

	}

	/**
	* Execute une fonction prédéfinie par l'utilisateur avec la gestion des methodes de $this
	*
	*	@param mixed $cuf	Paramètre 1 de la function  call_user_func_array de PHP
	*						Il est possible de lui renseigner $=>'true' en troisieme paramètre pour utiliser ${$cuf[0]} en entête de fonction
	*	@param array $cup	Paramètre 2 de la function  call_user_func_array de PHP
	*
	* @return array
	*/
	public  function callFunction($cuf,$cup=array()) {

		if ($cuf[0]=='$' )  {

			if ($cuf[1]!='this') {
				$cuf[1] = ${$cuf[1]};
			}else{
				$cuf[1] = $this;
			}

			$cuf = array($cuf[1],$cuf[2]);
		}

		return call_user_func_array($cuf,$cup);
	}

	/**
	* Retourne les paramètres de filtrage pour l'administration du module sous forme d'un couple ([filtres],[paramètre d'indexation])
	*
	*	@param mixed $mods un tableau contenant les modules associés à l'objet
	*
	* @return array
	*/
	public function getAdminListOptions($mods=false) {

		$filter = $order = array();


		$gets = $this->admin_nav;

		$gkeys = array_keys($this->vars);
		if ($extend = $this->getConfig('admin:curnav:extended_keys')) {
			$gkeys = array_merge($gkeys,$extend);
		}

		// chaîne WHERE
		foreach ($gkeys as $key) {
			if (isset($gets['fm_'.$key]) && isset($gets['fv_'.$key]) && strlen($gets['fv_'.$key])) {
				$search_val = html_entity_decode($gets['fv_'.$key]);
				$key_filter = !$mods ? $key : $this->dbPref($key);
				$key_filter = ($extend && (in_array($key,$extend))) ? $key : $key_filter;

				switch($gets['fm_'.$key]) {
					case 'contain' :	$filter[] = array($key_filter,'LIKE',$search_val,'%','%');	break;
					case 'start' :		$filter[] = array($key_filter,'LIKE',$search_val,'','%');	break;
					case 'stop' :		$filter[] = array($key_filter,'LIKE',$search_val,'%','');	break;
					case 'is' :			$filter[] = array($key_filter,'=',$search_val);		break;
				}
			}
		}

		//cas de jointure
		if (isset($gets['lo'])) {
			$valid_key = isset($this->vars[$gets['lo']]);
			if (($mods) && (!$valid_key)) {
				foreach ($mods as $module) {
					if (!$valid_key) {
						$m = new $module();
						if (strpos($gets['lo'],$m->dbPref(''))!==false) {
							$check = substr($gets['lo'],strlen($m->dbPref('')));
							$valid_key = in_array($check,$m->getKeys());
						}
					}
				}
			}
		}

		// chaîne ORDER
		if (isset($gets['lo']) && isset($gets['ld']) && $valid_key) {
			$order = array($gets['lo']=>($gets['ld']?true:false));
		}elseif (isset($this->config['admin']['list']['order'])) {
			$order = $this->config['admin']['list']['order'];
		}

		return array($filter,$order);
	}

	/**
	* Retourne le listing d'administration du module sous forme d'une chaîne de caractères mise en forme.
	*
	* @param array $conf Configuration (filter, order, mods, tpl modules )
	*
	* @return string
	*/
	public function printAdminList ($conf=array()) {

		$this->printAdminConfigure('list',$conf);

		//Gestion des filtres additionnels
		$add_filter = isset($conf['filter']) ? $conf['filter'] : array();
		$add_order = isset($conf['order']) ? $conf['order'] : array();
		$mods = isset($conf['mods']) ? $conf['mods'] : $this->getAdminListMods();

		$modules = array();

		//Display mode
		$basic = isset($conf['basic_mode']) ? $conf['basic_mode'] : false;

		//Template
		$conf['tpl'] = isset($conf['tpl']) ?  $conf['tpl'] : $this->getAdminTpl('list');

		//Initialisation
		$this->adminNav();
		$cur_nav = $this->getAdminCurNav();
		$ki = static::preKey();

		if ($mods) {
			$modules = array($this->getClass()=>$this->getClass());
			foreach (array_keys($mods) as $mod) {
				$modules[$mod] = $mod;
			}
			$conf['modules'] = $modules;
		}

		//Full mode
		if (!$basic) {

			//Configurations des filtres
			list($filter,$order) = $this->getAdminListOptions($modules);
			$hide_filter_ind = empty($filter);

			if (!empty($add_filter)) {
				$filter = array_merge($add_filter,$filter);
			}

			if (!empty($add_order)) {
				$order = array_merge($add_order,$order);
			}

			//Récupération des paramètres

			if ($mods) {
				$nb_elts = $this->dbListMods($mods,$filter,$order,'',true);
			}else{
				$nb_elts = $this->dbCount($filter);
			}

			list($ll,$limit,$page,$nav) = $this->getAdminListParams($nb_elts,$conf);

			//Récupération des détails d'affichage
			$infos = $this->printAdminListInfo($nb_elts,$page,$ll,$hide_filter_ind,$conf);

		}
		//Basic mode
		else{

			$infos = $limit = $nav = '';
			$filter = $add_filter;
			$order = $add_order;

		}

		//Initialisation du contenu
		$content = '';

		//Génération de la liste
		if ($mods) {
			$elts = $this->dbListMods($mods,$filter,$order,$limit);
		}else{
			$elts = $this->dbList($filter,$order,$limit);
		}

		if ($filter_head_content = $this->genAdminListHeadFilter($conf)) {
			$content .= $filter_head_content;
		}

		if ($elts) {
			$conf['req_elts'] = $elts;
			$content .= $this->genAdminListContent($elts,$conf);
		}

		return	$this->printAdminListBody($content,$infos,$nav,$conf);

	}

	/**
	* Génère le corps du listing d'administration du module sous forme d'une chaîne de caractères.
	* @param unknown_type $content Contenu
	* @param unknown_type $infos Les ibfos
	* @param unknown_type $nav La nav
	* @param unknown_type $conf Configuration
	* @return string
	*/
	public function printAdminListBody($content,$infos,$nav,$conf=array()) {
		//Appel du fichier template
		$tpl = isset($conf['tpl']['body']) ? $conf['tpl']['body'] : 'admin/admin-body-list.tpl';
		return	Acid::tpl($tpl,array('infos'=>$infos,'nav'=>$nav,'content'=>$content),$this);
	}

	/**
	 * Retourne des listes de filtrages dans le listing d'administration
	 * @param array $conf
	 * @return string
	 */
	public function genAdminListHeadFilter($conf=array()) {
		$content = '';

		$def_filter_head = isset($this->config['admin']['list']['head_filters']) ? $this->config['admin']['list']['head_filters'] : array();
		$filter_head = isset($conf['head_filters']) ? $conf['head_filters'] : $def_filter_head;
		if (!empty($filter_head)) {
			foreach ($filter_head as $key=>$elts) {
				$nav=$this->getAdminCurNav();
				$current = isset($nav[$this->preKey('fv_'.$key)]) ? $nav[$this->preKey('fv_'.$key)] : null;
				$content .= Acid::tpl('admin/admin-list-filter.tpl',array('current'=>$current,'elts'=>$elts,'key'=>$key),$this);
			}
		}

		return $content;
	}

	/**
	* Retourne la tête du listing d'administration du module sous forme de tableau.
	*
	* @param array $conf Configuration
	*
	* @return array
	*/
	public function genAdminListHead($conf=array()) {

		//initialisation
		$cur_nav = $this->getAdminCurNav();
		$ki = static::preKey();
		$modules = !empty($conf['modules']) ? $conf['modules'] : false;

		//préparation si jointures
		if ($modules) {
			$t_champs = array();
			$t_label = array();
			foreach ($modules as $mod) {
				$module = new $mod();
				foreach($module->getKeys() as $key) {
					$mod_champs[$module::dbPref($key)] = $module::dbPref($key);
					$t_label[$module::dbPref($key)] = $module->getLabel($key);
				}
			}

			if (!empty($this->config['admin']['list']['keys'])) {
				foreach ($this->config['admin']['list']['keys'] as $tk => $tkey) {
					if (strpos($tkey,'.')==false) {
						$this->config['admin']['list']['keys'][$tk] = self::dbPref($tkey);
					}
				}
			}

			if (!empty($this->config['admin']['list']['keys_excluded'])) {
				foreach ($this->config['admin']['list']['keys_excluded'] as $tk => $tkey) {
					if (strpos($tkey,'.')==false) {
						$this->config['admin']['list']['keys_excluded'][$tk] = self::dbPref($tkey);
					}
				}
			}
		}

		//les clés
		$def_vars = !$modules ? $this->vars : $mod_champs;
		if (isset($this->config['admin']['list']['keys'])) {
			$t_champs = $this->config['admin']['list']['keys'];
		}elseif (!empty($this->config['admin']['list']['keys_excluded'])) {
			$t_champs = $def_vars;
			foreach ($this->config['admin']['list']['keys_excluded'] as $kdel) {
				unset($t_champs[$kdel]);
			}
			$t_champs = array_keys($t_champs);
		}else{
			$t_champs = array_keys($def_vars);
		}

		//l'entête
		$th_tab = array();
		foreach ($t_champs as $key) {
			$ld = $this->getAdminListLd($key);
			$f = array_merge($cur_nav,array($ki.'lo'=>$key,$ki.'ld'=>$ld));

			$value = $this->getLabel($key);
			if ((!$value) && (isset($t_label[$key]))){
				$value = $t_label[$key];
			}

			if (isset($this->config['admin']['head'][$key]['func'])) {
				$specs = $this->config['admin']['head'][$key]['func'];
				if (is_array($specs)) {
					$args = isset($specs['args']) ? $specs['args'] : array('__VAL__');
					$func = isset($specs['name']) ? $specs['name'] : 'htmlspecialchars';

					foreach ($args as $k=>$v) {
						if ($args[$k] === '__VAL__') {
							$args[$k] = $value;
						}elseif ($args[$k] === '__CONF__') {
							$args[$k] = $conf;
						}else{
							$args[$k] = str_replace('__VAL__',$value,$v);
						}
					}

					$value =  call_user_func_array($func,$args);
				}
			}else{
				$value = htmlspecialchars($value);
			}

			$url = isset($this->config['admin']['head'][$key]['url']) ? $this->config['admin']['head'][$key]['url'] : AcidUrl::build($f);

			$th_val = array('url'=>$url,'name'=>$value, 'key'=>$key);
			if (isset($this->config['admin']['head'][$key]['attr'])) {
				$th_val['attr'] = $this->config['admin']['head'][$key]['attr'];
			}
			$th_tab[$key]=$th_val;
		}

		return $th_tab;

	}

	/**
	* Génère une ligne du listing d'administration du module sous forme de tableau.
	*
	* @param array $fields Champs à traiter
	* @param array $elt Element à traiter
	* @param array $conf Configuration
	*
	* @return array
	*/
	public function genAdminListLine($fields,$elt,$conf=array()) {
		$line = array();

		//pour chaque champ, une valeur
		foreach ($fields as $key=>$intitule) {
			$line[$key]=$this->getPrinted($key,$elt,$conf);
		}

		//si les boutons d'actions sont activés
		$disable_action = isset($conf['disable_actions']) ? $conf['disable_actions'] : $this->getDisableAction();
		if ( !$disable_action ) {
			$line['std_actions']=$this->genAdminListAction($elt,$conf);
		}

		return $line;
	}

	/**
	* Génère une cellule d'action du listing d'administration du module sous forme d'une chaîne de caractère.
	*
	* @param array $elt Element à traiter
	* @param array $conf Configuration
	*
	* @return string
	*/
	public function genAdminListAction($elt,$conf=array()) {

		$config_btn = isset($this->config['admin']['list']['actions']) ? $this->config['admin']['list']['actions']:null;

		if ($config_btn!==null) {
			list($btn,$form) = $this->getStandardActionTab($elt,$config_btn);
		}else{
			list($btn,$form) = $this->getStandardActionTab($elt);
		}

		return $this->printAdminActionTab($btn,$form,$elt,$conf);
	}

	/**
	* Traites le tableau d'éléments en entrée
	*
	* @param array $elts Element à traiter
	* @param array $conf Configuration
	*
	* @return string
	*/
	public function genAdminListFormat($elts,$conf=array()) {
		return $elts;
	}

	/**
	 * Retourne la classe css personnalisée de la ligne du tableau
	 *
	 * @param array $elt Element à traiter
	 * @param array $conf Configuration
	 *
	 * @return string
	 */
	public function genAdminListLineCustomClass($elt,$conf=array()) {
		return '';
	}

	/**
	* Formate pour le listing d'administration du module les éléments en entrée sous forme d'une chaîne de caractères mise en forme.
	*
	* @param array $elts Eléments à traiter
	* @param array $conf Configuration
	*
	* @return array
	*/
	public function genAdminListContent($elts,$conf=array()) {

			$elts = $this->genAdminListFormat($elts,$conf);
			$conf['format_elts'] = $elts;

			//le header
			$th_tab =  $this->genAdminListHead($conf);

			//les resultats
			$td_tab = array();
			$assoc_td_tab = array();
			$custom_class = array();
			foreach ($elts as $elt) {
				$assoc_td_tab[] = isset($elt[$this->tblId()]) ? $elt[$this->tblId()] : 0;
				$custom_class[] = $this->genAdminListLineCustomClass($elt,$conf);

				$td_tab[] = $this->genAdminListLine($th_tab,$elt,$conf);
			}

			$conf['assoc_rows_id'] = $assoc_td_tab;
			$conf['assoc_rows_classes'] = isset($conf['assoc_rows_classes']) ? $conf['assoc_rows_classes'] : $custom_class;

			//si les boutons d'actions sont activés
			$disable_action = isset($conf['disable_actions']) ? $conf['disable_actions'] : $this->getDisableAction();
			if ( !$disable_action ) {
				$th_tab['std_actions'] = array('url'=>false,'name'=>Acid::trad('admin_list_btns_label'), 'key'=>'std_actions');
			}

			return $this->printAdminTab($th_tab,$td_tab,$conf);

	}

	/**
	* Retourne le tableau des fichiers templates personnalisés
	*
	* @param string $do l'action affectuée
	*
	* @return array
	*/
	public function getAdminTpl($do) {
		return isset($this->config['admin'][$do]['tpl']) ? $this->config['admin'][$do]['tpl'] : array();
	}

	/**
	* Retourne la liaison de modules associée à l'objet
	*
	*
	* @return array
	*/
	public function getAdminListMods() {
		return isset($this->config['admin']['list']['mods']) ? $this->config['admin']['list']['mods'] : false;
	}

	/**
	 * Retourne le combo ORM associé à l'objet
	 *
	 *
	 * @return array
	 */
	public function getAdminListCombo() {
		return isset($this->config['admin']['list']['combo']) ? $this->config['admin']['list']['combo'] : 'AND';
	}

	/**
	 * Retourne le group by ORM associé à l'objet
	 *
	 *
	 * @return array
	 */
	public function getAdminListGroupBy() {
		return isset($this->config['admin']['list']['group_by']) ? $this->config['admin']['list']['group_by'] : '';
	}

	/**
	* Retourne le paramètre de tri ascendant / descendant pour la clé en entrée
	*
	* @param string $key le paramètre de tri par type
	*
	* @return array
	*/
	public function getAdminListLd($key) {
		$this->adminNav();
		$ld = 0;
		if (isset($this->admin_nav['lo']) && $this->admin_nav['lo'] == $key) {
			if (isset($this->admin_nav['ld']) && !$this->admin_nav['ld']) {
				$ld = 1;
			}
		}

		return $ld;
	}

	/**
	* Retourne les paramètrages du listing
	*
	* @param int $nb_elts le nombre d'éléments trouvés par lors de l'execution de la recherche
	* @param array $conf Configuration
	*
	* @return array  list( nombre maxi d'éléments par page , limite d'affichage, page courante, pagination )
	*/
	public function getAdminListParams($nb_elts,$conf=array()) {
		$cur_nav = $this->getAdminCurNav();

		$def_tpl = isset($this->config['admin']['list']['tpl']['pagination']) ? $this->config['admin']['list']['tpl']['pagination'] : null;
		$tpl = isset($conf['tpl']['pagination']) ? $conf['tpl']['pagination'] : $def_tpl;

		$ll = 	isset($this->admin_nav['ll']) ? $this->admin_nav['ll'] :(
		isset($this->config['admin']['list']['limit']) ? $this->config['admin']['list']['limit'] : 10
		);

		$page = isset($this->admin_nav['lp']) ? $this->admin_nav['lp'] : 1;
		$page = AcidPagination::getPage($page,$nb_elts,$ll);

		$start = AcidUrl::build($cur_nav,array(static::preKey('lp')));
		$middle = '&amp;'.static::preKey('lp').'=';

		$nav = AcidPagination::getNav($page,$nb_elts,$ll,$tpl,array('link_start'=>$start,'link_middle'=>$middle));

		$limit = $limit = ($ll*($page-1)).','.$ll;

		return array($ll,$limit,$page,$nav);
	}

	/**
	* Retourne une portion HTML mettant en forme des indications relatives au listing
	*
	* @param int 		$nb_elts le nombre d'éléments trouvés par lors de l'execution de la recherche
	* @param int 		$page page courante
	* @param int 		$ll nombre maxi d'éléments par page
	* @param boolean 	$hide_filter_ind true si on affiche la gestion des filtres
	* @param array		$conf Configuration
	*
	* @return array  list( nombre maxi d'éléments par page , limite d'affichage, page courante, pagination )
	*/
	public function printAdminListInfo($nb_elts,$page,$ll,$hide_filter_ind,$conf=array()) {
		//$this->getAdminCurNav();
		$cur_nav = $this->getAdminCurNav();

		$aff = $nb_elts < $ll ? $nb_elts : $ll;
		$start = ($page-1)*$ll + 1;
		$stop = $start + $ll -1;

		if ($stop > $nb_elts) {
			$aff = $nb_elts%$ll;
			$stop = $nb_elts;
		}


		$txt_info = Acid::trad('admin_list_total_elts',array('__TOTAL__'=>$nb_elts,'__NB__'=>$aff,'__START__'=>$start,'__STOP__'=>$stop));
		$vars = array(
					'prekey'=>static::preKey(),
					'get'=>$_GET,
					'cur_nav'=>$cur_nav,
					'hide_filter_ind'=>$hide_filter_ind,
					'page'=>$page,
					'll'=>$ll,
					'total'=>$nb_elts,
					'nb'=>$aff,
					'start'=>$start,
					'stop'=>$stop,
					'txt_info'=>$txt_info
		);

		$tpl = isset($conf['tpl']['info']) ? $conf['tpl']['info'] : 'admin/admin-list-info.tpl';
		return Acid::tpl($tpl,$vars,$this);

	}


	/**
	 * Préparation du module pour les différents affichage/formulaires
	 * @param string $do Vue en cours
	 * @param array $conf Configuration
	 */
	public function printAdminConfigure($do='default',$conf=array()) {

	}


	/**
	 * Préparation du module pour le retour CSV
	 */
	public function printAdminConfigureCSV() {
		$this->config['print'] = array();
		if ($bools = $this->getVarsByType(array('AcidVarBool'))) {
			foreach ($bools as $key =>$var) {
				$this->config['print'][$key] = array('type'=>'bool');
			}
		}

		if ($list = $this->getVarsByType(array('AcidVarList','AcidVarRadio'))) {
			foreach ($list as $key =>$var) {
				$this->config['print'][$key] = array('type'=>'tab','tab'=>$var->getElts());
			}
		}
	}

	/**
	* Retourne le formulaire d\'ajout de l'objet après initialisation
	*
	* @return string
	*/
	public function printAdminAdd () {
		if (isset($this->config['admin']['add']['def'])) {
			$this->initVars($this->config['admin']['add']['def']); //,true
		}
		return $this->printAdminAddForm();
	}

	/**
	* Retourne l'interface de modification d'un élément du module sous forme d'une chaîne de caractères mise en forme.
	*
	*
	* @return string
	*/
	public function printAdminUpdate () {
		return $this->printAdminUpdateForm();
	}

	/**
	* Retourne le formulaire d\'ajout de l'objet
	*
	* @return string
	*/
	protected function printAdminAddForm () {
		return $this->printAdminForm('add');
	}

	/**
	* Retourne le formulaire de mise à jour de l'objet
	*
	* @return string
	*/
	protected function printAdminUpdateForm () {
		return $this->printAdminForm('update');
	}

	/**
	* Assigne aux formulaires des éléments au debut
	*
	* @param object $form objet AcidForm
	* @param string $do la vue demandée
	*
	* @return string
	*/
	public function printAdminFormStart(&$form, $do) {
		$form->tableStart();
	}

	/**
	* Assigne aux formulaires des éléments à la fin
	*
	* @param object $form objet AcidForm
	* @param string $do la vue demandée
	*
	* @return string
	*/
	public function printAdminFormStop(&$form, $do) {

		$this->printAdminFormSubmit($form,$do);
		$form->tableStop();

	}

	/**
	* Assigne le bouton submit au formulaire d'administration de la vue en entrée
	*
	* @param object $form objet AcidForm
	* @param string $do la vue demandée
	*
	* @return string
	*/
	public function printAdminFormSubmit(&$form, $do) {

		$submit_mode = isset($this->config['admin'][$do]['submit']) ? $this->config['admin'][$do]['submit'] : null;
		$submit_label = ($do=='add'? Acid::trad('admin_action_add'):Acid::trad('admin_action_update'));
		$submit_attr = array();

		if ($submit_mode) {
			$submit_type = isset($submit_mode['type']) ? $submit_mode['type'] : 'default';
			$submit_label = isset($submit_mode['label']) ? $submit_mode['label'] : $submit_label;
			$submit_attr = isset($submit_mode['params']) ? $submit_mode['params'] : $submit_attr;

			switch ($submit_type) {
				case 'image' :
					$submit_src = isset($submit_mode['src']) ? $submit_mode['src'] : '';
					return $form->addImage('',$submit_src,$submit_label,$submit_attr);
				break;
			}
		}

		$submit_attr['class'] = empty($submit_attr['class']) ? $do : $submit_attr['class'].' '.$do;

		$start = '<span class="submit_bg '.$do.'">';
		$stop  = '</span>';
		return $form->addSubmit('',$submit_label,$submit_attr,$start,$stop);
	}

	/**
	* Retourne le formulaire d'administration du module pour la vue demandée
	*
	* @param string $do la vue demandée
	*
	* @return string
	*/
	public function printAdminForm($do) {
		$this->printAdminConfigure($do);

		if ($sess_form = AcidSession::tmpGet(self::preKey($do))) {
			$sess_id = isset($sess_form[$this->tblId()]) ? $sess_form[$this->tblId()] : 0;
			if ($sess_id==$this->getId()) {
				$this->initVars($sess_form);
				AcidSession::tmpKill(self::preKey($do));
			}
		}

		$lang_keys = isset($this->config['multilingual']['keys']) ? $this->config['multilingual']['keys'] : $this->getMultiLingualKeys();

		$images_keys = array_keys($this->getVarsImages());
		$files_keys = array_keys($this->getVarsFiles());

		$excluded_keys = isset($this->config['admin'][$do]['keys_excluded']) ? $this->config['admin'][$do]['keys_excluded'] : array();

		$next_page = isset($this->config['admin'][$do]['next_page']) ? $this->config['admin'][$do]['next_page'] : null;
		$action_page = isset($this->config['admin'][$do]['action']) ? $this->config['admin'][$do]['action'] : '';

		if ($next_page === null) {
			$next_page = ($do!='add') ? false : AcidUrl::build(array($this->preKey().'do'=>'list'));
		}

		if (isset($this->config['admin'][$do]['keys'])) {
			$keys = $this->config['admin'][$do]['keys'];
		} else {
			$keys = array();
			foreach ($this->vars as $key=>$var) {
				if ($key != static::tblId()) {
					$keys[] = $key;
				}
			}
		}

		$form = new AcidForm('post',$action_page);
		$form->setFormParams(array('class'=>$this::TBL_NAME.' '.$this->preKey($do).' admin_form'));

		$form->addHidden('',$this->preKey('do'),$do);
		$form->addHidden('','module_do',$this->getClass());

		if ($do!='add') {
			$form->addHidden('',static::tblId(),$this->getId());
		}

		if ($next_page !== false) {
				$form->addHidden('','next_page',$next_page);
		}

		$this->printAdminFormStart($form,$do);

		if ($keys) {
			foreach ($keys as $key) {
				if (isset($this->vars[$key])) {
					if (!in_array($key,$excluded_keys)) {
						$params = isset($this->config['admin'][$do]['params'][$key]) ? $this->config['admin'][$do]['params'][$key] : array();
						$start = isset($this->config['admin'][$do]['start'][$key]) ? $this->config['admin'][$do]['start'][$key] : '';
						$stop = isset($this->config['admin'][$do]['stop'][$key]) ? $this->config['admin'][$do]['stop'][$key] : '';
						$body_attrs = isset($this->config['admin'][$do]['body_attrs'][$key]) ? $this->config['admin'][$do]['body_attrs'][$key] : array();

						if (in_array($key,$lang_keys)) {
							$l = explode('_',$key);
							$l = $l[(count($l)-1)];
							$l_class = 'lang '.$l;
							$params['class'] = !empty($params['class']) ? $params['class'].' '.$l_class : $l_class;
							$body_attrs['class'] = !empty($body_attrs['class']) ? $body_attrs['class'].' '.$l_class : $l_class;
						}

						if(Conf::get('plupload:active')) {
							if(Conf::get('plupload:all') || Conf::get('plupload:key:'.$key)) {
								if(in_array($key, array_keys($this->getUploadVars()) )) {
									$params['class'] = isset($params['class']) ? $params['class'] . ' plupload_for_'.$key : 'plupload_for_'.$key;
									$as = !isset($this->config['plupload']['autosubmit']) ? Acid::get('plupload:autosubmit') : $this->config['plupload']['autosubmit'];
									$multi = !empty($this->config['plupload']['multi'][$key]);

									if ($multi) {
										$stop .= '<div class="plupload_multi_for_'.$key.'"></div>';
										Conf::add('plupload:multi', array('selector'=>'.'.$this::TBL_NAME.'.'.$this->preKey($do).' .plupload_multi_for_'.$key,'extensions'=>implode(',',$this->vars[$key]->getConfig('ext')), 'hide_submit'=>$as,'key'=>$key));
									}else{
										Conf::add('plupload:selectors', array('.plupload_for_'.$key, '.'.$this::TBL_NAME.'.'.$this->preKey($do), $this->vars[$key]->getConfig('ext'), $as));
									}

								}
							}
						}

						$this->vars[$key]->getForm($form,$key,true,$params,$start,$stop,$body_attrs);
					}
				} else {
					trigger_error(get_called_class().'::printAdminForm('.$do.') key "'.$key.'" does not exists',E_USER_WARNING);
				}

			}
		}

		$this->printAdminFormStop($form,$do);


		$flags = $lang_keys ? $this->printAdminFlags($do,$lang_keys) : '';
		return $flags . $form->html();

	}

	/**
	* Retourne une gestion multilingue en javascript des formulaires d'administrations
	*
	* @param string $do la vue demandée
	* @param array $keys les clés
	*/
	public function printAdminFlags ($do='default',$keys=null) {
		$default = isset($this->config['multilingual']['flags']['default'])  ?  $this->config['multilingual']['flags']['default'] : false ;
		$current = isset($this->config['multilingual']['flags'][$do])  ?  $this->config['multilingual']['flags'][$do] : $default ;
		$tpl = $this->getAdminTpl($do);
		$tpl_def = $this->getAdminTpl('default');

		if ($current) {
			$langs = Acid::get('lang:available');
			$cur = Acid::get('lang:current');

			$tpl_flag_def = isset($tpl_def['flags']) ? $tpl_def['flags'] : 'admin/admin-flags.tpl';
			$tpl_flag = isset($tpl['flags']) ? $tpl['flags'] : $tpl_flag_def;
			return Acid::tpl($tpl_flag,array('def_lang'=>$cur,'langs'=>$langs,'do'=>$do,'keys'=>$keys));
		}
	}

	/**
	* Retourne les éléments de l'objet sous forme d'une chaîne de caractères mise en forme.
	*
	*
	* @return string
	*/
	public function printAdminElt () {
		$this->printAdminConfigure('print');

		if (isset($this->config['admin']['elt']['keys'])) {
			$keys = $this->config['admin']['elt']['keys'];
		} else {
			$keys = array();
			foreach ($this->vars as $key=>$var) {
				$keys[] = $key;
			}
		}

		$tbl = new AcidTable();
		$tbl->diplayHeaders('lines',true);
		//$tbl->diplayHeaders('cols',true);
		$tbl->setEvenClass('even_line');
		$tbl->setOddClass('odd_line');

		$tbl->setTopLeftCorner($GLOBALS['lang']['words']['module']['Key']);
		$tbl->setHeader('cols',1,'Valeur');

		$line = 1;
		foreach ($keys as $key) {
			$tbl->setHeader('lines',$line,$this->getLabel($key));
			$tbl->addVal($line,1,$this->getPrinted($key));
			$line++;
		}

		return Acid::tpl('admin/admin-print.tpl',array('content'=>$tbl->html()),$this);
	}

	/**
	* Retourne l'interface de recherche d'un élément du module sous forme d'une chaîne de caractères mise en forme.
	*
	*
	* @return string
	*/
	public function printAdminSearch () {
		$this->printAdminConfigure('search');

		$this->adminNav();
		$admin_nav = $this->getAdminCurNav();

		if (isset($this->config['admin']['search']['keys'])) {
			$keys = $this->config['admin']['search']['keys'];
		} else {
			$keys = array();
			foreach ($this->vars as $key=>$var) {
				$keys[] = $key;
			}
		}

		$method_list = array(
								'contain'=>Acid::trad('admin_search_list_has'),
								'is'=>Acid::trad('admin_search_list_is'),
								'start'=>Acid::trad('admin_search_list_start'),
								'stop'=>Acid::trad('admin_search_list_stop')
		);

		$ll = isset($this->config['admin']['list']['limit']) ? $this->config['admin']['list']['limit'] : 10;

		$tpl = $this->getAdminTpl('search');
		$tpl = $tpl ? $tpl : 'admin/admin-search.tpl';
		return Acid::tpl($tpl,array('admin_nav'=>$admin_nav,'ll'=>$ll,'vars'=>$this->vars,'keys'=>$keys,'get'=>$_GET,'method_list'=>$method_list),$this);
	}

	/**
	* Affiche un formulaire de switch on/off
	* @param string $key Nom du paramètre
	* @param object $obj L'objet
	* @param boolean $ajax Le formulaire sera traité en ajax
	*
	* @return string
	*/
	public function printFormToggle($key,$obj=null,$ajax=false) {
		$config = array();

		$obj = $obj===null ? $this : $obj;
		$active = $obj->get($key);
		$new_state = $active ? 0 : 1;
		$ident = 'form_toggle_'.$obj->checkTbl().'_'.$obj->getId().'_'.$key;
		$form = new AcidForm('post','');
		$form->setFormParams(array('class'=>'toggle_form to_value_'.$new_state.' '.$ident));

		$form->addHidden('',$obj->preKey('do'),'update');
		$form->addHidden('',$obj->preKey('toggle'),'1');
		$form->addHidden('','module_do',$obj->getClass());
		$form->addHidden('',$obj->tblId(),$obj->getId());

		$form->addHidden('',$key,$new_state);

		$real_key = self::dbPrefRemove($key);

		if ($ajax) {
			$onclick=	"$('.".$ident."').find('[type=submit]').attr('disabled','disabled'); ".
						"$.post('".Acid::get('url:ajax')."', $(this).serialize(), ".
						"function (data) { ".
						"var res = $.parseJSON(data); ".
						"if (res.success) { ".
						"var nval = res.obj.".$real_key." ? 0 : 1 ; var nlab = !nval ? '".Acid::trad('yes')."':'".Acid::trad('no')."'; ".
						"$('.".$ident."').find('[name=".$key."]').val(nval); ".
						"$('.".$ident."').find('[type=submit]').val(nlab); ".
						"$('.".$ident."').removeClass('to_value_0'); ".
						"$('.".$ident."').removeClass('to_value_1'); ".
						"$('.".$ident."').addClass('to_value_'+nval); ".
						"} ".
						"if (res.js!=undefined) { ".
						"	eval(res.js); ".
						"} ".
						"$('.".$ident."').find('[type=submit]').removeAttr('disabled'); " .
						"}); return false;";

			$form->addHidden('','ajax',1);
			$form->setFormParams(array('onsubmit'=>$onclick));
		}

		$label = $active ? Acid::trad('yes') : Acid::trad('no');


		$form->addSubmit('',$label,$config);

		return $form->html();
	}

	/**
	 * Affiche un formulaire de "quick change"
	 * @param string $key Nom du paramètre
	 * @param object $obj L'objet
	 * @param boolean $ajax Le formulaire sera traité en ajax
	 *
	 * @return string
	 */
	public function printFormQuickChange($key,$obj=null,$ajax=false,$params=array()) {

		$obj = $obj===null ? $this : $obj;

		$ident = 'form_quickchange_'.$obj->checkTbl().'_'.$obj->getId().'_'.$key;
		$form = new AcidForm('post','');
		$form->setFormParams(array('class'=>'quickchange_form '.$ident));

		$form->addHidden('',$obj->preKey('do'),'update');
		$form->addHidden('',$obj->preKey('quickchange'),'1');
		$form->addHidden('','module_do',$obj->getClass());
		$form->addHidden('',$obj->tblId(),$obj->getId());

		if (!isset($params['onchange'])) {
			$params['onchange'] = "$('.".$ident."').find('[type=submit]').click();";
		}

		$form->addFreeText('',$obj->getVarForm($key,true,$params).'<span class="loader"></span>');

		$real_key = self::dbPrefRemove($key);

		if ($ajax) {
			$loader = htmlspecialchars('<img style="margin-left:30px; display:inline; vertical-align:middle;" src="'.Acid::themeUrl('img/admin/loading.gif').'" alt="..." title="'.Acid::trad('loading').'" />');
			$onclick=
					"$('.".$ident."').find('[type=submit]').attr('disabled','disabled'); ".
					"$('.".$ident."').find('.loader').html('".$loader."');".
					"$.post('".Acid::get('url:ajax')."', $(this).serialize(), ".
					"function (data) { ".
					"var res = $.parseJSON(data); ".
					"if (res.".$key." !=undefined) { " .
					"$('.".$ident."').find('[name=".$key."]').val(res.".$key."); ".
					"}" .
					"if (res.js!=undefined) { ".
					"	eval(res.js); ".
					"} ".
					"$('.".$ident."').find('[type=submit]').removeAttr('disabled'); " .
					"setTimeout(function() { $('.".$ident."').find('.loader').html(''); },300);" .
					"}); return false;";

			$form->addHidden('','ajax',1);
			$form->setFormParams(array('onsubmit'=>$onclick));
		}

		$label = Acid::trad('update');
		$form->addSubmit('',$label,array('style'=>'display:none;'));

		return $form->html();
	}

	/**
	 * Retourne un AcidCSV correspondant au module
	 * @param mixed $filter filtre à appliquer sue le dbList
	 * @param array $fields les champs à mettre dans le CSV
	 * @param array $config la configuration array(delimiter,enclosure,espace)
	 * @param bool $label utilise getLabel pour le nom des colonnes
	 * @param bool $printed utilise getPrined pour l'affichage des valeurs (attention, printAdminConfigureCSV ré-initialise $this->config)
	 * @return AcidCSV
	 */
	public function exportCSV($filter='',$fields=array(),$config=null,$label=false,$printed=true) {

		$this->printAdminConfigureCSV();

		$csv = new AcidCSV();

		if ($config) {
			$csv->setConfig(
					isset($config[0])? $config[0]:null, //delimiter
					isset($config[1])? $config[1]:null, //enclosure
					isset($config[2])? $config[2]:null //espace
			);
		}

		$csv->setHead($csv->getInitHead($this->getKeys(),$fields));
		$head = $csv->getHead();
		$res = static::dbList($filter);
		if ($res) {
			$tab = array();
			foreach ($res as $elt) {
				$mod = self::build($elt);
				$line = array();

				if ($printed) {
					foreach ($this->getKeys() as $key) {
						if ((!$fields) || in_array($key,$fields)) {
							$line[] = $this->getPrinted($key,$elt);
						}else{
							$line[] = '';
						}
					}
				}else{
					$line = array_values($mod->getVals());
				}

				$tab[] = $csv->getInitRow($line,$head);
			}

			$csv->setRows($tab);

		}

		if ($label) {
			$hkeys = array();
			foreach ($head as $key => $num) {
				$hkeys[$this->getLabel($key)] = $num;
			}
			$csv->setHead($hkeys);
		}


		return $csv;
	}


	/**
	* Retourne la pré-configuration d'affichage associée à la clé en entrée
	* @param string $key nom du paramètre
	* @return mixed
	*/
	public function getPrintedAssoc($key="") {

		$module = get_class($this->vars[$key]);

		$admin_format = ($module=='AcidVarImage') ? $this->vars[$key]->getConfig('admin_format') : '';

		$tab = 	array(
					'AcidVarBool'			=> array('type'=>'bool'),
					'AcidVarDateTime'		=> array('type'=>'date','format'=>'datetime','empty_val'=>'-'),
					'AcidVarDate'			=> array('type'=>'date','format'=>'date','empty_val'=>'-'),
					'AcidVarUrl'			=> array('type'=>'link','absolute'=>true),
					'AcidVarEmail' 			=> array('type'=>'mailto'),
					'AcidVarText' 			=> array('type'=>'split'),
					'AcidVarTime' 			=> array('type'=>'time'),
					'AcidVarImage'			=> array('type'=>'img','link'=>'src','size'=>($admin_format ? $admin_format : 'mini')),
					'AcidVarList' 			=> array('type'=>'tab', 'tab'=>$this->vars[$key]->getElts()),
					'AcidVarFile' 			=> array('type'=>'link', 'absolute'=>false, 'basename'=>true, 'popup'=>true)
		);


		if($module) {
			return isset($tab[$module]) ? $tab[$module] : false;
		}

		return $tab;
	}

	/**
	* Retourne une valeur de l'objet mise en forme pour l'interface d'adminsitration.
	* @param string $key	Nom du paramètre
	* @param array $elt 	Tableau representatif de l'objet
	* @param array $conf 	Configuration
	* @return mixed
	*/
	public function getPrinted($key,$elt=null,$conf=array()) {
		$nkey = isset($this->vars[$key]) ? $key : (isset($this->vars[self::dbPrefRemove($key)]) ? self::dbPrefRemove($key) : $key);

		if ($elt === null) {
			$elt = $this->getVals();
			$val = $this->trad($key);
		}elseif (is_array($elt)) {
			$val = isset($elt[$key]) ? $elt[$key] : null;
		}else{
			$val = $elt;
		}

		if (isset($this->vars[$nkey])) {
			$def_value = $this->getPrintedAssoc($nkey);
		}

		if ( isset($this->config['print'][$nkey]) || (!empty($def_value)) ) {

			$specs = isset($this->config['print'][$nkey]) ? $this->config['print'][$nkey] : $def_value;


			if (isset($specs['type'])) {
				switch ($specs['type']) {

					case 'link' :
						$prefix = isset($specs['absolute']) && $specs['absolute'] ? '' : Acid::get('url:folder');
						$url_val = $val ? $prefix.$val  : "";
						$text = isset($specs['text']) ? $specs['text'] : $url_val;
						$text = (isset($specs['basename']) && !isset($specs['text'])) ? basename($text) : $text;
						$popup = isset($specs['popup']) ? $specs['popup'] : false;
						return Acid::tpl('core/print/link.tpl',array('url'=>$url_val,'text'=>$text, 'popup'=>$popup),$this);
					break;

					case 'mailto' :
						$text = isset($specs['text']) ? $specs['text'] : $val;
						return Acid::tpl('core/print/mailto.tpl',array('email'=>$val,'text'=>$text),$this);
					break;

					case 'tab' :
						if (isset($specs['tab'])) {
							if (isset($specs['tab'][$val])) {
								return $specs['tab'][$val];
							}else{
								return $val;
							}
						}
					break;

					case 'bool' :
						return $val ? Acid::trad('yes') : Acid::trad('non');
					break;

					case 'toggle' :
						$toggle_key = $nkey;

						if (isset($specs['module'])) {
							$module = $specs['module']::build($elt);
							$toggle_key = $module::dbPrefRemove($key);
						}else{
							$module = static::build($elt);
						}

						return $module->printFormToggle($toggle_key,null,(!empty($specs['ajax'])));
					break;

					case 'quickchange' :
						$quickchange_key = $nkey;

						if (isset($specs['module'])) {
							$module = $specs['module']::build($elt);
							$quickchange_key = $module::dbPrefRemove($key);
						}else{
							$module = static::build($elt);
						}

						$params = isset($specs['params']) ? $specs['params'] : array();

						return $module->printFormQuickChange($quickchange_key,null,(!empty($specs['ajax'])),$params);
					break;

					case 'mod_tab' :
						if (isset($specs['mod']) && isset($specs['field'])) {
							if ($obj = new $specs['mod']($val)) {
								return $obj->get($specs['field']);
							}
						}
					break;

					case 'sql' :
						if (isset($specs['req']) && isset($specs['field'])) {
							if ($rep = AcidDB::query($req)) {
								return $rep[0][$specs['field']];
							}
						}
					break;

					case 'date' :
						$format  = isset($specs['format']) ? $specs['format'] : 'date';
						$format = $format == 'date' ? Acid::get('date_format:date','lang') : $format;
						$format = $format == 'datetime' ? Acid::get('date_format:datetime','lang') : $format;
						$format = $format == 'datetime_small' ? Acid::get('date_format:datetime_small','lang') : $format;

						$empty_val = isset($specs['empty_val']) ? $specs['empty_val'] : '';
						return AcidTime::conv($val,$format,$empty_val);
					break;

					case 'time' :
						return AcidTime::conv($val);
						break;

					case 'img' :
						$src_key = isset($specs['key']) ? $specs['key']  : $nkey;
						$size = isset($specs['size']) ? $specs['size']  : null;
						$view = isset($specs['link']) ? $specs['link']  : null;
						return Acid::tpl('core/print/img.tpl',array('size'=>$size,'view'=>$view,'src'=>$val,'key'=>$src_key),$this);
						break;

					case 'split' :
						$size = isset($specs['size']) ? $specs['size']  : 200;
						$end = isset($specs['end']) ? $specs['end']  : '...' ;

						return 	AcidVarString::split($val,$size,$end);
					break;

					case 'func' :
						if (isset($specs['name']) && isset($specs['args'])) {
							if (isset($specs['load'])) {
								Acid::load($specs['load']);
							}
							foreach ($specs['args'] as $k=>$v) {
								if ($specs['args'][$k] === '__ELT__') {
									$specs['args'][$k] = $elt;
								}elseif ($specs['args'][$k] === '__VAL__') {
									$specs['args'][$k] = $val;
								}elseif ($specs['args'][$k] === '__CONF__') {
									$specs['args'][$k] = $conf;
								}elseif ($specs['args'][$k] === '__ELTS__') {
									$specs['args'][$k] = isset($conf['format_elts']) ? $conf['format_elts'] : array();
								}else{
									$specs['args'][$k] = str_replace('__VAL__',$val,$v);
								}
							}
							return call_user_func_array($specs['name'],$specs['args']);
						}
					break;
				}
			}
		}
		return htmlspecialchars($val);
	}

}