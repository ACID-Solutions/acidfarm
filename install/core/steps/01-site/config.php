<?php

//--Site profile

//Site name
$inputs['site:name'] = array(
  'title' => 'Site name',
  'default' => '',
  'desc' => ''
);

//Site e-mail
$inputs['site:email'] = array(
    'title' => 'Site email',
    'default' => '',
    'desc'  => ''
);

//Site salt
$inputs['site:salt'] = array(
    'title' => 'Site salt',
    'default' => rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9),
    'desc' => ''
);


//--Site Administrator

//Administrator name
$inputs['site:admin:name'] = array(
    'title' => 'Admin name',
    'default' => '',
    'desc' => '(ex : used as sender for emails)'
);

//Administrator e-mail
$inputs['site:admin:email'] = array(
    'title' => 'Site email',
    'default' => '',
    'desc'  => '(ex : can receive system notifications)'
);

//--Site URL

//Site scheme
$inputs['site:scheme'] = array(
    'title' => 'Site scheme',
    'default' => empty($_SERVER['HTTPS']) ? 'http://' : 'https://',
    'desc' => ''
);

//Site domain
$inputs['site:domain'] = array(
    'title' => 'Site domain',
    'default' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '',
    'desc'  => '(ex : acidfarm.net)'
);

//Site folder
$folder = explode('/',$_SERVER['PHP_SELF']);
$folder = $folder ? implode('/',array_slice($folder,0,-1)) : '';
$folder = $folder.'/';

$inputs['site:folder'] = array(
    'title' => 'Site folder',
    'default' => $folder,
    'desc' => 'always finish with / (ex : /acidfarm/ )'
);