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
 * Variante "Chaîne de caractères" d'AcidVar
 * @package   Acidfarm/Vars
 */
class AcidVarString extends AcidVar {

    /**
     * Constructeur AcidVarString
     *
     * @param string $label
     * @param int $size
     * @param int $maxlength
     * @param string $def
     * @param string $regex
     * @param bool $force_def
     */
    public function __construct($label='AcidVarString',$size=20,$maxlength=255,$def='',$regex=null,$force_def=false) {

        parent::__construct($label,(string)$def,$regex,$force_def);

        // Infos sql
        $this->sql['type'] = 'varchar('.((int)$maxlength).')';

        // Infos form
        $this->setForm('text',array('size'=>(int)$size,'maxlength'=>(int)$maxlength));
        // Value
        //$this->setVal($val);
    }

    /**
     *  Assigne une chaîne de caractères à la variable
     * @param string $val
     */
    public function setVal($val) {
        return parent::setVal((string)$val);
    }

    /**
     *  Convertit une chaîne de caractères typée bbcode au format html.
     *
     *
     * @param string $text
     * @return string
     */
    public static function bbcode($text) {

        $text = preg_replace('`\[img\](.+?)\[/img\]`', '<img src="$1" alt="img" />', $text);
        $text = preg_replace('`\[url\](.+?)\[/url\]`', '<a href="$1">$1</a>', $text);
        $text = preg_replace('`\[url=(.+?)\](.+?)\[\/url\]`', '<a href=$1>$2</a>', $text);
        $text = preg_replace('`\[b\](.+?)\[\/b\]`', '<b>$1</b>', $text);
        $text = preg_replace('`\[i\](.+?)\[\/i\]`', '<i>$1</i>', $text);
        $text = preg_replace('`\[u\](.+?)\[\/u\]`', '<u>$1</u>', $text);

        $text = preg_replace('`\[code\](.+?)\[\/code\]`s', '<code>$1</code>', $text);
        $text = preg_replace('`\[quote\](.+?)\[\/quote\]`s', '<blockquote>$1</blockquote>', $text);
        $text = preg_replace('`\[quote=(.+?)\](.+?)\[\/quote\]`s', '<blockquote cite="$1">$2</blockquote>', $text);
        $text = preg_replace('`\[color=(.+?)\](.+?)\[\/color\]`s', '<span style="color:$1">$2</span>', $text);
        $text = nl2br($text);
        return $text;
    }

    /**
     *  Convertit une chaîne de caractères typée bbcode au format texte.
     *
     *
     * @param string $text
     * @return string
     */
    public static function stripbbcode($text) {

        $text = preg_replace('`\[img\](.+?)\[/img\]`', '$1', $text);
        $text = preg_replace('`\[url\](.+?)\[/url\]`', '$1', $text);
        $text = preg_replace('`\[url=(.+?)\](.+?)\[\/url\]`', '$2', $text);
        $text = preg_replace('`\[b\](.+?)\[\/b\]`', '$1', $text);
        $text = preg_replace('`\[i\](.+?)\[\/i\]`', '$1', $text);
        $text = preg_replace('`\[u\](.+?)\[\/u\]`', '$1', $text);

        $text = preg_replace('`\[code\](.+?)\[\/code\]`s', '$1', $text);
        $text = preg_replace('`\[quote\](.+?)\[\/quote\]`s', '$1', $text);
        $text = preg_replace('`\[quote=(.+?)\](.+?)\[\/quote\]`s', '$2', $text);
        $text = preg_replace('`\[color=(.+?)\](.+?)\[\/color\]`s', '$2', $text);

        return $text;
    }

    /**
     *  Abrège une chaîne de caractères.
     *
     *
     * @param string $string Chaîne en entrée.
     * @param int $length Longueur de la chaîne en sortie sans son suffixe.
     * @param string $end Suffixe de la chaîne en sortie.
     * @return string
     */
    public static function split($string, $length, $end=' ...'){

        $translate = self::entityTranslator();
        $string = str_replace(array_keys($translate), array_values($translate), $string);

        $string = utf8_decode($string);
        $string = strip_tags($string);
        $string = str_replace("\r\n",' ',$string);


        $string = html_entity_decode($string,null,'ISO-8859-1');
        $string = utf8_encode($string);

        while (strpos($string,'  ') !== false) {
            $string = str_replace('  ',' ',$string);
        }

        if ($length != 0 && strlen($string) > $length) {
            $length_backup = $length;
            while ($length > 0 && $string[$length] != ' ') {
                $length --;
            }
            if ($length == 0) {
                $length = $length_backup;
            }

            // $string = substr($string,0,$length);
            // $string = mb_substr($string,0,$length);

            /**
             * @see http://php.net/manual/fr/function.mb-substr.php#107698
             */
            $string = 	join("", array_slice(
                    preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY), 0, $length)
            );

            $string .= $end;
        }

        return $string;
    }

    /**
     *  Retourne un tableau de conversion de caractères spéciaux
     *
     * @return array()
     */
    public static function entityTranslator(){
        return array(

            "‚" => ",",
            "ƒ" => "f",
            "„" => ",,",
            "…" => "...",
            "†" => "T",
            "‡" => "I",
            "ˆ" => "^",
            "‰" => "L",
            "Š" => "Ŝ",
            "‹" => "‹",
            "Œ" => "OE",
            "‘" => "'",
            "’" => "'",
            "“" => '"',
            "”" => '"',
            "•" => "°",
            "–" => "_",
            "—" => "_",
            "˜" => "~",
            "™" => "TM",
            "š" => "ŝ",
            "›" => ">",
            "œ" => "oe",
            "Ÿ" => "Ÿ"

        );
    }

}
