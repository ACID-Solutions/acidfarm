<?php

if (php_sapi_name() == "cli") {
    require 'install/core/cli.php';
} else {
    require 'install/core/web.php';
}