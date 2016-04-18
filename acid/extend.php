<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Core
 * @version   0.1
 * @since     Version 0.5
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

if (file_exists($acid['core']['module']['path'])) {
	include $acid['core']['module']['path'];
}else{
	/**
	 * Permet la modification utilisateur de l'ensemble des AcidModule
	 * @package   Acidfarm\User Module
	 */
	abstract class AcidModule extends AcidModuleCore {}
}

if (file_exists($acid['core']['mail']['path'])) {
	include $acid['core']['mail']['path'];
}else{
	/**
	 * Permet l'envoi d'emails
	 * @package   Acidfarm\Tools
	 */
	abstract class Mailer extends AcidMail {}
}
