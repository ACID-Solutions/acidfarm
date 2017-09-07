<?php
$action = $_POST;

if (isset($action['acidfarm_do']) && ($action['acidfarm_do'] == 'check_database')) {
    $dbtype = addslashes($action['db_type']);
    $dbhost = addslashes($action['db_host']);
    $dbport = addslashes($action['db_port']);
    $dbuser = addslashes($action['db_username']);
    $dbpass = addslashes($action['db_password']);
    $dbname = addslashes($action['database']);
    $dbpref = addslashes($action['db_prefix']);
    
    $result = false;
    
    try {
        $result = checkDataBase($dbtype, $dbhost, $dbport, $dbname, $dbuser, $dbpass);
    } catch (Exception $e) {
        $result = false;
    }
    
    echo json_encode(['success' => $result]);
    
    exit();
} elseif (isset($action['acidfarm_do']) && ($action['acidfarm_do'] == 'check_mail')) {
    $from = addslashes($action['mail_from']);
    $to = addslashes($action['mail_to']);
    $mailmethod = addslashes($action['mail_method']);
    $mailhost = addslashes($action['mail_host']);
    $mailport = addslashes($action['mail_port']);
    $mailuser = addslashes($action['mail_username']);
    $mailpass = addslashes($action['mail_password']);
    $mailsecure = addslashes($action['mail_secure']);
    
    $result = false;
    
    try {
        $result = checkEmail($from, $to, $mailmethod, $mailhost, $mailport, $mailuser, $mailpass, $mailsecure);
    } catch (Exception $e) {
        $result = false;
    }
    
    echo json_encode(['result' => $result]);
    
    exit();
} elseif (isset($action['acidfarm_do']) && ($action['acidfarm_do'] == 'install')) {
    
    require 'treat.php';
    
    if (!empty($redirect_to)) {
        header('Location: '.$redirect_to);
        exit();
    }
}

