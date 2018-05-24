<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Model
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Outils d'assistance dans le contexte AJAX
 * @package   Acidfarm\Model
 */
class Ajax {
	
	/**
	 * Retourne vrai si on est dans un contexte AJAX
	 * @return bool
	 */
	public static function isActive() {
		return !Acid::isEmpty('ajax:active');
	}
	
	/**
	 * Active le contexte AJAX
	 */
	public static function enableAjax() {
		Acid::set('ajax:active',true);
	}
	
	/**
	 * Desactive le contexte AJAX
	 */
	public static function disableAjax() {
		Acid::set('ajax:active',false);
	}
	
	/**
	 * Définit une page de redirection au contexte AJAX
	 */
	public static function setNextPage() {
		$next_page = isset($_GET['next']) ? $_GET['next'] : ( isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
		Acid::set('ajax:next_page',$next_page);
	}
	
	/**
	 * Retourne la page de redirection du contexte AJAX
	 */
	public static function getNextPage() {
		return Acid::get('ajax:next_page');
	}

	/**
	 * Configure le module en utilisant la page de redirection du contexte AJAX
	 * @param object $module AcidModule
	 * @param string $do type d'action 
	 */
	public static function setNextPageConfig(&$module,$do='default') {
		$module->setConfig('admin:'.$do.':action',self::getNextPage());
		$module->setConfig('admin:'.$do.':next_page',self::getNextPage());
	}
	
	
	/**
	 * Définit la valeur de succès du contexte AJAX
	 * @param bool $bool valeur de succès
	 */
	public static function setSuccess($bool) {
		Acid::set('ajax:success',$bool);
	}
	
	/**
	 * Retourne la valeur de succès du contexte AJAX
	 * @return bool
	 */
	public static function getSuccess() {
		return Acid::exists('ajax:success') ?  Acid::get('ajax:success') : true;
	}
	
	/**
	 * Ajoute des données au contexte AJAX
	 * @param mixed $vals les données
	 */
	public static function setDatas($vals) {
		Acid::set('ajax:datas',$vals);
	}
	
	/**
	 * Retourne les données du contexte AJAX
	 * @return mixed
	 */
	public static function getDatas() {
		return Acid::exists('ajax:datas') ?  Acid::get('ajax:datas') : array();
	}
	
	
	/**
	 * Définit le code javascript du contexte AJAX
	 * @param string $js code javascript
	 */
	public static function addJs($js) {
		$cur_js = self::getJsAjax() ;
		$new_js = $cur_js ? ($cur_js. "\n" .$js) : $js ;
		Acid::set('ajax:js',$new_js);
	}
	
	/**
	 * Retourne le code javascript du contexte AJAX
	 */
	public static function getJsAjax() {
		return Acid::exists('ajax:js') ? Acid::get('ajax:js') : null;
	}
	
	/**
	 * Extrait le code javascript de la chaine en entrée
	 * @param string $content contenu à parser
	 * @param string $encoder charset
	 * @return array array('content'=>"contenu sans javascript",'js'=>"code javascript")
	 */
	public static function extractJs($content,$encoder='UTF-8') {
		$new_js = '';
		$new_content = '';
		$match = array();
		
		$parser_id = 'parser'.time();
		if ($content) { 
			$dom = new DOMDocument;
			$dom->formatOutput = true;
			$content = $encoder ? ('<?xml encoding="'.$encoder.'">'.$content) : $content;
			
			@$dom->loadHTML($content);
			$dom->encoding = 'UTF-8';
			
			$domxpath = new DOMXPath($dom);
			$scripts = $domxpath->query("//script");
			//	$scripts = $dom->getElementsByTagName('script');
			if ($scripts) {
				foreach ($scripts as $script) {
					
					if (!$script->getAttribute('src')) {
						$new_js .= $script->nodeValue . "\n" ;
						$script->parentNode->removeChild($script);
					}
					
				}
			}
			
			
			$new_content = substr($dom->saveXML($dom->getElementsByTagName('body')->item(0)), 6, -7);
			$new_js = str_replace('-->','//-->',str_replace('<!--','//<!--',$new_js));
		}
		return array('content'=>$new_content,'js'=>$new_js);
	}
	
	
	/**
	 * Retourne un tableau JSON préformaté par le contexte AJAX
	 * @param string $content contenu
	 * @param string $title titre
	 * @param array $config tableau à la base du JSON 
	 * @param string $js code javascript
	 * @param bool $success valeur de succès
	 * @param bool $parse_content si true, les balises scripte seront extraites de $content
	 * @return array array('content'=>"contenu sans javascript",'js'=>"code javascript")
	 */
	public static function returnJson($content,$title=null,$config=array(),$js=null,$success=null,$parse_content=true) {
		$js = $js ? $js : self::getJsAjax();
		
		if (trim($content) && $parse_content) {
			$parser = Ajax::extractJS($content);
			$config['js'] = isset($config['js']) ?  $config['js']. "\n" . $parser['js'] : $parser['js'];
			$content = $parser['content'];
		}
		
		$config['js'] = isset($config['js']) ?  $config['js']. "\n" . $js : $js; 
		
		$config['success'] = $success ? $success :  (isset($config['success']) ?  $config['success'] : self::getSuccess());
		$config['datas'] = (isset($config['datas']) ?  $config['datas'] : self::getDatas());
		
		$config['title'] = $title;
		
		$config['content'] = $content;
		
		$config['js'] = $config['js'];
		
		return json_encode($config);
	}
    
    /**
     * Retourne un tableau de données JSON préformaté par le contexte AJAX
     * @param array $config
     * @param null  $success
     *
     * @return array
     */
    public static function returnJsonData($config=array(),$success=null) {
	   return static::returnJson('',null,$config,null,$success);
    }
	
	
}
