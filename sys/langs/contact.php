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
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

$contact_lang  = Acid::exists('lang:contact') ? Acid::get('lang:contact') : Acid::get('lang:default');

switch ($contact_lang) {

	case 'fr' :
		$GLOBALS['lang']['trad']['contact_post_email']      = 'Email';
		$GLOBALS['lang']['trad']['contact_post_contact']    = 'Contact';
		$GLOBALS['lang']['trad']['contact_post_address']    = 'Adresse';

		$GLOBALS['lang']['trad']['contact_post_cp']     	 = 'Code Postal';
		$GLOBALS['lang']['trad']['contact_post_city']  	 = 'Ville';
		$GLOBALS['lang']['trad']['contact_post_phone'] 	 = 'Téléphone';
		$GLOBALS['lang']['trad']['contact_post_fax']        = 'Fax';
		$GLOBALS['lang']['trad']['contact_post_website']    = 'Site Web';

		$GLOBALS['lang']['trad']['contact_post_firstname']  = 'Prénom';
		$GLOBALS['lang']['trad']['contact_post_lastname']   = 'Nom';
		$GLOBALS['lang']['trad']['contact_post_message']    = 'Message';

		$GLOBALS['lang']['trad']['contact_post_cause_1']    = 'Avoir des informations';
		$GLOBALS['lang']['trad']['contact_post_cause_2']    = 'Soumetre un projet';
		$GLOBALS['lang']['trad']['contact_post_cause_3']    = 'Demander un devis';

		$GLOBALS['lang']['trad']['contact_post_get_newsletter']    = 'Recevoir la newsletter ?';

		$GLOBALS['lang']['trad']['contact_post_form_of']   		 = 'Formulaire __SITE__';
		$GLOBALS['lang']['trad']['contact_post_form_of_subject']    = 'Formulaire de contact - __SITE__';

		$GLOBALS['lang']['trad']['contact_post_mail_head']		= 'Un message a été envoyé depuis le formulaire __SITE__.';
		$GLOBALS['lang']['trad']['contact_post_mail_user_wants']= 'Cet utilisateur souhaite : ';
		$GLOBALS['lang']['trad']['contact_post_mail_msg_sent']	= 'Un message a été laissé : ';
	break;

	default :
	case 'en' :

		$GLOBALS['lang']['trad']['contact_post_email']      = 'Email';
		$GLOBALS['lang']['trad']['contact_post_contact']    = 'Contact';
		$GLOBALS['lang']['trad']['contact_post_address']    = 'Address';

		$GLOBALS['lang']['trad']['contact_post_cp']     	 = 'Zip code';
		$GLOBALS['lang']['trad']['contact_post_city']  	 = 'City';
		$GLOBALS['lang']['trad']['contact_post_phone'] 	 = 'Phone';
		$GLOBALS['lang']['trad']['contact_post_fax']        = 'Fax';
		$GLOBALS['lang']['trad']['contact_post_website']    = 'Website';

		$GLOBALS['lang']['trad']['contact_post_firstname']  = 'Firstname';
		$GLOBALS['lang']['trad']['contact_post_lastname']   = 'Lastname';
		$GLOBALS['lang']['trad']['contact_post_message']    = 'Message';

		$GLOBALS['lang']['trad']['contact_post_cause_1']    = 'Have information';
		$GLOBALS['lang']['trad']['contact_post_cause_2']    = 'Submit a project';
		$GLOBALS['lang']['trad']['contact_post_cause_3']    = 'Request a quote';

		$GLOBALS['lang']['trad']['contact_post_get_newsletter']    = 'Receive newsletter ?';

		$GLOBALS['lang']['trad']['contact_post_form_of']   		 = 'Form of  __SITE__';
		$GLOBALS['lang']['trad']['contact_post_form_of_subject']    = 'Contact Form - __SITE__';

		$GLOBALS['lang']['trad']['contact_post_mail_head']		= 'A message was sent by the form __SITE__.';
		$GLOBALS['lang']['trad']['contact_post_mail_user_wants']= 'This user wants:';
		$GLOBALS['lang']['trad']['contact_post_mail_msg_sent']= 'A message was sent:';

	break;

}