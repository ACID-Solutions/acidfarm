<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\ModelView
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

include pathinfo(__FILE__,PATHINFO_DIRNAME ).'/../sys/glue.php';

if ( Acid::get('compiler:enabled')) {
    $base = SITE_PATH;
    $base_url = Acid::get('url:system');

    $dev_mode = Acid::get('compiler:mode') == 'dev';

    $type = isset($_GET['type']) ? $_GET['type'] : '';
    $filename = isset($_GET['name']) ? $_GET['name'] : 'combine';
    $expiration_time = 3600;
    $return = isset($_GET['return']) ? $_GET['return'] : 'file';

    $dest_path = null;
    switch ($_GET['type']) {
        case 'css' :
            $dest_path = Acid::themePath('css', null, false, true) . '/compiled/combine/';
            break;
        case 'js' :
            $dest_path = Acid::themePath('js', null, false, true) . '/compiled/combine/';
            break;
        default:
            AcidUrl::error404();
            exit();
            break;
    }


    $files = explode(',', $_GET['files']);

    $file_path = $dest_path . AcidMinifier::fileName($files,$filename,$type);
    $expire_path = $file_path . '.expire';

    $is_expired = true;
    if (file_exists($expire_path)) {
        if (!$dev_mode) {
            $expire = file_get_contents($expire_path);
            $is_expired = $expire > time();
        } else {
            $is_expired = true;
        }
    }

    if ((!$is_expired) && file_exists($file_path)) {
        $content = file_get_contents($file_path);
    } else {

        if (!is_dir($dest_path)) {
            mkdir($dest_path);
        }

        $content = AcidMinifier::combineFromUrl($files, $type);
        file_put_contents($file_path, $content);
        file_put_contents($expire_path, (time() + $expiration_time));
    }

    if ($return=='path') {
        header("Content-Type: text/plain");
        echo $file_path;
        exit();
    }

    header("Content-Type: text/" . $type);
    echo $content;
    exit();
}