<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Model
 * @version   0.1
 * @since     Version 0.7
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Librairie de fonctions REST
 * @package   Acidfarm\Model
 */
class Rest {

	/**
	 * Retourne la valeur du digest
	 * @return Ambigous <boolean, unknown, string>
	 */
	public static function getDigest() {
		$digest = false;

	    // mod_php
	    if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
	        $digest = $_SERVER['PHP_AUTH_DIGEST'];
	    // most other servers
	    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
	         if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'digest')===0) {
	             $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
	         }
	    }

	    return $digest;
	}

	/**
	 * Parsing du digest
	 * @param $headerValue
	 * @return array|bool
	 */
	public static function parseHttpDigest( $headerValue ) {
		$needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1, 'opaque' => 1, 'realm'=>1);
		$data = array();
		$keys = implode('|', array_keys($needed_parts));
		preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $headerValue, $matches, PREG_SET_ORDER);
		foreach ( $matches as $m ) {
			$data[$m[1]] = $m[3] ? $m[3] : $m[4];
			unset($needed_parts[$m[1]]);
		}
		return $needed_parts ? false : $data;
	}

	/**
	 * Login command
	 * @param $realm
	 * @param $nonce
	 */
	public static function headerAuth($realm,$nonce,$session_id) {
		header('WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . $nonce . '",opaque="' . $session_id . '"');
	}

	/**
	 * Demande d'authentification digest
	 * @param $realm
	 * @param $nonce
	 */
	public static function requireLogin($realm,$nonce,$session_id,$datas=null) {
		self::headerAuth($realm,$nonce,$session_id);
		self::status401($datas);
	}

	/**
	 * Porcedure de log digest
	 * @param $realm
	 * @param $nonce
	 */
	public static function digestLogin($datas=array()) {

		$user = new User();

		if ($what = Rest::searchLoginType($datas['username'])) {

			$user->dbInitSearch(array($what=>$datas['username']));
			Acid::log('LOG',$what.'=>'.$datas['username']);


			$hash1 = md5($user->get($what).':'.Acid::get('rest:realm').':'.$user->get('password'));
			$hash2 = md5($_SERVER['REQUEST_METHOD'].':'.$datas['uri']);
			$require = md5($hash1.':'.$datas['nonce'].':'.$datas['nc'].':'.$datas['cnonce'].':'.$datas['qop'].':'.$hash2);
			Acid::log('DEBUG','comparing responses from server '.$require.' with request '.$datas['response']);

			if ($require == $datas['response'] ) {
				return $user;
			}

		}

		return false;
	}


	/**
	 * Recherche la credential d'authentification
	 * @param  $login
	 * @return number
	 */
	public static function searchLoginType($login) {
		$user = new User();
		$keys = $user->getConfig('identification');
		$keys = is_array($keys) ? $keys : array($keys);

		$count = 0;
		if($keys) {
			foreach ($keys as $key) {
				if ($user::dbCount(array(array($key, '=', $login)))) {
					return $key;
				}
			}
		}

	}

	/**
	 * Procédure d'Authentification
	 * Initialise / Gère les sessions
	 * @return boolean|object
	 */
	public static function authentification() {
		if ($digest = self::getDigest()) {
			if ($datas = self::parseHttpDigest($digest)) {

				Acid::log('DEBUG','digest found '.json_encode($datas));

				$restcookie = array(Acid::get('session:name')=>$datas['opaque']);
				$session = AcidSession::getInstance($restcookie,false);

				if (!empty($datas['username'])) {

					if (!AcidSession::get('auth_digest')) {
						AcidSession::set(array('auth_time'=>time(),'auth_digest'=>$datas));
					}

					if ($authuser = self::digestLogin($datas)) {
						$authuser->sessionMake(null,false,false);
						AcidSession::getInstance()->id_user = $authuser->getId();

						AcidSession::set(array('digest'=>$datas));

					}else{
						self::requireLogin(Acid::get('rest:realm'),Acid::get('rest:nonce'),$session->id);
						return false;
					}
					return AcidSession::getInstance();

				}elseif (Acid::get('rest:public_session:enable')) {
					return AcidSession::getInstance();
				}

			}
		}

		$restcookie = array(Acid::get('session:name')=>Acid::hash('rest'.$_SERVER['REMOTE_ADDR'] . microtime()));
		$session = AcidSession::getInstance($restcookie,false);

		self::requireLogin(Acid::get('rest:realm'),Acid::get('rest:nonce'),$session->id);
	}

	/**
	 * Reponse HTTP
	 * @param string $datas
	 * @param number $status
	 */
	public static function response($datas=null,$status=200) {
		$ct = (is_array($datas) || is_object($datas)) ? 'json' : 'text';
		http_response_code($status);
		header( $_SERVER['SERVER_PROTOCOL'] . ' ' .http_response_code() );
		header('Content-type: text/'.$ct.'; charset=UTF-8');
		if ($datas) {
			echo ($ct=='json') ? json_encode($datas) : $datas;
		}

		if (AcidSession::instanceExists()) {
			if (!AcidSession::getInstance()->in_db) {
				AcidSession::dbAdd();
			}
			include(ACID_PATH.'stop.php');
		}
		exit();

	}


	/**
	 * Reponse 400 Bad Request
	 * @param string $datas
	 */
	public static function status400($datas=null) {
		self::response($datas,400);
	}

	/**
	 * Reponse 401 Unauthorized
	 * @param string $datas
	 */
	public static function status401($datas=null) {
		self::response($datas,401);
	}

	/**
	 * Reponse 403 - Forbidden
	 * @param string $datas
	 */
	public static function status403($datas=null) {
		self::response($datas,403);
	}


	/**
	 * Reponse 404 Not Found
	 * @param string $datas
	 */
	public static function status404($datas=null) {
		self::response($datas,404);
	}

	/**
	 * Reponse 200 OK
	 * @param string $datas
	 */
	public static function status200($datas=null) {
		self::response($datas,200);
	}

	/**
	 * Reponse 201 Created
	 * @param string $datas
	 */
	public static function status201($datas=null) {
		self::response($datas,201);
	}

	/**
	 * Reponse 202
	 * @param string $datas
	 */
	public static function status202($datas=null) {
		self::response($datas,202);
	}

	/**
	 * Reponse 204 No Content
	 * @param string $datas
	 */
	public static function status204($datas=null) {
		self::response($datas,204);
	}

	/**
	 * Reponse 500 Internal Error
	 * @param string $datas
	 */
	public static function status500($datas=null) {
		self::response($datas,500);
	}


}