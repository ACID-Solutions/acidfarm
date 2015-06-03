<?php

class AcidRegistration {

	public static $url = 'http://10.11.1.61/plateforme-acidfarm/';

	public static $path = 'registration/';
	public static $file = 'maintenance.json';

	public static $upgrade_path = 'upgrade';

	public static $base_version_file = 'version.txt';

	public static $_datas = null;

	public static function datas($key=null,$def=null) {
		if (static::$_datas===null) {

			static::$_datas = array();

			if (file_exists(static::file())) {
				static::$_datas = json_decode(file_get_contents(static::file()),true);
			}
		}

		if ($key!==null) {
			return isset(static::$_datas[$key]) ? static::$_datas[$key] : $def;
		}

		return static::$_datas;
	}

	public static function url() {
		return static::$url;
	}

	public static function infoUrl() {
		return static::url().'rest/information/'.static::datas('id_client').'/'.static::datas('public').'/'.static::realversion();
	}

	public static function dlUrl($version=null) {
		$version = $version===null ? static::realversion() : $version;
		return static::url().'rest/download/'.static::datas('id_client').'/'.static::datas('public').'/'.$version;
	}

	public static function registerUrl() {
		return static::url().'rest/registration';
	}

	public static function file() {
		return SITE_PATH.static::$path.static::$file;
	}

	public static function subversionfile() {
		return SITE_PATH.'sys/'.static::$upgrade_path.'/cur_version.txt';
	}

	public static function subversion() {
		if (file_exists(static::subversionfile())) {
			return file_get_contents(static::subversionfile());
		}

		return '';
	}

	public static function version() {
		return static::datas('version');
	}

	public static function realversion() {
		$sb = static::subversion();
		$next = $sb ? '.'.$sb : '.000';
		return static::version().$next;
	}


	public static function executeRegistration($vals=null) {
		$vals = $vals===null ? $_POST : $vals;

		if (!empty($vals['registration']['allowed'])) {
			AcidRegistration::install($vals);
		}else{
			AcidRegistration::disable();
		}

	}


	public static function disable() {
		static::setJson(array('allowed'=>false));
	}

	public static function install($vals) {
		$fields = array();
		$fields_string = '';

		$acid_path = ACID_PATH;
		$version_path = SITE_PATH.static::$base_version_file;

		foreach ($vals['registration'] as $key =>$val) {	$fields[$key] = $val;}

		$fields['public'] = md5((rand(0,1000)*rand(0,1000)).(rand(0,1000)*rand(0,1000)).(rand(0,1000)*rand(0,1000)).(rand(0,1000)*rand(0,1000)));

		if (is_dir($acid_path)) {
			$fields['acid_path_code'] = static::md5Dir($acid_path);
		}

		if (file_exists($version_path)) {
			$fields['version'] = file_get_contents($version_path);
		}


		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.urlencode($value).'&'; }
		rtrim($fields_string, '&');

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, static::registerUrl());
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

		//execute post
		$result = curl_exec($ch);
		$return = json_decode($result,true);

		$return['init_request'] = $fields;
		$return['allowed'] = true;

		static::setJson($return);

		//close connection
		curl_close($ch);
	}

	public static function setJson($vals) {
		file_put_contents(static::file(),json_encode($vals));
	}

	public static function md5Dir($dir) {
		if (is_dir($dir)) {
			$sum = '';
			if ($tree = scandir($dir)) {
				foreach ($tree as $file) {
					if (!in_array($file, array('.','..'))) {
						$filepath = realpath($dir.'/'.$file);
						if (!is_link($filepath)) {
							if (is_dir($filepath)) {
								$sum.= static::md5Dir($filepath).'<br />';
							}else{
								$sum.= md5_file($filepath).'<br />';
							}
						}
					}
				}
			}
			return md5($sum);
		}
	}

}