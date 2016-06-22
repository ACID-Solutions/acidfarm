<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm/Vars
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Variante Image d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarWatermark extends AcidVarImage
{

    /**
     * Constructeur AcidVarImage
     * @param string $label étiquette
     * @param string $folder chemin vers le repertoire de stockage du fichier
     * @param array $config configuration
     * @param string $name nom du fichier (variables magiques : __NAME__, __ID__ )
     */
    public function __construct($label = 'AcidVarWatermark', $folder = null, $config = array(), $watermark=array(), $name = '')
    {
        parent::__construct($label, $folder, $config, $name);

        foreach ($watermark as $key => $mark) {
            $this->config['watermark'][$key] = $mark;
        }

    }

    public function applyMark($format) {
        Acid::log('IMAGE WM','WaterMark, format '.$format.' using '.$this->config['watermark'][$format]['url']);

        $mark_path = $this->config['watermark'][$format]['url'];
        $mark_density = $this->config['watermark'][$format]['opacity'];

        $p_ext = AcidFs::getExtension($mark_path);
        $i_ext = AcidFs::getExtension($this->getPath($format));

        $p_ext = ($p_ext == 'jpg')? 'jpeg' : $p_ext;
        $i_ext = ($i_ext == 'jpg')? 'jpeg' : $i_ext;



        list($protection_w,$protection_h,$protection_type) = getImageSize($mark_path);
        list($img_w,$img_h,$img_type) = getImageSize($this->getPath($format));

        $protection = ImageCreateTrueColor($protection_w, $protection_h);

        $fun = 'imagecreatefrom'.$p_ext;
        $protection = $fun($mark_path);

        $fun = 'imagecreatefrom'.$i_ext;
        $img = $fun($this->getPath($format));

        if ($protection_w > $img_w) {
            $pos_x=0;
            $ppos_x = ($protection_w - $img_w)/2;
        }else{
            $ppos_x=0;
            $pos_x = ($img_w - $protection_w)/2;
        }

        if ($protection_h > $img_h) {
            $pos_y=0;
            $ppos_y = ($protection_h - $img_h)/2;
        }else{
            $ppos_y=0;
            $pos_y = ($img_h - $protection_h)/2;
        }

        imagecopymerge($img,$protection,$pos_x,$pos_y,$ppos_x,$ppos_y,$protection_w,$protection_h,$mark_density);

        $fun = 'image'.$i_ext;
        $fun($img,$this->getPath($format));
        imagedestroy($protection);
        imagedestroy($img);
    }

    /**
     * Traite le processus de création/mise à jour des différents formats.
     *
     * @param array $format_filter
     */
    protected function formatProcess($format_filter=null) {

        $format_filter = (($format_filter !== null) && is_array($format_filter)) ? $format_filter : array_keys($this->config['format']);

        foreach ($this->config['format'] as $format => $elt) {
            if (in_array($format,$format_filter)) {
                $this->imgResize($format);
                if (!empty($elt['effect'])) {
                    foreach ($elt['effect'] as $effect) {
                        $this->effectProcess($effect,$format);
                    }
                }

                if (isset($this->config['watermark'][$format])) {
                    if ($format!='src') {
                        $this->applyMark($format);
                    }
                }
            }
        }

        if (isset($this->config['watermark']['src'])) {
            $this->applyMark('src');
        }

    }

}