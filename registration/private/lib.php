<?php

class AcidRegistration {

	public static $url = 'http://platform.acidfarm.net/';
	public static $api_version = 'v1';

	public static $path = 'registration/';
	public static $file = 'private/maintenance.json';
    public static $backup = 'private/backup/';

	public static $upgrade_path = 'upgrade';

	public static $base_version_file = 'version.txt';

	public static $_datas = null;

	public static function datas($key=null,$def=null) {
		if (static::$_datas===null) {

			static::$_datas = array();

			if (file_exists(static::file())) {
				static::$_datas = json_decode(file_get_contents(static::file()),true);
                if (!empty(static::$_datas['need_confirmation'])) {
                    if (static::$_datas['need_confirmation'] < (time()-300)) {
                        unlink(static::file());
                        static::$_datas = array();
                    }
                }

			}
		}

		if ($key!==null) {
			return isset(static::$_datas[$key]) ? static::$_datas[$key] : $def;
		}

		return static::$_datas;
	}

	public static function url() {
        if (Acid::get('url:registration')) {
            return Acid::get('url:registration');
        }

		return static::$url;
	}

	public static function infoUrl() {
		return static::url().'rest/'.static::$api_version.'/information/'.static::datas('id_client').'/'.static::datas('public').'/'.static::realversion();
	}

	public static function dlUrl($version=null) {
		$version = $version===null ? static::realversion() : $version;
		return static::url().'rest/'.static::$api_version.'/download/'.static::datas('id_client').'/'.static::datas('public').'/'.$version;
	}

	public static function registerUrl() {
		return static::url().'rest/'.static::$api_version.'/registration';
	}

    public static function postUrl() {
        return Acid::get('url:system').self::$path.'post.php';
    }

    public static function backUrl() {
        $url_base = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : Conf::get('url:admin');
        return Acid::get('url:system').AcidFs::removeBasePath($url_base);
    }

    public static function backupPath() {
        return SITE_PATH.static::$path.self::$backup;
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

    public static function executeConfirmation($vals=null) {
        Acid::log('REGISTRATION','Confirmation asked...');
        if (AcidRegistration::datas('need_confirmation')) {
            $return = $vals === null ? $_POST : $vals;
            $return['allowed'] = true;
            $return['need_confirmation'] = false;
            static::setJson($return);
            echo json_encode($return);
            exit();
        }
    }

    public static function manual($fields=null) {

        Acid::log('REGISTRATION','Manual mode...');

        $form = new AcidForm('post',self::registerUrl());
        $form->setFormParams(array('id'=>'registration_form','name'=>'registration_form'));
		if($fields) {
			foreach ($fields as $key => $value) {
				$form->addHidden('', $key, $value);
			}
		}
        $form->addHidden('','registration_mode','manual');
        $form->addHidden('','url_back',self::backUrl());
        $form->addHidden('','url_post',self::postUrl());
        $form->addSubmit('',Acid::trad('subscribe'),array('id'=>'registration_form_submit'));

        Acid::set('out','html');

        $js = '<script type="text/javascript">document.getElementById(\'registration_form_submit\').click();</script>';
        Conf::addToContent($form->html().$js);
        require SITE_PATH.'sys/stop.php';
    }


	public static function disable() {
        Acid::log('REGISTRATION','Running disable...');
		static::setJson(array('allowed'=>false));
	}

	public static function install($vals) {

        Acid::log('REGISTRATION','Running install...');

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

		if (true) {
		//if (!extension_loaded('curl')) {
            $fields['need_confirmation'] = time();
            static::setJson($fields);
			self::manual($fields);
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
        Acid::log('REGISTRATION','Setting values...');
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