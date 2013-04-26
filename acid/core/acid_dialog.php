<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Core
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/*****************************************************************************
 *
 *           Acid Dialog class
 *
 *****************************************************************************/

/**
 * Utilitaire de Communication
 * @package   Core
 *
 */
class AcidDialog {


	/**
	 *  Initialise le paramètre de dialogue en session.
	 */
	public static function initDialog(){
	
		if (Acid::get('session:enable')) {
		
			
			$sess = AcidSession::getInstance();
			
			if (!isset($sess->data['dialog'])) {
				$sess->data['dialog'] = array();
			}
			elseif (!is_array($sess->data['dialog'])) {
				$sess->data['dialog'] = array();
			}
			
			return true;
		}
		return false;
	}

	/**
	 *  Ajoute un élement de dialogue en session
	 *  
	 * @param string $type
	 * @param string $str
	 */
	public static function add($type,$str) {
		
		self::initDialog();
		
		$sess = AcidSession::getInstance();
		
		$dialog = &$sess->data['dialog'];
		
		
		$tab = array('str'=>$str,'type'=>$type);
		$dialog[] = $tab;
		
	}
	
	/**
	 * Récupère les éléments de dialogue en session mis en forme.
	 *
	 * @param array $blacklister liste des types de dialogue à exclure 
	 *
	 * @return string
	 */
	public static function getDialog($blacklister=array(),$tag='div') {
		self::initDialog();
		$sess = AcidSession::getInstance();
		$dialog = &$sess->data['dialog'];

		$output = '';
		if ($dialog) {
			while ($info = array_shift($dialog)){
				if (!in_array($info['type'],$blacklister)) {
					$output .=	'<'.$tag.' class="dialog_'.$info['type'].'">' .
					$info['str'] .
	            				'</'.$tag.'>' . "\n";
				}
			}
		}
		
		
		return $output;
	}

	/**
	 * Récupère les éléments de dialogue en session mis en forme.
	 * @param boolean $shift_mode si true, enlève les éléments du tableau de dialogue après récupération
	 * @param array $filter si vrai, retourne le resultat sous forme de tableau indexé par type. Si $filter est un tableau, le resultat sera filtré.
	 * @param array $blacklister liste de stype de dialogue à exclure
	 * @return mixed
	 */
	public static function getFiltredDialog($shift_mode=true,$filter=null,$blacklister=array()) {
	
		$output = '';
		$tab_dialog = array();
		
		if (self::initDialog()) {
				
			$sess = AcidSession::getInstance();
			if ($shift_mode) {
				$dialog = &$sess->data['dialog'];
			}else{
				$dialog = $sess->data['dialog'];
			}

			$output = '';
			if ($dialog) {
				
				while ($info = array_shift($dialog)){
				
					if (!in_array($info['type'],$blacklister)) {
						$output .=	'<p class="dialog_'.$info['type'].'">' .
						$info['str'] .
		            				'</p>' . "\n";
						
						if ($filter) {
							
							if ((!is_array($filter)) || (in_array($info['type'],$filter))) {
								
								$tab_dialog[$info['type']][] = $info['str'];
								
							}
						}
					}
				}
			}
		}
		

		
		if ($filter) {
 
			return $tab_dialog;
			
		}else{
			return $output;
		}
	
	}

	/**
	 *  Retourne une fenêtre de communication avec les éléments de dialogues en session.
	 *
	 *
	 * @return string
	 */
	public static function printDialog() {
		
		$content = self::getDialog();

		$output = '';

		if (!empty($content)) {
			$output =	'<div id="dialog">'. $content . '</div>' . "\n" .	'';
		}
		
		return $output;
	}

}
