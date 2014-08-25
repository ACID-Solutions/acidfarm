<?php
class AcidCookie {

	function setcookie ($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null) {

		$domain = is_array($domain) ? $domain : array($domain);

		foreach ($domain as $subdomain) {
			setcookie($name,$value,$expire,$path,$subdomain,$secure,$httponly);
		}

	}


}