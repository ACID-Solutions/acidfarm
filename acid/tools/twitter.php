<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Tool
 * @version   0.5
 * @since     Version 0.5
 * @copyright 2012 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/**
 * Classe de partage sur le réseau social Twitter.com
 * @package   Tool
 */
class AcidTwitter {
	
	const TWEET_BUTTON = 1;
	const FOLLOW_BUTTON = 2;
	
	/**
	 * Le type de widget à générer, correspond aux constantes de la classe.
	 * @var int Le type de widget.
	 */
	private $widgetType = null;

	/**
	 * URL utilisé pour le partage, utilisé pour les LIKE entre autres.
	 * DEFAULT = ''
	 * @var string L'URL cible de ce partage.
	 */
	private $url = '';
	
	/**
	 * Le texte à insérer dans le tweet.
	 * DEFAULT = ''
	 * @var string Le texteà insérer dans le tweet.
	 */
	private $texte = '';
	
	/**
	 * Le user tweeter du site
	 * @var string Le nom du compte Twitter à suivre
	 */
	private $twitterUser = '';

	/**
	 * Permet de modifier l'affichage du bouton Tweet pour afficher ou non le nombre de fois que cette page
	 * a été tweetée. Valeurs possibles : "none" | "horizontal" | "vertical"
	 * @var unknown_type
	 */
	private $countBox = '';
	
	/**
	 * Tableau d'autoconfiguration, valeurs par défaut pour le widget
	 * @var array Le tableau de conf
	 */
	private static $_autoconf = array(	'url' => '',
										'text' => '',
										'twitterUser' => '',
										'countBox' => 'none');

	
	// ========================
	// CONSTRUCTEURS
	// ========================
	
	/**
	 * Constructeur
	 * @param string $type
	 * @return AcidTwitter
	 */
	public function __construct($type = AcidTwitter::TWEET_BUTTON) {
		$this->widgetType = $type;
		return $this;
	}
	
	// ========================
	// METHODES DE CLASSE
	// ========================
	
	/**
	 * Retourne l'url de partage twitter avec le contenu passé en argument
	 * @param string $content Le contenu à afficher dans le tweet
	 */
	public static function urlShare($content=null) {
		$content = $content ? ', '.$content : '';
		return 'http://twitter.com/home?status='.urlencode($GLOBALS['acid']['site']['name'].$content);
	}
	
	/**
	 * Permet de définir une autoconfiguration par défaut du widget Tweet de Twitter. Clés autorisées :
	 * 'url' => string : L'URL à mettre dans le tweet
	 * 'twitterUser' => string : Le pseudo Twitter à suivre
	 * 'countBox' => 'none' | 'vertical' | 'horizontal' : Pour afficher un compteur de tweet de ce bouton
	 * 'texte' => string : Le texte suggest à mettre dans le tweet
	 *
	 * @param array $tabConf Le tableau de configuration
	 */
	public static function setAutoconf($tabConf = array()) {
		AcidTwitter::$_autoconf = $tabConf;
	}
	
	// ========================
	// METHODES
	// ========================
	
	/**
	 * Retourne le code HTML templaté du widget à afficher.
	 */
	public function printWidget()
	{
		switch($this->widgetType) {
			
			case AcidTwitter::FOLLOW_BUTTON :
				
				break;
			
			case AcidTwitter::TWEET_BUTTON :
			default:
				return $this->printTweet();
				break;
		}
	}
	
	/**
	 * Retourne le code HTML templaté du widget Twitter Button.
	 */
	private function printTweet() {
		$tplPath = 'tools/twitter.tpl';

		if (AcidTwitter::$_autoconf) {
			foreach (AcidTwitter::$_autoconf as $k => $v) {
				$this->$k = isset($this->$k) ? $this->$k : static::$_autoconf[$k];
			}
		}
		
		return Acid::tpl($tplPath, array(), $this);
	}
	
	/**
	 * Do Nothing
	 */
	private function printFollow() {
		
	}
	
	
	
	// ========================
	// GETTERS & SETTERS
	// ========================
	
	/**
	 * Attribue l'URL cible du partage à cet objet.
	 * @param string $string L'url à partager.
	 */
	public function setURL($string = "") {
		$this->url = $string;
		return $this;
	}
	
	/**
	 * Permet de définir si on montre ou non une countBox pour le widget.
	 * @param string $string "none" | "vertical" | "horizontal"
	 */
	public function setCountBox($string = 'none') {
		$this->countBox = $string;
		return $this;
	}
	
	/**
	 * Permet de fixer le nom de l'utilisateur twitter à follow (le comte du site)
	 * @param string $user Le nom du nickname à follow
	 */
	public function setTwitterUser($user = '') {
		$this->twitterUser = $user;
		return $this;
	}
	
	/**
	 * Permet de remplir le tweet qui va être posté via le clic sur le bouton
	 * @param string $texte Le texte qui sera twetté quand on clic sur le bouton
	 */
	public function setTexte($texte = '') {
		$this->texte = $texte;
		return $this;
	}
	
	/**
	 * Retourne l'url
	 * @return string
	 */
	public function getPrintedURL() {
		return $this->url;
	}
	
	/**
	 * Retourne la valeur countBox
	 * @return unknown_type
	 */
	public function getPrintedCountBox() {
		return $this->countBox;
	}
	
	/**
	 * Retourne le user twitter du site
	 * @return string
	 */
	public function getPrintedTwitterUser() {
		return $this->twitterUser;
	}
	
	/**
	 * Retourne le texte
	 * @return string
	 */
	public function getPrintedTexte() {
		return $this->texte;
	}
	
}
