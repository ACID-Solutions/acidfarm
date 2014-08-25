<?php
class AcidCookie {

	function setcookie ($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null) {

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


}