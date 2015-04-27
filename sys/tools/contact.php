<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   User Module
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

//$en_mode =  (Acid::get('lang:default')=='fr') ? false : true;
include(SITE_PATH.'sys/langs/contact.php');

Acid::set('session:enable',true);

/**
 *
 * Gestion du formulaire de contact
 * @package   User Module
 */
class Contact {

	/**
	 * Traitement du POST
	 */
	public  static function exePost() {
		if (isset($_POST['contact_do'])) {

			$do = $_POST['contact_do'];

			if ($do == 'send_form') {
				if ((!Conf::get('contact:shield')) || (Lib::getInPost(Conf::get('contact:shield_key'),'novalue')==Conf::get('contact:shield_val'))) {
					Contact::exeForm();
				}else{
					AcidDialog::add('error',Acid::trad('contact_please_enable_javascript'));
				}
			}

		}
	}

	/**
	 * Retourne les clés à ne pas prendre en compte dans les traitement du formulaire
	 * @return array()
	 */
	public static  function formExcludedKeys() {
		$def_excluded = array('contact_do','module_do','x','y');
		if (Conf::get('contact:shield_key')) {
			$def_excluded[] = Conf::get('contact:shield_key');
		}
		return Conf::exist('contact_form:exclude') ? Conf::get('contact_form:exclude') : $def_excluded;
	}

	/**
	 * Retourne les clés optionnelles lors du traitement du formulaire
	 * @return array()
	 */
	public static  function formOptionalKeys() {
		$def_tab =  array(
										'phone'                   =>array('label'=>Acid::trad('contact_post_phone'),'regex'=>'')  ,
										'address'				=>array('label'=>Acid::trad('contact_post_address'),'regex'=>''),

										'cause_1'				=>array('label'=>Acid::trad('contact_post_cause_1'),'regex'=>''),
										'cause_2'				=>array('label'=>Acid::trad('contact_post_cause_2'),'regex'=>''),
										'cause_3'				=>array('label'=>Acid::trad('contact_post_cause_3'),'regex'=>''),
										'get_newsletter'		=>array('label'=>Acid::trad('contact_post_get_newsletter'),'regex'=>'')


									);

		return Conf::exist('contact_form:optional') ? Conf::get('contact_form:optional'):$def_tab;
	}

	/**
	 * Retourne les clés obligatoires lors du traitement du formulaire
	 * @return array()
	 */
	public static  function formControlKeys() {
		$def_tab =  array(
										'message'				=>array('label'=>Acid::trad('contact_post_message'),'regex'=>''),
										'lastname'				=>array('label'=>Acid::trad('contact_post_lastname'),'regex'=>''),
										'firstname'				=>array('label'=>Acid::trad('contact_post_firstname'),'regex'=>''),
										'email'           =>array(
                                                                        'label'=>Acid::trad('contact_post_email'),
                                                                        'regex'=>'`^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$`i'
                                                                        )
									);

		return Conf::exist('contact-form:control') ? Conf::get('contact-form:control'):$def_tab;
	}

	/**
	 * Retourne la valeur en session désignée par $key
	 * @param string $key identifiant de variable
	 * @return mixed
	 */
	public static  function getSession($key) {
		return Acid::sessExist('contact_form:'.$key) ? Acid::sessGet('contact_form:'.$key) : null;
	}

	/**
	 * Retourne la valeur du "checked" désigné par $key
	 * @param string $key identifiant de variable
	 * @return string
	 */
	public static function getSessionChecked($key) {
		return (!Acid::sessEmpty('contact_form:'.$key)) ? ' checked="checked" ' : '';
	}

	/**
	 * Retourne une portion de code HTML correspondant au formulaire de contact
	 * @return string
	 */
	public  static function printContactPage() {
		return	'<div id="contact">' . "\n" .

				'<div id="contact_page_form">' . "\n" .
					self::printForm() . "\n" .
				'</div>' . "\n" .

				'</div>';
	}

	/**
	 * Retourne une portion de code HTML correspondant au formulaire de contact
	 * @return string
	 */
	public  static function printStandardForm() {
		return Acid::tpl('pages/contact.tpl',array());
	}

	/**
	 * Retourne une portion de code HTML correspondant au formulaire de contact
	 * @return string
	 */
	public static  function printForm() {
		$clear='<div class="clear"></div>'. "\n" ;

		return	'	<form id="contact_form" method="post" action="#">' . "\n" .
						'<div>' . "\n".
						'<input type="hidden" name="contact_do" value="send_form" />' . "\n" .
						'<input type="hidden" name="module_do" value="'.get_called_class().'" />' . "\n" .
						self::printStandardForm() . "\n" .
						'</div>'. "\n" .
				'	</form>' . "\n" . $clear ;

	}

	/**
	 * Traite le formulaire de contact
	 */
	public static function exeForm() {

		//init keys
		$form_excluded_keys=self::formExcludedKeys();
		$form_control_keys=self::formControlKeys();
		$form_optional_keys=self::formOptionalKeys();

		$form_controler = array_merge($form_control_keys,$form_optional_keys);

		$fill_missing = false;

		//session init
		Acid::sessSet('contact_form',array());

		//post init
		if ($fill_missing) {
			foreach ($form_control_keys as $key => $val) {
				if (!isset($_POST[$key])) {
					$_POST[$key] = '';
				}
			}
		}

		//Checking values
		$miss = array();
		$mistake = array();
		$add = array();
		foreach ($_POST as $name => $val) {

			//session
			if (!in_array($name,$form_excluded_keys)) {
				Acid::sessSet('contact_form:'.$name,$val);
				$add[$name]=$val;
			}

			//check for missing fields
			if ( (in_array($name,array_keys($form_control_keys))) && (!$val) ) {
					$miss[] = $name;
			}

			//checking values
			if ($val) {
				if (!empty($form_controler[$name]['regex'])) {
					if (!preg_match($form_controler[$name]['regex'],$val)) {
						$mistake[] = $name;
					}
				}
			}

		}

		$check_info ='' . "\n" ;


		// Control succeeded
		if ( empty($miss)  && empty($mistake) ) {

			// CONFIGURE BODY
				//email configuration
				$from_name = Acid::trad('contact_post_form_of',array('__SITE__'=>Acid::get('site:name')));
				$from_email = Conf::exist('contact_form:sender') ? Conf::get('contact_form:sender') : Acid::get('site:email');
				$to_email = $GLOBALS['site_config']->getConf('email');
				$subject = Acid::trad('contact_post_form_of_subject',array('__SITE__'=>Acid::get('site:name')));

				$sep =  "\n" ;

				//getting reply configuration
				$reply_mail = isset($add['email']) ? $add['email'] : '';
				$reply_firstname = isset($add['firstname']) ? $add['firstname'] : '';
				$reply_name = isset($add['lastname']) ? $add['lastname'] : '';
				$reply_name = $reply_firstname ? $reply_firstname .' '. $reply_name : $reply_name;


			// PREPARE BODY
				$cause = array();
				$profile = array();
				$message_val = array();

				//preparing message content
				foreach ($add as $key=>$val) {
					if (strpos($key,'cause_')===0) {
						$cause[] = $form_controler[$key]['label'] . "\n" ;
					}else{
						if ($key == 'message') {
							$message_val[(!empty($form_controler[$key]['label'])? $form_controler[$key]['label']:$key )] = $val  ;
						}else{
							$profile[(!empty($form_controler[$key]['label'])? $form_controler[$key]['label']:$key )] = $val ;
						}
					}
				}

				//generating email body
				$body = Acid::tpl('admin/contact-mail.tpl',array('cause'=>$cause,'profile'=>$profile,'message'=>$message_val));


			// SEND
				//adding reply to
				$function_tab = array();
				if ($reply_mail) {
					$reply_name = $reply_name ? $reply_name : $reply_mail;
					$function_tab['AddReplyTo'] =  array($reply_mail, utf8_decode($reply_name))	;
				}

				//recipient address defined
				if ($to_email) {

					//sending email
					if (Mailer::send($from_name,$from_email,$to_email,$subject,$body,true,array(),array(),$function_tab)) {
						Acid::sessKill('contact_form');
						$stats_contact = '<div id="stats_content">'. Acid::executeTpl(SITE_PATH . 'sys/stats/contact.tpl') . '</div>';
						AcidDialog::add('info',Acid::trad('contact_post_msg_sent').$stats_contact);

						AcidHook::call('contact_success');

					}
				}
				// no recipient address
				else{
					Acid::log('CONTACT','error : recipient address is not defined');
				}

		// Control failed
		}else{

			//PREPARE DIALOG
				$alert = '';

				if ($miss) {
					$alert .= count($miss)==1?
								'<br /><h4>'.Acid::trad('contact_post_field_missing').'</h4>' :
								'<br /><h4>'.Acid::trad('contact_post_fields_missing').'</h4>';

					$i=0;
					foreach ($miss as $name) {
						$alert .= $i? ', <br />':'';
						$alert .= '<b>'.$form_controler[$name]['label'].'</b>';
						$i++;
					}
				}

				if ($mistake) {
					$alert .= '<br /><h4>'.Acid::trad('contact_post_field_mistakes').'</h4>';

					$i=0;
					foreach ($mistake as $name) {
						$alert .= $i? ', <br />':'';
						$alert .= '<b>'.$form_controler[$name]['label'].'</b>';
						$i++;
					}
				}

			//SEND DIALOG
				AcidDialog::add('info',Acid::trad('contact_post_bad_request') . '<br />' . $alert);

		}

	}



}
?>