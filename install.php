<?php 
/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Core
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
 
$action = $_POST;

$dir_path = __DIR__.'/sys/server.php';
$htaccess_path = __DIR__.'/.htaccess';
$db_path = __DIR__.'/sys/db/init.sql';


include(__DIR__.'/acid/core/acid_db.php');


if (!is_writable(dirname($dir_path))) {
	echo 'Write access denied in <b>' . dirname($dir_path) . '</b>. Folder must be writable.';
	exit();
}




if (!file_exists($dir_path)) {
	
	if (isset($action['acidfarm_do'])) {
		
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
		
		$hoster 		= addslashes($action['hoster']);
		
		$dbtype 		= addslashes($action['db_type']);
		$dbhost 		= addslashes($action['db_host']);
		$dbport 		= addslashes($action['db_port']);
		$dbuser 		= addslashes($action['db_user']);
		$dbpass 		= addslashes($action['db_password']);
		$dbname 		= addslashes($action['db_name']);
		$dbpref 		= addslashes($action['db_pref']);
		
		$db_init = !empty($action['init_db']);

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
//\$acid['email']['method']          	= 'smtp';
//\$acid['email']['smtp']['host']		= 'localhost'; // If smtp'

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
//\$acid['cookie']['path']          = \$acid['url']['folder']; // Dossier pour lequel le cookie est accessible 
//\$acid['cookie']['domain']          = \$acid['url']['domain']; // Domaine pour lequel le cookie est accessible 

// Salt
\$acid['hash']['salt']           = '$salt';

// Debug
// ALL : '*'
// DEFINED : array('START','ACID','DEBUG','SQL','SESSION','INFO','URL','USER','DEPRECATED','ROUTER','PERMISSION','HACK','ERROR','PAYPAL','MAINTENANCE','FILE')
// PROD DEBUG : array('START','SQL','SESSION','INFO','URL','USER','DEPRECATED','ROUTER','PERMISSION','HACK','ERROR','PAYPAL','MAINTENANCE','FILE')
//\$acid['log']['keys']          	= array('URL','DEPRECATED','HACK','ERROR'); 
//\$acid['log']['type']          	= 'daily'; // single / daily 
//\$acid['log']['colorize']         = array();
//\$acid['debug']		         	= false;
//\$acid['error_report']['debug']	= E_ALL & ~E_STRICT;
//\$acid['error_report']['prod']	= 0;
				
// Maintenance
\$acid['maintenance']           	= false;
//\$acid['maintenance_desc']		= 'Site en maintenance...';

// Upgrade
\$acid['upgrade']['mode']        = 'dev'; // dev / prod / off

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
\$acid['css']['dynamic']['mode'] = 'default';	//debug (always), default (if not exists)

EOT;

		
		$h = fopen($dir_path,'w');
		$ec = fwrite($h, $config);
		fclose($h);
		
		$htaccessOVH = ($hoster == 'ovh') ?
		'# OVH' . "\n" .
		'SetEnv PHP_VER 5_TEST' . "\n" .
		'SetEnv REGISTER_GLOBALS 0' . "\n" .
		'SetEnv MAGIC_QUOTES 0' : '';
		
		$htaccess = <<< HTACC
# Charset
AddDefaultCharset UTF-8

$htaccessOVH

# URL Rewriting
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ {$folder}index.php?acid_nav=$1 [L,QSA]
HTACC;
		
		$h = fopen($htaccess_path,'w');
		$eh = fwrite($h, $htaccess);
		fclose($h);
			
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
			
		}
		
		//ADDING IGNORED FILES
		$file_to_add = array('/sys/stats.tpl','/sys/update/cur_version.txt','/sys/upgrade/cur_version.txt');
		foreach ($file_to_add as $fta) {
			$fa_path = __DIR__.$fta;
			if (!file_exists($fa_path)) {
				file_put_contents($fa_path,'');
			}
		}
		
		//ADDING IGNORED PATHS
		$path_to_add = array('/files','/files/users','/files/tmp','/files/home','/files/photo','/upload','/logs');
		foreach ($path_to_add as $pta) {
			$pa_path = __DIR__.$pta;
			if (!file_exists($pa_path)) {
				mkdir($pa_path);
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
		
		
		if ((!$ec) || (!$eh)) {
			echo 'An error happened.';
		}else{				
			echo 'Install complete<br /><br />'.'<a href="'.$scheme.$domain.$folder.'">Go to your website.</a>';
		}
		
		
	}else{
		$folder = explode('/',$_SERVER['PHP_SELF']);
		if (count($folder)) {
			unset($folder[count($folder)-1]);
		}
		$folder_sample = implode('/',$folder);
		$salt_sample = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
		
		$scheme = empty($_SERVER['HTTPS']) ? 'http' : 'https';
		
		$form = <<< FORM
<form action="#" method="post">
	<div><pre>
	<input type="hidden" name="acidfarm_do" value="install" />
	<h2>Site Profile</h2>
	Site name : <input type="text" name="site_name" value="" /><br /> 
	Site email : <input type="text" name="site_mail" value="" /><br />
	Site Salt : <input type="text" name="site_salt" value="{$salt_sample}" /> personal keycode used for securing passwords or other elements of your website<br />
	<h2>Site Url</h2>
	Site scheme : <input type="text" name="scheme" value="{$scheme}://" /><br /> 
	Site domain : <input type="text" name="domain" value="{$_SERVER['HTTP_HOST']}" /> (ex : acid-solutions.fr)<br />
	Site folder : <input type="text" name="folder" value="{$folder_sample}/" /> alway finish with / (ex : /acidfarm/ )<br />
	<h2>Site Profil</h2>
	Site contact : <input type="text" name="config_contact" value="" /> ( for example the webmaster )<br /> 
	Site address : <input type="text" name="config_address" value="" /><br />
	Site cp : <input type="text" name="config_cp" value="" /><br />
	Site city : <input type="text" name="config_city" value="" /><br />
	Site phone : <input type="text" name="config_phone" value="" /><br />
	Site fax : <input type="text" name="config_fax" value="" /><br />
	<h2>User Profile</h2>
	User name : <input type="text" name="user_name" value="admin" /><br /> 
	User password : <input type="password" name="user_pass" value="admin" /><br />
	User mail : <input type="text" name="user_mail" value="" /><br /> 
	<h2>Housing</h2>
	<select name="hoster">
		<option value="0">Hoster</option>
		<option value="ovh">OVH</option>
		<option value="1and1">1and1</option>
		<option value="other">other</option>
	</select> (ex :  some server host need a particular .htaccess file)
	<h2>Database</h2>
	Initialize database   <input type="checkbox" name="init_db"  value="1" checked="checked" /><br />
	type : <input type="text" name="db_type" value="mysql" /> (ex : mysql, pgsql, sqlite, odbc, oci (oracle), dblib (microsoft))<br /> 
	host : <input type="text" name="db_host" value="localhost" /><br /> 
	port : <input type="text" name="db_port" value="3306" /><br /> 
	username : <input type="text" name="db_user" value="" /><br /> 
	password : <input type="password" name="db_password" value="" /><br />
	database : <input type="text" name="db_name" value="" /><br />
	prefix : <input type="text" name="db_pref" value="acid_" /><br />
	<input type="submit" value="Install">
	</pre></div>
</form>
FORM;
		
		echo $form;
	}

}else{
	
	echo 'Installation has already been done.';

}