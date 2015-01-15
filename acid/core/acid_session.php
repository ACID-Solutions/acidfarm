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
 *          Acid Session Class
 *
 *****************************************************************************/

/**
 * Utilitaire de Session
 * @package   Core
 */
class AcidSession {

	/**
	 * @var object Instance
	 */
	private static $_session;

	/**
	 * @var string Table SQL de stockage des sessions
	 */
	private $_table;

	/**
	 * @var boolean true si une ligne est trouvée en bdd
	 */
	public $in_db      = false;

	/**
	 * @var int identifiant session
	 */
	public $id         = '';

	/**
	 * @var int identifiant user
	 */
	public $id_user    = 0;

	/**
	 * @var string timestamp d'expiration
	 */
	public $expire     = '';

	/**
	 * @var int ip utilisateur
	 */
	public $user_ip    = '';

	/**
	 * @var int user agent
	 */
	public $user_agent = '';

	/**
	 * @var array les données courantes
	 */
	public $data       = array();

	/**
	 * @var array les données courantes en bdd
	 */
	public $db_data    = array();

	/**
	 * Instancit une session.
	 *
	 * @return void
	 */
	private function __construct() {
		global $acid;
		$this->_table = $acid['session']['table'];
	}

	/**
	 * Méthode de clonage
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Créer ou récupére la session en cours.
	 *
	 * @return object
	 */
	public static function getInstance($cookie=null,$savecookie=true) {

		$cookie = $cookie===null ? $_COOKIE : $cookie;

		try {
			if (Acid::get('session:enable')) {

				if (self::$_session === null) {
					$hash = Acid::hash($_SERVER['REMOTE_ADDR'] . microtime());

					self::$_session = new AcidSession();
					self::$_session->expire = time()+Acid::get('session:expire');

					// First connexion or cookie disable
					if (!isset($cookie[Acid::get('session:name')])) {
						if ($savecookie) {
							AcidCookie::setcookie(  Acid::get('session:name'),$hash,time()+Acid::get('session:expire'),Acid::get('cookie:folder'),
							Acid::get('cookie:domain'),Acid::get('session:secure'),Acid::get('session:httponly'));
						}
						//self::$_session = false;
						$sess_id = $hash;
						$sess = false;
					}

					// Session founded
					else {
						$sess = AcidDB::query(	"SELECT * FROM " . Acid::get('session:table') .
		                    					" WHERE id='".addslashes($cookie[Acid::get('session:name')])."'")->fetch(PDO::FETCH_ASSOC);

						$sess_id = $cookie[Acid::get('session:name')];

						// Récupération des valeurs en BDD
						if ($sess) {
							if (!Acid::get('session:check_ua') || $sess['user_agent'] === $_SERVER['HTTP_USER_AGENT']) {
								if (!Acid::get('session:check_ip') || $sess['user_ip'] === $_SERVER['REMOTE_ADDR']) {
									if ($sess['expire'] >= time()) {
										self::$_session->id = $sess['id'];
										self::$_session->id_user = $sess['id_user'];
										self::$_session->user_ip = $sess['user_ip'];
										self::$_session->user_agent = $sess['user_agent'];
										self::$_session->db_data = self::$_session->data = json_decode($sess['data'],true);
										self::$_session->in_db = true;

									} else {
										Acid::log('session', 'Session expire');
										$sess_id = $hash;
									}
								} else {
									Acid::log('hack','Possible hack attempt : cookie steal (ip doesn\'t match)');
									$sess_id = Acid::hash($_SERVER['REMOTE_ADDR'] . microtime());
								}
							} else {
								Acid::log('hack','Possible hack attempt : cookie steal (user agent doesn\'t match)');
								$sess_id = Acid::hash($_SERVER['HTTP_USER_AGENT'] . microtime());
							}
						}
					}

					// Création du cookie (première connexion ou no cookie)
					if (!self::$_session->id) {
						self::$_session->id = $sess_id;
						self::$_session->id_user = 0;
						self::$_session->user_ip = $_SERVER['REMOTE_ADDR'];
						self::$_session->user_agent = $_SERVER['HTTP_USER_AGENT'];
						self::$_session->data = array('dialog'=>array());
						self::$_session->db_data = array('dialog'=>array());
						if (isset($_COOKIE[Acid::get('session:name')])) {
							self::dbAdd();
							self::$_session->in_db = true;
						}

						Acid::log('session','Session created in DB '.time());
					}

					if ($savecookie) {
						self::cookieUpdate();
					}

					Acid::log('session','Initialazing session');
				}

				return self::$_session;

			}else{

				throw new Exception('');

			}
		} catch (Exception $e) {
			trigger_error("AcidSession use not allowed",E_USER_ERROR);
		}
	}

	/**
	 * Retourne true si une session est définie
	 * @return object
	 */
	public static function instanceExists() {
		return !empty(self::$_session);
	}

	/**
	 * Met à jour les cookies.
	 */
	public static function cookieUpdate() {
		if ($s = self::getInstance()) {
			AcidCookie::setcookie(
			Acid::get('session:name'),
			$s->id,
			$s->expire,
			Acid::get('cookie:path'),
			Acid::get('cookie:domain'),
			Acid::get('session:secure'),
			Acid::get('session:httponly'));
		}
	}

	/**
	 * Met à jour la session en base de données.
	 */
	public static function dbUpdate($cookie=null) {
		$s = self::getInstance($cookie);
		if ($s->in_db) {
			// NOTE : When PHP >= 5.3, use json_encode($str,JSON_HEX_APOS)
			if ($s->db_data !== $s->data) {
				Acid::log('session','Session, updating data');
				AcidDB::exec(   "UPDATE " . Acid::get('session:table') . " SET expire='".addslashes($s->expire)."', id_user='".$s->id_user."', ".
            	    			"data='".str_replace("'","\\'",str_replace("\\","\\\\",json_encode($s->data)))."' WHERE id='".addslashes($s->id)."'");
			} else {
				AcidDB::exec(   "UPDATE " . Acid::get('session:table') . " SET expire='".addslashes($s->expire)."', id_user='".$s->id_user."' WHERE id='".addslashes($s->id)."'");
			}
		}
	}

	/**
	 * Ajoute une session en base de données.
	 *
	 * @return void
	 */
	public static function dbAdd() {
		$s = self::getInstance();
		if ($s->id) {
			AcidDB::exec(	"INSERT INTO " . Acid::get('session:table') . " (id,expire,id_user,user_ip,user_agent,data) ".
            				"VALUES ('".addslashes($s->id)."','".addslashes($s->expire)."','".addslashes($s->id_user)."',".
            						"'".addslashes($s->user_ip)."','".addslashes($s->user_agent)."','[]')");
		}
	}

	/**
	 * Detruit le cookie de session, et efface la session en base de données.
	 */
	public static function destroy() {
		$sess = self::getInstance();
		AcidCookie::setcookie(
		Acid::get('session:name'),
		0,
		time()-63072000,
		Acid::get('cookie:path'),
		Acid::get('cookie:domain'),
		Acid::get('session:secure'),
		Acid::get('session:httponly')
		);
		AcidDB::exec("DELETE FROM " . Acid::get('session:table') . " WHERE id='".addslashes($sess->id)."'");
	}

	/**
	 * Définit des paramètres à la session.
	 * @param array $sets paramètres de la session
	 */
	public static function set($sets) {
		$s = self::getInstance();
		if ($s->id) {
			foreach ($sets as $key => $val) {
				$s->data[$key] = $val;
			}
		}
	}

	/**
	 * Récupère un paramètre défini ou tous les paramètres de la session.
	 * @param string $key identifiant
	 */
	public static function get($key=null) {
		$s = self::getInstance();
		if ($s->id) {
			if ($key === null) {
				return $s->data;
			} elseif (isset($s->data[$key])) {
				return $s->data[$key];
			} else {
				return null;
			}
		}
		return null;
	}

	/**
	 * Vide les données de la session.
	 */
	public static function eraseData() {
		$s = self::getInstance();
		if ($s->id) {
			$s->data = array();
		}
	}

	/**
	 * Nettoie la base de données recueillant les sessions.
	 */
	public static function garbage_collector() {
		AcidDB::exec("DELETE FROM " . Acid::get('session:table') . " WHERE expire < '".time()."'");
	}

	/**
	 * Contrôleur de la mémoire tampon
	 * @param string $key identifiant
	 */
	private static function tmpController($key) {

		if ((Acid::sessExist('session_tmptime:'.$key.':time')) && (Acid::sessExist('session_tmptime:'.$key.':duration'))) {

			$time= Acid::sessGet('session_tmptime:'.$key.':time');
			$duration = Acid::sessGet('session_tmptime:'.$key.':duration');

			if ($duration!==NULL) {
				if ( ($time+$duration) < time() ) {
					 self::tmpKill($key);
					 Acid::log('session','tmp session : '.$key.' has expired');
				}
			}
		}
	}

	/**
	 * Setter de la mémoire tampon
	 * @param string $key identifiant
	 * @param mixed $val valeur à stocker
	 * @param int $duration durée de validité en seconde
	 */
	public static function tmpSet($key,$val,$duration=180) {
		Acid::sessSet('session_tmp:'.$key,$val);
		Acid::sessSet('session_tmptime:'.$key.':time',time());
		Acid::sessSet('session_tmptime:'.$key.':duration',$duration);
		Acid::log('session','Setting tmp session : '.$key.' => '.serialize($val).', duration : '.$duration);
	}

	/**
	 * Getter de la mémoire tampon
	 * @param string $key identifiant
	 * @param mixed $def valeur de retour en cas d'échec
	 * @return mixed
	 */
	public static function tmpGet($key,$def=null) {
		self::tmpController($key);


		if (Acid::sessExist('session_tmp:'.$key)) {
			$tmp = Acid::sessGet('session_tmp:'.$key);
			$log = 'a value is returned';
		}else{
			$tmp = $def;
			$log = 'not found, default value returned';
		}

		Acid::log('session','Getting tmp session : '.$key.' - '.$log);
		return $tmp;
	}

	/**
	 * Killer de la mémoire tampon
	 * @param string key identifiant
	 */
	public static function tmpKill($key=null) {
		if ($key!==null) {
			Acid::sessKill('session_tmp:'.$key);
			Acid::log('session','Session, killing '.$key.' tmp session');
		}else{
			Acid::sessKill('session_tmp');
			Acid::log('session','Session, killing all tmp sessions');
		}
	}


}
