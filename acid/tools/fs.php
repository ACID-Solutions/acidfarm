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
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Gestionnaire de Fichier et Ressources Fichier/Image
 * @package   Tool
 */
class AcidFs
{

    /**
     * Retourne l'extension du fichier en entrée ou false en cas d'échec.
     *
     * @param string $file_name
     *
     * @return string | bool
     */
    public static function getExtension($file_name) {
        if (!empty($file_name) && !strpos('.',$file_name)) {
    		$strsr = explode('.',$file_name);
    		$strs = array_reverse($strsr);
    		if ($strs[0] != 'gz') return strtolower($strs[0]);
    		elseif ($strs[1] == 'tar') return strtolower($strs[1].'.'.$strs[0]);
    		else return strtolower($strs[0]);
	    }else return false;
    }


    /**
     * Retourne le nom du fichier en entrée sans extension.
     *
     * @param string $file_name
     *
     * @return string
     */
    public static function removeExtension($file_name) {
		$name = $file_name;

    	if ($ext = self::getExtension($file_name)) {
    		$name = substr($name,0,(strlen('.'.$ext)*-1));
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
    public static function removeBasePath($file_name) {

    	if (strpos($file_name,Acid::get('url:system_lang'))===0) {
			return substr($file_name,strlen(Acid::get('url:system_lang')));
    	}

    	if (strpos($file_name,SITE_PATH.Acid::get('url:folder_lang'))===0) {
    		return substr($file_name,strlen(SITE_PATH.Acid::get('url:folder_lang')));
    	}

    	if (strpos($file_name,Acid::get('url:folder_lang'))===0) {
    		return substr($file_name,strlen(Acid::get('url:folder_lang')));
    	}


    	return $file_name;
    }

    /**
     * Retourne le couple (largeur, hauteur) correspondant à l'encoffrage d'une image de dimension $w x $h dans $mw x $mh.
     *
     * @param int $w Largeur à traiter.
     * @param int $h Hauteur à traiter.
     * @param int $mw Largeur du coffre.
     * @param int $mh Hauteur du coffre.
     *
     * @return array ( int , int )
     */
    public static function getImgSmallerSize($w,$h,$mw,$mh) {
        if ($w <= $mw && $h <= $mh) return array($w,$h);
    	elseif($mw > 0 && $mh > 0 && $w > 0 && $h > 0){
    		$factor = $w / $h;
    		$mfactor = $mw / $mh;
    		if ($factor > $mfactor) {
    			$mh = $h / ($w/$mw);
    		}
    		else {
    			$mw = $w / ($h/$mh);
    		}
    		return array(round($mw),round($mh));
    	}
    }


     /**
     * Retourne un tableau correspondant au remplissage du coffre de dimension $mw x $mh par une image de dimension $w x $h.
     *
     * @param int $w Largeur de l'image.
     * @param int $h Hauteur de l'image.
     * @param int $mw Largeur du coffre.
     * @param int $mh Hauteur du coffre.
     *
     * @return array ( int : largeur, int : hauteur, int : capture_x, int : capture_y )
     */
    public static function getImgSrcSizeCroped($w,$h,$mw,$mh) {

    	$ratio_src = $w/$h;
        $ratio_dst = $mw/$mh;

        if ($ratio_src == $ratio_dst) {
        	$src_x = $src_y = 0;
        	$bw = $w;
        	$bh = $h;
        }
        // Largeur plus grande
        elseif ($ratio_src > $ratio_dst) {
        	$bw = round($ratio_dst*$h);
        	$src_x = round(($w-$bw) / 2);
        	$bh = $h;
        	$src_y = 0;
        }

        // Hauteur plus grande
        else {
        	$bh = round(1/$ratio_dst*$w);
        	$src_y = round(($h-$bh)/2);
        	$bw = $w;
        	$src_x = 0;
        }


    	return array($bw,$bh,$src_x,$src_y);
    }



	/**
	 * Créer une image redimensionnée en fonction des paramètres en entrée et retourne true en cas de succès, false sinon.
	 *
	 * @param string $src_path
	 * @param string $dst_path
	 * @param int $img_w
	 * @param int $img_h
	 * @param int $src_w
	 * @param int $src_h
	 * @param int $src_type
	 * @param int $src_x
	 * @param int $src_y
	 *
	 * @return bool
	 */
	public static function imgResize($src_path,$dst_path,$img_w,$img_h,$src_w=null,$src_h=null,$src_type=null,$src_x=0,$src_y=0){
		if (file_exists($src_path)) {
		    if ($src_w === null || $src_h === null || $src_type === null) {
			    list($src_w,$src_h,$src_type) = getimagesize($src_path);
		    }

			$dst_source = ImageCreateTrueColor ($img_w,$img_h);
			imagealphablending($dst_source,false);
			imagesavealpha($dst_source,true);

			$success = true;
			switch($src_type) {
				case 1	:	// Cas du GIF
							$img_source = ImageCreateFromGif($src_path);
							ImageCopyResampled($dst_source, $img_source, 0,0,$src_x,$src_y, $img_w, $img_h, $src_w, $src_h);
							$success = ImageGif($dst_source, $dst_path);
							imagedestroy($dst_source);
							break;

				case 2	:	// Cas du JPG
							$img_source = ImageCreateFromJpeg($src_path);
							ImageCopyResampled($dst_source, $img_source, 0,0,$src_x,$src_y, $img_w, $img_h, $src_w, $src_h);
							$success = ImageJpeg($dst_source, $dst_path,100);
							imagedestroy($dst_source);
							break;

				case 3	:	// Cas du PNG
							$img_source = ImageCreateFromPng($src_path);
							ImageCopyResampled($dst_source, $img_source, 0,0,$src_x,$src_y, $img_w, $img_h, $src_w, $src_h);
							$success = ImagePng($dst_source, $dst_path);
							imagedestroy($dst_source);
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
	 *
	 */
	public static function imgGray($src_path,$dst_path) {
		if (file_exists($src_path)) {
			list($img_w,$img_h,$type) = getImageSize($src_path);

			switch($type) {
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
					trigger_error('Acids::imgGrayCopy : Image must be a GIF, JPG, or PNG. Current is type "'.$type.'"',E_USER_ERROR);
				break;
			}


			// convert to grayscale
			for ($y = 0; $y <$img_h; $y++) {
				for ($x = 0; $x <$img_w; $x++) {
					$rgb = imagecolorat($img_source, $x, $y);
					$red   = ($rgb >> 16) & 0xFF;
					$green = ($rgb >> 8)  & 0xFF;
					$blue  = $rgb & 0xFF;

					$gray = round(.299*$red + .587*$green + .114*$blue);

					// shift gray level to the left
					$grayR = $gray << 16;   // R: red
					$grayG = $gray << 8;    // G: green
					$grayB = $gray;         // B: blue

					// OR operation to compute gray value
					$grayColor = $grayR | $grayG | $grayB;

					// set the pixel color
					imagesetpixel ($img_source, $x, $y, $grayColor);
					imagecolorallocate ($img_source, $gray, $gray, $gray);
				}
			}

			// copy pixel values to new file buffer
			$dst_source = ImageCreateTrueColor($img_w, $img_h);
			imagealphablending($dst_source,false);
			imagesavealpha($dst_source,true);


			imagecopy($dst_source, $img_source, 0, 0, 0, 0, $img_w, $img_h);

			switch($type) {

				case 1 : // GIF
					imagegif($dst_source,$dst_path);
				break;

				case 2 : // JPG
					imagejpeg($dst_source,$dst_path);
				break;

				case 3 : // PNG
					imagepng($dst_source,$dst_path);
				break;

			}

			imagedestroy($dst_source);
			imagedestroy($img_source);
		}
	}

	/**
	 * Centre l'image et remplit dans un espace coloré
	 * @param string $src_path
	 * @param string $dst_path
	 * @param int $dst_w
	 * @param int $dst_h
	 * @param string $color
	 */
	public function fill($src_path,$dst_path,$dst_w,$dst_h,$color) {
		if (file_exists($src_path)) {
			list($img_w,$img_h,$type) = getImageSize($src_path);

			$margin_left = floor(($dst_w - $img_w) / 2);
			$margin_left = ($margin_left < 0) ? 0 : $margin_left ;

			$margin_top = floor(($dst_h - $img_h) / 2);
			$margin_top = ($margin_top < 0) ? 0 : $margin_top ;



			switch($type) {
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
					trigger_error('Acids::fill : Image must be a GIF, JPG, or PNG. Current is type "'.$type.'"',E_USER_ERROR);
				break;
			}

			$color[0] = isset($color[0]) ? $color[0] : 0;
			$color[1] = isset($color[1]) ? $color[1] : 0;
			$color[2] = isset($color[2]) ? $color[2] : 0;

			$transparent = false;
			if (($color[0]===false) && ($color[1]===false) && ($color[2]===false)) {
				$color[0] = $color[1] = $color[2] = 255;
				$transparent = in_array($type,array(2,3));
			}

			// filling dst_source
			if ($transparent) {
				$dst_source = imagecreatetruecolor  ($dst_w, $dst_h);
				$cur_color = imagecolorallocatealpha ($dst_source, $color[0], $color[1], $color[2], 127);
				imagefill($dst_source,0,0,$cur_color);
				imagesavealpha($dst_source, TRUE);
			}else{
				$dst_source = imagecreatetruecolor  ($dst_w, $dst_h);
				$cur_color = ImageColorAllocate ($dst_source, $color[0], $color[1], $color[2]);
				imagefill($dst_source,0,0,$cur_color);
			}

			$cur_source = imagecreatetruecolor  ($img_w, $img_h);
			imagefill($cur_source,0,0,$cur_color);

			imagecopyresampled($cur_source,$img_source,0,0,0,0,$img_w,$img_h,$img_w,$img_h);
			//imagecopymerge($cur_source,$img_source,0,0,0,0,$img_w,$img_h,100);

			//imagefill($cur_source,0,0,$cur_color);

			imagecopyresampled($dst_source,$cur_source,$margin_left,$margin_top,0,0,$img_w,$img_h,$img_w,$img_h);
			//imagecopymerge($dst_source,$cur_source,$margin_left,$margin_top,0,0,$img_w,$img_h,100);

			switch($type) {

				case 1 : // GIF
					imagegif($dst_source,$dst_path);
				break;

				case 2 : // JPG
					imagejpeg($dst_source,$dst_path);
				break;

				case 3 : // PNG
					imagepng($dst_source,$dst_path);
				break;

			}

			imagedestroy($dst_source);
			imagedestroy($img_source);
			imagedestroy($cur_source);

		}
	}
}
