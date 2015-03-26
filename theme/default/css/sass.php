<?php

$directory = "stylesheets";
require '../../../sys/glue.php';

$sass_environement = __DIR__.'/sass/_environement.scss';
$sass = '$sitepath:"'.Acid::get('url:folder') .'";'. "\n" .
		'$basepath:"'.Acid::get('url:theme') .'";'. "\n" ;
file_put_contents($sass_environement,$sass);

Acid::load(Acid::get('externals:sass:path:lib'));
scss_server::serveFrom(__DIR__);