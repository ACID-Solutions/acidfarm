<?php

$base_dir = realpath(INSTALL_PATH . '../') . '/';
$config_path = $base_dir . 'sys/server.php';
$htaccess_path = $base_dir . '.htaccess';
$nginx_path = $base_dir . '.nginx.sample';

if (!is_writable($base_dir)) {
    print_error_and_exit('Write access denied in <b>' . dirname($base_dir) . '</b>. Folder must be writable.');
}

if (!is_writable(dirname($config_path))) {
    print_error_and_exit('Write access denied in <b>' . dirname($config_path) . '</b>. Folder must be writable.');
}

#Create sys/server.php
$server_file = '';
include INSTALL_PATH . 'core/generator/server.php';
$h = fopen($config_path, 'w');
$ec = fwrite($h, $server_file);
fclose($h);

#Create .htaccess
$htaccess_file = '';

include INSTALL_PATH . 'core/generator/htaccess.php';
$h = fopen($htaccess_path, 'w');
$ec = fwrite($h, $htaccess_file);
fclose($h);

#Create nginx sample
$nginx_file = '';

include INSTALL_PATH . 'core/generator/nginx.php';
$h = fopen($nginx_path, 'w');
$ec = fwrite($h, $nginx_file);
fclose($h);

#Init database
$db_path = $base_dir . '/sys/db/init.sql';
$db_ml_path = $base_dir . '/sys/db/multilingual.sql';
$dbpref = $action['database:prefix'];
$user_salt = uniqid();

if (!empty($action['database:init'])) {
    $requete = file_get_contents($db_path);
    $requete = str_replace('`acid_', '`' . $dbpref, $requete);
    
    $requete .= "\n" . "UPDATE `" . $dbpref . "user` SET " .
                "`username`='" . $action['user:name'] . "', " .
                "`password`=MD5('" . $action['site:salt'] . $action['user:password'] . $user_salt . "'), " .
                "`email`='" . $action['user:email'] . "', " .
                "`user_salt`='" . $user_salt . "'" . "WHERE `id_user`='1';";
    
    
    $configuration = [
        'email'        => $action['site:email'],
        'phone'        => $action['coords:phone'],
        'fax'          => $action['coords:fax'],
        'contact'      => $action['coords:contact'],
        'city'         => $action['coords:city'],
        'cp'           => $action['coords:cp'],
        'website'      => $action['site:scheme'] . $action['site:domain'] . $action['site:folder'],
        'home_content' => $initial_home_content
    ];
    
    $initial_home_content =
        '<p><span id="result_box" lang="en"><strong>Welcome to the home page AcidFarm !</strong><br /> Thank you for choosing our solution.</span></p>';
    $home_content_keys = (!empty($action['lang:multilingual'])) ?
        ['home_content_fr','home_content_en','home_content_de','home_content_es','home_content_it'] : ['home_content'];
    foreach ($home_content_keys as $hck) {
        $configuration[$hck] = $initial_home_content;
    }
    
    foreach ($configuration as $key => $val) {
        $val = addslashes($val);
        $requete .= "\n" . "INSERT INTO `" . $dbpref
                    . "config` (`id_config`, `name`, `value`) VALUES (NULL, '$key', '$val');";
    }
 
    //Add policy default content fr + en
    $website = $action['site:scheme'] . $action['site:domain'] . $action['site:folder'];
    
    $policy_content['policy_title']   =
    $policy_content['policy_title_fr']   ='Politique sur les données';
    
    $policy_content['policy_content'] =
    $policy_content['policy_content_fr'] =  <<<EOPOLICY
<p>Les données personnelles susceptibles d'être collectées dans le cadre des services proposés sur {$website} seront
traités au moyen de protocoles sécurisés et restreints (à l'exception de ceux recueillis par des tiers) à
 un usage interne seulement (sécurité, diagnostic). Ce site utilise des cookies pour son fonctionnement
et peut l'utiliser à des fins statistiques.</p>
EOPOLICY;
    
    foreach (['_en','_de','_es','_it'] as $sf) {
        $policy_content['policy_title'.$sf]   =  'Data Policy';
        $policy_content['policy_content'.$sf] =  <<<EOPOLICY
<p>Personal data that may be collected as part of the services offered on {$website} will be
treated under secure and restricted protocols (apart from those collected by third-party
internal use only (safety, diagnostics). This website uses cookies for its operation
and can use it for statistical purposes.</p>
EOPOLICY;
    }
    
    //Add consent policy default content fr + en
    $policy_content['consent_message_fr'] =  'En poursuivant votre navigation sur ce site sans modifier vos'.
    '<a target="_blank" aria-label="configure cookies" role="button" tabindex="1"'.
    'class="cc-link" href="{{href}}#policy_admin">préférences</a>, vous acceptez l\'utilisation des cookies.';
    $policy_content['consent_dismiss_btn_fr'] =  'J\'ai compris !';
    $policy_content['consent_learnmore_btn_fr'] =  'En savoir plus';
    $policy_content['consent_revoke_btn_fr'] =  'Politique sur les données';
    
    foreach (['_en','_de','_es','_it'] as $sf) {
        $policy_content['consent_message'.$sf] = 'By continuing your navigation on this site without modifying your' .
        '<a target="_blank" aria-label="configure cookies" role="button" tabindex="1"'
        .
        'class="cc-link" href="{{href}}#policy_admin">preferences</a>, you accept the use of cookies.';
    }

    foreach ($policy_content as $pck =>$pckv) {
        $configuration[$pck] = $pckv;
    }
    
    foreach ($configuration as $key => $val) {
        $val = addslashes($val);
        $requete .= "\n" . "INSERT INTO `" . $dbpref
                    . "config` (`id_config`, `name`, `value`) VALUES (NULL, '$key', '$val');";
    }
    
    $ressource = new PDO(
        $action['database:type'] . ':host=' . $action['database:host'] . ';port=' . $action['database:port']
        . ';dbname=' . $action['database:database'],
        $action['database:username'],
        $action['database:password']
    );
    $ressource->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $ressource->exec("SET CHARACTER SET UTF8");
    $ressource->exec($requete);
    
    if (!empty($action['lang:multilingual'])) {
        $ressource->exec('COMMIT;');
        $requeteml = file_get_contents($db_ml_path);
        $requeteml = str_replace('`acid_', '`' . $dbpref, $requeteml);
        $ressource->exec($requeteml);
    }
}

//Add ignored path
$path_to_add = [
    'sys/stats', 'files', 'files/users', 'files/tmp', 'files/home', 'files/news', 'files/page',
    'files/photo', 'files/seo', 'upload', 'logs'
];
foreach ($path_to_add as $pta) {
    $pa_path = $base_dir . $pta;
    if (!file_exists($pa_path)) {
        mkdir($pa_path);
    }
}

//Touch ignored files
$file_to_add = [
    'sys/stats/stats.tpl', 'sys/stats/contact.tpl', 'sys/update/cur_version.txt',
    'sys/update/.system/cur_version.txt', 'sys/update/.content/cur_version.txt'
];
foreach ($file_to_add as $fta) {
    $fa_path = $base_dir . $fta;
    if (!file_exists($fa_path)) {
        file_put_contents($fa_path, '');
    }
}

//Deny from all some path
$path_to_secure = ['logs'];
foreach ($path_to_secure as $pts) {
    $fs_path = $base_dir . $pts . '/.htaccess';
    if (!file_exists($fs_path)) {
        $h = fopen($fs_path, 'w');
        $ec = fwrite($h, 'deny from all');
        fclose($h);
    }
}

$redirect_to = $action['site:folder'];