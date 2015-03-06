<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Script
 * @version   0.1
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Pre-génère des fichiers de module à partir d'une base de donnée
 */

include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../glue.php';


//Getting PARAMS
$opt = getopt('t:d:f:p:h::',array('help::'));

$table = isset($_GET['tbl']) ? $_GET['tbl'] : null;
if (isset($opt['t'])) {
	$table = $opt['t'];
}

$class_to = isset($_GET['to']) ? $_GET['to'] : null;
if (isset($opt['d'])) {
	$class_to = $opt['d'];
}

$file_to = isset($_GET['file']) ? $_GET['file'] : null;
if (isset($opt['f'])) {
	$file_to = $opt['f'];
}

$tbl_pref = isset($_GET['tbl_pref']) ? $_GET['tbl_pref'] : null;
if (isset($opt['p'])) {
	$tbl_pref = $opt['p'];
}

$dir = isset($_GET['dir']) ? $_GET['dir'] : 'sys/script/class/';
if (isset($opt['w'])) {
	$dir = $opt['w'];
}


if (isset($opt['h'])||isset($opt['help'])||(!isset($opt['t']))) {
	echo "-t [all|nom de la table] : table(s) à traiter (obligatoire)" . "\n" ;
	echo "-d [nom de la classe] : nom de la classe de destination (optionnel)" . "\n" ;
	echo "-p [prefixe des tables] : prefixe associé aux tables SQL (optionnel)" . "\n" ;
	echo "-f [nom fichier] =nom de la table : nom du fichier de classe (optionnel)" . "\n" ;
	echo "-w [chemin du dossier] ='sys/script/class/' : chemin vers le dossier de destination des fichiers de classe depuis SITE_PATH (optionnel)" . "\n" ;
	exit();
}


$dir = SITE_PATH.$dir;
if (!is_dir($dir)) {
	mkdir($dir);
}

/**
 * Genère un nom de classe en fonction du nom de table
 * @param string $tbl nom de la table
 * @return string
 */
function buildClassName($tbl) {
	if ($tbl) {
		$classname = '';
		$parse = explode('_',$tbl);
		if ($parse) {
			foreach ($parse as $str) {
				$classname .= ucfirst($str);
			}
		}

		return $classname;
	}

	return $tbl;
}

/**
 * Génère le contenu d'un fichier de classe PHP
 * @param unknown_type $row
 * @param unknown_type $tbl
 * @return string
 */
function buildFromTbl($row,$tbl) {
	$info = explode(')',$row['Type']);
	$attributes = trim(implode(')',array_slice($info,1)));

	$t = explode('(',$info[0]);
	$type = strtolower(trim($t[0]));
	$type_size = trim(implode('(',array_slice($t,1)));
	$def = trim($row['Default']);
	$key = trim($row['Field']);
	$extra =trim($row['Extra']);

	$config = '';
	$val = null;
	Acid::log('SCRIPT','Generating '.$key . ' => ' . $type . ',  '. $type_size . ' => '. $attributes . ' => ' . $def .' => '.$extra);

	switch ($type) {
		//AcidVarInt
		case 'int' :
		case 'tinyint' :
		case 'mediumint' :
		case 'smallint' :
		case 'bigint' :
			$unsigned = (strtolower($attributes)=='unsigned') ? 'true' : 'false';
			$default = $def ? ','.$def : '';
			$val = 'new AcidVarInt($this->modTrad(\''.$key.'\'),'.$unsigned.$default.')';
		break;
		//AcidVarFloat
		case 'float' :
		case 'double' :
		case 'real' :
			$unsigned = (strtolower($attributes)=='unsigned') ? 'true' : 'false';
			$default = $def ? ','.$def : '';
			$val = 'new AcidVarFloat($this->modTrad(\''.$key.'\'),'.$unsigned.$default.')';
		break;
		//AcidVarString / AcidVarImage / AcidVarFile
		case 'varchar' :
			if ((strpos($key,'src')===0) || (strpos($key,'file')===0)) {
				$val = 'new AcidVarFile($this->modTrad(\''.$key.'\'),Acid::get(\'path:files\').\''.$tbl.'/\', array() , \'__ID__-'.$key.'\')';
			}elseif((strpos($key,'img')===0) || (strpos($key,'avatar')===0) || (strpos($key,'image')===0)){
				$config = <<<'EOF'

		$img_format 		=	array(
							'src'=>array(	'size' => array(0,0,false), 'suffix' => '', 'effect' => array() ),
							'large'=>array(	'size' => array(500,500,false),	'suffix' => '_l', 'effect' => array() ),
							'medium'=>array( 'size' => array(180,180,true),	'suffix' => '_m', 'effect' => array() ),
							'small'=>array(	'size' => array(48,48,true), 'suffix' => '_s', 'effect' => array()	)
						);
		$img_config = array('format'=>$img_format,'admin_format'=>'small');

EOF;
				$val = 'new AcidVarImage($this->modTrad(\''.$key.'\'),Acid::get(\'path:files\').\''.$tbl.'/\', $img_config, \'__ID__-'.$key.'\')';
			}else{
				$default = $def ? ','.$def : '';
				$field_size = ($type_size < 40) ? $type_size : round($type_size/2);
				$field_size = ($field_size > 25) ? 25 : $field_size;
				$val = 'new AcidVarString($this->modTrad(\''.$key.'\'),'.$field_size.','.$type_size.$default.')';
			}
		break;
		//AcidVarText
		case 'text' :
		case 'longtext' :
		case 'mediumtext' :
		case 'tinytext' :
			$default = $def ? ','.$def : '';
			$val = 'new AcidVarText($this->modTrad(\''.$key.'\'),60,5'.$default.')';
		break;
		//AcidVarTime
		case 'time' :
			$val = 'new AcidVarTime($this->modTrad(\''.$key.'\'))';
		break;
		//AcidVarDateTime
		case 'datetime' :
		case 'timestamp' :
			$val = 'new AcidVarDateTime($this->modTrad(\''.$key.'\'))';
		break;
		//AcidVarDate
		case 'date' :
			$unsigned = (strtolower($attributes)=='unsigned') ? 'true' : 'false';
			$val = 'new AcidVarDate($this->modTrad(\''.$key.'\'))';
		break;
		//AcidVarBool / AcidVarList
		case 'enum' :
			if ($type_size=="'0','1'") {
				$default = $def ? ','.$def : '';
				$val = 'new AcidVarBool($this->modTrad(\''.$key.'\')'.$default.')';
			}else{
				$default = $def ? $def : '';
				$val = 'new AcidVarList($this->modTrad(\''.$key.'\'),array('.$type_size.'),\''.$default.'\',false,false)';
			}
		break;
	}

	$config = $config ? ($config . "\n" . '		') : '';
	$val = $config.'$this->vars[\''.$key.'\']	=	'.(!$val ? 'null' : $val) .';' ;

	Acid::log('SCRIPT','Built :  '.$val);
	return $val;
}

$tables = array();

if ($table) {
	if ($table!='all') {
		$tables[] = array('table'=>$table,'class_to'=>$class_to,'file_to'=>$file_to);
	}else{
		$req = AcidDB::query('SHOW TABLES')->fetchAll(PDO::FETCH_ASSOC);

		$tblname = '';
		foreach ($req as $key => $value) {


			$ta = $value['Tables_in_'.Acid::get('db:base')];
			if (strpos($ta,$tbl_pref)===0){
				$has_no_pref = false;
				$ta = substr($ta,strlen($tbl_pref));
			}else{
				$has_no_pref = true;
			}
			Acid::log('SCRIPT','Table found '.$ta);
			$tables[] = array('table'=>$ta,'has_no_pref'=>$has_no_pref);
		}
	}

	if ($tables) {
		$include_txt = '';
		$include_lang = '';
		foreach ($tables as $tbl_config) {
			$table  = empty($tbl_config['table']) ? '' : $tbl_config['table'];


			if ($table) {
				$class_to = empty($tbl_config['class_to']) ? buildClassName($table) : $tbl_config['class_to'];
				$file_to = empty($tbl_config['file_to']) ? $table.'.php' : $tbl_config['file_to'];
				$has_no_pref = empty($tbl_config['has_no_pref']) ? false : $tbl_config['has_no_pref'];

				$include_txt .= '$acid[\'includes\'][\''.$class_to.'\'] 			= \'sys/modules/'.$table.'.php\';' . "\n" ;

				$reqpref = $has_no_pref ? $table : $tbl_pref.$table;
				Acid::log('SCRIPT','Building '.$class_to.' from '.$reqpref.$table.' in file '.$file_to);

				$result = AcidDB::query("SHOW FIELDS FROM ".addslashes($reqpref))->fetchAll(PDO::FETCH_ASSOC);

				$i = 1;
				$primary = '';
				$builder = array();

				$include_lang .= '//--'.$class_to . "\n";
				$include_lang .= '$lang[\'mod\'][\''.$table.'\'][\'__NAME__\'] = \''.ucfirst(str_replace('_',' ',addslashes($class_to))).'\';' . "\n" ;
				foreach ($result as $row) {
					// [Field]
			    	// [Type]
			    	// [Null]
			    	// [Key]
			    	// [Default]
			    	// [Extra]
			    	// [Attributs]
					 $key = $row['Field'];
				 	 if ($row['Key']=='PRI') {
				 	 	$primary = $key;
				 	 	Acid::log('SCRIPT',$primary . ' is PRIMARY');
				 	 }

				 	 $builder[$key] = buildFromTbl($row,$table);
				 	 $include_lang .= '$lang[\'mod\'][\''.$table.'\'][\''.$key.'\'] = \''.ucfirst(str_replace('_',' ',addslashes($key))).'\';' . "\n" ;

				}
				$include_lang .= "\n";

				$print_vars = implode("\n".'		',$builder);
			$class_content = <<<EOF
<?php

class $class_to extends AcidModule {
	const TBL_NAME = '$table';
	const TBL_PRIMARY = '$primary';

	public function __construct(\$init_id=null) {

		$print_vars

		parent::__construct(\$init_id);

	}

}

EOF;
			$handle = fopen($dir.$file_to, 'w+');
			flock($handle, LOCK_EX);
			fwrite($handle, $class_content);
			flock($handle, LOCK_UN);
			fclose($handle);

			}
		}

		$handle = fopen($dir.'_includer.txt', 'w+');
		flock($handle, LOCK_EX);
		fwrite($handle, $include_txt);
		flock($handle, LOCK_UN);
		fclose($handle);

		$handle = fopen($dir.'_lang.txt', 'w+');
		flock($handle, LOCK_EX);
		fwrite($handle, $include_lang);
		flock($handle, LOCK_UN);
		fclose($handle);

	}
}