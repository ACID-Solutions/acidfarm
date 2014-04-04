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

		$stats =  '<div id="stats_content">'. file_get_contents(SITE_PATH . 'sys/stats.tpl') . '</div>';

		return Acid::tpl('footer.tpl',$v,$this).$stats;

	}


	/**
	 * Retourne le corps  de la pop-in de dialogue du template
	 */
	public function getDialog() {

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
				foreach($bantab as $ban) {
					$banner .= '<span class="banner_'.$key.'">'. $ban . '</span>' . '<br />' . "\n" ;
				}
			}

			$banner = Acid::tpl('screens/dialog_banner.tpl',array('dialog'=>$banner));
		}


		return $banner . $popup;
	}


	/**
	 * Retourne le corps de la pop-in du template
	 */
	public function getBwin() {
		//return Acid::tpl('screens/bwin.tpl');
		return '';
	}


	/**
	 * Active le plugin tiny_mce pour les contenus dynamiques
	 */
	public function tinyMCE() {
		if (isset($GLOBALS['tinymce']['active'])) {

			if (isset($GLOBALS['tinymce']['popup'])) {
				$this->addJS(Acid::get('url:folder').'js/tiny_mce/tiny_mce_popup.js');
			}

			$this->addJS(Acid::get('url:folder').'js/tiny_mce/tiny_mce.js');

			$ids = isset($GLOBALS['tinymce']['ids']) ? $GLOBALS['tinymce']['ids'] : array();

			$my_js = Acid::tpl('tools/tiny-mce.tpl',array('ids'=>$ids),$this);

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
			$this->addCss(Acid::get('url:folder').'js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css');

			$ids = Conf::exist('plupload:ids') ? Conf::get('plupload:ids') : array();
			$my_js = Acid::tpl('tools/plupload.tpl', array('ids'=>$ids), $this);

			$this->add($my_js . "\n");
		}
	}


	/**
	 * Renvoie le contenu du stop.tpl, lequel contient le JavaScript
	 * à mettre en fin de page.
	 */
	public function callStop(){
		return Acid::tpl('stop.tpl');
	}


	/**
	 * Template par défaut
	 */
	public function print_default () {
		$this->addCSS(Acid::get('url:css').Acid::get('css:theme').'.css');
	    $this->addCSS(Acid::get('url:css').Acid::get('css:dialog').'.css');

		$this->addJS(Acid::get('url:folder').'js/jquery-1.7.1.min.js');
		$this->addJS(Acid::get('url:folder').'js/jquery.textchange.min.js');
		$this->addJS(Acid::get('url:folder').'js/jquery.lightbox-0.5.min.js');

		if (isset($GLOBALS['datepicker'])) {
	    	$this->addCSS(Acid::get('url:css').'jquery-ui.css');
	    	$this->addJS(Acid::get('url:folder').'js/jquery-ui.min.js');
	    }

	    $this->addCss(Acid::get('url:css').'jquery.lightbox-0.5.css');
	    $this->addCss(Acid::get('url:css').'carousel.css');
	    $this->addJs(Acid::get('url:js').'carousel.js');

	    $this->addJS(Acid::get('url:folder').'js/acid.js');


		$this->addFavicon('favicon.ico');

		$js_path = 	'<script type="text/javascript">' .  "\n" .
					'<!--' . "\n" .
					'	url_base = "'.Acid::get('url:folder').'";' . "\n" .
					'	url_ajax = "'.Acid::get('url:ajax').'";' . "\n" .
					'	url_upload = "'.Acid::get('url:upload').'";' . "\n" .
					'	url_img = "'.Acid::get('url:img').'";' . "\n" .
					'	url_theme = "'.Acid::get('url:theme').'";' . "\n" .
					'-->' . "\n" .
					'</script>' . "\n" .
					'';

		$output = $js_path;

		include Acid::outPath();

		$this->output = $output ;
		$this->output .= $this->callStop();

		$this->print_html();
	}


	/**
	 * Template de l'admin
	 */
	public function print_siteadmin () {
        global $tps_gen_page;

		$this->addCSS(Acid::get('url:css').'admin.css');
		$this->addCSS(Acid::get('url:css').'admin-form.css');

		$this->addCSS(Acid::get('url:css').'dialog.css');

		$this->addJS(Acid::get('url:folder').'js/jquery-1.4.4.min.js');
		$this->addJS(Acid::get('url:folder').'js/jquery.textchange.min.js');

		$this->addJS(Acid::get('url:folder').'js/acid.js');

		if (isset($GLOBALS['datepicker'])) {
	    	$this->addCSS(Acid::get('url:css').'jquery-ui.css');
	    	$this->addJS(Acid::get('url:folder').'js/jquery-ui.min.js');
	    	$this->addJS(Acid::get('url:folder').'js/datepicker/'.Acid::get('lang:current').'.js');
	    }

	    $this->tinyMCE();
	    $this->plupload();

		$this->addFavicon('favicon.ico');
		$js_path =
					'<script type="text/javascript">' .  "\n" .
					'<!--' . "\n" .
					'	url_base = "'.Acid::get('url:folder').'";' . "\n" .
					'	url_ajax = "'.Acid::get('url:ajax').'";' . "\n" .
					'	url_upload = "'.Acid::get('url:upload').'";' . "\n" .
					'	url_img = "'.Acid::get('url:img').'";' . "\n" .
					'	url_theme = "'.Acid::get('url:theme').'";' . "\n" .
					'-->' . "\n" .
					'</script>' . "\n" .
					'';


		$this->output =	$js_path .
								'' . $this->getDialog() . "\n" .
        						'	<div id="site" class="admin">' . "\n" .
        								$this->output .
								'	</div>' . "\n" .
        						'';

		$this->print_html();
	}


}
