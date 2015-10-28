<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model / View
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Outils de templatage
 * @package   Model / View
 */
class MyTemplate extends AcidTemplate {


	/**
	 * Retourne le header du template
	 */
	public function __construct() {
		parent::__construct();
		$this->setDoctype(self::HTML_STD);
	}

	/**
	 * Retourne le header du template
	 */
	public function getHeader() {
		$v = array();
		return Acid::tpl('header.tpl',$v,$this);
	}


	/**
	 * Retourne le footer du template
	 */
	public function getFooter() {
		$v =	array();
		$stats =  '<div id="stats_content">'. Acid::executeTpl(SITE_PATH . 'sys/stats/stats.tpl') . '</div>';
		return Acid::tpl('footer.tpl',$v,$this).$stats;
	}


	/**
	 * Retourne le corps  de la pop-in de dialogue du template
	 */
	public function getDialog() {
		if (!Acid::get('session:enable')) {
			return '';
		}

		$dialog_banner = AcidDialog::getFiltredDialog(false,array('banner'));
		$dialog = AcidDialog::getDialog(array('banner'));

		if (empty($dialog)) {
			$popup =  '';
		} else {
			$popup =  Acid::tpl('screens/dialog_popup.tpl',array('dialog'=>$dialog));
		}

		if (empty($dialog_banner)) {
			$banner = '';
		}else{
			$banner = '';
			foreach ($dialog_banner as $key => $bantab) {
				if($bantab) {
					foreach ($bantab as $ban) {
						$banner .= '<span class="banner_' . $key . '">' . $ban . '</span>' . '<br />' . "\n";
					}
				}
			}

			$banner = Acid::tpl('screens/dialog_banner.tpl',array('dialog'=>$banner));
		}

		return $banner . $popup;
	}

	/**
	 * Retourne le corps de la pop-in du template
	 */
	public function getCookieWarning() {
		if (Acid::get('session:enable')) {
			if (empty($_COOKIE['cookie_warning'])) {
				AcidCookie::setcookie('cookie_warning',1,(time()+60*60*24*365));
				return Acid::tpl('screens/notification.tpl',array('ident'=>'cookie','content'=>Acid::trad('cookie_legacy')));
			}
		}
	}

	/**
	 * Retourne le corps de la pop-in du template
	 */
	public function getBwin() {
		//return Acid::tpl('screens/bwin.tpl');
		return '';
	}

	/**
	 * Renvoie le contenu du stop.tpl, lequel contient le JavaScript
	 * à mettre en fin de page.
	 */
	public function callStop(){
		return Acid::tpl('stop.tpl');
	}

	/**
	 * Renvoie le contenu du admin/stop.tpl, lequel contient le JavaScript
	 * à mettre en fin de page.
	 */
	public function callAdminStop(){
		return Acid::tpl('admin/stop.tpl');
	}

	/**
	 * Active le plugin tiny_mce pour les contenus dynamiques
	 */
	public function tinyMCE() {

		if (isset($GLOBALS['acid']['tinymce']['active'])) {

			if (isset($GLOBALS['acid']['tinymce']['popup'])) {
				//$this->addJS(Acid::get('url:folder').'js/tiny_mce/tiny_mce_popup.js');
				$this->addJS(Acid::get('url:folder').'js/tiny_mce_417/tiny_mce_popup.js');
			}

			//$this->addJS(Acid::get('url:folder').'js/tiny_mce/tiny_mce.js');
			$this->addJS(Acid::get('url:folder').'js/tiny_mce_417/tinymce.min.js');
			if (file_exists(SITE_PATH.'js/tiny_mce_417/langs/'.Acid::get('lang:current').'.js')){
				$this->addJS(Acid::get('url:folder').'js/tiny_mce_417/langs/'.Acid::get('lang:current').'.js');
			}

			$ids = isset($GLOBALS['acid']['tinymce']['ids']) ? $GLOBALS['acid']['tinymce']['ids'] : array();
			//$tpl = isset($GLOBALS['acid']['tinymce']['tpl']) ? $GLOBALS['acid']['tinymce']['tpl'] : 'tools/deprecated/tiny-mce.tpl';
			$tpl = isset($GLOBALS['acid']['tinymce']['tpl']) ? $GLOBALS['acid']['tinymce']['tpl'] : 'tools/tiny-mce-417.tpl';
			$my_js = Acid::tpl($tpl,array('ids'=>$ids),$this);

			$this->add( $my_js . "\n" );

		}

	}


	/**
	 * Active le plugin plupload pour l'upload de fichier
	 */
	public function plupload() {

		if(Conf::exist('plupload:active')) {
			$this->addJS(Acid::get('url:folder').'js/plupload/plupload.js');
			$this->addJS(Acid::get('url:folder').'js/plupload/plupload.html5.js');
			$this->addJS(Acid::get('url:folder').'js/plupload/plupload.flash.js');

			$this->addJS(Acid::get('url:folder').'js/plupload/i18n/'.Acid::get('lang:current').'.js');

			$this->addJS(Acid::get('url:folder').'js/plupload/jquery.plupload.queue/jquery.plupload.queue.js');
			$this->addCSS(Acid::get('url:folder').'js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css');

			$ids = Conf::exist('plupload:ids') ? Conf::get('plupload:ids') : array();
			$my_js = Acid::tpl('tools/plupload.tpl', array('ids'=>$ids), $this);

			$this->add($my_js . "\n");
		}

	}

	/**
	 * Active le plugin plupload pour l'upload de fichier
	 */
	public function jQuery() {

		$this->addJS(Acid::get('url:folder').'js/jquery-1.11.1.min.js');
		$this->addJS(Acid::get('url:folder').'js/jquery.textchange.min.js');

	}

	/**
	 * Active le plugin UI pour les contenus dynamiques
	 */
	public function jQueryUI() {

		if (!empty($GLOBALS['datepicker'])) {
			$this->addCSS(Acid::themeUrl('css/jquery-ui.css'));
			$this->addJS(Acid::get('url:folder').'js/jquery-ui.min.js');
			$this->addJS(Acid::get('url:folder').'js/datepicker/timepicker.js');
			$this->addJS(Acid::get('url:folder').'js/datepicker/'.Acid::get('lang:current').'.js');
		}

	}

	/**
	 * Active le plugin plupload pour l'upload de fichier
	 */
	public function jQueryLightBox() {

		//$this->addJS(Acid::get('url:folder').'js/jquery.lightbox-0.5.min.js');
		//$this->addCss(Acid::themeUrl('css/jquery.lightbox-0.5.css'));
		$this->addJS(Acid::get('url:folder').'js/mpopup/jquery.magnific-popup.min.js');
		$this->addCss(Acid::get('url:folder').'js/mpopup/magnific-popup.css');

	}


	/**
	 * Définit les variables d'environnement Acid en javascript
	 */
	public function jsVars() {
		$js_path = 	'<script type="text/javascript">' .  "\n" .
				'<!--' . "\n" .
				'	url_base = "'.Acid::get('url:folder').'";' . "\n" .
				'	url_ajax = "'.Acid::get('url:ajax').'";' . "\n" .
				'	url_upload = "'.Acid::get('url:upload').'";' . "\n" .
				'	url_img = "'.Acid::get('url:img').'";' . "\n" .
				'	url_theme = "'.Acid::get('url:theme').'";' . "\n" .
				'	acid_cur_lang = "'.Acid::get('lang:current').'";' . "\n" .
				'	acid_def_lang = "'.Acid::get('lang:default').'";' . "\n" .
				'-->' . "\n" .
				'</script>' . "\n" .
				'';

		return $js_path;
	}

	/**
	 * Fait appel aux dépendences globales à tous les thèmes
	 */
	public function dependencies () {
		$this->addJS(Acid::get('url:folder').'js/acid.js');

		$this->addFavicon('favicon.ico');
	}

	/**
	 * Template par défaut
	 */
	public function print_default () {

		include Acid::outPath('includes.php');

		include Acid::outPath();

		$this->output = $this->jsVars() . $this->output ;
		$this->output .= $this->callStop();

		$this->print_html();

	}

	/**
	 * Template par défaut
	 */
	public function print_siteadmin () {

		include Acid::outPath('siteadmin-includes.php');

		include Acid::outPath();

		$this->output = $this->jsVars() . $this->output ;
		$this->output .= $this->callAdminStop();

		$this->print_html();

	}

// 	/**
// 	 * Template de l'admin
// 	 */
// 	public function print_siteadmin () {
// 		global $tps_gen_page;

// 		$this->addCSS(Acid::themeUrl('css/admin.css'));
// 		$this->addCSS(Acid::themeUrl('css/admin-form.css'));

// 		$this->addCSS(Acid::themeUrl('css/dialog.css'));

// 		$this->jQuery();
// 		$this->jqueryUI();
// 		$this->tinyMCE();
// 		$this->plupload();

// 		$this->dependencies();


// 		$this->output =	$this->jsVars() .
// 		'' . $this->getDialog() . "\n" .
// 		'	<div id="site" class="admin">' . "\n" .
// 		$this->output .
// 		'	</div>' . "\n" .
// 		Acid::tpl('admin/stop.tpl') .
// 		'';

// 		$this->print_html();
// 	}


}