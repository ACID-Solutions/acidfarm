<?php

@ini_set('AddDefaultCharset', 'utf-8');

define('INSTALL_PATH', realpath(__DIR__.'/..').'/');


if (empty($cli_mode)) {
    if (!file_exists('.htaccess')) {
        $h = fopen('.htaccess', 'w');
        $ec = fwrite($h, 'AddDefaultCharset UTF-8');
        fclose($h);
        header('Location: ' . $_SERVER['REQUEST_URI']);
    }
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

require 'lib.php';

if (file_exists( realpath(INSTALL_PATH . '../sys/server.php')) ) {
    print_error_and_exit('Install has already been done.','');
}