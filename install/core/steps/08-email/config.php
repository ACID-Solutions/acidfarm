<?php

//-- Mail server

//Method
$inputs['email:method'] = array(
    'title' => 'Method for sending mails',
    'default' => '',
    'values' => array('mail'=>'mail','smtp'=>'smtp'),
    'desc' => ''
);

//-- SMTP Configuration

//Host
$inputs['email:smtp:host'] = array(
    'title' => 'SMTP host',
    'default' => 'localhost',
    'desc' => ''
);

//User
$inputs['email:smtp:user'] = array(
    'title' => 'SMTP user',
    'default' => '',
    'desc' => ''
);

//Pass
$inputs['email:smtp:pass'] = array(
    'title' => 'SMTP password',
    'default' => '',
    'desc' => ''
);

//Port
$inputs['email:smtp:port'] = array(
    'title' => 'SMTP port',
    'default' => '',
    'desc' => ''
);

//Secure
$inputs['email:smtp:secure'] = array(
    'title' => 'SMTP secure',
    'default' => '',
    'values' => array(''=>'','tls'=>'tls','ssl'=>'ssl'),
    'desc' => ''
);

//Debug
$inputs['email:smtp:debug'] = array(
    'title' => 'SMTP debug',
    'default' => false,
    'bool' => true,
    'desc' => ''
);