<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Config
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


// Maintenance
$acid['maintenance']            = false;
$acid['maintenance_desc']		= 'Site en maintenance...';

// Core
$acid['core']['module']['path'] = SITE_PATH.'sys/modules/core.php';
$acid['core']['mail']['path'] 	= SITE_PATH.'sys/tools/mail.php';

// Site info
$acid['site']['name']           = 'ACID Farm';
$acid['site']['email']          = 'mail@domain.tld';
$acid['admin']['name']          = 'Dev';
$acid['admin']['email']         = 'mail@domain.tld';
$acid['admin']['contact']       = '';
$acid['admin']['website']       = '';

// Database
$acid['db']['type']             = 'mysql';
$acid['db']['host']             = 'localhost';
$acid['db']['port']             = '3306';
$acid['db']['user']             = 'acidfarm';
$acid['db']['pass']             = 'acidfarm';
$acid['db']['base']             = 'acidfarm';
$acid['db']['prefix']           = 'af_';
$acid['db']['sql_mode']         = "''";
$acid['db']['charset']         = "UTF8";

// Meta tags
$acid['title']['left']          = '';
$acid['title']['right']         = '';
$acid['meta']['publisher']      = 'ACID-Solutions';
$acid['meta']['robots']         = '';
$acid['meta']['revisit-after']  = '';
$acid['meta']['author']         = '';
$acid['meta']['description']    = '';
$acid['meta']['keywords']       = array();
$acid['meta']['generator']      = 'AcidFarm';


// Reserved keys/paths
$acid['keys']['page']           = '';
$acid['keys']['admin']          = 'siteadmin';
$acid['keys']['theme']			= 'theme';
$acid['keys']['static']			= 'static';

//Templates acid
$acid['tpl']['path']          	  = 'tpl/';
$acid['tpl']['html']['version']	  = 5;

// Theme
$acid['def_theme']	            = 'default';
$acid['theme']					= 'default';


// URLs
$acid['url']['scheme']          = 'http://';						// Protocol (http or https)
$acid['url']['domain']          = 'dev.acid-solutions.fr';			// subdmain and domain
$acid['url']['folder']          = '/base_acidfarm/';						// Always end with a "/" (slash)
$acid['url']['system']          = $acid['url']['scheme'].$acid['url']['domain'].$acid['url']['folder'];
$acid['url']['img']             = $acid['url']['folder'] . $acid['keys']['theme'] . '/'.$acid['theme'].'/img/';

$acid['url']['folder_lang'] 	= $acid['url']['folder'];
$acid['url']['system_lang'] 	= $acid['url']['system'];

$acid['url']['params']['allowed'] = array('gclid','_ga');

//Hook acid
$acid['hook']['path']          	  = 'hook/';

// User
$acid['lvl']['visitor'] = 0;
$acid['lvl']['robot'] = 1;
$acid['lvl']['invalid'] = 2;
$acid['lvl']['registered'] = 3;
$acid['lvl']['member'] = 4;
$acid['lvl']['vip'] = 5;
$acid['lvl']['modo'] = 7;
$acid['lvl']['admin'] = 9;
$acid['lvl']['dev'] = 10;

$acid['lvl_def'] = $acid['lvl']['admin'];


$acid['user']['levels'] = array(
                                    $acid['lvl']['visitor']   => 'Visiteur',
                                    $acid['lvl']['robot']     => 'Robot',
                                    $acid['lvl']['invalid']   => 'Invalide',
                                    $acid['lvl']['registered'] => 'Enregistré',
                                    $acid['lvl']['member']    => 'Membre',
                                    $acid['lvl']['vip']       => 'VIP',
                                    $acid['lvl']['modo']      => 'Modérateur',
                                    $acid['lvl']['admin']     => 'Administrateur',
                                    $acid['lvl']['dev']       => 'Développeur'
                                );

// Permissions
$acid['permission']	 			= array();
$acid['permission_groups']	 	= array('id_user','id_group','level');
$acid['permission_active']		= true;

// Lang
$acid['lang']['default']        = 'fr';
$acid['lang']['available']      = array('fr');
$acid['lang']['current']      	= $acid['lang']['default'];


$acid['lang']['use_nav_0'] 		= false;

// User account
$acid['user']['page']           = $acid['url']['folder_lang'] . 'account';
$acid['user']['login_max_size'] = 255;

// Hash
$acid['hash']['type']           = 'md5';
$acid['hash']['salt']           = '00000';


// Cookies
$acid['cookie']['expire']       = 1296000;                   // Délais d'expiration en secondes (1296000 = 15jours)
$acid['cookie']['path']         = $acid['url']['folder'];    // Dossier pour lequel le cookie est accessible
$acid['cookie']['domain']       = $acid['url']['domain'];    // Domaine pour lequel le cookie est accessible
$acid['cookie']['secure']       = false;                     // Client en utilisation HTTPS (à coder coté serveur)
$acid['cookie']['httponly']     = true;                      // Protection le vol de session par script client
$acid['cookie']['dyndomain']	= true;						 // Si true, autorise le cookie sur un domaine à la volée

// Sessions
$acid['session']['enable']      = isset($acid['session']['enable']) ?
								  $acid['session']['enable'] : true;  // Enable/Disable session
$acid['session']['name']        = 'session';                           // Cookie name
$acid['session']['table']       = $acid['db']['prefix'] . 'session';   // Cookie name
$acid['session']['expire']      = 14440;                               // Expire date in seconds
$acid['session']['secure']      = false;                               // HTTPS only
$acid['session']['httponly']    = true;                                // Only HTTP, no javascript
$acid['session']['check_ip']    = false;                               // Check IP
$acid['session']['check_ua']    = true;                                // Check User Agent


// Emails
$acid['email']['method']        = 'mail';         // Could be "smtp" or "mail" (ie. php mail() function)
$acid['email']['smtp']['host']  = 'localhost';    // If smtp
$acid['email']['smtp']['user']  = '';             // If smtp
$acid['email']['smtp']['pass']  = '';             // If smtp
$acid['email']['smtp']['debug']  = false;         // If smtp
$acid['email']['smtp']['secure']  = '';           // If smtp, could be "tls" or "ssl"


// File system
$acid['path']['uploads']        = 'upload/';
$acid['path']['files']          = 'files/';
$acid['path']['tmp']         	= 'files/tmp/';

// Browser
$acid['browser']['acl']			= $acid['lvl']['admin'];

// Default out scheme
$acid['out']					= 'default';

// Information pages
$acid['info']['base']           = $acid['url']['folder'] . (empty($acid['keys']['page']) ? '' : $acid['keys']['page'] . '/' );
$acid['info']['baninfo']        = 'baninfo';
$acid['info']['sitemap']        = 'sitemap';

//Config
$acid['post']['ajax']['key']	= 'ajax';

// Logs
$acid['log']['enable']          = true;
$acid['log']['level']           = 1;
$acid['log']['path']            = SITE_PATH . 'logs/';
$acid['log']['filename']        = 'acidfarm';
$acid['log']['filename_date']   = 'Y-m-d';
$acid['log']['date_format']     = 'Y-m-d H:i:s';
$acid['log']['keys']			= '*'; // '*' OR array('ACID','DEBUG','SQL','SESSION','INFO','URL','USER','DEPRECATED','HACK','ERROR')
$acid['log']['type'] 			= 'single'; // single / daily
$acid['log']['custom']			= '';
$acid['log']['colorize']		= array();


// Updates and Upgrade
$acid['upgrade']['types']					= array('update','upgrade','content');
$acid['upgrade']['path']['update'] 			= SITE_PATH.'sys/update/';
$acid['upgrade']['path']['upgrade']			= SITE_PATH.'sys/update/.system/';
$acid['upgrade']['path']['content']			= SITE_PATH.'sys/update/.content/';
$acid['upgrade']['db']['sample_prefix']		= array('acid_','af_');

$acid['upgrade']['mode']			= 'off'; // dev / prod / off
$acid['upgrade']['excluded']			= array('files'=>array(),'folders'=>array('.svn'),'dotfiles'=>false);

//PHP error logs
$acid['debug']                  = true;
$acid['phplog']['enable']       = true;
$acid['phplog']['filename']     = 'php_errors';
$acid['phplog']['path']		    = SITE_PATH . 'logs/';
$acid['phplog']['type']		    = 'single'; // single / daily
$acid['error_report']['debug']	= E_ALL & ~E_STRICT;
$acid['error_report']['prod']	= 0;

// Vars
$acid['vars']['file']                  = array('AcidVarFile');
$acid['vars']['image']                  = array('AcidVarImage', 'AcidVarWatermark');
$acid['vars']['upload']                  =  array_merge($acid['vars']['file'], $acid['vars']['image']);

// Files

// --mode
$acid['files']['file_mode']     = 0644;

// --assoc
$acid['files']['ext_assoc']['img']		 	= 0; // 0 Images avec miniatures
$acid['files']['ext_assoc']['img_print'] 	= 1; // 1 Images
$acid['files']['ext_assoc']['sound']		= 2; // 2 Son
$acid['files']['ext_assoc']['movie'] 		= 3; // 3 Vidéo
$acid['files']['ext_assoc']['pdf']		 	= 4; // 4 PDF
$acid['files']['ext_assoc']['text'] 		= 5; // 5 Documents texte
$acid['files']['ext_assoc']['calc']		 	= 6; // 6 Feuilles de caculs
$acid['files']['ext_assoc']['archive']		= 7; // 7 Archives

// --extension
$acid['files']['ext'] = array(

							$acid['files']['ext_assoc']['img']		 	=> array('jpg','jpeg','png','gif'),
							$acid['files']['ext_assoc']['img_print'] 	=> array('bmp','psd','eps','tiff','ai'),
							$acid['files']['ext_assoc']['sound']		=> array('mp3','wav','mpc','ogg','wma'),
							$acid['files']['ext_assoc']['movie'] 		=> array('avi','mpg','mpeg','wmv','flv','mp4'),
							$acid['files']['ext_assoc']['pdf']		 	=> array('pdf'),
							$acid['files']['ext_assoc']['text'] 		=> array('txt','doc','odt','rtf','docx'),
							$acid['files']['ext_assoc']['calc']		 	=> array('xls','ods','xlsx'),
							$acid['files']['ext_assoc']['archive']		=> array('zip','rar','ace','tar.gz','bzip','gz','bzip2','tgz')

						);

// --icons
$acid['files']['icons'] = array(

		$acid['files']['ext_assoc']['img']		 	=> 'image.png',
		$acid['files']['ext_assoc']['img_print'] 	=> 'image.png',
		$acid['files']['ext_assoc']['sound']		=> 'musique.png',
		$acid['files']['ext_assoc']['movie'] 		=> 'video.png',
		$acid['files']['ext_assoc']['pdf']		 	=> 'pdf.png',
		$acid['files']['ext_assoc']['text'] 		=> 'txt.png',
		$acid['files']['ext_assoc']['calc']		 	=> 'calc.png',
		$acid['files']['ext_assoc']['archive']		=> 'archive.png'

);

// --files shortcut
$acid['ext']['files'] =  array_merge(
							$acid['files']['ext'][$acid['files']['ext_assoc']['img']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['img_print']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['sound']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['movie']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['pdf']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['text']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['calc']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['archive']]
						);

// --image shortcut
$acid['ext']['images'] 	= array_merge(
							$acid['files']['ext'][$acid['files']['ext_assoc']['img']],
							$acid['files']['ext'][$acid['files']['ext_assoc']['img_print']]
						);

// --acidvarimage shortcut
$acid['ext']['varimage'] = $acid['files']['ext'][$acid['files']['ext_assoc']['img']];


// Pagination
$acid['pagination']['max_nav_pages'] = 5;


// DB Counter/Timer
$acid['timer']['db']['global'] = array();
$acid['timer']['db']['current']['start'] = 0;
$acid['timer']['db']['current']['stop'] = 0;
$acid['timer']['db']['opened'] = 0;
$acid['counter']['db'] = 0;

// DB CONNEXIONS Counter/Timer
$acid['timer']['db-connect']['global'] = array();
$acid['timer']['db-connect']['current']['start'] = 0;
$acid['timer']['db-connect']['current']['stop'] = 0;
$acid['timer']['db-connect']['opened'] = 0;
$acid['counter']['db-connect'] = 0;

// TPL Counter/Timer
$acid['timer']['tpl']['global'] = array();
$acid['timer']['tpl']['current']['start'] = 0;
$acid['timer']['tpl']['current']['stop'] = 0;
$acid['timer']['tpl']['opened'] = 0;
$acid['counter']['tpl'] = 0;

// Plupload
$acid['plupload']['folder'] = 'plupload/';	// Dossier dans /js/
$acid['plupload']['chunk_size'] = 2;	// En Mo
$acid['plupload']['max_size'] = 500;	// En Mo
$acid['plupload']['session_time'] = 4 * 60 * 60;	// Temps d'execution max de transfert en secondes (4H par défaut)
$acid['plupload']['file_tmp_age'] = 24 * 60 * 60;	// Temps de vie des fichiers temporaires en secondes (24H par défaut)
$acid['plupload']['show_upload'] = false;	// Affiche le bouton pour upload le fichier
$acid['plupload']['autosubmit'] = true;	// Soumet (par défaut) le formulaire admin après l'envoie de tous les fichiers
$acid['plupload']['runtimes'] = array('html5', 'flash');	// Définie les runtimes pour le plugin plupload
$acid['plupload']['restriction'] = 'logged';	// Définit les restrictions sur l'uplaod (securité)

// Css
$acid['css']['dynamic']['active'] = false;		//generate css file referring to php file
$acid['css']['dynamic']['files'] = array(); 	//path to php file
$acid['css']['dynamic']['mode'] = 'default';	//debug (always), default (if not exists)

//Versioning
$acid['versioning']['file'] = 'sys/versioning.txt';
$acid['versioning']['val'] = ''; //if value, override versionning file

// Sass
$acid['sass']['enable'] = false;
$acid['sass']['path']['compiled'] = '';
$acid['sass']['mode'] = 'dev';

?>