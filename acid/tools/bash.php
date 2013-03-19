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
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Outils Bash en PHP
 * @package   Tool
 */
class AcidBash {

	/**
	 * Coloration bash 
	 * @param string $text : le text a colorer
	 * @param string $txtColor : couleur voulue (black,red,green,cyan,magenta,black...)
	 * @param string $bgColor : couleur de fond
	 * @param string $styleTxt : style du text (bold,underline,reverse,flashing)
	 *
	 * @return string A noter que certaines mises en forme ne fonctionnent pas sous tout les clients
	 */
	 public static function shColorText($text='', $txtColor='',$bgColor='',$styleTxt='none'){
		$__ESC = "\033";
		$__START = "[";
		$__END = "m";
		$__CLEAR = $__ESC."[2J";
		$__NORMAL = $__ESC."[0m";
		if($text === 'CLEAR') return $__NORMAL.$__CLEAR;
		if(empty($text) || !$text) return $__NORMAL;
		//Text color
		$aTextColor['black'] = 30;
		$aTextColor['red'] = 31;
		$aTextColor['green'] = 32;
		$aTextColor['yellow'] = 33;
		$aTextColor['blue'] = 34;
		$aTextColor['magenta'] = 35;
		$aTextColor['cyan'] = 36;
		$aTextColor['white'] = 37;
		//Background color
		$aBgColor['black'] = 40;
		$aBgColor['red'] = 41;
		$aBgColor['green'] = 42;
		$aBgColor['yellow'] = 43;
		$aBgColor['blue'] = 44;
		$aBgColor['magenta'] = 45;
		$aBgColor['cyan'] = 46;
		$aBgColor['white'] = 47;
		//style text
		$aStyle['none'] = 0; //normal
		$aStyle['bold'] = 1; //gras
		$aStyle['underline'] = 4; //souligné
		$aStyle['flashing'] = 5; //clignotant
		$aStyle['reverse'] = 7; //inversé
		$c = $__ESC.$__START;
		if($styleTxt && isset($aStyle[$styleTxt])) $a[] = $aStyle[$styleTxt];
		if($txtColor && isset($aTextColor[$txtColor])) $a[] = $aTextColor[$txtColor];
		if($bgColor && isset($aBgColor[$bgColor])) $a[] = $aBgColor[$bgColor];
		if(!is_array($a)) return $text;
		$c = $__ESC.$__START.join(';',$a).$__END;
		return $c.$text.$__NORMAL;
	}

	/**
	 * Permet de mettre en forme la police d'un texte par des balises
	 * ex : Ceci est un <c c=blue bg=white s=bold>TEST</c>
	 * 
	 * @param string $str
	 * 
	 * @return string
	 */
	public static function parseShColorTag($str){
		$tag = "/(<c[^>]*>)([^<]*)<\/c>/";
		$innerTag = "/([\w]+)=([\w]+)/";
		preg_match_all($tag,$str,$r);
		if(!is_array($r[1])) return $str;
		foreach($r[1] as $k => $v){
			preg_match_all($innerTag,$v,$r2);
			if(!is_array($r2[1])) return $str;
			$c = $bg = $s = false;
			while(list($i,$value)=each($r2[1])){
				switch($value){
					case 'c':
						$c = $r2[2][$i];
						break;
					case 'bg':
						$bg = $r2[2][$i];
						break;
					case 's':
						$s = $r2[2][$i];
						break;
				}
			}
			$string = shColorText($r[2][$k], $c,$bg,$s);
			$str = str_replace($r[0][$k],$string,$str);
		}
		return $str;
	}
}
?>