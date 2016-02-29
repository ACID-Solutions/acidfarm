<?php
// This is global bootstrap for autoloading

require __DIR__.'/../../glue.php';

if (Acid::get('lang:use_nav_0')) {
    Acid::set('url:folder_lang', (Acid::get('url:folder').Acid::get('lang:current').'/') );
    Acid::set('url:system_lang', (Acid::get('url:scheme').Acid::get('url:domain').Acid::get('url:folder_lang')) );
}