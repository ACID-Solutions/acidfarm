<?php

$directory = "stylesheets";
require '../../../sys/glue.php';

Acid::load(Acid::get('externals:sass:path:lib'));
scss_server::serveFrom(__DIR__);