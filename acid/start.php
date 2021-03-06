<?php

/**
 * AcidFarm - Yet Another Framework
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

$server_required_keys = [
    'SERVER_PROTOCOL' => 'NO_SERVER_PROTOCOL',
    'REQUEST_METHOD'  => 'NO_SERVER_METHOD',
    'REQUEST_URI'     => 'NO_REQUEST_URI',
    'REMOTE_ADDR'     => 'NO_REMOTE_ADDR',
    'SERVER_NAME'     => 'NO_SERVER_NAME',
    'SERVER_PROTOCOL' => 'NO_SERVER_PROTOCOL',
    'HTTP_USER_AGENT' => 'NO_HTTP_USER_AGENT'
];

foreach ($server_required_keys as $key => $def) {
    if (!isset($_SERVER[$key])) {
        $_SERVER[$key] = $def;
    }
}

if (!defined('SITE_PATH')) {
    trigger_error('You must defined SITE_PATH constant before using AcidFarm', E_USER_ERROR);
}

if (!file_exists($acid['log']['path'])) {
    echo 'Unable to find log path ' . $acid['log']['path'];
    exit();
}

//define('ACID_PATH'	,pathinfo(__FILE__,PATHINFO_DIRNAME ). '/');

/*****************************************************************************
 *           Tools config
 *****************************************************************************/

$acid['includes']['AcidBrowser'] = $acid['folder'] . 'tools/browser.php';
$acid['includes']['AcidCookie'] = $acid['folder'] . 'tools/cookie.php';
$acid['includes']['AcidForm'] = $acid['folder'] . 'tools/form.php';
$acid['includes']['AcidFs'] = $acid['folder'] . 'tools/fs.php';
$acid['includes']['AcidMail'] = $acid['folder'] . 'tools/mail.php';
$acid['includes']['AcidPagination'] = $acid['folder'] . 'tools/pagination.php';
$acid['includes']['AcidRss'] = $acid['folder'] . 'tools/rss.php';
$acid['includes']['AcidSitemap'] = $acid['folder'] . 'tools/sitemap.php';
$acid['includes']['AcidTable'] = $acid['folder'] . 'tools/table.php';
$acid['includes']['AcidTemplate'] = $acid['folder'] . 'tools/template.php';
$acid['includes']['AcidTime'] = $acid['folder'] . 'tools/time.php';
$acid['includes']['AcidTimer'] = $acid['folder'] . 'tools/timer.php';
$acid['includes']['AcidUrl'] = $acid['folder'] . 'tools/url.php';
$acid['includes']['AcidCSV'] = $acid['folder'] . 'tools/csv.php';
$acid['includes']['AcidGMap'] = $acid['folder'] . 'tools/gmap.php';
$acid['includes']['AcidPaypal'] = $acid['folder'] . 'tools/paypal.php';
$acid['includes']['AcidFacebook'] = $acid['folder'] . '/tools/facebook.php';
$acid['includes']['AcidLinkedIn'] = $acid['folder'] . '/tools/linkedin.php';
$acid['includes']['AcidTwitter'] = $acid['folder'] . '/tools/twitter.php';
$acid['includes']['AcidZip'] = $acid['folder'] . '/tools/zip.php';
$acid['includes']['AcidBash'] = $acid['folder'] . '/tools/bash.php';
$acid['includes']['AcidExport'] = $acid['folder'] . '/tools/export.php';
$acid['includes']['AcidMediatheque'] = $acid['folder'] . '/tools/mediatheque.php';
$acid['includes']['AcidMinifier'] = $acid['folder'] . '/tools/minifier.php';

/**
 * Autoloader
 * $acid['includes'][$class_name] doit avoir pour valeur le chemin vers le fichier de définition de la classe PHP
 * depuis SITE_PATH
 *
 * @param string $class_name nom de la classe
 *
 * @return boolean
 */
function AcidAutoLoader($class_name)
{
    $path = null;
    
    if (strpos($class_name, 'AcidVar') === 0) {
        $acid_path = ACID_PATH . 'core/vars/' . $class_name . '.php';
        $site_path = SITE_PATH . 'sys/vars/' . $class_name . '.php';
        
        if (file_exists($site_path)) {
            require_once $site_path;
            
            return true;
        }
        
        if (file_exists($acid_path)) {
            require_once $acid_path;
            
            return true;
        }
    }
    
    if (isset($GLOBALS['acid']['includes'][$class_name])) {
        $file_path = $GLOBALS['acid']['includes'][$class_name];
        Acid::log('acid', 'Autoload::' . $class_name . ' - ' . $file_path);
        
        include(SITE_PATH . $file_path);
    } elseif (count(spl_autoload_functions()) == 1) {
        trigger_error($class_name . ' path must be defined in $acid[\'includes\'][\'' . $class_name . '\']',
            E_USER_ERROR);
    }
    
    return false;
}

$GLOBALS['acid'] = $acid;
$GLOBALS['sess'] = !isset($GLOBALS['sess']) ? [] : $GLOBALS['sess'];

spl_autoload_register('AcidAutoLoader');

// Externals
$acid['externals']['phpmailer']['path']['dir'] = 'PHPMailer';
$acid['externals']['phpmailer']['path']['autoload'] =
    'externals/' . $acid['externals']['phpmailer']['path']['dir'] . '/PHPMailerAutoload.php';

$acid['externals']['sass']['path']['dir'] = 'SassPHP_v0.0.12';
$acid['externals']['sass']['path']['lib'] = 'externals/' . $acid['externals']['sass']['path']['dir'] . '/scss.inc.php';

//require ACID_PATH . 'config.php';

/*****************************************************************************
 *           LOG INIT
 *****************************************************************************/

// PHP error logs

if ($acid['phplog']['enable']) {
    switch ($acid['phplog']['type']) {
        case 'daily' :
            $phplog_name = $acid['phplog']['filename'] . '_' . date($acid['log']['filename_date']) . '.log';
            break;
        
        default :
            $phplog_name = $acid['phplog']['filename'] . '.log';
            break;
    }
    
    ini_set('error_log', $acid['phplog']['path'] . $phplog_name);
    ini_set('log_errors', 'On');
}

// PHP error screen
$debug_report = isset($acid['error_report']['debug']) ? $acid['error_report']['debug'] : E_ALL;
$prod_report = isset($acid['error_report']['prod']) ? $acid['error_report']['prod'] : E_ALL;

if ($acid['debug']) {
    error_reporting($debug_report);
    ini_set('display_errors', 'On');
} else {
    error_reporting($prod_report);
    ini_set('display_errors', 'Off');
}

global $lang;
require ACID_PATH . 'langs/' . $acid['lang']['current'] . '.php';
require ACID_PATH . 'langs/module_' . $acid['lang']['current'] . '.php';
require ACID_PATH . 'tools/bash.php';

/**
 * retourne une exception lors d'une erreur
 *
 * @param unknown_type $errno
 * @param unknown_type $errstr
 *
 * @throws Exception
 * @return boolean
 */
function unserialize_handler($errno, $errstr)
{
    throw new Exception('error detected');
    
    return false;
}

if (!function_exists('dd')) {
    function dd()
    {
        $args = func_get_args();
        echo '<pre style="font-size:12px; border: 1px solid #000; margin:15px; padding: 30px;">';
        call_user_func_array('var_dump', $args);
        echo '</pre>';
        die();
    }
}

require ACID_PATH . 'core/acid.php';

Acid::log('START', '--------------------------');
Acid::log('info', $_SERVER['SERVER_PROTOCOL'] . ' ' . $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI']);

require ACID_PATH . 'core/acid_db.php';
require ACID_PATH . 'core/acid_session.php';
require ACID_PATH . 'core/acid_dialog.php';
require ACID_PATH . 'core/acid_upgrade.php';
require ACID_PATH . 'core/acid_hook.php';

//require ACID_PATH . 'core/vars.php';
require ACID_PATH . 'core/module.php';

require_once ACID_PATH . 'extend.php';
require_once ACID_PATH . 'retro.php';

require_once ACID_PATH . 'core/router/acid_router.php';
require_once ACID_PATH . 'core/router/acid_route.php';
require_once ACID_PATH . 'core/router/acid_event.php';
require_once SITE_PATH . 'sys/langs/router/lang_router.php';

//UPDATES AND UPGRADES
$acid_system_updaters = [];

foreach ($acid['upgrade']['types'] as $type) {
    $acid_system_updaters[$type] = new AcidUpgrade($type);
}

if ($acid_system_updaters) {
    foreach ($acid_system_updaters as $updater) {
        if (Acid::get('upgrade:check')) {
            $updater->testUpgrade();
        }
        $updater->launchUpgrade();
    }
}

?>
