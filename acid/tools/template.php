<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Outil AcidTemplate, Gestionnaire de rendu HTML.
 * @package   Acidfarm\Tool
 */
class AcidTemplate {

	/**
	 * @var string doctype
	 */
	protected $doctype      = '';

	/**
	 * @var string Header others (favicon, ...)
	 */
	protected $head         = '';

	/**
	 * @var string Titre de la page
	 */
	protected $head_title   = '';

	/**
	 * @var string Favicon
	 */
	protected $head_favicon = '';

	/**
	 * @var array Liens vers fichiers CSS
	 */
	protected $head_css     = array();

	/**
	 * @var array Liens vers fichiers JS Combinés
	 */
	protected $head_css_combined     = array();

	/**
	 * @var array  Liens vers fichiers JavaScript
	 */
	protected $head_js      = array();

	/**
	 * @var array Liens vers fichiers JS Combinés
	 */
	protected $head_js_combined     = array();

	/**
	 * @var array  Liens vers les flux RSS
	 */
	protected $head_rss     = array();

	/**
	 * @var array Tableau d'attributs de <body>
	 */
	protected $body_attrs   = array();

	/**
	 * @var string Contenu de la page via $html en php
	 */
	protected $output  = '';

	/**
	 * @var string Contenu de la page
	 */
	protected $body_corpus  = '';

	const XHTML_10_STRICT =
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

	const XHTML_10_TRANSITIONAL =
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

	const XHTM_11 =
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';

	const HTML_STD =	'<!DOCTYPE html>';

	/**
	 * Constructeur AcidTemplate
	 * Attribue son Doctype et son Title à l'objet.
	 */
	public function __construct() {

		$this->setDoctype();
		$this->setTitle();

	}

	/**
	 * Attribue au template le doctype en entrée.
	 *
	 * @param string $doctype
	 */
	public function setDoctype($doctype='') {
		$this->doctype = $doctype;
	}

	/**
	 *  Attribue au template le titre renseigné en entrée .
	 *
	 * @param string $title
	 * @param bool $alone True si on ajoute ni préfixe, ni suffixe au titre.
	 */
	public function setTitle ($title=null,$alone=true) {

		if ($title === null) {
			$this->head_title = Acid::get('site:name');
		} else {
			if ($alone) {
				$this->head_title = $title;
			} else {
				$this->head_title = Acid::get('title:left') . $title . Acid::get('title:right');
			}
		}

	}

	/**
	 * Recupère le numéro de versioning
	 * @return mixed
	 */
	public static function versioningVal() {
		if (!Acid::get('versioning:val')) {
			if (Acid::get('versioning:file')) {
				if (file_exists(SITE_PATH.Acid::get('versioning:file'))) {
					$value = file_get_contents(SITE_PATH.Acid::get('versioning:file'));
					$value = $value ? AcidUrl::normalize($value) : 0;
					Acid::set('versioning:val',$value);
				}
			}
		}

		return Acid::get('versioning:val');
	}

	/**
	 * Ajoute la version à l'url si possible
	 * @param string $url
	 * @return string
	 */
	public static function versioningUrl($url) {
		$version = '';

		if (Acid::get('versioning:way')=='htaccess') {
			if ($version = self::versioningVal()) {
				if (strpos($url,'http')!==0) {
					$base_extension = AcidFs::getExtension($url);
					$available_ext =  Acid::get('versioning:ext') ? Acid::get('versioning:ext') : array('css','js');

					if (in_array($base_extension,$available_ext)) {
						$base_url = AcidFs::removeExtension($url);
						$base_tag = Acid::get('versioning:tag') ? Acid::get('versioning:tag') : '-__VERSION__';
						$base_url_new = $base_url.str_replace('__VERSION__',$version,$base_tag).'.'.$base_extension;

						return $base_url_new;
					}

					return $url;
				}
			}
		}

		if (Acid::get('versioning:path')) {
			if ($version = self::versioningVal()) {
				if (strpos($url,'http')!==0) {
					$base_url = AcidFs::removeBasePath($url);
					$base_url_new = str_replace('__VERSION__', $version, Acid::get('url:folder') . Acid::get('versioning:path'));
					if (realpath(SITE_PATH.$base_url)) {
						return $base_url_new . $base_url;
					}
				}
			}
		}

		if (strpos($url,'?')===false) {
			if ($version = self::versioningVal()) {
				$url .='?version='.$version;
			}
		}

		return $url;
	}

	/**
	 * Transforme une feuille de style php en css en fonction des configurations
	 */
	public function cssPrepare() {
		if (Acid::get('css:dynamic:active')) {
			$files = Acid::get('css:dynamic:files');
			$mode =  Acid::get('css:dynamic:mode');
			if ($files) {
				foreach ($files as $phpf) {

					//$config
					if (is_array($phpf)) {
						$to = isset($phpf['to']) ? $phpf['to'] : null;
						$from = isset($phpf['from']) ? $phpf['from'] : null;
						$vars = isset($phpf['vars']) ? $phpf['vars'] : array();
					}else{
						$to = null;
						$from = $phpf;
						$vars = array();
					}

					//execution
					if (file_exists($from)) {
						$cssf = $to ? $to : AcidFs::removeExtension($from).'.css';
						$has_css = file_exists($cssf);

						if ((!$has_css) || ($mode=='debug')) {
							$css = Acid::executeTpl($from,$vars);
							$handle = fopen($cssf, 'w+');
							flock($handle, LOCK_EX);
							fwrite($handle, $css);
							flock($handle, LOCK_UN);
							fclose($handle);
						}
					}
				}
			}
		}
	}

	/**
	 * Retourne le lien sass ou non en fonction du context
	 *
	 * @param $what
	 * @return string
	 */
	public static function sassUrl($what) {
		if (Acid::get('sass:enable')) {
			if (in_array(Acid::get('sass:mode'), array('dev','debug'))) {
				return Acid::get('url:css').'sass.php/sass/'.$what.'.scss';
			}
		}

		return  Acid::get('url:css').Acid::get('sass:path:compiled').$what.'.css';
	}

	/**
	 * Retourne true si le fichier doit être combiné
	 * @param $url
	 * @param string $type
	 * @return bool
	 */
	public static function canCombine($url,$type='css')  {
		return $url && Acid::get('compiler:enable') && (!Acid::get('compiler:'.$type.':disable')) && ((strpos($url,'http://')!==0) && (strpos($url,'https://')!==0));
	}

	/**
	 * Empile le fichier js s'il doit être combiné
	 * @param $url
	 * @return bool
	 */
	public function combineJs($url) {
		if ($this->canCombine($url,'js')) {
			$this->head_js_combined[] = AcidFs::removeBasePath($url);
			return true;
		}

		return  false;
	}

	/**
	 * Empile le fichier css s'il doit être combiné
	 * @param $url
	 * @return bool
	 */
	public function combineCss($url) {
		if ($this->canCombine($url,'css')) {
			$this->head_css_combined[] = AcidFs::removeBasePath($url);
			return true;
		}
		return  false;
	}

	/**
	 * Génère un fichier de combinaison en fonction des fichiers en entrée et retourne le chemin vers le fichier généré
	 * @param array $files
	 * @param string $type
	 * @return bool|string
	 */
	public function generateCombineFile($files=array(),$type='css') {

		if ($files) {

			$filename = Acid::get('compiler:name') ? Acid::get('compiler:name') : 'combine';
			$dir = Acid::get('compiler:' . $type . ':path') ? Acid::get('compiler:' . $type . ':path') : (Acid::themePath($type, null, false, true) . '/compiled/combine/');
			$name = AcidMinifier::fileName($files, $filename, $type);

			$dest_path = $dir . $name;

			if (!is_dir($dir)) {
				mkdir($dir);
			}

			$this->combineFiles($dest_path, $files, $type);

			return Acid::get('url:folder').AcidFs::removeBasePath($dest_path);
		}

		return false;
	}

	/**
	 * Combine les fichiers dans le fichier renseigné en entrée
	 * @param $dest_file
	 * @param array $files
	 * @param string $type
	 * @return bool
	 */
	public function combineFiles($dest_file,$files=array(),$type='css') {

		Acid::log('COMBINE',$_SERVER['REQUEST_URI'].' generating combined file '. $dest_file . ' with '.implode(',',$files));

		$dev_mode = Acid::get('compiler:mode') == 'dev';
		$compress = Acid::exists('compiler:'.$type.':compression') ? Acid::get('compiler:'.$type.':compression') : true;

		$expire_path = $dest_file . '.expire';
		$expiration_time = Acid::exists('compiler:expiration') ? Acid::get('compiler:expiration') : 60*60*24;

		$is_expired = true;
		if (!$dev_mode) {
			if (file_exists($expire_path)) {
				$expire = file_get_contents($expire_path);
				$is_expired = $expire > time();
			}
		}

		if ( ($is_expired) || (!file_exists($dest_file)) ) {
			$content = AcidMinifier::combineFromUrl($files, $type,$compress);
			file_put_contents($dest_file, $content);
			file_put_contents($expire_path, (time() + $expiration_time));
			return true;
		}

		return false;
	}

	/**
	 * Retourne l'url du fichier de combinaison CSS
	 * @param bool|true $versioning
	 * @return bool|string
	 */
	public function combineCssUrl($versioning=true) {
		if ($url = $this->generateCombineFile($this->head_css_combined,'css')) {
			return ($versioning ? static::versioningUrl($url) : $url);
		}

		return false;
	}

	/**
	 * Retourne l'url du fichier de combinaison JS
	 * @param bool|true $versioning
	 * @return bool|string
	 */
	public function combineJsUrl($versioning=true) {
		if ($url = $this->generateCombineFile($this->head_js_combined,'js')) {
			return ($versioning ? static::versioningUrl($url) : $url);
		}

		return false;
	}

	/**
	 * Associe une feuille de style au template.
	 *
	 * @param $url
	 * @param bool|true $versioning
	 */
	public function addCSS($url,$versioning=true) {
		if (!in_array($url,$this->head_css)) {
			$this->head_css[] = $url;

			if (!$this->combineCss($url)) {
				$this->head .= '	<link href="'.($versioning ? static::versioningUrl($url) : $url).'" rel="stylesheet" type="text/css"/>' . "\n";
			}
		}
	}

	/**
	 * Associe un favicon au template.
	 *
	 * @param string $url
	 */
	public function addFavicon($url) {
		$this->head_favicon = $url;
		$this->head .= 	"\n" .
		    			'	<link rel="shortcut icon" type="image/x-icon" href="'.Acid::get('url:folder').$this->head_favicon.'" />' . "\n";
	}

	/**
	 * Associe un document javascript au template.
	 *
	 * @param string $url
	 * @param bool|true $versioning
	 */
	public function addJS($url,$versioning=true) {
		if (!in_array($url,$this->head_js)) {
			$this->head_js[] = $url;
			if (!$this->combineJs($url)) {
				$this->head .= '	<script type="text/javascript" src="' . ($versioning ? static::versioningUrl($url) : $url) . '"></script>' . "\n";
			}
		}
	}

	/**
	 * Associe un flux RSS au template.
	 *
	 * @param string $title
	 * @param string $url
	 */
	public function addRss($title,$url) {
		$this->addInHead('<link rel="alternate" type="application/rss+xml" title="'.htmlentities($title).'" href="'.$url.'" />');
	}

	/**
	 * Ajoute le contenu en entrée dans le template.
	 *
	 * @param string $str
	 */
	public function addInHead($str) {
		$this->head .= "\t" . $str . "\n";
	}

	/**
	 * Définit les attributs du body
	 * @param array $tab ([attributs]=>[valeurs])
	 *
	 */
	public function setBodyAttrs($tab) {
		foreach ($tab as $name=>$value) {
			$this->body_attrs[$name] = $value;
		}
	}

	/**
	 * Ajoute un contenu au corps HTML du template
	 *
	 * @param string $str
	 */
	public function add ($str) {
		$this->output .= $str;
	}

	/**
	 * Retourne l'entête HTML du template
	 *
	 *
	 * @return string
	 */
	protected function getHead() {
		$doc_std = false;

		switch ($this->doctype) {
			case self::HTML_STD :
				$doc = '<meta charset="UTF-8">';
				$doc_std = true;
			break;

			default:
				$doc = '	<meta http-equiv="Content-Language" content="'.Acid::get('lang:current').'" />' . "\n" .
					   '	<meta http-equiv="Content-type" content="application/xhtml+xml; charset=UTF-8" />' ;
			break;
		}

		$output =   '<head>' . "\n" .
					$doc . "\n\n" .
					'	<title>'.$this->head_title.'</title>' . "\n\n" ;

		foreach (Acid::get('meta') as $key => $val) {
			if (!empty($val)) {
				if ($key === 'keywords') {
					$val = implode(', ', $val);
				}
				$output .= '	<meta name="'.$key.'" content="'.htmlspecialchars($val).'" />' . "\n";
			}
		}

		if (AcidUrl::requestURI() === Acid::get('url:folder')) {
			if (!$doc_std) {
				$output .= '	<meta name="identifier-url" content="'.Acid::get('url:system').'"/>' . "\n";
			}
		}

        if (Acid::get('canonical:url')) {
            $output .= '    <link rel="canonical" href="'.htmlspecialchars(Acid::get('canonical:url')).'" />' . "\n";
        }

        if (Acid::get('seo:prev_url')) {
            $output .= '    <link rel="prev" href="'.htmlspecialchars(Acid::get('seo:prev_url')).'" />' . "\n";
        }

        if (Acid::get('seo:next_url')) {
            $output .= '    <link rel="next" href="'.htmlspecialchars(Acid::get('seo:next_url')).'" />' . "\n";
        }



		$output .= "\n";

		if ($combine_css = $this->combineCssUrl()) {
			$output .= '	<link href="'.$combine_css.'" rel="stylesheet" type="text/css"/>' . "\n";
		}

		if ($combine_js = $this->combineJsUrl()) {
			$output .= '	<script type="text/javascript" src="' . $combine_js . '"></script>';
		}

		/*
		foreach ($this->head_css as $url) {
			$output .= '	<link href="'.$url.'" rel="stylesheet" type="text/css"/>' . "\n";
		}

		$output .= "\n";

		foreach ($this->head_js as $url) {
			$output .= '	<script type="text/javascript" src="'.$url.'"></script>' . "\n";
		}


		if (!empty($this->head_favicon)) {
			$output .= 	"\n" .
		    			'	<link rel="shortcut icon" type="image/x-icon" href="'.$acid['url']['folder'].$this->head_favicon.'" />' . "\n";
		}
		*/

		$output .=	"\n" .
					$this->head .
					'</head>' . "\n\n";

		return $output;
	}



	/**
	 * Retourne les attributs du Body sous leur forme HTML.
	 *
	 *
	 * @return string
	 */
	public function getBodyAttrs() {
		if ($this->body_attrs) {
			$output = '';
			foreach ($this->body_attrs as $name => $val) {
				$output .= ' ' . $name.'="'.$val.'"';
			}
			return $output;
		} else {
			return '';
		}
	}






	/**
	 * Retourne l'appel HTML d'un élément flash en fonction des paramètres en entrée.
	 *
	 *
	 * @param string $path Chemin vers le fichier Flash.
	 * @param string $width Largeur.
	 * @param string $height Hauteur.
	 * @param string $id_obj Identifiant HTML.
	 * @param array $flashvars Liste des variables Flash.
	 * @param string $alternative_text Texte alternatif.
	 * @param string $tabs Tabulation.
	 *
	 * @return string
	 */
	public static function getFlashComponent($path,$width,$height,$id_obj='',$flashvars=array(),$alternative_text='',$tabs='') {
		if (!empty($id_obj)) $id_object = 'id="'.$id_obj.'"';
		else $id_object = '';

		if(!empty($alternative_text)) {$alternative_text = $tabs . $alternative_text . "\n";}
		else {$alternative_text = '';}

		$fv = '';
		if ($flashvars && is_array($flashvars)) {
			foreach ($flashvars as $key=>$val) {
				$fv .= $key.'='.$val.'&amp;';
			}
			$fv = $tabs . '	<param name="flashvars" value="'.substr($fv,0,-5).'" />' . "\n";
		}

		$output = 	$tabs . '<object type="application/x-shockwave-flash" data="'.$path.'" width="'.$width.'" height="'.$height.'" '.$id_object.'>' . "\n" .
				$tabs . '	<param name="wmode" value="Transparent" />' . "\n" .
				$tabs . '	<param name="movie" value="'.$path.'" />' . "\n" .
				$tabs . '	<param name="quality" value="high" />' . "\n" .
				$tabs . '	<param name="menu" value="false" />' . "\n" .
				$tabs . $fv .
				$tabs . $alternative_text .
				$tabs . '</object>' . "\n";

		return $output;
	}





	// Méthodes d'affichage



	/**
	 * Affiche le template sous sa forme XML
	 */
	public function print_xml() {
		header("Content-Type: application/xml; charset=UTF-8");
		echo 	'<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo	$this->output;
	}

	/**
	 * Affiche le template sous sa forme Text
	 */
	public function print_text() {
		header("Content-Type: text/plain");
		echo $this->output;
	}

	/**
	 * Affiche le corps du template
	 */
	public function print_empty() {
		echo $this->output;
	}

	/**
	 * Affiche le corps du template
	 */
	public function print_default() {
		echo $this->print_empty();
	}

	/**
	 * Affiche le template sous sa forme HTML
	 */
	protected function print_html() {
		echo 		$this->doctype . "\n" .
					'<html xmlns="http://www.w3.org/1999/xhtml" ' .
						'xml:lang="'.Acid::get('lang:current').'" lang="'.Acid::get('lang:current').'">' . "\n" .

		$this->getHead() .

						'<body'.$this->getBodyAttrs().'>' . "\n" .
		$this->output .
						'</body>' . "\n" .
					'</html>' . "\n";
	}




	/**
	 * Affiche la page au format renseigné par $GLOBALS['acid']['out'].
	 *
	 * Cette méthode utilise la variable globale $GLOBALS['acid']['out']
	 */
	public function printPage() {
		if (!method_exists($this,'print_'.Acid::get('out'))) {
// 			Acid::set('out','empty');
// 			trigger_error('Le template spécifié n\'existe pas !',E_USER_WARNING);
			$this->print_default();
		} else {
			$this->{'print_'.Acid::get('out')}();
		}
	}


}
