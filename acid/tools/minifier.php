<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Tool
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Outils Minifier en PHP
 * @package   Tool
 */
class AcidMinifier {

    /**
     * Minify CSS
     * @param $input
     * @return mixed
     */
    public static function css($input) {
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
            ),array('$1','$1$2$3$4$5$6$7','$1',':0','$1:0 0','.$1','$1$3','$1$2$4$5','$1$2$3','$1:0','$1$2'),$input);
    }

    /**
     * Minify Js
     * @param $input
     * @return mixed
     */
    public static function js($input) {
        /*TODO*/
        return $input;
    }

    /**
     * Génère le nom de fichier compilé
     * @param $minify
     * @param string $type
     */
    public static function fileName( $files, $name, $type='css') {
        return $name . '-' . md5(implode(',', $files)) . '.' . $type;
    }

    /**
     * Minify content acccording to type
     * @param $minify
     * @param string $type
     * @return mixed
     */
    public static function compress( $minify, $type='css') {
        if ($type=='css') {
            return static::css($minify);
        }elseif($type=='js') {
            return static::js($minify);
        }
        return $minify;
    }

    /**
     * Combine resources from curl requests
     * @param $files
     * @param string $type
     */
    public static function combineFromUrl($files, $type='css',$compress=true,$base_url=null,$base_path=null) {
        $base_url = $base_url===null? Acid::get('url:system') : $base_url;
        $base_path = $base_path===null? SITE_PATH : $base_url;

        $contents = '';
        if ($files && is_array($files)) {
            foreach ($files as $file) {
                $url = $base_url.$file;
                $fpath = $base_path.$file;
                $sub_content = '';

                if (file_exists($fpath)) {
                    Acid::log('COMBINE','from path '. $fpath);
                    $sub_content = @file_get_contents($fpath);
                }else{
                    Acid::log('COMBINE','from url '. $url);
                    $curlSession = curl_init();
                    curl_setopt($curlSession, CURLOPT_URL, $url);
                    curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
                    $sub_content = @curl_exec($curlSession);
                    curl_close($curlSession);
                }

                if ($sub_content)  {
                    if ($compress) {
                        $sub_content = static::compress($sub_content,$type);
                    }
                    if ($type=='css') {
                        $refpath = (Acid::get('url:folder').AcidFs::removeBasePath(dirname($fpath)));
                        $sub_content = preg_replace('/url\(\s*[\'"]?\/?(.+?)[\'"]?\s*\)/i', 'url('.$refpath.'/$1)', $sub_content);
                    }
                    $contents .=  "/* ".$url." */"."\n" .$sub_content. "\n" ;
                }
            }
        }

        return $contents;
    }
}