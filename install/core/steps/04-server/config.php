<?php

//--Server Configuration

//Server mode
$inputs['server:mode'] = array(
    'title' => 'Server Mode',
    'default' => ((isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'],'www.')===0) ? 'prod' : 'dev'),
    'values' => array(
        'dev'=>'Dev',
        'preprod'=>'Preprod',
        'prod'=>'Prod'
    ),
    'desc' => ''
);

//Server mode
$inputs['server:resources:versioning'] = array(
    'title' => 'Versioning of resources from htaccess',
    'default' => 1,
    'bool' => true,
    'desc' => '(ex : style.css is called by style-001.css)'
);


//--Server Housing

//Hoster
$inputs['server:hoster'] = array(
    'title' => 'Server Housing',
    'default' => '',
    'values' => array(
        ''=>'-',
        'ovh'=>'OVH',
        '1and1'=>'1and1',
        'other'=>'Other'
    ),
    'desc' => '(ex : some server host need a particular .htaccess file) '
);

//--Server Logs

//Logs
$inputs['server:log:type'] = array(
    'title' => 'Server logs type',
    'default' => 'single',
    'values' => array(
        'single'=>'single',
        'daily'=>'daily'
    ),
    'desc' => 'if daily : one file per day, only one otherwise'
);