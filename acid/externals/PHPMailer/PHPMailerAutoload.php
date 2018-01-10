<?php

$GLOBALS['acid']['externals']['phpmailer']['path']['subdir'] = 'PHPMailer_v6.0.3';

/**
 * PHPMailer SPL autoloader.
 *
 * @param string $classname The name of the class to load
 */
function PHPMailerAutoload($classname)
{
    
    $namespace = 'PHPMailer\\PHPMailer\\';
    $versionfolder = $GLOBALS['acid']['externals']['phpmailer']['path']['subdir'];
    $subpath = dirname(__FILE__) . DIRECTORY_SEPARATOR .
               $versionfolder . DIRECTORY_SEPARATOR .
               'src' . DIRECTORY_SEPARATOR;
    
    if (substr($versionfolder,strlen('PHPMailer_')) <= 'v6') {
        require_once $subpath.'PHPMailerAutoload.php';
        return false;
    }
    
    if (strpos($classname,$namespace)!==0) {
        return false;
    }
    
    $classname = substr($classname, strlen($namespace));
    $filename = $subpath . $classname . '.php';

    if (is_readable($filename)) {
        require $filename;
    }
}

//SPL autoloading was introduced in PHP 5.1.2
if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
    spl_autoload_register('PHPMailerAutoload', true, true);
} else {
    spl_autoload_register('PHPMailerAutoload');
}