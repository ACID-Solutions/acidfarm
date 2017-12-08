<?php

$nginx_folder = $action['site:folder'];
$nginx_domain = $action['site:domain'];

$nginx_file = <<< NGX
server {
        listen 80;

        server_name {$nginx_domain};
        
        root {$_SERVER['DOCUMENT_ROOT']};
        index index.php index.html;

        charset utf-8;

        #Maintenance mode
        if (-f \$document_root{$nginx_folder}maintenance.html){
                set \$rule_0 1\$rule_0;
        }
        if (-f \$document_root{$nginx_folder}maintenance.enable){
                set \$rule_0 2\$rule_0;
        }
        if (\$uri !~ "/maintenance.html"){
                set \$rule_0 3\$rule_0;
        }
        if (\$rule_0 = "321"){
                rewrite ^/.*$ {$nginx_folder}maintenance.html redirect;
        }
        
        #Allow resources rewriting
        rewrite /(.*?)-([-0-9]*?).js$ /$1.js last;
        rewrite /(.*?)-([-0-9]*?).css$ /$1.css last;

        #Rest API URL Rewriting
        location /rest/ {
                try_files \$uri \$uri/ {$nginx_folder}rest/index.php?acid_nav=\$uri&\$args;
        }

        #Site URL Rewriting
        location / {
                try_files \$uri \$uri/ {$nginx_folder}index.php?acid_nav=\$uri&\$args;
        }
        

        #PHP files and on the fly sass
        location ~ \.(php|scss)$ {
                include snippets/fastcgi-php.conf;

                # With php7-fpm:
                fastcgi_pass unix:/run/php/php7.1-fpm.sock;
                fastcgi_param  SCRIPT_FILENAME  \$realpath_root\$fastcgi_script_name;
        }
}
NGX;
