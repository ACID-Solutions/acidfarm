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

//Langue
$lang['trad']['lang_fr'] = 'French';
$lang['trad']['lang_de'] = 'German';
$lang['trad']['lang_es'] = 'Spanish';
$lang['trad']['lang_en'] = 'English';
$lang['trad']['lang_it'] = 'Italian';


//Header


//Config
$lang['trad']['config_delete_conf'] = 'Delete __NAME__';
$lang['trad']['config_ask_delete_conf'] = 'Delete __NAME__ ?';
$lang['trad']['config_add_conf'] = 'Add configuration';
$lang['trad']['config_btn_validate'] = 'Validate';


//Actualités
$lang['trad']['actu_home_unavailable'] = 'No news available.';
$lang['trad']['actu_h1'] = 'History of news';


//Pagination
$lang['trad']['pagination_prev'] = 'Previous page';
$lang['trad']['pagination_next'] = 'Next page';


//Menu
$lang['trad']['menu_accueil'] = 'Home';
$lang['trad']['menu_presentation'] = 'Presentation';
$lang['trad']['menu_news']         = 'News';
$lang['trad']['menu_contact']      = 'Contact';


//Contact
$lang['trad']['contact_title'] = 'Contact';


//Contact Popup
$lang['trad']['contact_popup_ask_note']  = 'Please, write :';
$lang['trad']['contact_popup_title'] = 'Contact';
$lang['trad']['contact_popup_mail']  = 'E-mail';
$lang['trad']['contact_popup_msg']   = 'Message';
$lang['trad']['contact_popup_send']  = 'Send';


//Contact Mail
$lang['trad']['contact_mail_head']         = 'You have got mail from the form __SITE__.';
$lang['trad']['contact_mail_cause']        = 'This user wants:  ';
$lang['trad']['contact_mail_object']       = 'About: ';
$lang['trad']['contact_mail_msg']          = 'You have got mail: ';
$lang['trad']['contact_mail_contact_form'] = 'Contact form';
$lang['trad']['contact_mail_confirmation'] = 'Your message was sent successfully';
$lang['trad']['contact_mail_error_sing']   = 'The following field has not been filled in';
$lang['trad']['contact_mail_error_plur']   = 'The following fields have not been filled in';
$lang['trad']['contact_mail_missing_sing'] = 'The following field has been filled in incorrectly';
$lang['trad']['contact_mail_missing_plur'] = 'The following fields have been filled in incorrectly';
$lang['trad']['contact_mail_bad_request']  = 'The form has been filled in incorrectly.';


//CheckVals
$lang['trad']['checkvals_error_plur']  	   = 'The following fields have not been filled in';


//Popup
$lang['trad']['popup_title_error']   = 'Error';
$lang['trad']['popup_title_banner']  = 'Warning';
$lang['trad']['popup_title_default'] = 'Information';


//User
$lang['trad']['user_error_log']                     = 'Error in user name or password';
$lang['trad']['user_error_pass_dismatch']           = 'The passwords are not identical';
$lang['trad']['user_error_email_dismatch']          = 'No account corresponds to this e-mail address';
$lang['trad']['user_error_user_exists']             = 'This user name already exists';
$lang['trad']['user_error_ident_exists']            = 'The identifier already exists';
$lang['trad']['user_error_login_too_long']          = 'Login too long';
$lang['trad']['user_error_email_invalid_format']    = 'The format of your e-mail address is incorrect';
$lang['trad']['user_error_pass_nbc']                = 'Your password has to contain at least __NUM__ characters';
$lang['trad']['user_error_same_email']              = 'The e-mail address is identical!';
$lang['trad']['user_error_bad_cur_password']        = 'The current password is not correct';

$lang['trad']['user_bad_email_exists']     			= 'An account is already registered for this e-mail address';
$lang['trad']['user_bad_password_forget_link']      = 'An account is already registered for this e-mail address __LINK__';
$lang['trad']['user_bad_password_forget_innerlink'] = 'Have you forgotten your password?';

$lang['trad']['user_ask_choose_login']              = 'Please choose a login';
$lang['trad']['user_ask_choose_pass']               = 'Please choose a password';
$lang['trad']['user_ask_set_mail']                  = 'Please enter your e-mail address';

$lang['trad']['user_mail_sent']                     = 'You have got mail on __MAIL__';
$lang['trad']['user_valid_mail_sent']               = 'Consult your mailbox in order to valid your address';

$lang['trad']['user_mail_subject_new_user_subscribe']		= 'Subscribe to __SITE__';
$lang['trad']['user_mail_subject_new_user_subscribe_admin']	= 'New User: __USER__';
$lang['trad']['user_mail_subject_user_change_mail']			= 'Validation of your new email address';
$lang['trad']['user_mail_subject_user_forget_pass']			= 'Forgot Password';
$lang['trad']['user_mail_user_new_private']					= 'New private message';

$lang['trad']['user_password_ask_enter_mail']       = 'Enter your e-mail address in order to change your password: ';
$lang['trad']['user_password_ask_click']            = 'Consult your mailbox in order to change your password';
$lang['trad']['user_change_mail_ask_click']         = 'If you want to change your e-mail address, please click on the following link: ';
$lang['trad']['user_forget_password_ask_click']     = 'If you want to change your password, please click on the following link: ';
$lang['trad']['user_subscribe_ask_click']    		= 'To confirm, please click on the following link: ';

$lang['trad']['user_password_change_success']       = 'Password changed';
$lang['trad']['user_valid_mail_success']            = 'Your address has been valid';

$lang['trad']['user_welcom']          				= 'Hello __USER__, ';
$lang['trad']['user_new_user']                      = 'New user';
$lang['trad']['user_new_user_registred']            = 'A new user registered on __SITE__: __NAME__';
$lang['trad']['user_new_user_print_infos']  		= 'Here are your login details to keep:';
$lang['trad']['user_new_user_register_asked']  		= 'You just sign up on __SITE__';
$lang['trad']['user_new_user_validation_asked']  	= 'You asked the link to confirm your email __SITE__.';

$lang['trad']['user_message_received']  			= 'A new private message "__USER__" just arrived on __SITE__ dealing with:';
$lang['trad']['user_message_read_it']  				= 'To read your message, click on this link:';

$lang['trad']['user_valid_mail_action']             = 'Confirmation of your new e-mail address';
$lang['trad']['user_mail_footer']                   = 'The team __SITE__';

$lang['trad']['user_forget_password']               = 'Password forgotten';
$lang['trad']['user_forget_password_site']          = 'You have forgotten your password __SITE__' ;

$lang['trad']['user_date_expired']                  = 'The validity of you account is expired';
$lang['trad']['user_validation_done']               = 'Validation finished';
$lang['trad']['user_back_to_userspace']             = 'Back to the member community';
$lang['trad']['user_need_mail_to_go_on']            = 'If you want to continue, you need a valid e-mail address.';
$lang['trad']['user_how_to_validate']               = 'In order to valid it, consult your mailbox and click on the link for validation.';
$lang['trad']['user_valid_mail_resend']             = 'Send the validation e-mail again';
$lang['trad']['user_change_mail']                   = 'Change your contact e-mail address';

$lang['trad']['user_mail_confirmed']                = 'Your e-mail address has been valid.';
$lang['trad']['user_cur_password']                  = 'Current password';
$lang['trad']['user_new_password']                  = 'New password';
$lang['trad']['user_confirm_password']              = 'Confirmation';

$lang['trad']['user_registrer_form']                = 'Registration form';
$lang['trad']['user_btn_change']                    = 'Change';
$lang['trad']['user_btn_register']                  = 'Register';
$lang['trad']['user_btn_login']                  	= 'Login';
$lang['trad']['user_btn_logout']                  	= 'Logout';
$lang['trad']['user_btn_validate']                  = 'Validate';

$lang['trad']['user_ask_change_pass']               = 'Change my password';
$lang['trad']['user_ask_change_mail']               = 'Change my e-mail address';


//Admin

//--Welcome
$lang['trad']['admin_title']           = 'Administration';
$lang['trad']['admin_welcom']          = 'Hello __USER__, ';
$lang['trad']['admin_welcom_to_space'] = 'Welcome in the administration field';
$lang['trad']['admin_no_permission']	= 'You do not have permission to access this page.';
$lang['trad']['admin_no_permission_user']	= 'You do not have permission to manage this page.';

//--General
$lang['trad']['yes'] = 'yes';
$lang['trad']['no']  = 'no';

$lang['trad']['all']  = 'all';
$lang['trad']['show_all']  = 'show all';
$lang['trad']['filter_show_all']  = 'All';

$lang['trad']['user']                    = 'User';
$lang['trad']['login']                   = 'User name';
$lang['trad']['mail']                    = 'E-mail';
$lang['trad']['password']                = 'Password';
$lang['trad']['back']                    = 'Back';
$lang['trad']['next']                    = 'Continue';
$lang['trad']['previous']                = 'Previous';

$lang['trad']['no_content_available']	 = 'No content available';

$lang['trad']['admin_menu_home']         = 'Home';
$lang['trad']['admin_menu_infos']        = 'My Profile';
$lang['trad']['admin_menu_config']       = 'Configuration';
$lang['trad']['admin_menu_seo']      	 = 'SEO';

$lang['trad']['admin_menu_user']         		= 'Users';
$lang['trad']['admin_menu_user_group']        	= 'User Groups';
$lang['trad']['admin_menu_user_permission']   	= 'User Permissions';
$lang['trad']['admin_menu_photo']        		= 'Photos';
$lang['trad']['admin_menu_photo_home']   		= 'Photos home';
$lang['trad']['admin_menu_news']         		= 'News';
$lang['trad']['admin_menu_page']         		= 'Pages';

$lang['trad']['admin_menu_contact']      = 'Contact';
$lang['trad']['admin_menu_browser']      = 'Media centre';

$lang['trad']['admin_menu_back']         = 'Back to the page';
$lang['trad']['admin_menu_unlog']        = 'Log out';

$lang['trad']['admin_btn_add']           = 'Add';
$lang['trad']['admin_btn_update']        = 'Modify';

$lang['trad']['admin_btn_search']        = 'Search';
$lang['trad']['admin_btn_change']        = 'Change';
$lang['trad']['admin_btn_ok']            = 'OK';

$lang['trad']['admin_search_list_has']   = 'contains';
$lang['trad']['admin_search_list_is']    = 'is';
$lang['trad']['admin_search_list_start'] = 'starts with';
$lang['trad']['admin_search_list_stop']  = 'ends by';
$lang['trad']['admin_search_list_isnt']  = 'unconsidered';
$lang['trad']['admin_search_pagination'] = 'Show __INPUT__ elements';

$lang['trad']['admin_onglet_list']       = 'List';
$lang['trad']['admin_onglet_add']        = 'Add';
$lang['trad']['admin_onglet_search']     = 'Search';
$lang['trad']['admin_onglet_multi']      = 'Multi Add';

$lang['trad']['admin_action_print']      = 'Show';
$lang['trad']['admin_action_add']        = 'Add';
$lang['trad']['admin_action_update']     = 'Modify';
$lang['trad']['admin_action_remove']     = 'Delete';

$lang['trad']['licence_title']			 = 'License';

$lang['trad']['admin_add_succeed']     	  =  'Adding completed successfully';
$lang['trad']['admin_add_failed']     	  =  'Failed to add';
$lang['trad']['admin_update_succeed']     =  'Update completed successfully';
$lang['trad']['admin_update_failed']      =  'Failed to update';
$lang['trad']['admin_delete_succeed']     =  'Deletion completed successfully';
$lang['trad']['admin_delete_failed']      =  'Failed to delete';

//--Page
$lang['trad']['admin_page_ident_exists']       = 'This page key already exists, please choose another one';
$lang['trad']['admin_page_ident_config']       = 'Your page key can only be composed of small letters, figures and hyphens';
$lang['trad']['admin_page_ident_reserved_key'] = 'Your page key is a reserved word, please choose another one';
$lang['trad']['admin_page_ident_empty']        = 'You have to enter a new page key';

$lang['trad']['admin_page_update_canceled']    =  'The modifications can not be registered until your page key is incorrect';
$lang['trad']['admin_page_update_page']        =  'Modify this page';
$lang['trad']['admin_page_updating_page']      =  'Modification of this page';
$lang['trad']['admin_page_choose_page']        =  'Please select a page';

//--Config
$lang['trad']['admin_config_title_pass']   = 'Change your password: ';
$lang['trad']['admin_config_pass']         = 'Current password';
$lang['trad']['admin_config_npass']        = 'New password';
$lang['trad']['admin_config_confirm']      = 'Confirmation';

$lang['trad']['admin_config_title_email'] = 'Change your e-mail address: ';

//--List
$lang['trad']['admin_list_btns_label'] = 'Actions';
$lang['trad']['admin_list_total_elts'] = '__TOTAL__ elements altogether, indication of __NB__ elements (__START__ to __STOP__)';
$lang['trad']['admin_list_pagination'] = 'Number of elements per page';
$lang['trad']['admin_list_del_filter'] = 'Delete the search filter';


//Tools

//--Plupload
$lang['trad']['plupload_init']	 = 'initialization...';
$lang['trad']['plupload_select'] = 'Select file';
$lang['trad']['plupload_upload'] = 'Upload file';
$lang['trad']['plupload_cancel_prepare'] = "A file has been prepared, do you want to cancel it ?";
$lang['trad']['plupload_cancel_upload'] = "A file is uploading, do you want to cancel it ?";

//--Vars
$lang['trad']['vars_bad_file'] = 'This data type is not authorised';

//--Browser
$lang['trad']['browser_home']    		= 'Home';

$lang['trad']['browser_error']          = 'An error has occured!';
$lang['trad']['browser_bad_file']       = 'This data type is not authorised';

$lang['trad']['browser_ask_new_folder'] = 'Name of the new folder';
$lang['trad']['browser_new_folder']     = 'New folder';
$lang['trad']['browser_ask_new_name']   = 'New name';
$lang['trad']['browser_ask_del_file']   = 'Delete this data?';
$lang['trad']['browser_ask_del_folder'] = 'Delete this folder AND ITS WHOLE CONTENT?';

$lang['trad']['browser_choose_btn']     = 'Choose';
$lang['trad']['browser_change_btn']     = 'Modify';
$lang['trad']['browser_change_name']    = 'Modify the name';
$lang['trad']['browser_editor_btn']     		= 'Editor';
$lang['trad']['browser_editor_edit']   			= 'Edit';
$lang['trad']['browser_editor_close']     		= 'Close';
$lang['trad']['browser_editor_output_name']    	= 'Output Name';
$lang['trad']['browser_editor_output_format']   = 'Output dimensions';
$lang['trad']['browser_editor_original_format']   = 'Image size';
$lang['trad']['browser_editor_selection_format']   = 'Selection size';
$lang['trad']['browser_editor_rotate']    		= 'Rotation';
$lang['trad']['browser_editor_compress']    	= 'Compression';
$lang['trad']['browser_editor_ask_output_name']    		= 'Please enter an output name.';
$lang['trad']['browser_editor_ask_output_override']    	= '__NAME__ already exists, do you want to overwrite it?';
$lang['trad']['browser_cancel']         = 'Cancel';
$lang['trad']['browser_delete']         = 'Delete';
$lang['trad']['browser_upload_file']    = 'Uploader data';

//--Url
$lang['trad']['url_access_denied']   = 'Access unauthorised';
$lang['trad']['url_error404']        = 'Error 404 (page non found)';
$lang['trad']['url_site_unvailable'] = 'Page not available';

$lang['trad']['url_403']             = '403 Forbidden';
$lang['trad']['url_404']             = '404 Not found';
$lang['trad']['url_503']             = '503 Service not available';
$lang['trad']['url_301']             = '301 permanent displacement';

//--Mail
$lang['trad']['mail_footer_auto_generation'] = 'This mail was automatically generated by __NAME__.';
$lang['trad']['mail_footer_no_response'] = 'Do not reply to this email to contact us, please use __LINK__.';
$lang['trad']['mail_footer_contact_form'] = 'the contact form';
$lang['trad']['mail_footer_staff_generation'] = 'Email send from __LINK__.';


//Levels
$lang['lvl']['visitor']   = 'Visitor';
$lang['lvl']['robot']     = 'Robot';
$lang['lvl']['unvalid']   = 'Unvalid';
$lang['lvl']['registred'] = 'Registred';
$lang['lvl']['member']    = 'Member';
$lang['lvl']['vip']       = 'VIP';
$lang['lvl']['modo']      = 'Modo';
$lang['lvl']['admin']     = 'Admin';
$lang['lvl']['dev']       = 'Developer';


// Devise
$langs['currency']['symbol']       = '__VAL__ €';
$langs['currency']['letters_sing'] = '__VAL__ euro';
$langs['currency']['letters_plur'] = '__VAL__ euros';


//Date
$lang['date_format']['date']     = 'd/m/Y';
$lang['date_format']['datetime'] = 'd/m/Y H:i:s';
$lang['date_format']['datetime_small'] = 'd/m/Y H:i';

$lang['date']['month'] = array(
								1	=>	'January',
								2	=>	'February',
								3	=>	'March',
								4	=>	'April',
								5	=>	'May',
								6	=>	'June',
								7	=>	'July',
								8	=>	'August',
								9	=>	'September',
								10	=>	'October',
								11	=>	'November',
								12	=>	'December'
);

$lang['date']['month_s'] = array(
								1	=>	'Jan',
								2	=>	'Feb',
								3	=>	'Mar',
								4	=>	'Apr',
								5	=>	'May',
								6	=>	'Jun',
								7	=>	'Jul',
								8	=>	'Aug',
								9	=>	'Sep',
								10	=>	'Oct',
								11	=>	'Nov',
								12	=>	'Dec'
);

$lang['date']['day'] = array(
								0	=>	'Sunday',
								1	=>	'Monday',
								2	=>	'Tuesday',
								3	=>	'Wednesday',
								4	=>	'Thursday',
								5	=>	'Friday',
								6	=>	'Saturday'
);

$lang['date']['day_s'] = array(
								0	=>	'Sun',
								1	=>	'Mon',
								2	=>	'Tue',
								3	=>	'Wed',
								4	=>	'Thu',
								5	=>	'Fri',
								6	=>	'Sat'
);

$lang['words']['module'] = array(
								 'Key'=>'Key'
);