<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Core
 * @version   0.2.1
 * @since     Version 0.2
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


@ini_set('AddDefaultCharset','utf-8');
if (!file_exists('.htaccess')) {
	$h = fopen('.htaccess','w');
	$ec = fwrite($h, 'AddDefaultCharset UTF-8');
	fclose($h);
	header('Location: '.$_SERVER['REQUEST_URI']);
}


if (get_magic_quotes_gpc()) {


	/**
	 * Applique un stripslashes sur un tableau ou une chaine
	 * @param mixed $value tableau ou chaine
	 * @return string
	 */
	function stripslashes_deep($value) {
		$value = is_array($value) ?
					array_map('stripslashes_deep', $value) :
					stripslashes($value);
		return $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}


/**
 *  Génération aléatoire de mot de passe
 *
 *  @return string
 */
function getRandPasswordSalt() {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$nbchars = strlen($chars);
		$ident = '';

		$salt_size = 8;
		for ($i=0;$i<=$salt_size;$i++) {
			$ident .= $chars[rand(0,$nbchars-1)];
		}
		return $ident;
}

/**
 * Retourne la liste des thèmes disponibles à l'install
 * @return string
 */
function getThemes() {

	$themes = array(''=>'default');
	$path = __DIR__.'/theme';

	if (is_dir($path)) {
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if (!in_array($entry,array('.','..','default'))) {
					if (is_dir($path.'/'.$entry)) {
						$themes[$entry] = $entry;
					}
				}
		    }

		    closedir($handle);
		}

	}

	return $themes;
}

/**
 * Test si les informations de BDD sont correctes
 * @param string $db_type
 * @param string $db_host
 * @param string $db_port
 * @param string $db_base
 * @param string $db_user
 * @param string $db_pass
 * @return boolean
 */
function checkDataBase($db_type,$db_host,$db_port,$db_base,$db_user,$db_pass) {

	$db = new PDO($db_type.
			':host='.$db_host.
			';port='.$db_port.
			';dbname='.$db_base,
			$db_user,
			$db_pass
	);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	//ACCESS
	$rand = rand(1,100);
	$res = $db->query('SELECT '.$rand.' as rand_value')->fetch(PDO::FETCH_ASSOC);

	return  ($res['rand_value'] == $rand);

}


$action = $_POST;

$dir_path = __DIR__.'/sys/server.php';
$htaccess_path = __DIR__.'/.htaccess';
$htaccess_path_rest = __DIR__.'/rest/.htaccess';
$htaccess_vress_quote = empty($action['ress_versioning']) ? '#' : '';

$db_path = __DIR__.'/sys/db/init.sql';
$dbml_path = __DIR__.'/sys/db/multilingual.sql';

include(__DIR__.'/acid/core/acid_db.php');

$css = <<< CSS
<style type="text/css">
<!--

html, body { background-color:#9D9FA0; }


.corpus{
	display:table;
	margin:auto;
	padding:50px;
	background-color:#EFEFEF;
	border:1px solid #CCCCCC;
}

.corpus .block{ }
.corpus	h2 { margin:0px; margin-bottom:5px; padding-bottom:5px; border:0px solid #000000; border-bottom-width:1px;  }


.corpus .btn{
	margin:auto;
	display:block;
	cursor:pointer;
	width:150px;
	padding:10px;
	border:1px solid #FFFFFF;
	background-color:#2A2A2A;
	color:#FFFFFF;
	font-weight:bold;
}

.corpus .btn:hover{ opacity:0.5; filter:alpha(opacity=50); }

-->
</style>
CSS;

if (!is_writable(dirname($dir_path))) {
	echo $css. "\n" .'<div class="corpus">';
	echo 'Write access denied in <b>' . dirname($dir_path) . '</b>. Folder must be writable.';
	echo '</div>';
	exit();
}




if (!file_exists($dir_path)) {

	if (isset($action['acidfarm_do']) && ($action['acidfarm_do']=='install')) {

		$name 			= addslashes($action['site_name']);
		$mail 			= addslashes($action['site_mail']);
		$salt 			= addslashes($action['site_salt']);

		$config_contact 		= addslashes($action['config_contact']);
		$config_phone 			= addslashes($action['config_phone']);
		$config_fax 			= addslashes($action['config_fax']);
		$config_address 		= addslashes($action['config_address']);
		$config_cp 				= addslashes($action['config_cp']);
		$config_city 			= addslashes($action['config_city']);

		$username 		= addslashes($action['user_name']);
		$userpass 		= addslashes($action['user_pass']);
		$usermail 		= addslashes($action['user_mail']);
		$user_salt		= getRandPasswordSalt();

		$scheme 		= addslashes($action['scheme']);
		$domain 		= addslashes($action['domain']);
		$folder 		= addslashes($action['folder']);

		$devmode		= addslashes($action['devmode']);
		$upg_mode		= $devmode=='prod' ? 'prod' : 'dev';
		$lprod_quote	= $devmode=='prod' ? '' : '//';
		$lpp_quote		= $devmode=='preprod' ? '' : '//';
		$lpp_plus_quote  = in_array($devmode,array('preprod','prod')) ? '':'//';
		$lpp_less_quote  = in_array($devmode,array('preprod','dev')) ? '':'//';

		$ldev_quote		= $devmode=='dev' ? '' : '//';
		$cssdyn_mode	= $devmode=='dev' ? 'debug' : 'default';

		$hoster 		= addslashes($action['hoster']);

		$dbtype 		= addslashes($action['db_type']);
		$dbhost 		= addslashes($action['db_host']);
		$dbport 		= addslashes($action['db_port']);
		$dbuser 		= addslashes($action['db_user']);
		$dbpass 		= addslashes($action['db_password']);
		$dbname 		= addslashes($action['db_name']);
		$dbpref 		= addslashes($action['db_pref']);

		$db_init = !empty($action['init_db']);

		$multilingual = !empty($action['multilingual']);

		$server_theme = isset($action['server_theme']) ? $action['server_theme'] : '';
		$theme_quote = $server_theme ? '' : '//';

		$lang_manual = !empty($action['site-lang-manual']);
		$lang_quote = $lang_manual ? '' : '//';

		$lang_available = !empty($action['site-lang-available']) ? $action['site-lang-available'] : array();
		$lang_default = !empty($action['site-lang-default']) ? $action['site-lang-default'] : '';
		$lang_use_nav_0 = (count($lang_available) > 1);

		$lang_use_nav_0_value =  $lang_use_nav_0 ? 'true' : 'false';
		$lang_available_value =  "'".implode("','",$lang_available)."'";

		$version_way_quote = empty($action['ress_versioning']) ? '//' : '';

		$config = <<< EOT
<?php
// Site info
\$acid['site']['name']           = '$name';
\$acid['site']['email']          = '$mail';

\$acid['admin']['name']          = '$username';
\$acid['admin']['email']         = '$usermail';

// Meta tags
//\$acid['title']['left']        = '';
\$acid['title']['right']         = ' - '.\$acid['site']['name'];

// DataBase
\$acid['db']['type']             = '$dbtype';
\$acid['db']['host']             = '$dbhost';
\$acid['db']['port']             = '$dbport';
\$acid['db']['user']             = '$dbuser';
\$acid['db']['pass']             = '$dbpass';
\$acid['db']['base']             = '$dbname';
\$acid['db']['prefix']           = '$dbpref';

// Emails
$lpp_plus_quote\$acid['email']['method']          	= 'smtp';
$lpp_plus_quote\$acid['email']['smtp']['host']		= 'localhost'; // If smtp'
//\$acid['email']['smtp']['user']					= ''; // If smtp
//\$acid['email']['smtp']['pass']					= ''; // If smtp
//\$acid['email']['smtp']['port']					= ''; // If smtp

// Lang
$lang_quote\$acid['lang']['use_server'] 	= true;
$lang_quote\$acid['lang']['use_nav_0'] 		= $lang_use_nav_0_value;
$lang_quote\$acid['lang']['default']        = '$lang_default';
$lang_quote\$acid['lang']['available']      = array($lang_available_value);

// Theme
$theme_quote\$acid['server_theme'] 	= '$server_theme';

// SESSION
\$acid['session']['table']	     = \$acid['db']['prefix'] . 'session';

// URL
\$acid['url']['scheme']          = '$scheme';
\$acid['url']['domain']          = '$domain';
\$acid['url']['folder']          = '$folder';
\$acid['url']['system']          = \$acid['url']['scheme'].\$acid['url']['domain'].\$acid['url']['folder'];
\$acid['url']['system_lang']     = \$acid['url']['system'];
\$acid['url']['folder_lang']     = \$acid['url']['folder'];

// COOKIES
//\$acid['cookie']['use_server'] 	= true;
//\$acid['cookie']['path']          = \$acid['url']['folder']; // Dossier pour lequel le cookie est accessible
//\$acid['cookie']['domain']        = array(\$acid['url']['domain']); // Domaine pour lequel le cookie est accessible
//\$acid['cookie']['dyndomain']		= true;	// Si true, autorise le cookie sur un domaine à la volée

// Salt
\$acid['hash']['salt']           = '$salt';

// Debug
// ALL : '*'
// DEFINED : array('START','ACID','DEBUG','SQL','SESSION','INFO','POSTINFO','URL','USER','DEPRECATED','ROUTER','PERMISSION','HACK','ERROR','PAYPAL','PAYMENT','MAINTENANCE','FILE')
// PROD DEBUG : array('START','SQL','SESSION','INFO','URL','USER','DEPRECATED','ROUTER','PERMISSION','HACK','ERROR','PAYPAL','PAYMENT','MAINTENANCE','FILE')
$lpp_quote\$acid['log']['keys']          	= array('START','SQL','SESSION','INFO','POSTINFO','URL','USER','DEPRECATED','ROUTER','PERMISSION','HACK','ERROR','PAYPAL','PAYMENT','MAINTENANCE','FILE'); //Preprod
$lprod_quote\$acid['log']['keys']          	= array('INFO','POSTINFO','URL','DEPRECATED','HACK','ERROR','PAYMENT','MAINTENANCE','FILE'); //Prod
$lprod_quote\$acid['log']['type']          	= 'daily'; // single / daily
//\$acid['log']['colorize']         = array(); // array('HACK'=>'red','DEBUG'=>'yellow')
$lprod_quote\$acid['debug']		         	= false;
$lprod_quote\$acid['error_report']['debug']	= E_ALL & ~E_STRICT;
$lprod_quote\$acid['error_report']['prod']	= E_ALL & ~E_STRICT;

// Maintenance
\$acid['maintenance']           	= false;
//\$acid['maintenance_desc']		= 'Site en maintenance...';

// Upgrade
\$acid['upgrade']['mode']        = '$upg_mode'; // dev / prod / off

// Plupload
\$acid['plupload']['chunk_size'] = 2;	// En Mo
\$acid['plupload']['max_size'] = 500;	// En Mo
\$acid['plupload']['session_time'] = 4 * 60 * 60;	// Temps d'execution max de transfert en secondes (4H par défaut)
\$acid['plupload']['file_tmp_age'] = 24 * 60 * 60;	// Temps de vie des fichiers temporaires en secondes (24H par défaut)
\$acid['plupload']['show_upload'] = false;	// Affiche le bouton pour upload le fichier
\$acid['plupload']['autosubmit'] = true;	// Soumet (par défaut) le formulaire admin après l'envoie de tous les fichiers
\$acid['plupload']['runtimes'] = array('html5', 'flash');	// Définie les runtimes pour le plugin plupload

// Css
\$acid['css']['dynamic']['active'] = false;	//generate css file referring to php file
\$acid['css']['dynamic']['mode'] = '$cssdyn_mode';	//debug (always), default (if not exists)

//Versioning
//\$acid['versioning']['file'] = 'sys/versioning.txt';
//\$acid['versioning']['val'] = ''; //if value, override versionning file
$version_way_quote\$acid['versioning']['way'] = 'htaccess';
//\$acid['versioning']['tag'] = '-c__VERSION__';

// Sass
\$acid['sass']['enable'] = true;
$ldev_quote\$acid['sass']['mode'] = 'dev';
$lprod_quote\$acid['sass']['mode'] = 'prod';

// Compiler
//\$acid['compiler']['enable'] = true;
//\$acid['compiler']['expiration'] = 60*60*24*15; //15jours
$ldev_quote\$acid['compiler']['mode'] = 'dev';
//\$acid['compiler']['css']['disable'] = true;
//\$acid['compiler']['js']['disable'] = true;
//\$acid['compiler']['css']['compression'] = false;
//\$acid['compiler']['js']['compression'] = false;

//Disallow indexation
$lpp_quote\$acid['donotindex'] = true;

//Disallow deploy for security patches
//\$acid['deploy']['allowed'] = true;

//Sentry url for supervision, need Raven (ex : composer require raven/raven)
//\$acid['sentry']['url'] = '';
//\$acid['sentry']['report_level'] = E_ALL;

EOT;


		$h = fopen($dir_path,'w');
		$ec = fwrite($h, $config);
		fclose($h);

		$htaccessOVH = ($hoster == 'ovh') ?
		'# OVH' . "\n" .
		'SetEnv PHP_VER 5_TEST' . "\n" .
		'SetEnv REGISTER_GLOBALS 0' . "\n" .
		'SetEnv MAGIC_QUOTES 0' : '';

		$htaccess_vress = 	$htaccess_vress_quote.'RewriteRule (.+)-([0-9]+).js$ $1.js [L,QSA]'. "\n" .
							$htaccess_vress_quote.'RewriteRule (.+)-([0-9]+).css$ $1.css [L,QSA]' . "\n" ;

		$htaccessMaintenance = <<< HTACC
#RewriteCond %{REMOTE_ADDR} !^123\.456\.789\.000
RewriteCond %{DOCUMENT_ROOT}{$folder}maintenance.html -f
RewriteCond %{DOCUMENT_ROOT}{$folder}maintenance.enable -f
RewriteCond %{SCRIPT_FILENAME} !{$folder}maintenance.html
RewriteRule ^.*$ {$folder}maintenance.html [R=503,L]
ErrorDocument 503 {$folder}maintenance.html
HTACC;

		$htaccess = <<< HTACC
# Charset
AddDefaultCharset UTF-8

$htaccessOVH

# URL Rewriting
RewriteEngine on

$htaccessMaintenance

$htaccess_vress
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ {$folder}index.php?acid_nav=$1 [L,QSA]
HTACC;

		$htaccessrest = <<< HTACC
# Charset
AddDefaultCharset UTF-8

$htaccessOVH

# URL Rewriting
RewriteEngine on
$htaccessMaintenance
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ {$folder}rest/index.php?acid_nav=$1 [L,QSA]
HTACC;



		$h = fopen($htaccess_path,'w');
		$eh = fwrite($h, $htaccess);
		fclose($h);

        if (file_exists(__DIR__.'/rest')) {
            $h = fopen($htaccess_path_rest, 'w');
            $eh = fwrite($h, $htaccessrest);
            fclose($h);
        }

		if ($db_init) {

			$requete= file_get_contents($db_path);
			$requete = str_replace('`acid_','`'.$dbpref, $requete);

			$requete .= "\n" ."UPDATE `".$dbpref."user` SET `username`='".$username."', `password`=MD5('".$salt.$userpass.$user_salt."'),`email`='".$usermail."', `user_salt`='".$user_salt."' WHERE `id_user`='1';";

			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'email', '".$mail."');" ;
			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'fax', '".$config_fax."');" ;
			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'phone', '".$config_phone."');" ;
			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'address', '".$config_address."');" ;
			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'city', '".$config_city."');" ;
			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'cp', '".$config_cp."');" ;
			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'contact', '".$config_contact."');" ;
			$requete .= "\n" ."INSERT INTO `".$dbpref."config` (`id_config`, `name`, `value`) VALUES (NULL, 'website', '".htmlspecialchars($scheme.$domain.$folder)."');" ;

			$ressource = new PDO($dbtype.':host='.$dbhost.';port='.$dbport.';dbname='.$dbname, $dbuser, $dbpass);
			$ressource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$ressource->exec("SET CHARACTER SET UTF8");
			$ressource->exec($requete);

			if ($multilingual) {
				$ressource->exec('COMMIT;');
				$requeteml= file_get_contents($dbml_path);
				$requeteml = str_replace('`acid_','`'.$dbpref, $requeteml);
				$ressource->exec($requeteml);
			}


		}

		//ADDING IGNORED PATHS
		$path_to_add = array('/sys/stats','/files','/files/users','/files/tmp','/files/home','/files/actu','/files/page','/files/photo','/files/seo','/upload','/logs');
		foreach ($path_to_add as $pta) {
			$pa_path = __DIR__.$pta;
			if (!file_exists($pa_path)) {
				mkdir($pa_path);
			}
		}

		//ADDING IGNORED FILES
		$file_to_add = array('/sys/stats/stats.tpl','/sys/stats/contact.tpl','/sys/update/cur_version.txt','/sys/update/.system/cur_version.txt','/sys/update/.content/cur_version.txt');
		foreach ($file_to_add as $fta) {
			$fa_path = __DIR__.$fta;
			if (!file_exists($fa_path)) {
				file_put_contents($fa_path,'');
			}
		}

		//ADDING DENY FROM ALL
		$path_to_secure = array('/logs');
		foreach ($path_to_secure as $pts) {
			$fs_path = __DIR__.$pts.'/.htaccess';
			if (!file_exists($fs_path)) {
				$h = fopen($fs_path,'w');
				$ec = fwrite($h, 'deny from all');
				fclose($h);
			}
		}


		echo $css. "\n" .'<div class="corpus">';

		if ((!$ec) || (!$eh)) {
			echo 'An error happened.';
		}else{
			echo 'Install complete<br /><br />'.'<a href="'.$scheme.$domain.$folder.'">Go to your website.</a>';
		}


		echo '</div>';

	}elseif (isset($action['acidfarm_do']) && ($action['acidfarm_do']=='check_database')) {

		$dbtype 		= addslashes($action['db_type']);
		$dbhost 		= addslashes($action['db_host']);
		$dbport 		= addslashes($action['db_port']);
		$dbuser 		= addslashes($action['db_user']);
		$dbpass 		= addslashes($action['db_password']);
		$dbname 		= addslashes($action['database']);
		$dbpref 		= addslashes($action['db_pref']);

		$result = false;

		try {
			$result = checkDataBase($dbtype,$dbhost,$dbport,$dbname,$dbuser,$dbpass);
		}catch(Exception $e){ $result =false; }

		echo json_encode(array('success'=>$result));


		exit();
	}else{
		$folder = explode('/',$_SERVER['PHP_SELF']);
		if (count($folder)) {
			unset($folder[count($folder)-1]);
		}
		$folder_sample = implode('/',$folder);
		$salt_sample = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

		$scheme = empty($_SERVER['HTTPS']) ? 'http' : 'https';

		$prod_selected = strpos($_SERVER['HTTP_HOST'],'www.')===0 ? ' selected="selected" ' : '';

		$theme_options = '';
		foreach (getThemes() as $k=>$t) {
			$theme_options .= '<option value="'.$k.'" >' . $t .'</option>' . "\n" ;
		}
		$theme_list = '<select name="server_theme">'.$theme_options.'</select>';

		$form = <<< FORM
<form class="corpus" action="#" method="post">
	<div><pre><h1>Install your AcidFarm</h1>
	<input type="hidden" name="acidfarm_do" value="install" />
	<div class="block">
	<h2>Site Profile</h2>
	Site name : <input type="text" name="site_name" value="" /><br />
	Site email : <input type="text" name="site_mail" value="" /><br />
	Site Salt : <input type="text" name="site_salt" value="{$salt_sample}" /> personal keycode used for securing passwords or other elements of your website<br />
	</div>
	<div class="block">
	<h2>Site Url</h2>
	Site scheme : <input type="text" name="scheme" value="{$scheme}://" /><br />
	Site domain : <input type="text" name="domain" value="{$_SERVER['HTTP_HOST']}" /> (ex : acid-solutions.fr)<br />
	Site folder : <input type="text" name="folder" value="{$folder_sample}/" /> alway finish with / (ex : /acidfarm/ )<br />
	</div>
	<div class="block">
	<h2>Site Profil</h2>
	Site contact : <input type="text" name="config_contact" value="" /> ( for example the webmaster )<br />
	Site address : <input type="text" name="config_address" value="" /><br />
	Site cp : <input type="text" name="config_cp" value="" /><br />
	Site city : <input type="text" name="config_city" value="" /><br />
	Site phone : <input type="text" name="config_phone" value="" /><br />
	Site fax : <input type="text" name="config_fax" value="" /><br />
	</div>
	<div class="block">
	<h2>User Profile</h2>
	User name : <input type="text" name="user_name" value="admin" /><br />
	User password : <input type="password" name="user_pass" value="admin" /><br />
	User mail : <input type="text" name="user_mail" value="" /><br />
	</div>
	<div class="block">
	<h2>Configuration</h2>
	Server Mode : <select name="devmode"><option value="dev">Dev</option><option value="preprod">Preprod</option><option value="prod"{$prod_selected}>Prod</option></select>
	Versioning of resources (htaccess) : <input type="checkbox" value="1" name="ress_versioning" checked="checked" />
	</div>
	<div class="block">
	<h2>Housing</h2>
	<select name="hoster">
		<option value="0">Hoster</option>
		<option value="ovh">OVH</option>
		<option value="1and1">1and1</option>
		<option value="other">other</option>
	</select> (ex :  some server host need a particular .htaccess file)
	</div>
	<div class="block">
	<h2>Lang</h2>
	<label><input type="checkbox" name="site-lang-manual" value="1" /> Custom mode -</label> <b>Do not check for default setting</b><br />
		<label>Prepare database for multilingual <input type="checkbox" name="multilingual" value="1" /> </label><br />
		Available : <label> fr <input type="checkbox" name="site-lang-available[]" value="fr" /></label> <label> en <input type="checkbox" name="site-lang-available[]" value="en" /></label> <label> de <input type="checkbox" name="site-lang-available[]" value="de" /></label> <label> es <input type="checkbox" name="site-lang-available[]" value="es" /></label> <label> it <input type="checkbox" name="site-lang-available[]" value="it" /></label><br />
		Default : <select name="site-lang-default" ><option value=""></option> <option value="fr">fr</option><option value="en">en</option><option value="de">de</option><option value="es">es</option><option value="it">it</option></select>
	</div>
	<div class="block">
	<h2>Theme</h2>
	$theme_list
	</div>
	<div class="block">
	<h2>Database</h2>
	<label>Initialize database   <input type="checkbox" name="init_db"  value="1" checked="checked" /></label><br />
	type : <input type="text" id="db_type"  name="db_type" value="mysql" /> (ex : mysql, pgsql, sqlite, odbc, oci (oracle), dblib (microsoft))<br />
	host : <input type="text" id="db_host" name="db_host" value="localhost" /><br />
	port : <input type="text" id="db_port" name="db_port" value="3306" /><br />
	username : <input type="text" id="db_user" name="db_user" value="" /><br />
	password : <input type="password" id="db_password" name="db_password" value="" /><br />
	database : <input type="text" id="db_name" name="db_name" value="" /><br />
	prefix : <input type="text" id="db_pref" name="db_pref" value="acid_" /><br />
	<a href="#" onclick="InstallTools.checkDB(); return false;" >Check Database</a> : <span id="check_data_base_result"></span>
	</div>
	<input class="btn" type="submit" value="Install">

	</pre></div>
</form>


<script type="text/javascript">
<!--

var InstallTools = {

	checkDB : function() {
		var http = new XMLHttpRequest();
		var url = "install.php";
		var params = "acidfarm_do=check_database"+
					 "&db_type="+document.getElementById("db_type").value+
					 "&db_host="+document.getElementById("db_host").value+
					 "&db_port="+document.getElementById("db_port").value+
					 "&db_user="+document.getElementById("db_user").value+
					 "&db_password="+document.getElementById("db_password").value+
					 "&db_pref="+document.getElementById("db_pref").value+
					 "&database="+document.getElementById("db_name").value;

		http.open("POST", url, true);

		//Send the proper header information along with the request
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.setRequestHeader("Content-length", params.length);
		http.setRequestHeader("Connection", "close");

		http.onreadystatechange = function() {//Call a function when the state changes.
			if(http.readyState == 4) { //&& http.status == 200

				if (http.responseText) {
					var res = JSON.parse(http.responseText);
					if (res.success==true) {
						document.getElementById("check_data_base_result").innerHTML='Success';
					}else{
						document.getElementById("check_data_base_result").innerHTML='Bad params for database';
					}
				}else{
					document.getElementById("check_data_base_result").innerHTML='Failed';
				}

			}
		}
		http.send(params);

		document.getElementById("check_data_base_result").innerHTML='Treatment';

	}

}

-->
</script>

FORM;

		echo $css. "\n" . $form;
	}

}else{

	echo $css. "\n" .'<div class="corpus">';
	echo 'Installation has already been done.';
	echo '</div>';

}