<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model
 * @version   0.1
 * @since     Version 0.7
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Librairie de fonctions REST
 * @package   Model
 */
class Rest {

	public static function response($datas=null,$status=200) {
		$ct = (is_array($datas) || is_object($datas)) ? 'json' : 'text';
		http_response_code($status);
		header( $_SERVER['SERVER_PROTOCOL'] . ' ' .http_response_code() );
		header('Content-type: text/'.$ct.'; charset=UTF-8');
		if ($datas) {
			echo ($ct=='json') ? json_encode($datas) : $datas;
		}
		exit();
	}

	public static function status400($datas=null) {
		self::response($datas,400);
	}

	public static function status403($datas=null) {
		self::response($datas,403);
	}

	public static function status404($datas=null) {
		self::response($datas,404);
	}

	public static function status200($datas=null) {
		self::response($datas,200);
	}

	public static function status201($datas=null) {
		self::response($datas,201);
	}

	public static function status202($datas=null) {
		self::response($datas,202);
	}

	public static function status204($datas=null) {
		self::response($datas,204);
	}

	public static function status500($datas=null) {
		self::response($datas,500);
	}

	public static function parseHttpDigest( $headerValue ) {
		$needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));
		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $headerValue, $matches, PREG_SET_ORDER);
		foreach ( $matches as $m ) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}
		return $needed_parts ? false : $data;
	}


}