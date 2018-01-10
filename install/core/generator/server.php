<?php

$admin_name = empty($action['site:admin:email']) ? $action['user:name'] : $action['site:admin:name'];
$admin_mail = empty($action['site:admin:email']) ? $action['user:email'] : $action['site:admin:email'];

$use_smtp = (get_in_tab('email:method',$action,'') == 'smtp') ;
$custom_lang = !empty($action['lang:custom']);
$custom_lang_available = !empty($action['lang:available']) ? $action['lang:available'] : array($action['lang:default']);
$custom_lang_available = is_array($custom_lang_available) ? $custom_lang_available : explode(';', $custom_lang_available);

$is_dev = $action['server:mode'] == 'dev';
$is_prod = $action['server:mode'] == 'prod';
$is_preprod = $action['server:mode'] == 'preprod';

$log_type = !empty($action['server:log:type']) ? $action['server:log:type'] : '';

ob_start();

echo get_sf_start();

//Site Profile
echo get_sf_label_line('Site Profile');
echo get_sf_line_for_string("acid:site:name", get_in_tab('site:name',$action));
echo get_sf_line_for_string("acid:site:email", get_in_tab('site:email',$action));
echo get_sf_line_skip();
echo get_sf_line_for_string("acid:admin:name", $admin_name);
echo get_sf_line_for_string("acid:admin:email", $admin_mail);

//Site Salt
echo get_sf_label_line('Site Salt');
echo get_sf_line_for_string("acid:hash:salt",  get_in_tab('site:salt',$action));

//Site Url
echo get_sf_label_line('Site Url');
echo get_sf_line_for_string("acid:url:scheme", get_in_tab('site:scheme',$action));
echo get_sf_line_for_string("acid:url:domain", get_in_tab('site:domain',$action));
echo get_sf_line_for_string("acid:url:folder", get_in_tab('site:folder',$action));
echo get_sf_line("acid:url:system", get_sf_concat_variables(array('acid:url:scheme', 'acid:url:domain', 'acid:url:folder')));
echo get_sf_line("acid:url:system_lang", get_sf_variable('acid:url:system'));
echo get_sf_line("acid:url:folder_lang", get_sf_variable('acid:url:folder'));

//Database
echo get_sf_label_line('Database');
echo get_sf_line_for_string("acid:db:type", get_in_tab('database:type',$action));
echo get_sf_line_for_string("acid:db:host", get_in_tab('database:host',$action));
echo get_sf_line_for_string("acid:db:port", get_in_tab('database:port',$action));
echo get_sf_line_for_string("acid:db:user", get_in_tab('database:username',$action));
echo get_sf_line_for_string("acid:db:pass", get_in_tab('database:password',$action));
echo get_sf_line_for_string("acid:db:base", get_in_tab('database:database',$action));
echo get_sf_line_for_string("acid:db:prefix", get_in_tab('database:prefix',$action));

//Emails
echo get_sf_label_line('Mail server');
echo get_sf_line_for_string("acid:email:method", get_in_tab('email:method',$action));
echo get_sf_label_line('SMTP Configuration');
echo get_sf_line_for_string("acid:email:smtp:host", get_in_tab('email:smtp:host',$action), !$use_smtp);
echo get_sf_line_for_string("acid:email:smtp:user", get_in_tab('email:smtp:user',$action), !$use_smtp);
echo get_sf_line_for_string("acid:email:smtp:pass", get_in_tab('email:smtp:pass',$action), !$use_smtp);
echo get_sf_line_for_string("acid:email:smtp:port", get_in_tab('email:smtp:port',$action), !$use_smtp);
echo get_sf_line_for_string("acid:email:smtp:secure", get_in_tab('email:smtp:secure',$action), !$use_smtp);
echo get_sf_line_for_bool("acid:email:smtp:debug", get_in_tab('email:smtp:debug',$action), !$use_smtp);

//Allow deploy for security patches
echo get_sf_label_line('Allow deploy for security patches (unsafe)');
echo get_sf_line_for_bool("acid:deploy:allowed", true, true, 'decrease security');

//Debug & Maintenance & Logs
echo get_sf_label_line('Maintenance');
echo get_sf_line_for_bool("acid:maintenance", false);
echo get_sf_line_for_string("acid:maintenance_desc", 'Site en maintenance...', true);

echo get_sf_label_line('Upgrade');
echo get_sf_line_for_string("acid:upgrade:mode", ($is_prod ? 'prod' : 'dev'), false, 'prod/dev/off');

echo get_sf_label_line('Debug');
echo get_sf_line_for_bool("acid:debug", false, (!$is_prod));

echo get_sf_label_line('Logs');
echo get_sf_comment_line("All : '*'");
echo get_sf_comment_line("Defined : array('ACID','BROWSER','COMBINE','CONTACT','CRON','DEBUG','DEPRECATED','ERROR','FILE','HACK','IMAGE WM','INFO','LOG','MAIL','MAINTENANCE','MAJ','META','PAYMENT','PAYPAL','PERMISSION','POSTINFO','REGISTRATION','ROUTER','SCRIPT','SESSION','SQL','START','TIMER','UPLOAD','URL','USER')");
echo get_sf_line(
    "acid:log:keys",
    "'*'",
    !$is_dev,
    'Dev sample'
);
echo get_sf_line(
    "acid:log:keys",
    "array('INFO','POSTINFO','URL','DEPRECATED','HACK','ERROR','PAYMENT','MAINTENANCE','FILE')",
    !$is_prod,
    'Prod sample'
);
echo get_sf_line(
    "acid:log:keys",
    "array('START','SQL','SESSION','INFO','POSTINFO','URL','USER','DEPRECATED','ROUTER','PERMISSION','HACK','ERROR','PAYPAL','PAYMENT','MAINTENANCE','FILE')",
    !$is_preprod,
    'Preprod sample'
);
echo get_sf_line_for_string("acid:log:type", ($log_type ? $log_type : 'daily'), ((!$log_type) && (!$is_prod)), 'single/daily');
echo get_sf_line("acid:log:colorize", 'array()', true, "array('HACK'=>'red','DEBUG'=>'yellow')");
echo get_sf_line("acid:error_report:debug", 'E_ALL & ~E_STRICT', (!$is_prod));
echo get_sf_line("acid:error_report:prod", 'E_ALL & ~E_STRICT', (!$is_prod));

//Language
echo get_sf_label_line('Use custom language configuration');
echo get_sf_line_for_bool("acid:lang:use_server", $custom_lang, !$custom_lang);
echo get_sf_line_for_bool("acid:lang:use_nav_0", (count($custom_lang_available) > 1), !$custom_lang);
echo get_sf_line_for_string("acid:lang:default", get_in_tab('lang:default',$action), !$custom_lang);
echo get_sf_line_for_string_array("acid:lang:available", $custom_lang_available, !$custom_lang);

//Theme
echo get_sf_label_line('Use custom theme');
echo get_sf_line_for_string("acid:server_theme", get_in_tab('theme:selection',$action), !get_in_tab('theme:selection',$action));

//Sessions
echo get_sf_label_line('Sessions');
echo get_sf_line_for_bool("acid:session:enable", true, true, 'Enable/Disable session');
echo get_sf_line("acid:session:table", get_sf_variable('acid:db:prefix') . ".'session'", false, ' Session\'s table name');
echo get_sf_line("acid:session:expire", "14440", true, 'Expire date in seconds');
echo get_sf_line_for_bool("acid:session:secure", false, true, 'HTTPS only');
echo get_sf_line_for_bool("acid:session:httponly", true, true, 'Only HTTP, no javascript');
echo get_sf_line_for_bool("acid:session:check_ip", false, true, 'Check IP');
echo get_sf_line_for_bool("acid:session:check_ua", true, true, 'Check User Agent');

//Cookies
echo get_sf_label_line('Cookies');
echo get_sf_line_for_bool("acid:cookie:use_server", true, true, 'Enable custom cookies configuration');
echo get_sf_line("acid:cookie:path", get_sf_variable('acid:url:folder'), true, 'Folder for which the cookie is accessible');
echo get_sf_line("acid:cookie:domain", get_sf_variable('acid:url:domain'), true, 'Domain for which the cookie is accessible');
echo get_sf_line_for_bool("acid:cookie:dyndomain", true, true, 'Define the cookie domain on the fly');

//Plupload
echo get_sf_label_line('Plupload');
echo get_sf_line("acid:plupload:chunk_size", 2, false, 'En Mo');
echo get_sf_line("acid:plupload:max_size", 500, false, 'En Mo');
echo get_sf_line("acid:plupload:session_time", '4 * 60 * 60', false, 'Maximum transfer time in seconds (4H by default)');
echo get_sf_line("acid:plupload:file_tmp_age", '24 * 60 * 60', false, 'Temporary files lifetime in seconds (24H by default)');
echo get_sf_line_for_bool("acid:plupload:show_upload", false, false, 'Displays the button to upload the file');
echo get_sf_line_for_bool("acid:plupload:autosubmit", true, false, 'Submits (by default) the admin form after sending all the files');
echo get_sf_line_for_string_array("acid:plupload:runtimes", array('html5', 'flash'), false, 'Sets the runtimes for the plugin plupload');

//Css using php files
echo get_sf_label_line('Css using php file');
echo get_sf_comment_line('acid:css:dynamic:files need to be defined (cf sys/dynamic.php)');
echo get_sf_line_for_bool("acid:css:dynamic:active", false, false, 'generate a css file referring to php file');
echo get_sf_line_for_string("acid:css:dynamic:mode", $is_dev ? 'debug' : 'default', false , 'debug (always), default (if not exists)');
echo get_sf_line_for_string_array("acid:css:dynamic:files", array(), true , "['path/to/css.php','path/to/css2.php']");

//Css using php sass
echo get_sf_label_line('Css using php sass');
echo get_sf_line_for_bool("acid:sass:enable", true, false, 'generate a css file referring to php file');
echo get_sf_line_for_string("acid:sass:mode", $is_dev ? 'dev' : 'default', false , 'dev (always), default (if not exists)');

//Versioning
echo get_sf_label_line('Resources versioning');
echo get_sf_line_for_string("acid:versioning:file", 'sys/versioning.txt', true , 'enable versionning with the file value');
echo get_sf_line_for_string("acid:versioning:val", '', true , 'if set, override versionning file');
echo get_sf_line_for_string("acid:versioning:tag", '-c__VERSION__', true , '');
echo get_sf_line_for_string("acid:versioning:way", 'htaccess', (!empty($action['server:resources:versioning'])) , 'htaccess, path, get');

//Compiler
echo get_sf_label_line('Compiler');
echo get_sf_line_for_bool("acid:compiler:enable", true, true, 'Apply compilation to assets');
echo get_sf_line("acid:compiler:expiration", '60*60*24*15', true , '15 days');
echo get_sf_line("acid:compiler:mode", $is_dev ? 'dev' : 'prod', true , 'if prod, compile if expired');
echo get_sf_line("acid:compiler:css:disable", true, true , 'do not combine css');
echo get_sf_line("acid:compiler:js:disable", true, true , 'do not combine js');
echo get_sf_line("acid:compiler:css:compression", false, true , 'minify css');
echo get_sf_line("acid:compiler:js:compression", false, true , 'minify js');

//Sentry url for supervision, need Raven (ex : composer require raven/raven)
echo get_sf_label_line('Sentry url for supervision, need Raven (ex : composer require raven/raven)');
echo get_sf_line_for_string("acid:sentry:url", '', true);
echo get_sf_line("acid:sentry:report_level", 'E_ALL', true);

//Disallow indexation
echo get_sf_label_line('Disallow indexation');
echo get_sf_line_for_bool("acid:donotindex", true, (!$is_preprod));

//Seo
echo get_sf_label_line('Meta tags');
echo get_sf_line_for_string("acid:title:left", "", true);
echo get_sf_line("acid:title:right", "' - '." . get_sf_variable('acid:site:name'));

echo get_sf_stop();
$server_file = ob_get_clean();