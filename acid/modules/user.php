<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Module
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/**
 * Module AcidUser, Gestionnaire d'utilisateurs
 * @package   Module
 */
abstract class AcidUser extends AcidModule {


	/**
	 * Constructeur AcidUser
	 *
	 *
	 * @param string $general_path Répertoire accueillant les avatars.
	 * @param mixed $init_id Identifiant pour initialisation. (si défini)
	 *
	 * @return object
	 */
	public function __construct ($general_path,$init_id=null) {
		global $acid;


		$this->vars['id_user'] = empty($this->vars['id_user']) ? new AcidVarInt($this->modTrad('id_user'),true) : $this->vars['id_user'];
		$this->vars['id_group'] = empty($this->vars['id_group']) ? new AcidVarInt($this->modTrad('id_group'),true) : $this->vars['id_group'];
		$this->vars['username'] = empty($this->vars['username']) ? new AcidVarString($this->modTrad('username')) : $this->vars['username'];
		$this->vars['password'] = empty($this->vars['password']) ? new AcidVarPassword($this->modTrad('password')) : $this->vars['password'];
		$this->vars['user_salt'] = empty($this->vars['user_salt']) ? new AcidVarString($this->modTrad('user_salt')) : $this->vars['user_salt'];
		$this->vars['email'] = empty($this->vars['email']) ? new AcidVarEmail($this->modTrad('email')) : $this->vars['email'];
		$this->vars['firstname'] = empty($this->vars['firstname']) ? new AcidVarString($this->modTrad('firstname')) : $this->vars['firstname'];
		$this->vars['lastname'] = empty($this->vars['lastname']) ? new AcidVarString($this->modTrad('lastname')) : $this->vars['lastname'];
		$this->vars['address'] =	empty($this->vars['address']) ? new AcidVarString($this->modTrad('address')) : $this->vars['address'];
		$this->vars['cp'] =	empty($this->vars['cp']) ? new AcidVarString($this->modTrad('cp'),15,15) : $this->vars['cp'];
		$this->vars['city']	=	empty($this->vars['city']) ? new AcidVarString($this->modTrad('city')) : $this->vars['city'];
		$this->vars['country'] =	empty($this->vars['country']) ? new AcidVarString($this->modTrad('country')) : $this->vars['country'];
		$this->vars['phone'] =	empty($this->vars['phone']) ? new AcidVarString($this->modTrad('phone'),15,20) : $this->vars['phone'];
		$this->vars['level'] = empty($this->vars['level']) ? new AcidVarList($this->modTrad('level'),$acid['user']['levels'],0,false,true) : $this->vars['level'];
		$this->vars['date_creation'] = empty($this->vars['date_creation']) ? new AcidVarDateTime($this->modTrad('date_creation')) : $this->vars['date_creation'];
		$this->vars['date_activation'] = empty($this->vars['date_activation']) ? new AcidVarDateTime($this->modTrad('date_activation')) : $this->vars['date_activation'];
		$this->vars['date_deactivation'] = empty($this->vars['date_deactivation']) ? new AcidVarDateTime($this->modTrad('date_deactivation')) : $this->vars['date_deactivation'];
		$this->vars['date_connexion'] = empty($this->vars['date_connexion']) ? new AcidVarDateTime($this->modTrad('date_connexion')) : $this->vars['date_connexion'];
		$this->vars['lang'] = empty($this->vars['lang']) ? new AcidVarList($this->modTrad('lang'),Acid::get('lang:available'),Acid::get('lang:default'),false,false) : $this->vars['lang'];
		$this->vars['last_lang'] = empty($this->vars['last_lang']) ? new AcidVarInfo($this->modTrad('last_lang')) : $this->vars['last_lang'];
		$this->vars['ip'] = empty($this->vars['ip']) ? new AcidVarString($this->modTrad('ip'),20,15) : $this->vars['ip'];
		$this->vars['active'] = empty($this->vars['active']) ? new AcidVarBool($this->modTrad('active'),1) : $this->vars['active'];

		$avatar_effect = array(
								'fill_gray'=>array('AcidFs::fill',array('__PATH__','__PATH__','__WIDTH__','__HEIGHT__',array(100,100,100))),
						);

		$avatar_format = array(	'large'=>array(	'size' => array(300,300,false),	'suffix' => '_l', 'effect' => array() ),
								'medium'=>array( 'size' => array(120,120,false), 'suffix' => '_m', 'effect' => array() ),
								'small'=>array(	'size' => array(60,60,false), 'suffix' => '_s', 'effect' => array()	),
								'gray'=>array( 'size' => array(300,300,false), 'suffix' => '_g', 'effect' => array('gray')),
								'gray_fill'=>array( 'size' => array(300,300,false), 'suffix' => '_gf', 'effect' => array('fill_gray','gray')),
								'black'=>array( 'size' => array(300,300,false), 'suffix' => '_b', 'effect' => array('fill_black'))
						 	  );

		$this->vars['image_0'] =  empty($this->vars['image_0']) ? new AcidVarImage($this->modTrad('avatar'),$general_path, array('format'=>$avatar_format,'effects'=>$avatar_effect,'admin_format'=>'small'),'avatar__ID__') : $this->vars['image_0'];

		$cur_date = date('Y-m-d H:i:s');
		$this->config['admin']['add']['def'] = array('date_creation'=>$cur_date);
		$this->config['print']['active'] = array('type'=>'toggle');
		$res = parent::__construct($init_id);

		return $res;
	}



	/**
	 * Retourne true si la chaîne de caractères en entrée est déjà associée à un nom d'utilisateur, false sinon.
	 *
	 * @param string $name
	 */
	public function usernameExists($name) {
		return $this->dbCount(array(array('username','=',$name)));
	}

	/**
	 * Retourne true si la chaîne de caractères en entrée est déjà associée à un nom d'utilisateur, false sinon.
	 *
	 * @param string $login
	 */
	public function loginExists($login) {
		$keys = $this->getConfig('identification');
		$keys = is_array($keys) ? $keys : array($keys);

		$count = 0;
		foreach($keys as $key) {
			$count += $this->dbCount(array(array($key,'=',$login)));
		}

		return $count;
	}

	/**
	 * Retourne le nombre minimum de caractères pour les mots de passe.
	 *
	 */
	public static function passwordMinNumber() {
		return 6;
	}

	/**
	 * Retourne le niveau de l'utilisateur courant si $level n'est pas défini,
	 * Teste si les droits de l'utilisateur courant son supérieurs ou égaux à $level sinon
	 *
	 * @param int $level
	 * cf : ( $GLOBALS['acid']['lvl']['visitor'], $GLOBALS['acid']['lvl']['robot'], $GLOBALS['acid']['lvl']['unvalid'], $GLOBALS['acid']['lvl']['registred'], $GLOBALS['acid']['lvl']['member'], $GLOBALS['acid']['lvl']['vip'], $GLOBALS['acid']['lvl']['modo'], $GLOBALS['acid']['lvl']['admin'], $GLOBALS['acid']['lvl']['dev'] )
	 *
	 * @return bool
	 */
	public static function curLevel($level=null) {
	    if ($level === null) {
	        return User::curValue('level',0);
	    } else {
	        return (User::curValue('level',0) >= $level);
	    }
	}

	/**
	 * Retourne l'utilisateur courant
	 *
	 *
	 * @return object
	 */
	public static function curUser() {
	    $user = AcidSession::get('user');
	    $my_user = new User();

	    if ($user) {
	    	$my_user->initVars($user);
	    }

	    return $my_user;
	}

	/**
	 * Retourne une variable associée à l'utilisateur courant
	 *
	 * @param string $key nom du champ dont on doit retourner la valeur
	 * @param mixed $def valeur à retourner si pas d'utilisateur courant
	 *
	 * @return mixed
	 */
	public static function curValue($key,$def=null) {
	    $user = AcidSession::get('user');
	    return isset($user[$key]) ?  $user[$key] : $def;
	}

	/**
	 * Retourne vrai si l'identifiant en entrée correspond à l'utilisateur courant
	 *
	 * @param int $id_user
	 *
	 * @return bool
	 */
	public static function isUser($id_user) {
		$cur_id = static::curValue('id_user');
	    return ($cur_id &&  ($cur_id == $id_user));
	}

	/**
	 * Initialise une page selon l'utilisateur relatif aux sessions ou aux cookies, et créer la session associée.
	 *
	 * @param bool $autolog make a cookie for autolog
	 *
	 */
	public static function initUser($autolog=true) {
		$my_user = new User();
	    if ($user = AcidSession::get('user')) {
			if ($user['id_user']) {
			    $my_user->dbInit($user['id_user']);
			} else {
			    // TODO Check User Agent For Robot detection
			}

			$my_user->sessionMake(null,$autolog);
		}


        if (!$my_user->getId() && isset($_COOKIE['user'])) {
			if (isset($_COOKIE['user']['id']) && isset($_COOKIE['user']['code'])) {
				$my_user->dbInit($_COOKIE['user']['id']);
				if ($my_user->getCookieCode() == $_COOKIE['user']['code']) {
					$my_user->sessionMake(null,$autolog);
				} else {
				    $my_user->cleanVars();
					Acid::mod('User')->unsetCookie();
				}
			}
		}

		//$GLOBALS['user'] = $my_user;
	}

	/**
	 * Met à jour le profil de l'utilisateur
	 */
	public static function updateInstance() {
		$user = User::curUser();
		if ($user->getId()) {
			$changes = $user->initVars(array('last_lang'=>Acid::get('lang:current'),'date_connexion'=>AcidVarDateTime::now()));
			$user->dbUpdate($changes);
		}
	}

	/**
	 * Retourne un tableau des groupes associés à utilisateurs
	 *
	 *
	 * @return string
	 */
	public function getGroups() {
		$tab = array();
		if ($this->get('id_group')) {
			$tab[] = $this->get('id_group');
		}

		return $tab;
	}

	/**
	 * Retourne le nom complet de l'utilisateur
	 */
	public function fullName($hsc=true) {
		$name = trim(Acid::trad($this->get('firstname').' '.$this->get('lastname')));
		$name =  $name ? $name : $this->get('username');
		return $hsc ? htmlspecialchars($name) : $name;
	}

	/**
	 * Retourne l'adresse complete de l'utilisateur
	 */
	public function address($hsc=true) {
		$address = trim(Acid::trad($this->get('address').' '.$this->get('cp').' '.$this->get('city').' '.$this->get('country')));
		return $hsc ? htmlspecialchars($address) : $address;
	}

	/**
	 * Retourne une forme hachée du mot de passe associé à l'objet.
	 *
	 *
	 * @return string
	 */
	public function getCookieCode() {
		return Acid::hash($this->get('username') . Acid::get('hash:salt') . $this->get('password'));
	}

	/**
	 * Créer les cookies de l'utilisateur.
	 *
	 * @param bool $autolog Connexion automatique
	 */
	public function setCookie($autolog) {

	    $expire = (Acid::get('cookie:expire') == 0) ? time() + 63072000 : time() + Acid::get('cookie:expire');

		$ident = isset($this->config['identification']) ? $this->config['identification'] : array('username');
		$ident = !is_array($ident) ? array($ident) : $ident;

		setcookie(	'user[login]',$this->get($ident[0]), $expire,
					Acid::get('url:folder'), Acid::get('url:domain'),
		            Acid::get('session:secure'),Acid::get('session:httponly'));

		if ($autolog || isset($_COOKIE['user']['id'])) {
			setcookie(	'user[id]',$this->get('id_user'), $expire,
						Acid::get('url:folder'), Acid::get('url:domain'),
		                Acid::get('session:secure'),Acid::get('session:httponly'));
		    setcookie(	'user[code]',$this->getCookieCode(), $expire,
		    			Acid::get('url:folder'), Acid::get('url:domain'),
		                Acid::get('session:secure'),Acid::get('session:httponly'));
		}
	}


	/**
	 * Supprime les cookies de l'utilisateur.
	 */
	public static function unsetCookie() {
		setcookie(	'user[id]','',time() - 63072000,Acid::get('url:folder'), Acid::get('url:domain'),
					Acid::get('session:secure'),Acid::get('session:httponly'));
		setcookie('user[code]','',time() - 63072000,Acid::get('url:folder'), Acid::get('url:domain'),
					Acid::get('session:secure'),Acid::get('session:httponly'));
	}



	/* *****************************
	 * Traitement des formulaires
	 * *****************************/

	/**
	 * Retourne un tableau de champs autorisés à l'inscription
	 * @return multitype:
	 */
	public static function exeUserCreateKeys() {
		//$mod = self::build();
		//$exclued = array('id_user','id_group','user_salt','username','password','email','level','date_creation','date_activation','ip');
		//return array_diff($mod->getKeys(),$exclued);
		return array('firstname','lastname','address','cp','city','country','phone','lang');
	}


	/**
	 * Inscription d'un compte utilisateur
	 * @param array $vals
	 * @param array $post
	 * @param array $files
	 */
	public static function exeUserCreate($vals=null,$post=null,$files=null) {
		$post = $post===null ? $_POST : $post;
		$files = $files===null ? $_FILES : $files;
		$vals = $vals===null ? $post : $vals;

		$login = Acid::sessExist('connexion:login') ? Acid::sessGet('connexion:login') : (isset($vals['username']) ? $vals['username'] : '');
		$email = Acid::sessExist('connexion:email') ? Acid::sessGet('connexion:email') : (isset($vals['email']) ? $vals['email'] : '');
		$user_salt = Acid::sessExist('connexion:user_salt') ? Acid::sessGet('connexion:user_salt') : (isset($vals['user_salt']) ? $vals['user_salt'] : '');
		$pass = Acid::sessExist('connexion:pass') ? Acid::sessGet('connexion:pass') : (isset($vals['password']) ? $vals['password'] : '');

		$my_user = new User();
		$my_user->initVars( array(
				'username'=>$login,
				'user_salt'=>$user_salt,
				'password'=>static::getHashedPassword($pass,$user_salt),
				'email'=>$email,
				'lang'=>Acid::get('lang:current'),
				'level'=>static::getLevelNextInscription(),
				'active'=>static::getActiveNextInscription(),
				'date_creation'=>AcidVarDatetime::now(),
				'ip_inscription'=>$_SERVER['REMOTE_ADDR']
		));

		if ($vals) {
			if ($initkeys = static::exeUserCreateKeys()) {
				$init = $vals;
				foreach ($init as $ikey=>$ival) {
					if (!in_array($ikey,$initkeys)) {
						unset($init[$ikey]);
					}
				}
				$my_user->initVars($init);
			}
		}

		$my_user->dbAdd();

		User::newInscription($login,$email,$pass,$my_user);

		Acid::sessSet('connexion',array());
		$my_user->sessionMake();

		if (!User::curLevel(static::getLevelNextActivation())) {
			AcidDialog::add('info',Acid::trad('user_valid_mail_sent'));
		}

		static::exeUserAction($vals,$my_user);

		return  $my_user;
	}

	/**
	 * Traitement d'actions post inscription
	 * @param array $vals
	 * @param object $user
	 */
	public static function exeUserAction($vals=null,$user=null) {
		$user = $user===null ? User::curUser() : $user;
		$vals = $vals===null ? $_POST : $vals;

		if (Acid::sessGet('useraction:waitlogin')) {
			if (Acid::sessExist('useraction:function:name') && is_callable(Acid::sessGet('useraction:function:name'))) {
				$name = Acid::sessGet('useraction:function:name');
				$args = Acid::sessGet('useraction:function:args') ? Acid::sessGet('useraction:function:args') : array();
				foreach ($args as $karg => $arg) {
					if ($arg == '__USER__') {
						$args[$karg] = $user;
					}elseif($arg == '__VALS__') {
						$args[$karg] = $vals;
					}
				}
				$res_action = call_user_func_array($name,$args);
				Acid::sessKill('useraction');

				return $res_action;
			}
		}
	}

	/**
	 * Traite les différentes procédures d'administration d'un utilisateur depuis un formulaire.
	 */
	public static function exeUser() {

		global $acid;

		$sess = &AcidSession::getInstance()->data;
		$user = Acid::mod('User');

		if (isset($_POST['connexion_do'])) {
			switch($_POST['connexion_do']) {

			    case 'inscription' :
					if (isset($_POST['login']) && isset($_POST['email']) && isset($_POST['pass']) && isset($_POST['confirmation'])) {

					    $sess['connexion']['login'] = trim($_POST['login']);
						$sess['connexion']['email'] = trim($_POST['email']);
						if ($_POST['pass'] == $_POST['confirmation']) {
							$sess['connexion']['pass'] = trim($_POST['pass']);
							$sess['connexion']['user_salt'] = static::getRandPasswordSalt();
						}else{
							$sess['connexion']['pass'] = '';
							AcidDialog::add('error',Acid::trad('user_error_pass_dismatch'));
						}

						$user_valid = $pass_valid = $email_valid = false;

						// Vérification Login
						if (!empty($sess['connexion']['login'])) {
							if (strlen($sess['connexion']['login']) < 18) {
								if ( ! $user->loginExists($sess['connexion']['login'])) {
									$user_valid = true;
								}else{
									AcidDialog::add('error',Acid::trad('user_error_user_exists'));
									$sess['connexion']['login'] = '';
								}
							}else{
								AcidDialog::add('error',Acid::trad('user_error_login_too_long'));
							}
						}else{
							AcidDialog::add('error',Acid::trad('user_ask_choose_login'));
						}

						// Vérification du pass
						if (!empty($sess['connexion']['pass'])) {
							if (strlen($sess['connexion']['pass']) >= static::passwordMinNumber()) {
								$pass_valid = true;
							} else {
								AcidDialog::add('error',Acid::trad('user_error_pass_nbc',array('__NUM__'=>static::passwordMinNumber())));
							}
						}else{
							AcidDialog::add('error',Acid::trad('user_ask_choose_pass'));
						}

						// Vérification de l'email
						if (!empty($sess['connexion']['email'])) {
							$my_email = new AcidVarEmail();
							if ($my_email->validEntry($sess['connexion']['email'])) {
								if ( ! $user->dbCount(array(array('email','=',$sess['connexion']['email'])))) {
									$email_valid = true;
								} else {

									$link = '<a href="'.Acid::get('user:page').'?pass_oublie='.htmlspecialchars($sess['connexion']['email']).'">'. "\n" .
											Acid::trad('user_bad_password_forget_innerlink') .
											'</a>';
									$e_msg = Acid::trad('user_bad_password_forget_link',array('__LINK__'=>$link));

									AcidDialog::add('error',$e_msg);
								}

							} else {
								AcidDialog::add('error',Acid::trad('user_error_email_invalid_format'));
							}
						} else {
							AcidDialog::add('error',Acid::trad('user_ask_set_mail'));
						}

						//Si tout est valide
						if ($user_valid && $pass_valid && $email_valid) {

							//Création de l'utilisateur
							static::exeUserCreate();

						}
					}
				break;





				case 'change_email' :

					if (User::curLevel(static::getLevelBeforeActivation()) && isset($_POST['email'])) {
						$email_valid = false;
						if (!empty($_POST['email'])) {
							if ($_POST['email'] != $sess['user']['email']) {
								$my_mail = new AcidVarEmail('test');
								if ($my_mail->validEntry($_POST['email'])) {
									if ( ! $user->dbCount(array(array('email','=',$_POST['email'])))) {
										$email_valid = true;
									}else{
										$link = '<a href="'.$acid['user']['page'].'?pass_oublie='.htmlspecialchars($_POST['email']).'">'. "\n" .
													Acid::trad('user_bad_password_forget_innerlink') .
												'</a>';
										$e_msg = Acid::trad('user_bad_password_forget_link',array('__LINK__'=>$link));

										AcidDialog::add('error',$e_msg);
									}

								}else {
									AcidDialog::add('error',Acid::trad('user_error_email_invalid_format'));
								}
							} else {
								AcidDialog::add('error',Acid::trad('user_error_same_email'));
							}
						}else{
							AcidDialog::add('error',Acid::trad('user_ask_set_mail'));
						}

						if ($email_valid) {
						    User::changeEmail($sess['user']['username'],$sess['user']['email'],$_POST['email']);
							AcidDialog::add('error',Acid::trad('user_mail_sent',array('__MAIL__'=>$_POST['email'])));
							$_POST['next_page'] = Acid::get('user:page');
						}

					}
				break;



				case 'send_mail_confirmation' :
					if (User::curLevel() === static::getLevelBeforeActivation()) {
						User::newInscription($sess['user']['username'],$sess['user']['email'],false,null,true);
						AcidDialog::add('info',Acid::trad('user_mail_sent',array('__MAIL__'=>$sess['user']['email'])));
					}
				break;


				case 'change_password' :
					if (isset($_POST['old_pass']) && isset($_POST['new_pass']) && isset($_POST['confirmation'])) {
						if (static::getHashedPassword($_POST['old_pass'],$sess['user']['user_salt']) === $sess['user']['password']) {
							if (strlen($_POST['new_pass']) >= static::passwordMinNumber()) {
								if ($_POST['new_pass'] == $_POST['confirmation']) {
									$my_user = new User($sess['user']['id_user']);
									$my_user->initVars(array('password'=>static::getHashedPassword($_POST['new_pass'],$my_user->get('user_salt'))));
									$my_user->dbUpdate(array('password'));
									$my_user->sessionMake();

									AcidDialog::add('success',Acid::trad('user_password_change_success'));
								}else{
									AcidDialog::add('error',Acid::trad('user_error_pass_dismatch'));
								}
							}else{
								AcidDialog::add('error',Acid::trad('user_error_pass_nbc',array('__NUM__'=>static::passwordMinNumber())));
							}
						}else{
							AcidDialog::add('error',Acid::trad('user_error_bad_cur_password'));
						}
					}
				break;



				case 'forget_pass' :

					$next_page = null;
					if (isset($_POST['next_page'])) {
						$next_page = $_POST['next_page'];
						unset($_POST['next_page']);
					}

					if (isset($_GET['pass_oublie']) && isset($_GET['code']) && isset($_POST['pass']) && isset($_POST['confirmation'])) {
						if (strlen($_POST['pass']) >= static::passwordMinNumber()) {
							if ($_POST['pass'] === $_POST['confirmation']) {

								if ($rep = $user->dbList(array(array('email','=',$_GET['pass_oublie'])))) {
									$res = $rep[0];
								    $code = Acid::hash(Acid::get('hash:salt').$res['email'].$res['password']);
									if ($_GET['code'] === $code) {

										$my_user = new User();
										$my_user->initVars($res);
										$my_user->initVars(array('password'=>static::getHashedPassword($_POST['pass'],$my_user->get('user_salt'))));
										$updates = array('password');
										AcidDialog::add('success',Acid::trad('user_password_change_success'));
										if ($my_user->get('level') === static::getLevelBeforeActivation()) {
											$my_user->initVars(array('level'=>$my_user->getLevelNextActivation(),'active'=>$my_user->getActiveNextActivation(),'date_activation'=>date('Y-m-d H:i:s')));
											array_push($updates,'level','date_activation');
											AcidDialog::add('success',Acid::trad('user_valid_mail_success'));
										}
										$my_user->dbUpdate($updates);
										$my_user->sessionMake();

										if ($next_page) {
											$_POST['next_page'] = $next_page;
										}elseif ($next_page !== 0) {
											$_POST['next_page'] = $acid['user']['page'];
										}

										$sucess = true;

									}else{
										Acid::log('hack','Wrong code for changing pass of ' . $_GET['pass_oublie']);
									}
								}else{
									Acid::log('hack','Wrong code for changing pass of unexisting email ' . $_GET['pass_oublie']);
								}
							}else{
								AcidDialog::add('error', Acid::trad('user_error_pass_dismatch'));
							}
						}else{
							AcidDialog::add('error',Acid::trad('user_error_pass_nbc',array('__NUM__'=>static::passwordMinNumber())));
						}
					}

					if (!$sucess) {
						unset($_POST['next_page']);
					}
				break;
			}
		}
	}



	/* *****************************
	 * Emails
	 * *****************************/

	/**
	 * Envoi un email à l'utilisateur
	 * @param string $subject
	 * @param string $body
	 * @param string $from_name
	 * @param string $from_email
	 * @param string $email
	 * @return boolean
	 */
	public function sendMail($subject,$body,$from_name=null,$from_email=null,$email=null) {
		$from_name = $from_name===null ? Acid::get('site:name') : $from_name;
		$from_email = $from_email===null ? Acid::get('site:email') : $from_email;
		$email = $email===null ? $this->get('email') : $email;

		return Mailer::send($from_name,$from_email,$email,$subject,$body);
	}

	/**
	 * Retourne le sujet d'un email en fonction de $subject
	 * @param string $subject
	 * @param array $replace
	 * @param boolean $staff
	 * @return Ambigous <string, string, mixed, NULL, array>|unknown
	 */
	public function subjectMail($subject,$replace=array(),$staff=false) {

		if (!isset($replace['__SITE__'])) {
			$replace['__SITE__'] = Acid::get('site:name');
		}

		if (!isset($replace['__SITE_EMAIL__'])) {
			$replace['__SITE_EMAIL__'] = Acid::get('site:email');
		}

		if (!isset($replace['__SITE_URL__'])) {
			$replace['__SITE_URL__'] = Acid::get('url:system');
		}

		if (!isset($replace['__USERNAME__'])) {
			$replace['__USERNAME__'] = $this->trad('username');
		}

		$prefix = 'user_mail_subject_';
		$key = $prefix.$subject;

		return Acid::tradExists($key) ? Acid::trad($key,$replace) : Acid::trad($subject,$replace);

	}

	/**
	 * Appelle un template mail à destination de utilisateur
	 * @param string $tpl
	 * @param array $vars
	 * @param object $object
	 * @return string
	 */
	public function bodyMail($tpl,$vars=array(),$object=null) {
		return Acid::tpl($tpl,$vars,$object);
	}

	/**
	 * Retourne le sujet d'un email destiné au staff en fonction de $subject
	 * @param string $subject
	 * @param array $replace
	 */
	public static function subjectMailStaff($subject,$replace=array()) {
		$u = new User();
		return $u->subjectMail($subject,$replace,true);
	}

	/**
	 * Appelle un template mail à destination du staff
	 * @param string $tpl
	 * @param array $vars
	 * @param object $object
	 * @return string
	 */
	public static function bodyMailStaff($tpl,$vars=array(),$object=null) {
		return Acid::tpl($tpl,$vars,$object);
	}

	/**
	 * Gère l'envoi du mail d'inscription d'un utilisateur.
	 *
	 * @param string $user username
	 * @param string $email email
	 * @param string $pass password
	 * @param string $usermod objet user
	 * @param string $need_validation validation email requise ?
	 */
	public static function newInscription($user,$email,$pass,$usermod=null,$need_validation=null) {
		Acid::load('tools/mail.php');
		$usermod = $usermod===null ? static::build() : $usermod;
		$usermod = is_object($usermod) ? $usermod : static::build($usermod);

		$new = false;

		$new = ($pass != false);
		$subject = 'Inscription à ' . Acid::get('site:name');
		$link = Acid::get('url:scheme').Acid::get('url:domain').Acid::get('user:page').'?valid_email='.$email.'&code='.Acid::hash(Acid::get('hash:salt').$email);

		if ($need_validation===null) {
			$need_validation = $usermod->getId() ? ($usermod->get('level') < static::getLevelNextActivation()) : (static::getLevelNextInscription() < static::getLevelNextActivation());
		}

		$vars = array('username'=>$user,'pass'=>$pass,'email'=>$email,'link'=>$link,'need_validation'=>$need_validation);
		$body = $usermod->bodyMail('modules/user/mail/user-new-inscription.tpl',$vars,Acid::mod(get_called_class()));
		$subject = $usermod->subjectMail('new_user_subscribe',array('__USER__'=>$user));
		$usermod->sendMail($subject,$body);

		if ($new) {
			$subject = static::subjectMailStaff('new_user_subscribe_admin',array('__USER__'=>$user));
			$body = static::bodyMailStaff('modules/user/mail/admin-new-inscription.tpl',$vars,$usermod);
			Mailer::sendStaff($subject,$body);
		}

	}


	/**
	 * Gère le changement d'adresse e-mail d'un utilisateur.
	 * @param string $user
	 * @param string $email
	 * @param string $new_email
	 */
	public static function changeEmail($user,$email,$new_email) {
		Acid::load('tools/mail.php');

		$link = Acid::get('url:scheme').Acid::get('url:domain').Acid::get('user:page').'?new_email='.$new_email.'&old_email='.$email.'&code='.Acid::hash(Acid::get('hash:salt').$email.$new_email);

		$vars = array('username'=>$user,'new_mail'=>$new_email,'email'=>$email,'link'=>$link);
		$body = User::curUser()->bodyMail('modules/user/mail/user-new-mail.tpl',$vars,Acid::mod(get_called_class()));
		$subject = User::curUser()->subjectMail('user_change_mail');
		User::curUser()->sendMail($subject,$body,null,null,$new_email);
	}

	/**
	 * Gère l'oubli de mot de passe d'un utilisateur
	 * Envoie un e-mail permettant le changement du mot de passe.
	 *
	 * @param string $email
	 * @param string $user
	 * @param string $pass
	 * @param string $src_page
	 */
	public static function passOublie($email,$user,$pass,$src_page=null) {
		$src_page = ($src_page===null) ? Acid::get('user:page') : $src_page;

		Acid::load('tools/mail.php');

		$code = Acid::hash(Acid::get('hash:salt').$email.$pass);
		$link = Acid::get('url:scheme').Acid::get('url:domain') . $src_page . '?pass_oublie='.$email.'&code='. $code;

		$vars = array('username'=>$user,'pass'=>$pass,'email'=>$email,'link'=>$link,'src_page'=>$src_page);
		$body = User::curUser()->bodyMail('modules/user/mail/user-forget-password.tpl',$vars,Acid::mod(get_called_class()));
		$subject = User::curUser()->subjectMail('user_forget_pass');
		User::curUser()->sendMail($subject,$body,null,null,$email);
	}


	/*
	public static function newPrivateMessage($email,$from_user,$dest_user,$title,$id_message) {
		global $acid;

		Acid::load('tools/mail.php');

		$link = $acid['url']['scheme'].$acid['url']['domain'] . $acid['user']['page'] . '?page=messagerie&mailbox=' . $id_message ;
		$vars = array('dest_user'=>$dest_user,'from_user'=>$from_user,'title'=>$title,'email'=>$email,'link'=>$link);
		$body = User::curUser()->bodyMail('modules/user/mail/user-new-private-message.tpl',$vars,Acid::mod(get_called_class()));
		$subject = User::curUser()->subjectMail('user_new_private');
		User::curUser()->sendMail($subject,$body,null,null,$email);

	}
	*/

	/**
	 * Retourne un grain de sel utilisateur aléatoire.
	 *
	 * @return string
	 */
	public static function getRandPasswordSalt() {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$nbchars = strlen($chars);
		$my_salt = '';

		$salt_size = 8;
		for ($i=0;$i<=$salt_size;$i++) {
			$my_salt .= $chars[rand(0,$nbchars-1)];
		}
		return $my_salt;
	}

	/**
	 * Retourne le mot de passe en entrée sous sa forme hachée.
	 *
	 * @param unknown_type $pass
	 * @param string $user_salt grain de sel utilistateur
	 * @return string
	 */
	public static function getHashedPassword($pass,$user_salt='') {
		return Acid::hash(Acid::get('hash:salt').$pass.$user_salt);
	}


	/**
	 * Gère la connexion d'un utilisateur
	 * Retourne true en cas de réussite, false sinon.
	 *
	 *
	 * @param string $login Login.
	 * @param string $pass Mot de passe.
	 * @param boolean $session_make True si on créer une session.
	 * @param boolean $autolog True si on active l'auto-connexion.
	 * @param boolean $print_error true pour ajouter un dialogue en cas d'erreur
	 * @param boolean $hashed_password true si le mdp fourni est déjà haché
	 * @return boolean
	 */
	public static function login($login,$pass,$session_make=false,$autolog=false,$print_error=true,$hashed_password=false) {

		$comp = new User();

		$identifiants = $comp->getConfig('identification') !== null ? $comp->getConfig('identification') : array('username');
		$identifiants = !is_array($identifiants) ? array($identifiants) : $identifiants;

		foreach($identifiants as $ident) {
			if ($comp->dbInitSearch(array($ident=>$login))) {
				if ($comp->get('active')) {
					$the_password = $hashed_password ?  $pass : User::getHashedPassword($pass,$comp->get('user_salt'));
					if ($comp->get('password') == $the_password) {
						// Vérification de la date de validité du compte (00-00-0000 => illimité)
						if ( ($comp->get('date_deactivation') == '0000-00-00 00:00:00') || (strtotime($comp->get('date_deactivation')) > time()) ) {

							if ($session_make) {
								$comp->sessionMake(null,$autolog);
							}

							$sess = AcidSession::getInstance();
							$sess->id_user = $comp->getId();

							return true;
						} else {
							Acid::log('user', 'Identification failed for ' . $login . ' (account has expired)');

							if ($print_error) {
								AcidDialog::add('error',Acid::trad('user_date_expired'));
							}

							return false;
						}
					}
				}
			}
		}

		Acid::log('user', 'Identification failed for ' . $login);

	    if ($print_error) {
			AcidDialog::add('error',Acid::trad('user_error_log'));
	    }

		return false;
	}

	/**
	 * Gère la déconnexion de l'utilisateur courant.
	 */
	public static function logout() {
	    AcidSession::destroy();
	    User::unsetCookie();
	}




	/* *****************************
	 * Gestion de l'avatar
	 * *****************************/


	/**
	 * Attribue un avatar à un utilisateur.
	 * @param string $ext Extension de l'Image.
	 * @param int $img Numéro de l'avatar.
	 */
	public function setUrl($ext,$img) {
		$this->vars['image_'.$img]->initVal($this->getId(),$ext,$img);
		//$this->vars['image_'.$img]->setVal($this->dir_path.sprintf('%010d',$this->getId()).'_'.$img.'.'.$ext);
	}


									/* *****************************
									 * *****************************
									 * ******  Formulaires  ********
									 * *****************************
									 * *****************************/


	/* *****************************
	 * Classic log-in
	 * *****************************/

	/**
	 * Retourne un formulaire de connexion.
	 *
	 * @param bool $focus True pour selectionner le formulaire par défaut.
	 *
	 * @return string
	 */
	public static function printLogginForm($focus = true) {
		return Acid::tpl('modules/user/log-form.tpl',array('focus'=>$focus),Acid::mod(get_called_class()));;
	}

	/**
	 * Retourne un formulaire de connexion.
	 *
	 * @param bool $focus True pour selectionner le formulaire par défaut.
	 *
	 * @return string
	 */
	public static function printLoggoutForm($focus = true) {
		return Acid::tpl('modules/user/logout-form.tpl',array('focus'=>$focus),Acid::mod(get_called_class()));;
	}


	/**
	 * Retourne un formulaire de connexion admin.
	 * @param string $msg message personnalisé
	 *
	 * @return string
	 */
	public static function printAdminLogginForm($msg=null) {
		return Acid::tpl('admin/admin-form.tpl',array('msg'=>$msg),Acid::mod(get_called_class()));
	}


	/* *****************************
	 * Validation du compte email
	 * *****************************/

	/**
	 * Valide un utilisateur si le code renseigné en entrée correpond à l'e-mail en entrée
	 * Retourne texte de confirmation si réussi.
	 *
	 * @param string $email
	 * @param unknostringwn_type $code
	 *
	 * @return string
	 */
	public function emailValidation($email,$code) {
		$output = '';

		if ($res = $this->dbList(array(array('email','=',$email)))) {
			$tab = $res[0];
		    if ($tab['level'] == static::getLevelBeforeActivation()) {
				if (Acid::hash(Acid::get('hash:salt').$tab['email']) === $code) {

					$my_user = new User();
					$my_user->initVars($tab);
					$my_user->initVars(array('level'=>$this->getLevelNextActivation(),'active'=>$this->getActiveNextActivation(),'date_activation'=>date('Y-m-d H:i:s')));
					$my_user->dbUpdate(array('level','date_activation'));
					$my_user->sessionMake();
					$output .= $this->printResEmailValidation();
				}
			}
		}
		return $output;
	}

	/**
	 * Retourne un texte de confirmation de validation de mail.
	 *
	 *
	 * @return string
	 */
	public function printResEmailValidation() {
	    return	Acid::tpl('modules/user/res-validation.tpl',array(),$this);
	}

	/**
	 * Retourne le niveau de membre relatif à un compte activé.
	 * @return int
	 */
	public static function getLevelNextActivation() {
	    return Acid::get('lvl:member');
	}

	/**
	 * Retourne le niveau de membre relatif à un compte non activé.
	 * @return int
	 */
	public static function getLevelBeforeActivation() {
		return Acid::get('lvl:unvalid');
	}

	/**
	 * Retourne le niveau de membre relatif à un compte dès l'inscription.
	 * @return int
	 */
	public static function getLevelNextInscription() {
		return static::getLevelBeforeActivation();
	}

	/**
	 * Retourne l'état actif de membre relatif à un compte activé.
	 * @return int
	 */
	public static function getActiveNextActivation() {
		return 1;
	}

	/**
	 * Retourne  l'état actif de membre relatif à un compte non activé.
	 * @return int
	 */
	public static function getActiveBeforeActivation() {
		return 1;
	}

	/**
	 * Retourne  l'état actifde membre relatif à un compte dès l'inscription.
	 * @return int
	 */
	public static function getActiveNextInscription() {
		return 1;
	}

	/**
	 * Retourne un e-mail de validation.
	 *
	 * @return string
	 */
	public function printEmailValidation() {
		$valid_user = '';
		if (User::curLevel() ===  static::getLevelBeforeActivation()) {
			$valid_user = Acid::tpl('modules/user/validation.tpl',array(),$this);
		}

		return	$valid_user;
	}


	/**
	 * Processus d'administration du module.
	 *
	 */
	public function exePost() {

		if ( (!empty($_POST['password'])) || (!empty($_POST['user_salt'])) ) {

			if (!empty($_POST['password'])) {
				$_POST['user_salt'] = !empty($_POST['user_salt']) ? $_POST['user_salt'] : User::getRandPasswordSalt();
				$_POST['password'] = User::getHashedPassword($_POST['password'],$_POST['user_salt']);
			}else{
				unset($_POST['user_salt']);
				unset($_POST['password']);
			}
		}

		return parent::exePost();
	}


	/**
	 * Processus d'ajout d'un élément.
	 * @param array $vals
	 * @param mixed $dialog
	 *
	 * @return mixed
	 */
	public function postAdd ($vals,$dialog=null) {
		if (isset($vals['level'])) {
			if ($vals['level'] > User::curLevel()) {
				$vals['level'] = User::curLevel();
			}
		}

		return parent::postAdd($vals,$dialog);
	}


	/**
	 * Processus de suppression d'un élément.
	 * @param int $id
	 * @param mixed $dialog
	 *
	 * @return mixed
	 */
	public function postRemove ($id=null,$dialog=null) {
		$obj = new User($id);
		if (User::curLevel($obj->get('level'))) {
			return parent::postRemove($id,$dialog);
		}
	}


	/**
	 * Processus de mise à jour du module.
	 * @param array $vals
	 * @param mixed $dialog
	 *
	 * @return mixed
	 */
	public function postUpdate($vals,$dialog=null) {
		if (empty($vals['password'])) {
			unset($vals['password']);
			unset($vals['user_salt']);
		}

		if (isset($vals['level'])) {
			if ($vals['level'] > User::curLevel()) {
				$vals['level'] = User::curLevel();
			}
		}

		$obj = new User($vals['id_user']);

		if (User::curLevel($obj->get('level'))) {
			return parent::postUpdate($vals,$dialog);
		}

	}


	/**
	 * Processus d'affichage d'un élément.
	 *
	 * @return string
	 */
	public function printAdminElt () {
		if (User::curLevel($this->get('level'))) {
			return parent::printAdminElt();
		}
	}


	/**
	 * Formulaire admin de mise à jour du module.
	 *
	 * @return string
	 */
	public function printAdminUpdate() {
		$this->initVars(array('password'=>''));
		if (User::curLevel($this->get('level'))) {
			return parent::printAdminUpdate();
		}
	}

	/**
	 * Formulaire admin d'ajout du module.
	 *
	 * @return string
	 */
	public function printAdminAdd() {
		$this->initVars(array('user_salt'=>User::getRandPasswordSalt()));
		return parent::printAdminAdd();
	}



	/* *****************************
	 * Changement de l'adresse email
	 * *****************************/

	/**
	 * Change $old_email en $new_email si $code est bien associé à $old_email, et retourne un texte de confirmation.
	 *
	 * @param string $old_email
	 * @param string $new_email
	 * @param string $code
	 *
	 * @return string
	 */
	public function emailChange($old_email,$new_email,$code) {
		$output = '';

		$success = false;
		if ($res = $this->dbList(array(array('email','=',$old_email)))) {
			$tab = $res[0];
		    if (Acid::hash(Acid::get('hash:salt').$old_email.$new_email) === $code) {
				$success = true;
				$my_user = new User();
				$my_user->initVars($tab);
				$my_user->initVars(array('email'=>$new_email));
				if ($tab['level'] == $this->getLevelBeforeActivation()) {
					$my_user->initVars(array('level'=>$this->getLevelNextActivation(),'active'=>$this->getActiveNextActivation(),'date_activation'=>date('Y-m-d H:i:s')));
				}
				$my_user->dbUpdate(array('level','email'));
				$my_user->sessionMake();
			}
		}

		$output = $this->printResEmailChange($success);

		return $output;
	}

	/**
	 * Retourne un texte de confirmation de changement d'adresse e-mail.
	 *
	 * @param bool $success Si true, on veut signaler que l'opération s'est bien déroulée, false sinon.
	 *
	 * @return string
	 */
	public function printResEmailChange($success) {

	    if ($success) {
	  	    return Acid::tpl('modules/user/res-email-change.tpl',array(),$this);
	    }

	    return '';
	}


	/**
	 * Retourne un formulaire de changement d'e-mail.
	 *
	 *
	 * @return string
	 */
	public static function printEmailChange() {
		$user = AcidSession::get('user');

		return Acid::tpl('modules/user/email-change.tpl',array('user'=>$user),Acid::mod(get_called_class()));
	}






	/* *****************************
	 * Form change password
	 * *****************************/

	/**
	 * Retourne un formulaire de changement de mot de passe.
	 *
	 *
	 * @return string
	 */
	public static function printPasswordChange() {
		$user = AcidSession::get('user');

		return Acid::tpl('modules/user/password-change.tpl',array('user'=>$user),Acid::mod(get_called_class()));
	}


	/* *****************************
	 * Mot de passe oublié
	 * Demande de nouveau mot de passe
	 * *****************************/

	/**
	 * Gère le formulaire d'oubli de mot de passe.
	 *
	 * @param string $email
	 * @param string $src_page
	 *
	 * @return string
	 */
	public function printPasswordForgotten($email,$src_page=null) {
		$output = '';

	    if (!empty($email)) {
			$content = '';

			if ($res = $this->dbList(array(array('email','=',$email)))) {
				$tab = $res[0];
			    if (!isset($_GET['code'])) {
					User::passOublie($tab['email'],$tab['username'],$tab['password'],$src_page);

			        $content = Acid::tpl('modules/user/res-forget.tpl',array('src_page'=>$src_page,'email'=>htmlspecialchars($tab['email'])),Acid::mod(get_called_class()));
				}else{
					$code = Acid::hash(Acid::get('hash:salt').$tab['email'].$tab['password']);
					if ($_GET['code'] == $code) {
						$content = Acid::tpl('modules/user/new-password-forget-form.tpl',array('src_page'=>$src_page),Acid::mod(get_called_class()));
					}
				}
			}else{
				$content = Acid::tpl('modules/user/new-email-forget-form.tpl',array('src_page'=>$src_page,'email'=>htmlspecialchars($email)),Acid::mod(get_called_class()));
			}
		}else{
			$content = Acid::tpl('modules/user/ask-email-forget-form.tpl',array('src_page'=>$src_page,'email'=>htmlspecialchars($email)),Acid::mod(get_called_class()));
		}

		$output =	'<div class="user_content">'. "\n" .
					'	' . $content . "\n" .
					'</div>' . "\n";




		return $output;
	}


	/**
	 * Gère le formulaire de gestion de compte utilisateur.
	 *
	 * @return string
	 */
	public static function printUserForms() {
		$user_form = '';
		$my_user = Acid::mod('User');

		if (isset($_GET['valid_email'])) {
			if (isset($_GET['code'])) {
		        $user_form .= $my_user->emailValidation($_GET['valid_email'],$_GET['code']);
			}
		}

		if (isset($_GET['pass_oublie'])) {
		    $user_form .= $my_user->printPasswordForgotten($_GET['pass_oublie']);
		}

		if (isset($_GET['new_email']) && isset($_GET['old_email']) && isset($_GET['code'])) {
			$user_form .= $my_user->emailChange($_GET['old_email'],$_GET['new_email'],$_GET['code']);
		}

		if (isset($_GET['change']) && $_GET['change'] == 'email') {
			$user_form .= User::printEmailChange();
		}

		if (User::curLevel(User::getLevelNextActivation())) {

			if (isset($_GET['change']) && $_GET['change'] == 'password') {
			    $user_form .= User::printPasswordChange();
			}
		}

		if (!empty($user_form)) {
		    $user_form .= Acid::tpl('modules/user/form-back.tpl',array('link'=>Acid::get('user:page')));
		}

		return $user_form;
	}


	/**
	 * Retourne le formulaire d'inscription d'un utilisateur.
	 *
	 *
	 * @return string
	 */
	public static function printCreateForm() {

		$insert = array('login'=>'','pass'=>'','email'=>'');

		if ($sess_co = AcidSession::get('connexion')) {

			foreach ($sess_co as $key=>$val) {

				if (in_array($key, array('login','pass','mail'))) {
					$insert[$key] = htmlspecialchars($val);
				}else{
					$insert[$key] = $val;
				}

			}
		}

		return	Acid::tpl('modules/user/create-form.tpl',$insert,Acid::mod(get_called_class()));
	}


	/**
	 * Retourne la page utilisateur.
	 *
	 *
	 * @return string
	 */
	public static function userPage() {
		return Acid::tpl('modules/user/user-page.tpl',array(),Acid::mod(get_called_class()));
	}


	/**
	 * Retourne l'espace utilisateur selon ses différents états d'authentification.
	 *
	 * @param mixed $forms
	 */
	public static function printUserSpace($forms=null) {
		if ($forms === null) $forms = User::printUserForms(false);

		if (!empty($forms)) return $forms;

		//if ($unvalid = self::printEmailValidation()) return $unvalid;

		if (User::curLevel(User::getLevelNextActivation())) {
			return User::userPage();
		}

		elseif (User::curLevel() === User::getLevelBeforeActivation()) {
			return Acid::mod('User')->printEmailValidation();
		}

		else {
		    return User::printLogginForm();
		}
	}


	/**
	 * Créer une session utilisateur.
	 *
	 * @param int $id Identifiant. Par défaut : NULL
	 * @param bool $connexion_auto
	 */
	public function sessionMake($id=null,$connexion_auto=false) {
		parent::sessionMake($id);

		if (User::curLevel($this->getLevelBeforeActivation())) {
			$this->setCookie($connexion_auto);
	    }
	}

}

?>
