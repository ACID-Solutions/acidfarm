<?php

/**
 * AcidFarm - Yet Another Framework
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Gestionnaire de Fichier et Ressources Fichier/Image
 *
 * @package   Acidfarm\Tool
 */
class AcidFs
{

    /**
     * Convertit un chemin vers une image en image html faisant en base64
     * @param        $file_name
     * @param string $alt
     * @param array  $params
     *
     * @return string
     */
    public static function base64Image($file_name,$alt='',$params=[]) {
        
        try {
            if ($content = @file_get_contents($file_name)) {
                
                $imgData = base64_encode($content);
                
                $src = 'data: ' . mime_content_type($file_name) . ';base64,' . $imgData;
                
                if (!isset($params['alt'])) {
                    $params['alt'] =  $alt;
                }
                
                return '<img src="' . $src . '" ' . implode(' ', array_map(
                        function ($k, $v) { return $k . '="' . htmlspecialchars($v) . '"'; },
                        array_keys($params), $params
                    )) . ' >';
            }
            
            return $alt;
        }catch (Exception $e) {
            return $alt;
        }
    }
    
    /**
     * Retourne l'extension du fichier en entrée ou false en cas d'échec.
     *
     * @param string $file_name
     *
     * @return string | bool
     */
    public static function getExtension($file_name)
    {
        if (!empty($file_name) && !strpos('.', $file_name)) {
            $strsr = explode('.', $file_name);
            $strs = array_reverse($strsr);
            if (count($strsr) < 2) {
                return '';
            }
            if ($strs[0] != 'gz') {
                return strtolower($strs[0]);
            } elseif ($strs[1] == 'tar') {
                return strtolower($strs[1] . '.' . $strs[0]);
            } else return strtolower($strs[0]);
        } else return false;
    }
    
    /**
     * Retourne le nom du fichier en entrée sans extension.
     *
     * @param string $file_name
     *
     * @return string
     */
    public static function removeExtension($file_name)
    {
        $name = $file_name;
        
        if ($ext = self::getExtension($file_name)) {
            $name = substr($name, 0, (strlen('.' . $ext) * -1));
        }
        
        return $name;
    }
    
    /**
     * Retourne le nom du fichier delesté du basepath le cas échéant
     *
     * @param string $file_name
     *
     * @return string
     */
    public static function removeBasePath($file_name)
    {
        $path_pref = substr(SITE_PATH, 0, (strlen(Acid::get('url:folder_lang')) * -1));
        
        if (strpos($file_name, Acid::get('url:system_lang')) === 0) {
            return substr($file_name, strlen(Acid::get('url:system_lang')));
        }
        
        if (strpos($file_name, $path_pref . Acid::get('url:folder_lang')) === 0) {
            return substr($file_name, strlen($path_pref . Acid::get('url:folder_lang')));
        }
        
        if (strpos($file_name, Acid::get('url:folder_lang')) === 0) {
            return substr($file_name, strlen(Acid::get('url:folder_lang')));
        }
        
        return $file_name;
    }
    
    /**
     * Copie un dossier de manière recursive
     *
     * @param string $src
     * @param string $dst
     */
    public static function recurseCopy($src, $dst)
    {
        if ($dir = opendir($src)) {
            @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }
    }
    
    /**
     * Retourne le couple (largeur, hauteur) correspondant à l'encoffrage d'une image de dimension $w x $h dans $mw x
     * $mh.
     *
     * @param int $w  Largeur à traiter.
     * @param int $h  Hauteur à traiter.
     * @param int $mw Largeur du coffre.
     * @param int $mh Hauteur du coffre.
     *
     * @return array ( int , int )
     */
    public static function getImgSmallerSize($w, $h, $mw, $mh)
    {
        if ($w <= $mw && $h <= $mh) {
            return [$w, $h];
        } elseif ($mw > 0 && $mh > 0 && $w > 0 && $h > 0) {
            $factor = $w / $h;
            $mfactor = $mw / $mh;
            if ($factor > $mfactor) {
                $mh = $h / ($w / $mw);
            } else {
                $mw = $w / ($h / $mh);
            }
            
            return [round($mw), round($mh)];
        }
    }
    
    /**
     * Retourne un tableau correspondant au remplissage du coffre de dimension $mw x $mh par une image de dimension $w
     * x $h.
     *
     * @param int $w  Largeur de l'image.
     * @param int $h  Hauteur de l'image.
     * @param int $mw Largeur du coffre.
     * @param int $mh Hauteur du coffre.
     *
     * @return array ( int : largeur, int : hauteur, int : capture_x, int : capture_y )
     */
    public static function getImgSrcSizeCroped($w, $h, $mw, $mh, $mode = 'center')
    {
        $ratio_src = $w / $h;
        $ratio_dst = $mw / $mh;
        
        if ($ratio_src == $ratio_dst) {
            $src_x = $src_y = 0;
            $bw = $w;
            $bh = $h;
        } // Largeur plus grande
        elseif ($ratio_src > $ratio_dst) {
            $bw = round($ratio_dst * $h);
            $src_x = round(($w - $bw) / 2);
            $bh = $h;
            $src_y = 0;
        } // Hauteur plus grande
        else {
            $bh = round(1 / $ratio_dst * $w);
            $src_y = round(($h - $bh) / 2);
            $bw = $w;
            $src_x = 0;
        }
        
        //Position du crop (centré par défaut)
        switch ((string) $mode) {
            case 'top left' :
                $src_x = 0;
                $src_y = 0;
                break;
            
            case 'top right' :
                $src_x = ($w - $bw);
                $src_y = 0;
                break;
            
            case 'bottom left' :
                $src_x = 0;
                $src_y = ($h - $bh);
                break;
            
            case 'bottom right' :
                $src_x = ($w - $bw);
                $src_y = ($h - $bh);
                break;
                
            case 'left' :
                $src_x = 0;
                break;
    
            case 'right' :
                $src_x = ($w - $bw);
                break;
    
            case 'top' :
                $src_y = 0;
                break;
            
            case 'bottom' :
                $src_y = ($h - $bh);
                break;
            
            case 'center' :
            default:
                break;
        }
        
        return [$bw, $bh, $src_x, $src_y];
    }
    
    /**
     * Créer une image redimensionnée en fonction des paramètres en entrée et retourne true en cas de succès, false
     * sinon.
     *
     * @param string $src_path
     * @param string $dst_path
     * @param int    $img_w
     * @param int    $img_h
     * @param int    $src_w
     * @param int    $src_h
     * @param int    $src_type
     * @param int    $src_x
     * @param int    $src_y
     * @param null   $quality
     *
     * @return bool
     */
    public static function imgResize(
        $src_path,
        $dst_path,
        $img_w,
        $img_h,
        $src_w = null,
        $src_h = null,
        $src_type = null,
        $src_x = 0,
        $src_y = 0,
        $quality = null
    ) {
        if (file_exists($src_path)) {
            if ($src_w === null || $src_h === null || $src_type === null) {
                list($src_w, $src_h, $src_type) = getimagesize($src_path);
            }
            
            if (($img_w == $src_w) && ($img_h == $src_h)) {
                if ((!$src_x) && (!$src_y)) {
                    if ($quality === null) {
                        return copy($src_path, $dst_path);
                    }
                }
            }
            
            $dst_source = ImageCreateTrueColor($img_w, $img_h);
            imagealphablending($dst_source, false);
            imagesavealpha($dst_source, true);
            
            $success = true;
            switch ($src_type) {
                case 1    :    // Cas du GIF
                    $img_source = ImageCreateFromGif($src_path);
                    ImageCopyResampled($dst_source, $img_source, 0, 0, $src_x, $src_y, $img_w, $img_h, $src_w, $src_h);
                    $success = ImageGif($dst_source, $dst_path);
                    imagedestroy($dst_source);
                    break;
                
                case 2    :    // Cas du JPG
                    $img_source = ImageCreateFromJpeg($src_path);
                    ImageCopyResampled($dst_source, $img_source, 0, 0, $src_x, $src_y, $img_w, $img_h, $src_w, $src_h);
                    $quality = $quality === null ? 100 : $quality;
                    $success = ImageJpeg($dst_source, $dst_path, $quality);
                    imagedestroy($dst_source);
                    break;
                
                case 3    :    // Cas du PNG
                    $img_source = ImageCreateFromPng($src_path);
                    ImageCopyResampled($dst_source, $img_source, 0, 0, $src_x, $src_y, $img_w, $img_h, $src_w, $src_h);
                    $success = ImagePng($dst_source, $dst_path, $quality);
                    imagedestroy($dst_source);
                    break;
            }
            
            return $success;
        }
    }
    
    /**
     *  Créer une replique avec rotation de l'image désignée par $src_path et la stocke en $dst_path.
     *
     * @param string $src_path
     * @param string $dst_path
     * @param float  $degrees
     * @param int    $bgd_color
     * @param int    $ignore_transparent
     * @param null   $quality
     *
     * @return bool
     */
    public static function imgRotate(
        $src_path,
        $dst_path,
        $degrees,
        $bgd_color = 0,
        $ignore_transparent = 0,
        $quality = null
    ) {
        if (file_exists($src_path)) {
            list($src_w, $src_h, $src_type) = getimagesize($src_path);
            
            $success = true;
            switch ($src_type) {
                case 1    :    // Cas du GIF
                    $img_source = ImageCreateFromGif($src_path);
                    $rotate = imagerotate($img_source, $degrees, $bgd_color);
                    $success = ImageGif($rotate, $dst_path);
                    
                    break;
                
                case 2    :    // Cas du JPG
                    $img_source = ImageCreateFromJpeg($src_path);
                    $rotate = imagerotate($img_source, $degrees, $bgd_color);
                    $quality = $quality === null ? 100 : $quality;
                    $success = ImageJpeg($rotate, $dst_path, $quality);
                    
                    break;
                
                case 3    :    // Cas du PNG
                    $img_source = ImageCreateFromPng($src_path);
                    $rotate = imagerotate($img_source, $degrees, $bgd_color);
                    $success = ImagePng($rotate, $dst_path, $quality);
                    break;
            }
            
            return $success;
        }
    }
    
    /**
     * Créer une replique grisée de l'image désignée par $src_path et la stocke en $dst_path.
     *
     * @param string $src_path
     * @param string $dst_path
     */
    public static function imgGray($src_path, $dst_path)
    {
        if (file_exists($src_path)) {
            list($img_w, $img_h, $type) = getImageSize($src_path);
            
            switch ($type) {
                case 1 : // GIF
                    $img_source = imagecreatefromgif($src_path);
                    break;
                
                case 2 : // JPG
                    $img_source = imagecreatefromjpeg($src_path);
                    break;
                
                case 3 : // PNG
                    $img_source = imagecreatefrompng($src_path);
                    break;
                
                default :
                    trigger_error('Acids::imgGrayCopy : Image must be a GIF, JPG, or PNG. Current is type "' . $type
                                  . '"', E_USER_ERROR);
                    break;
            }

            imagefilter($img_source, IMG_FILTER_GRAYSCALE);
            
            // copy pixel values to new file buffer
            $dst_source = ImageCreateTrueColor($img_w, $img_h);
            imagealphablending($dst_source, false);
            imagesavealpha($dst_source, true);
            
            imagecopy($dst_source, $img_source, 0, 0, 0, 0, $img_w, $img_h);
            
            switch ($type) {
                case 1 : // GIF
                    imagegif($dst_source, $dst_path);
                    break;
                
                case 2 : // JPG
                    imagejpeg($dst_source, $dst_path);
                    break;
                
                case 3 : // PNG
                    imagepng($dst_source, $dst_path);
                    break;
            }
            
            imagedestroy($dst_source);
            imagedestroy($img_source);
        }
    }
    
    /**
     * Centre l'image et remplit dans un espace coloré
     *
     * @param string $src_path
     * @param string $dst_path
     * @param int    $dst_w
     * @param int    $dst_h
     * @param string $color
     */
    public function fill($src_path, $dst_path, $dst_w, $dst_h, $color)
    {
        if (file_exists($src_path)) {
            list($img_w, $img_h, $type) = getImageSize($src_path);
            
            $margin_left = floor(($dst_w - $img_w) / 2);
            $margin_left = ($margin_left < 0) ? 0 : $margin_left;
            
            $margin_top = floor(($dst_h - $img_h) / 2);
            $margin_top = ($margin_top < 0) ? 0 : $margin_top;
            
            switch ($type) {
                case 1 : // GIF
                    $img_source = imagecreatefromgif($src_path);
                    break;
                
                case 2 : // JPG
                    $img_source = imagecreatefromjpeg($src_path);
                    break;
                
                case 3 : // PNG
                    $img_source = imagecreatefrompng($src_path);
                    break;
                
                default :
                    trigger_error('Acids::fill : Image must be a GIF, JPG, or PNG. Current is type "' . $type . '"',
                        E_USER_ERROR);
                    break;
            }
            
            $color[0] = isset($color[0]) ? $color[0] : 0;
            $color[1] = isset($color[1]) ? $color[1] : 0;
            $color[2] = isset($color[2]) ? $color[2] : 0;
            
            $transparent = false;
            if (($color[0] === false) && ($color[1] === false) && ($color[2] === false)) {
                $color[0] = $color[1] = $color[2] = 255;
                $transparent = in_array($type, [2, 3]);
            }
            
            // filling dst_source
            if ($transparent) {
                $dst_source = imagecreatetruecolor($dst_w, $dst_h);
                $cur_color = imagecolorallocatealpha($dst_source, $color[0], $color[1], $color[2], 127);
                imagefill($dst_source, 0, 0, $cur_color);
                imagesavealpha($dst_source, true);
            } else {
                $dst_source = imagecreatetruecolor($dst_w, $dst_h);
                $cur_color = ImageColorAllocate($dst_source, $color[0], $color[1], $color[2]);
                imagefill($dst_source, 0, 0, $cur_color);
            }
            
            $cur_source = imagecreatetruecolor($img_w, $img_h);
            imagefill($cur_source, 0, 0, $cur_color);
            
            imagecopyresampled($cur_source, $img_source, 0, 0, 0, 0, $img_w, $img_h, $img_w, $img_h);
            //imagecopymerge($cur_source,$img_source,0,0,0,0,$img_w,$img_h,100);
            
            //imagefill($cur_source,0,0,$cur_color);
            
            imagecopyresampled($dst_source, $cur_source, $margin_left, $margin_top, 0, 0, $img_w, $img_h, $img_w,
                $img_h);
            //imagecopymerge($dst_source,$cur_source,$margin_left,$margin_top,0,0,$img_w,$img_h,100);
            
            switch ($type) {
                case 1 : // GIF
                    imagegif($dst_source, $dst_path);
                    break;
                
                case 2 : // JPG
                    imagejpeg($dst_source, $dst_path);
                    break;
                
                case 3 : // PNG
                    imagepng($dst_source, $dst_path);
                    break;
            }
            
            imagedestroy($dst_source);
            imagedestroy($img_source);
            imagedestroy($cur_source);
        }
    }
    
    /**
     * Import array of Mime Types from url
     *
     * @param string $source_url
     *
     * @return array
     */
    public static function importMimeArray(
        $source_url = 'http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types'
    ) {
        $s = [];
        if ($mime = @explode("\n", @file_get_contents($source_url))) {
            foreach ($mime as $x) {
                if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1])
                    && ($c = count($out[1])) > 1) {
                    for ($i = 1; $i < $c; $i++) {
                        $s[$out[1][$i]] = $out[1][0];
                    }
                }
            }
        }
        
        return $s;
    }
}
