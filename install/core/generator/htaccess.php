<?php

$htaccessOVH =
    ($action['server:mode'] == 'ovh') ?
        '# OVH' . "\n" .
        'SetEnv PHP_VER 7_0' . "\n" .
        'SetEnv REGISTER_GLOBALS 0' . "\n" .
        'SetEnv MAGIC_QUOTES 0' : '';

$htaccess_vress_quote = (!empty($action['server:resources:versioning']) ? '' : '#');
$htaccess_vress = $htaccess_vress_quote . 'RewriteRule (.+)-([0-9]+).js$ $1.js [L,QSA]' . "\n" .
                  $htaccess_vress_quote . 'RewriteRule (.+)-([0-9]+).css$ $1.css [L,QSA]' . "\n";

$htaccess_folder = $action['site:folder'];
$htaccessMaintenance = <<< HTACC
#RewriteCond %{REMOTE_ADDR} !^123\.456\.789\.000
RewriteCond %{DOCUMENT_ROOT}{$htaccess_folder}maintenance.html -f
RewriteCond %{DOCUMENT_ROOT}{$htaccess_folder}maintenance.enable -f
RewriteCond %{SCRIPT_FILENAME} !{$htaccess_folder}maintenance.html
RewriteRule ^.*$ {$htaccess_folder}maintenance.html [R=503,L]
ErrorDocument 503 {$htaccess_folder}maintenance.html
HTACC;

$htaccessRest = <<< HTACC
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} ^{$htaccess_folder}rest/ [NC]
RewriteRule ^(.*)$ {$htaccess_folder}rest/index.php?acid_nav=$1 [L,QSA]
HTACC;

$htaccess_file = <<< HTACC
# Charset
AddDefaultCharset UTF-8
Options -Indexes

$htaccessOVH

# URL Rewriting
RewriteEngine on

# Maintenance Mode
$htaccessMaintenance

# Versioning of Resources
$htaccess_vress

# Rest API
$htaccessRest

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ {$htaccess_folder}index.php?acid_nav=$1 [L,QSA]
HTACC;
