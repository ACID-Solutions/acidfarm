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
 * @since     Version 0.6
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Class AcidCookie
 */
class AcidCookie {

	/**
	 * Setter de cookie
	 * @param $name
	 * @param null $value
	 * @param null $expire
	 * @param null $path
	 * @param null $domain
	 * @param null $secure
	 * @param null $httponly
	 */
	public static function setcookie ($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null) {
		$domain = is_array($domain) ? $domain : array($domain);

		if (Acid::get('cookie:dyndomain') && !empty($_SERVER['HTTP_HOST'])) {
			if (!in_array($_SERVER['HTTP_HOST'],$domain)) {
				$domain[] = $_SERVER['HTTP_HOST'];
			}
		}

		foreach ($domain as $subdomain) {
			setcookie($name,$value,$expire,$path,$subdomain,$secure,$httponly);
		}
	}

	/**
	 * Killer de cookie
	 * @param $name
	 * @param null $path
	 * @param null $domain
	 * @param null $secure
	 * @param null $httponly
	 */
	public static function unsetcookie ($name, $path = null, $domain = null, $secure = null, $httponly = null) {

		self::setcookie($name, null, -1, $path, $domain, $secure, $httponly);
		unset($_COOKIE[$name]);

	}
}