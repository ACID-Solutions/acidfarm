<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Registration
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Class AcidRegistration
 */
class AcidRegistration {

	/**
	 * @var string url vers la plateforme d'enregistrement
	 */
	public static $url = 'https://platform.acidfarm.net/';

	/**
	 * @var string dossier de version de l'api
	 */
	public static $api_version = 'v1';

	/**
	 * @var string chemin vers le dossier d'enregistrement
	 */
	public static $path = 'registration/';

	/**
	 * @var string chemin vers le fichier d'enregistrement
	 */
	public static $file = 'private/maintenance.json';

	/**
	 * @var string chemin vers le dossier d'auto-backup
	 */
	public static $backup = 'private/backup/';


	/**
	 * @var string chemin vers le dossier d'upgrade depuis le dossier sys/
	 */
	public static $upgrade_path = 'update/.system';

	/**
	 * @var string chemin vers le fichier de version principale
	 */
	public static $base_version_file = 'version.txt';

	/**
	 * @var null propriétés
	 */
	public static $_data = null;

	/**
	 * Retourne une propriété ou toutes les propriétés de l'objet
	 * @param null $key
	 * @param null $def
	 * @return null
	 */
	public static function data($key=null,$def=null) {
		if (static::$_data===null) {

			static::$_data = array();

			if (file_exists(static::file())) {
				static::$_data = json_decode(file_get_contents(static::file()),true);
                if (!empty(static::$_data['need_confirmation'])) {
                    if (static::$_data['need_confirmation'] < (time()-300)) {
                        unlink(static::file());
                        static::$_data = array();
                    }
                }

			}
		}

		if ($key!==null) {
			return isset(static::$_data[$key]) ? static::$_data[$key] : $def;
		}

		return static::$_data;
	}

	/**
	 * Retourne l'url de la plateforme d'enregistrement
	 * @return mixed|string
	 */
	public static function url() {
        if (Acid::get('url:registration')) {
            return Acid::get('url:registration');
        }

		return static::$url;
	}

	/**
	 * Retourne l'url api d'information spécifique au site
	 * @return string
	 */
	public static function infoUrl() {
		return static::url().'rest/'.static::$api_version.'/information/'.static::data('id_client').'/'.static::data('public').'/'.static::realversion();
	}

	/**
	 * Retourne l'url api de téléchargement spécifique au site
	 * @param null $version
	 * @return string
	 */
	public static function dlUrl($version=null) {
		$version = $version===null ? static::realversion() : $version;
		return static::url().'rest/'.static::$api_version.'/download/'.static::data('id_client').'/'.static::data('public').'/'.$version;
	}

	/**
	 * Retourne l'url api d'enregistrement
	 * @return string
	 */
	public static function registerUrl() {
		return static::url().'rest/'.static::$api_version.'/registration';
	}

	/**
	 * Retourne l'url de post traitement du site
	 * @return string
	 */
    public static function postUrl() {
        return Acid::get('url:system').self::$path.'post.php';
    }

	/**
	 * Retourne l'url de retour à fournir à la plateforme d'enregistrement
	 * @return string
	 */
    public static function backUrl() {
        $url_base = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : Conf::get('url:admin');
        return Acid::get('url:system').AcidFs::removeBasePath($url_base);
    }

	/**
	 * Retourne le chemin complet vers le dossier de backup
	 * @return string
	 */
    public static function backupPath() {
        return SITE_PATH.static::$path.self::$backup;
    }


	/**
	 * Retourne le chemin complet vers le fichier d'enregistrement
	 * @return string
	 */
	public static function file() {
		return SITE_PATH.static::$path.static::$file;
	}

	/**
	 * Retourne le chemin complet vers le fichier de sousversioning
	 * @return string
	 */
	public static function subversionfile() {
		return SITE_PATH.'sys/'.static::$upgrade_path.'/cur_version.txt';
	}

	/**
	 * Retourne la valeur de sousversioning
	 * @return string
	 */
	public static function subversion() {
		if (file_exists(static::subversionfile())) {
			return file_get_contents(static::subversionfile());
		}

		return '';
	}

	/**
	 * Retourne la version majeur du site
	 * @return null
	 */
	public static function version() {
		return static::data('version');
	}

	/**
	 * Retourne la version complète du site
	 * @return string
	 */
	public static function realversion() {
		$sb = static::subversion();
		$next = $sb ? '.'.$sb : '.000';
		return static::version().$next;
	}

	/**
	 * Effectue l'enregistrement
	 * @param null $vals
	 */
	public static function executeRegistration($vals=null) {
		$vals = $vals===null ? $_POST : $vals;

		if (!empty($vals['registration']['allowed'])) {
			AcidRegistration::install($vals);
		}else{
			AcidRegistration::disable();
		}
	}

	/**
	 * Confirme l'enregistrement
	 * @param null $vals
	 */
    public static function executeConfirmation($vals=null) {
        Acid::log('REGISTRATION','Confirmation asked...');
        if (AcidRegistration::data('need_confirmation')) {
            $return = $vals === null ? $_POST : $vals;
            $return['allowed'] = true;
            $return['need_confirmation'] = false;
            static::setJson($return);
            echo json_encode($return);
            exit();
        }
    }

	/**
	 * Formulaire d'enregistrement manuel
	 * @param null $fields
	 */
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


	/**
	 * Désactive l'enregistrement
	 */
	public static function disable() {
        Acid::log('REGISTRATION','Running disable...');
		static::setJson(array('allowed'=>false));
	}

	/**
	 * Processus d'enregistrement
	 * @param $vals
	 */
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

	/**
	 * Altère le fichier d'enregistrement
	 * @param $vals
	 */
	public static function setJson($vals) {
        Acid::log('REGISTRATION','Setting values...');
		file_put_contents(static::file(),json_encode($vals));
	}

	/**
	 * Checksum d'un dossier
	 * @param $dir
	 * @return string
	 */
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