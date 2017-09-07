<?php

//--Database

//Initialize
$inputs['database:init'] = array(
    'title' => 'Initialize database',
    'default' => 1,
    'bool' => true,
    'desc' => ''
);

//Type
$inputs['database:type'] = array(
    'title' => 'Database type',
    'default' => 'mysql',
    'desc' => '(ex : mysql, pgsql, sqlite, odbc, oci : oracle, dblib : microsoft)'
);

//Host
$inputs['database:host'] = array(
    'title' => 'Database host',
    'default' => 'localhost',
    'desc' => ''
);

//Port
$inputs['database:port'] = array(
    'title' => 'Database port',
    'default' => '3306',
    'desc' => ''
);

//Username
$inputs['database:username'] = array(
    'title' => 'Database username',
    'default' => '',
    'desc' => ''
);

//Password
$inputs['database:password'] = array(
    'title' => 'Database password',
    'default' => '',
    'password' => true,
    'desc' => ''
);

//Database
$inputs['database:database'] = array(
    'title' => 'Database name',
    'default' => '',
    'desc' => ''
);

//Prefix
$inputs['database:prefix'] = array(
    'title' => 'Database prefix',
    'default' => 'acid_',
    'desc' => ''
);

