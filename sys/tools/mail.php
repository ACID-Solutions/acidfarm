<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

Acid::load('tools/mail.php');

/**
 *
 * Outil Mailer, Override du Gestionnaire Mail
 * @package   Tool
 *
 */
class Mailer extends AcidMail {

	/**
	 *
	 * @param string $from_name
	 * @param string $from_email
	 * @param string $to_email
	 * @param string $subject
	 * @param string $body
	 * @param bool $is_html
	 * @param array $attached
	 * @param array $functions
	 * @param string $tpl
	 *
	 * @return boolean
	 */
	/**
	 * Envoie un e-mail en fonction des paramètres renseignés en entrée et retourne true en cas de réussite, false sinon.
	 * @param string $from_name Nom de l'émetteur.
	 * @param string $from_email Email de l'émetteur.
	 * @param string $to_email Email du destinataire.
	 * @param string $subject Sujet de l'e-mail.
	 * @param string $body Corps de l'e-mail.
	 * @param bool|true $is_html True si l'HTML est actif.
	 * @param array $attached Liste des éléments attachés. ([noms]=>[chemins])
	 * @param array $stream  Liste des éléments attachés en mode stream. ([noms]=>[streams])
	 * @param array $functions liste de fonctions à appliquer à l'objet PHPMAILER
	 * @param null $tpl le fichier template à utiliser
	 * @return bool
	 */
	public static function send($from_name,$from_email,$to_email,$subject,$body,$is_html=true,$attached=array(),$stream=array(),$functions=array(),$tpl=null) {

		$tpl = $tpl===null ? 'mail/body.tpl' : $tpl;

		if ($tpl!==false) {
			$body = Acid::tpl($tpl,array('content'=>$body),User::curUser());
		}

		return parent::send($from_name,$from_email,$to_email,$subject,$body,$is_html,$attached,$stream,$functions);

	}

	/**
	 * Envoie un e-mail au staff en fonction des paramètres renseignés en entrée et retourne true en cas de réussite, false sinon.
	 * @param string $subject Sujet de l'e-mail.
	 * @param string $body Corps de l'e-mail.
	 * @param bool|true $is_html True si l'HTML est actif.
	 * @param array $attached Liste des éléments attachés. ([noms]=>[chemins])
	 * @param array $stream Liste des éléments attachés en mode stream. ([noms]=>[streams])
	 * @param array $functions liste de fonctions à appliquer à l'objet PHPMAILER
	 * @param null $tpl le fichier template à utiliser
	 * @return bool
	 */
	public static function sendStaff($subject,$body,$is_html=true,$attached=array(),$stream=array(),$functions=array(),$tpl=null) {

		$tpl = $tpl===null ? 'mail/staff.tpl' : $tpl;

		$from_name = Acid::get('site:name');
		$from_email = Acid::get('site:email');
		$to_email = Acid::get('site:email');

		return self::send($from_name,$from_email,$to_email,$subject,$body,$is_html,$attached,$stream,$functions,$tpl);

	}


}
