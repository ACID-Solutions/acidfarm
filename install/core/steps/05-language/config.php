<?php
//--Server Lang

//Use custom languages configuration
$inputs['lang:custom'] = array(
    'title' => 'Force language configuration',
    'default' => '',
    'bool' => true,
    'desc' => '',
    'warning' => 'Do not check for default setting'
);

$inputs['lang:multilingual'] = array(
    'title' => 'Prepare database for multilingual',
    'default' => '',
    'bool' => true,
    'desc' => ''
);

$inputs['lang:available'] = array(
    'title' => 'Available languages',
    'default' => '',
    'values' => array(
        'fr'=>'fr',
        'en'=>'en',
        'de'=>'de',
        'es'=>'es',
        'it'=>'it',
    ),
    'multi' => true,
    'desc' => ''
);

$inputs['lang:default'] = array(
    'title' => 'Default language',
    'default' => '',
    'values' => array(
        '' => '',
        'fr'=>'fr',
        'en'=>'en',
        'de'=>'de',
        'es'=>'es',
        'it'=>'it',
    ),
    'desc' => ''
);