<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Traduction
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 * Class Lang
 * @package   Acidfarm\Traduction
 */
class Lang {

	/**
	 * Retourne l'url courante traduite pour une autre langue
	 * @param string $lang code langue
	 * @param string $nav tableau representatif de l'url à traduire
	 * @return string
	 */
	public static function langUrl($lang,$nav=null) {
		$nav = ($nav === null) ? $GLOBALS['nav'] : $nav;
		$nav[0] = ($nav[0] == 'index') ? '' : $nav[0];
		if (($key = AcidRouter::searchKey($nav[0]))) {
			$nav[0] = AcidRouter::getKey($key,$lang);
		}elseif ($object = Acid::get('tmp_current_object')) {
			return Route::buildUrl($object->checkTbl(),$object->getVals(),false,$lang);
		}
		return Acid::get('url:folder').$lang.'/'.implode('/',$nav);
	}

	/**
	 * Retourne le code html du drapeau de la langue en entrée
	 * @param string $lang code langue
	 * @param string $style style css
	 * @param array $attr attributs html
	 * @param bool true pour appliquer l'effet de selection
	 * @return string
	 */
	public static function langFlag($lang,$style='',$attr=array(),$sel=true) {
		$attribute='';
		foreach ($attr as $key=>$val) {
			if ($key != 'style') {
				$attribute .= ' '.$key.'="'.$val.'" ';
			}else{
				$style .= ' '.$val;
			}
		}

		$end = $sel ? 'sel':'unsel';

		$style = $style ? 'style="'.$style.'"' : '';
		$url_lang = Acid::themeUrl('img/langs/'.$lang.'_'.$end.'.png');
		return '<img '.$attribute.' src="'.$url_lang.'" alt="'.Acid::trad('lang_'.$lang).'" title="'.Acid::trad('lang_'.$lang).'" '.$style.' />' . "\n" ;
	}

	/**
	 * Retourne le tableau de traduction de la langue courante
	 * @param string $show
	 * @return string
	 */
	public static function debugTrad($show=true) {
		$content = '';
		$content .= '<div style="text-align:center;">' ;
		$content .= '<h2>Site</h2>' ;
		$content .= '<table style="border-collapse:collapse; margin:auto;" >' ;
		foreach (Acid::get('trad','lang') as $key => $val) {
			$content .= '<tr><th style=" background-color:#CCCCCC; text-align:left; border:1px solid; padding:1px 2px; color:#000000;">'.htmlspecialchars($key).'</th><td style="text-align:left; padding:1px 2px; border:1px solid; background-color:#EFEFEF; color:#000000; ">'.htmlspecialchars($val).'</td></tr>';
		}
		$content .= '</table>' ;

		foreach (Acid::get('mod','lang') as $table => $trads) {
			$content .= '<h2>Module '.$table.'</h2>' ;
			$content .= '<table style="border-collapse:collapse; margin:auto;" >' ;
			foreach ($trads as $key => $val) {
				$content .= '<tr><th style=" background-color:#CCCCCC; text-align:left; border:1px solid; padding:1px 2px; color:#000000;">'.htmlspecialchars($key).'</th><td style="text-align:left; padding:1px 2px; border:1px solid; background-color:#EFEFEF; color:#000000; ">'.htmlspecialchars($val).'</td></tr>';
			}
			$content .= '</table>' ;
		}
		$content .= '</div>';

		if ($show) {
			echo $content;
		}

		return $content;
	}

	/**
	 * Retourne le tableau de traduction des routers
	 * @param string $show
	 * @return string
	 */
	public static function debugRouterTrad($show=true) {
		$content = '';
		$content .= '<div style="text-align:center;">' ;

		foreach (Acid::get('router','lang') as $key => $tab) {
			$content .= '<h2>'.$key.'</h2>' ;

			$content .= '<table style="border-collapse:collapse; margin:auto;" >' ;
			foreach ($tab as $lang => $values) {
				foreach ($values as $vk =>$vv) {
					$content .= '<tr><th style=" background-color:#CCCCCC; text-align:left; border:1px solid; padding:1px 2px; color:#000000;">'.htmlspecialchars($vk).' '.htmlspecialchars($lang).'</th><td style="text-align:left; padding:1px 2px; border:1px solid; background-color:#EFEFEF; color:#000000; ">'.htmlspecialchars($vv).'</td></tr>';

				}
			}
			$content .= '</table>' ;
		}
		$content .= '</div>' ;

		if ($show) {
			echo $content;
		}

		return $content;
	}

	/**
	 * Retourne la traduction pour la langue courante de la clé en entrée ou false si la clé n'existe pas
	 * @param string $key identifiant
	 * @return mixed
	 */
	public static function getKey($key) {
		if ($res=self::getRouterKey($key)) {
			return $res;
		}

		if (Conf::exists('keys:'.Acid::get('lang:current').':'.$key)) {
			return Conf::get('keys:'.Acid::get('lang:current').':'.$key);
		}elseif (Conf::exists('keys:'.Acid::get('lang:default').':'.$key)) {
			return Conf::get('keys:'.Acid::get('lang:default').':'.$key);
		}

		return false;
	}

	/**
	 * Retourne le titre traduit  pour la langue courante associé à la clé en entrée ou false si la clé n'existe pas
	 * @param string $key identifiant
	 * @return mixed
	 */
	public static function getName($key) {
		if ($res=AcidRouter::getName($key)) {
			return $res;
		}

		if (Conf::exists('name:'.Acid::get('lang:current').':'.$key)) {
			return Conf::get('name:'.Acid::get('lang:current').':'.$key);
		}elseif (Conf::exists('name:'.Acid::get('lang:default').':'.$key)) {
			return Conf::get('name:'.Acid::get('lang:default').':'.$key);
		}

		return false;
	}

	/**
	 * Retourne la traduction pour la langue courante associé à la clé de routage en entrée ou false si la clé n'existe pas
	 * @param string $key identifiant de routage
	 * @return mixed
	 */
	public static function getRouterKey($key) {
		$lang = (AcidRouter::getCurrentLang()==='')?Acid::get('lang:default'):AcidRouter::getCurrentLang();

		if (Acid::exists('router:'.$key.':'.$lang.':key','lang')) {
			return Acid::get('router:'.$key.':'.$lang.':key','lang');
		}


		return false;
	}

	/**
	 * Change de langue en intégrant un rollback
	 *
	 * @param $lang
	 * @param bool|true $change_current si true, altère la valeur Acid::get('lang;current')
	 */
	public static function switchTo($lang,$change_current=true) {
		Acid::save(null,'lang');
		if($change_current) {
			Acid::save('lang:current','acid');
			Acid::set('lang:current',$lang);
			Acid::save('url:folder_lang','acid');
			Acid::set('url:folder_lang', ( Acid::get('lang:use_nav_0') ? (Acid::get('url:folder').$lang.'/') : Acid::get('url:folder')) );
			Acid::save('url:system_lang','acid');
			Acid::set('url:system_lang',( Acid::get('lang:use_nav_0') ? (Acid::get('url:system').$lang.'/'):Acid::get('url:system')) );
		}

		$GLOBALS['lang'] = array();
		Lang::loadLang($lang);
	}

	/**
	 * Remonte dans l'historique de langue
	 *
	 * @param bool|true $change_current si true, altère la valeur Acid::get('lang;current')
	 */
	public static function rollback($change_current=true) {
		Acid::rollback(null,'lang');
		if($change_current) {
			Acid::rollback('lang:current','acid');
			Acid::rollback('url:folder_lang','acid');
			Acid::rollback('url:system_lang','acid');
		}

	}

	/**
	 * Charge les fichiers de langue
	 * @param string $lang identifiant de langue
	 */
	public static function loadLang($language=null) {

		global $lang;

		$language = $language===null ? Acid::get('lang:default') : $language;

		$acid_lang_path 	 =  ACID_PATH . 'langs/'.$language.'.php';
		$acid_mod_lang_path  =  ACID_PATH . 'langs/module_'.$language.'.php';
		$router_lang_path 	 =  SITE_PATH . 'sys/langs/router/lang_router.php';
		$site_lang_path 	 =  SITE_PATH . 'sys/langs/'.$language.'.php';
		$mod_lang_path  	 =  SITE_PATH . 'sys/langs/module_'.$language.'.php';
		$override_lang_path  =  SITE_PATH . 'sys/langs/override_'.$language.'.php';

		if ( file_exists($router_lang_path) ) {
			require($router_lang_path);
		}

		if ( file_exists($acid_lang_path) ) {
			require($acid_lang_path);
		}

		if ( file_exists($acid_mod_lang_path) ) {
			require($acid_mod_lang_path);
		}

		if ( file_exists($site_lang_path) ) {
			require($site_lang_path);
		}

		if ( file_exists($mod_lang_path) ) {
			require($mod_lang_path);
		}

		if ( file_exists($override_lang_path) ) {
			require($override_lang_path);
		}

	}

}