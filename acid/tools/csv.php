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
 * Outil AcidCSV, Gestionnaire de CSV
 * @package   Acidfarm\Tool
 */
class AcidCSV {

	/**
	 * @var array entete courante
	 */
	protected $head = array();

	/**
	 * @var array lignes courantes
	 */
	protected $rows = array();

	/**
	 * @var string CSV sous forme de chaine
	 */
	protected $csv_str = '';

	/**
	 * @var string Chemin vers le CSV
	 */
	protected $csv_path = '';



	/**
	 * @var string delimiter du CSV
	 */
	public $delimiter=',';

	/**
	 * @var string enclosure du CSV
	 */
	public $enclosure='"';

	/**
	 *
	 * @var string echappement du CSV
	 */
	public $escape='\\';


	const FILTER_SENSIBILITY = false;
	const INIT_STR = 1;
	const INIT_PATH = 2;
	const INIT_PATH_TO_STR = 3;

	/**
	 * CONSTRUCTEUR : DEPUIS un string ou un path, on alimente le csv_str ou le csv_path
	 * @param string $string source CSV
	 * @param int $method methode d'initialisation du CSV
	 * @param array $config configuration du CSV
	 */
	public function __construct($string='',$method=self::INIT_STR,$config=null) {
		switch ($method) {

			//on alimente le csv_str
			case self::INIT_PATH_TO_STR :
				$string = file_get_contents($string);
			case self::INIT_STR :
				$this->setStr($string);
			break;

			//on alimente le csv_path
			case self::INIT_PATH :
				$this->setPath($string);
			break;

		}

		if ($config) {
			$this->setConfig(
				isset($config[0])? $config[0]:null,
				isset($config[1])? $config[1]:null,
				isset($config[2])? $config[2]:null
			);
		}
	}

	/**
	 * Définit les paramètre de configuration du csv
	 * @param string $delimiter
	 * @param string $enclosure
	 * @param string $escape
	 */
	public function setConfig($delimiter=null,$enclosure=null,$escape=null) {

		if ($delimiter!==null) {
			$this->delimiter=$delimiter;
		}

		if ($enclosure!==null) {
			$this->enclosure=$enclosure;
		}

		if ($escape!==null) {
			$this->escape=$escape;
		}

	}

	/**
	 * Alimente csv_str
	 * @param string $string
	 */
	protected function setStr($string) {
		if ($string) {
			$this->csv_str = $string;
		}
	}

	/**
	 * Alimente csv_path
	 * @param string $path
	 */
	protected function setPath($path) {
		if ($path) {
			$this->csv_path = $path;
		}
	}

	/**
	 * Change l'entete du CSV
	 * @param array $tab
	 */
	public function setHead($tab) {
		$this->head = $tab;
	}


	/**
	 * Changes les lignes du CSV
	 * @param array $tab
	 */
	public function setRows($tab) {
		$this->rows = $tab;
	}

	/**
	 * Cherche la présence d'un élément dans un tableau et retourne son indice si c'est le cas, sinon retourne false
	 * Si sensible est à true, alors on est sensible à la casse
	 * @param string $needle
	 * @param array $haystack
	 * @param boolean $sensible
	 * @return mixed|unknown
	 */
	public static function array_search_key($needle, $haystack, $sensible = true) {
		if ($sensible) {
			return  array_search($needle, $haystack);
		}else{
			$s = false;
			foreach ($haystack as $key =>$val) {
				if ((!$s) && (strtoupper($val)==strtoupper($needle))) {
					$s = $key;
				}
			}

			return $s;
		}
	}

	/**
	 * Retourne l'entete de csv_str / csv_path  , filtré ou non , sous la forme d'un tableau 'nom'=>indice (colonne de la source)
	 * @param array() $filter
	 * @return mixed
	 */
	public function readHead($filter=null) {

		//on récupère la première ligne

		//source chaine de caractères
		if ($this->csv_str) {
			$line =  self::strGetFirstLine($this->csv_str);
		}
		//source fichier
		elseif ($this->csv_path){
			$line = self::pathGetFirstLine($this->csv_path);
		}
		//pas de source
		else{
			return false;
		}




		$head = array();

		if ($line) {
			$tab = str_getcsv($line,$this->delimiter,$this->enclosure,$this->escape);

			$head = $this->getInitHead($tab,$filter);

		}

		return $head;
	}

	/**
	 * Retourne la première ligne d'une chaine de caractère
	 * @param string $str
	 * @return string
	 */
	public static function strGetFirstLine($str) {
		$pos = strpos($str,"\n");
		if ($pos !== false) {
			$line = substr($str,0,$pos);
		}else{
			$line = $str;
		}

		return $line;
	}

	/**
	 * Retourne la première ligne d'un fichier
	 * @param string $path
	 * @return string
	 */
	public static function pathGetFirstLine($path) {
		$line = '';

		$handle = fopen($path, "r");
		if ($handle) {
			$line = fgets($handle);
		}
		fclose($handle);

		return $line;
	}

	/**
	 * Génère le tableau head / rows ,  avec entête filtrée ou non depuis csv_str / csv_path
	 * @param array $filter
	 */
	public function init($filter=null) {

		$this->head = $this->readHead($filter);

		$this->initRows($filter);

	}

	/**
	 * Retourne un tableau d'entete correspondant à $tab filtré par $filter
	 * @param array $tab
	 * @param array $filter
	 * @return mixed
	 */
	public static function getInitHead($tab,$filter=array()) {
		$head = array();

		if ($filter) {


			foreach ($filter as $needle) {
	 			$s = self::array_search_key($needle, $tab, self::FILTER_SENSIBILITY);
	 			if ($s !== false) {
	 				$head[$needle] = $s;
	 			}
		 	}


		}else{

			foreach ($tab as $key=>$val) {
	 			$head[$val] = $key;
	 		}

		}

		return $head;
	}

	/**
	 * Retourne la valeur avec l'enclosure
	 * @param string $val
	 */
	public  function getEnclosedVal($val) {
		$replace = str_replace($this->enclosure,$this->escape.$this->enclosure,$val);
		return $this->enclosure. $replace . $this->enclosure ;
	}


	/**
	 * Retourne un tableau correspondant à $tab filtré par $head
	 * @param array $tab tableau sources
	 * @param array $head tableau de filtrage
	 * @param boolean $use_enclosure
	 * @return array
	 */
	public function getInitRow($tab,$head,$use_enclosure=false) {
		$row = array();


		foreach ($head as $val => $key) {
			if ($use_enclosure) {
				$row[$key] = isset($tab[$key])? self::getEnclosedVal($tab[$key]):null ;
			}else{
				$row[$key] = isset($tab[$key])? $tab[$key]:null ;
			}
		}

		return $row;
	}


	/**
	 * Alimente rows selon head, depuis csv_str / csv_path
	 * @param array filter
	 */
	protected function initRows($filter=null) {

		//source chaine de caractères
		if ($this->csv_str) {
			$tab =  self::parseStr($this->csv_str,$filter,$this->delimiter,$this->enclosure,$this->escape);
		}
		//source fichier
		elseif ($this->csv_path){
			$tab =  self::parseFile($this->csv_path,$filter,$this->delimiter,$this->enclosure,$this->escape);
		}
		//pas de source
		else{
			return false;
		}

		$i = 1;

		if ($this->head) {
			$nb_row = count($tab);
			if ($nb_row>1) {

				//traitement des lignes
			 	while ($i < $nb_row) {
			 		$row = array();

			 		if (is_array($tab[$i])) {

			 			$this->rows[] = $this->getInitRow($tab[$i],$this->head);
			 		}
			 		$i++;
			   	}

			}

		}
	}


	/**
	 * Ecrit le CSV dans une chaine de caractères
	 * @param array $filter les champs du CSV
	 */
	public function writeStr($filter=null) {
		$str = '';
		if ($filter) {
				$new_head = $this->getInitHead(array_flip($this->head),$filter);
				$str .= implode($this->delimiter,array_keys($new_head)) . "\n";

				foreach ($this->rows as $fields) {
					$str .= implode($this->delimiter,$this->getInitRow($fields,$new_head,true)) . "\n" ;
				}
		}
		//Si c'est une copie à l'identique
		else{


				$str .= implode($this->delimiter,array_keys($this->head)) . "\n";

				foreach ($this->rows as $fields) {
					//$str .= implode($this->delimiter,$fields) . "\n" ;

					$str .= implode($this->delimiter,$this->getInitRow($fields,$this->head,true)) . "\n" ;
				}
		}


		return $str;
	}

	/**
	 * Ecrit le CSV dans un fichier
	 * @param string $path destination
	 * @param array $filter les champs du CSV
	 */
	public function writeFile($path,$filter=null) {
		$handle = fopen($path, 'w');

		if ($handle) {

			//si c'est une copie filtrée
			if ($filter) {
				$new_head = $this->getInitHead(array_flip($this->head),$filter);
				fputcsv($handle, array_keys($new_head) , $this->delimiter, $this->enclosure);

				foreach ($this->rows as $fields) {
				    fputcsv($handle, $this->getInitRow($fields,$new_head), $this->delimiter, $this->enclosure);
				}
			}
			//Si c'est une copie à l'identique
			else{

				fputcsv($handle, array_keys($this->head) , $this->delimiter, $this->enclosure);

				foreach ($this->rows as $fields) {
				    fputcsv($handle, $fields, $this->delimiter, $this->enclosure);
				}

			}


			fclose($handle);

		}
	}

	/**
	 * Génère un tableau à l'image du csv STR filtré ou non
	 * La première ligne correspond à l'entete
	 * @param string $str
	 * @param array $filter
	 * @param string $delimiter
	 * @param string $enclosure
	 * @param string $escape
	 * @return array
	 */
	public static function parseStr($str,$filter=null,$delimiter=',',$enclosure='"',$escape='\\') {
		$lines = explode("\n",$str);


		$rows = array();
		$i = 0;
		foreach ( $lines as $line) {

			$r = str_getcsv($line,$delimiter,$enclosure,$escape);

			if ($filter) {

				if ($i==0) {
					$head = self::getInitHead($r,$filter);
				}

				$r = self::getInitRow($r,$head);


			}

			$rows[] = $r;

			$i++;
		}

		return $rows;
	}

	/**
	 * Génère un tableau à l'image du csv de PATH filtré ou non
	 * La première ligne correspond à l'entete
	 * @param string $path
	 * @param array $filter
	 * @param string $delimiter
	 * @param string $enclosure
	 * @param string $escape
	 * @return array
	 */
	 public static function parseFile($path,$filter=null,$delimiter=',',$enclosure='"',$escape='\\') {
		$rows = array();

		$handle = fopen($path, "r");
		if ($handle) {
			while (($line = fgetcsv($handle,null,$delimiter,$enclosure,$escape)) !== false) {
				$rows[] = $line;
			}
		}
		fclose($handle);
		return $rows;
	}

	/**
	 * retourne l'indice de stockage de la colonne si elle existe, retourne false sinon
	 * @param string $needle
	 */
	public function getCol($needle) {
		return isset($this->head[$needle])? $this->head[$needle]:false;
	}

	/**
	 * Génère un tableau des valeurs de la colonne needle
	 * @param string $needle
	 */
	public function getColVals($needle) {
		$tab=array();

		$s = $this->getCol($needle);

		if ($s!==false) {

			foreach ($this->rows as $line =>$elt) {
				$tab[] = isset($elt[$s]) ? $elt[$s] : null;
			}

			return $tab;

		}else{
			return false;
		}
	}

	/**
	 * Génère un tableau des valeurs de la ligne line
	 * @param int $line
	 */
	public function getRowVals($line) {
		$tab=array();

		if (isset($this->rows[$line])) {
			return $this->rows[$line];
		}else{
			return false;
		}
	}

	/**
	 * retourne la valeur de la colonne needle à la ligne line
	 * @param string $needle
	 * @param int $line
	 * @return boolean
	 */
	public function getVal($needle,$line) {
		$s = $this->getCol($needle);

		if ($s!==false) {
			if (isset($this->rows[$line][$s])) {
				return $this->rows[$line][$s];
			}
		}

		return false;
	}

	/**
	 * Retourne les valeurs du csv
	 *
	 */
	public function getVals() {
		return $this->rows;
	}

	/**
	 * Retourne les lignes du CSV
	 */
	public function getRows() {
		return $this->rows;
	}

	/**
	 * Retourne l'entete du CSV
	 */
	public function getHead() {
		return $this->head;
	}

	/**
	 * Affiche le tableau de l'objet
	 * @param boolean $axe si true, affiche les numéro de ligne
	 */
	public function show($axe=true) {
		$res ='<table>';

		$res .= '<tr>';
		$res .= $axe ? '<td>*</td>':'';
		foreach ($this->head as $val => $key) {
			$res .= '<th>'.$val.'</th>';
		}
		$res .= '</tr>';



		foreach ($this->rows as $k => $elt) {
			$res .= '<tr>';
			$res .= $axe ? '<td>'.$k.'</td>':'';

			foreach ($elt as $key => $val) {
				$res .= '<td>'.$val.'</td>';
			}
			$res .= '</tr>';
		}

		$res .= '</table>';


		return $res;
	}
}
