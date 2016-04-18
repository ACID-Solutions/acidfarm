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
 * Outil AcidTimer, Gestionnaire de chronomètres.
 * @package   Acidfarm\Tool
 */
class AcidTimer {
	
	/**
	 * @var float microtime
	 */
	var $s;
	
	/**
	 * @var float pause
	 */
	var $p = 0;
	
	/**
	 * Constructeur AcidTimer
	 * @param bool $autostart True si on démarre le timer à l'instanciation.
	 */
	function __construct($autostart=true) {
		if ($autostart) $this->start();
	}
	
	/**
	 * Démarre le timer.
	 * 
	 * @param string $microtime
	 */
	function start($microtime = '') {
		if (!empty($microtime)) {
			$this->s = $microtime;
		}else{
			$this->s = $this->getmicrotime();
		}
	}
	
	/**
	 * Met le timer en pause.
	 */
	function pause() {
		$this->p = $this->getmicrotime();
	}
	
	/**
	 * Sort le timer du mode pause.
	 */
	function unpause() {
		$this->s += ($this->getmicrotime() - $this->p);
		$this->p = 0;
	}
	
	/**
	 * Retourne la valeur mesurée par le timer
	 *
	 * @param int $decimalPlaces Arrondi après la virgule.
	 * 
	 * @return float
	 */
	function fetch($decimalPlaces = 10) {
		return round(($this->getmicrotime() - $this->s), $decimalPlaces);
	}
	

	/**
	 * Renvoi un nombre en secondes basé sur le microtime() PHP
	 * 
	 * @return float
	 */
	static function getmicrotime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}

?>
