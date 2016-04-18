<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tool
 * @version   0.5
 * @since     Version 0.5
 * @copyright 2012 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */



/**
 * Classe de partage sur le réseau social Facebook.com
 * @package   Acidfarm\Tool
 */
class AcidFacebook {
	
	const BUTTON_LIKE = 1;
	
	/**
	 * Le type de widget à générer, correspond aux constantes de la classe.
	 * @var int Le type de widget.
	 */
	private $widgetType = null;

	/**
	 * URL utilisé pour le partage, utilisé pour les LIKE entre autres.
	 * @var string L'URL cible de ce partage.
	 */
	private $url;
	
	/**
	 * Taille du widget, à vérifier si elle fonctionne pour tous les widgets.
	 * @var int La taille en PX du widget.
	 */
	private $width;

	/**
	 * Active ou non l'affichage des visages des personnes qui ont utilisé ce widget.
	 * @var bool True : Affiche les visages, False : N'affiche pas les visages
	 */
	private $showFaces;
	
	/**
	 * Verbe à utiliser pour le widget de partage.
	 * @var string Le verbe à afficher pour le widget.
	 */
	private $verb;
	
	/**
	 * Permet de changer la forme du widget en countBox, ne fonctionne que pour le like.
	 * @var bool True pour le mettre en countBox, False pour ne pas le mettre en countBox.
	 */
	private $countBox;
	
	/**
	 * Tableau d'autoconfiguration, valeurs par défaut pour le widget
	 * @var array Le tableau de conf
	 */
	private static $_autoconf = array(	'url' => '',
										'width' => 450,
										'showFaces' => false,
										'verb' => 'like',
										'countBox' => false);
	
	// ========================
	// CONSTRUCTEURS
	// ========================
	/**
	 * Constructeur
	 * @param int $type
	 * @return AcidFacebook
	 */
	public function __construct($type = AcidFacebook::BUTTON_LIKE) {
		$this->widgetType = $type;
		return $this;
	}
	
	// ========================
	// METHODES DE CLASSE
	// ========================
	
	/**
	 * Permet de définir une autoconfiguration par défaut du widget LIKE de Facebook. Clés autorisées :
	 * 'url' => string : L'URL à aimer
	 * 'width' => int : La taille du widget en pixels
	 * 'showFaces' => bool : TRUE pour afficher les visages, FALSE pour ne pas les afficher
	 * 'verb' => 'like' | 'recommend' : Le verbe à afficher pour le bouton (s'adapte à la langue)
	 * 'countBox' => bool : TRUE pour afficher un compteur de Like, FALSE pour ne pas l'afficher
	 * 
	 * @param array $tabConf Le tableau de configuration
	 */
	public static function setAutoconf($tabConf = array()) {
		AcidFacebook::$_autoconf = $tabConf;
	}
	
	/**
	 * Retourne une URL de partage Facebook en fonction des paramètre
	 * @param string $title Le titre du lien à partager
	 * @param string $url L'URL à partager
	 */
	public static function urlShare($title=null,$url=null) {
		return 'http://www.facebook.com/sharer.php?u='.urlencode($url).'&t='.urlencode($title);
	}
	
	// ========================
	// METHODES
	// ========================
	
	/**
	 * Retourne le code HTML templaté du widget à afficher.
	 */
	public function printWidget()
	{
		$tplPath = 'tools/facebook.tpl';
		
		switch($this->widgetType) {
			
			case AcidFacebook::BUTTON_LIKE:
			default:
				return $this->printLike($tplPath);
				break;
		}
	}
	
	/**
	 * Retourne le code HTML templaté du widget LIKE.
	 * @param string $tplPath chemin vers le template
	 */
	private function printLike($tplPath) {
		if (AcidFacebook::$_autoconf) {
			foreach (AcidFacebook::$_autoconf as $k => $v) {
				$this->$k = isset($this->$k) ? $this->$k : AcidFacebook::$_autoconf[$k];
			}
		}
		
		return Acid::tpl($tplPath, array(), $this);
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
	 * Attribue la taille du widget à cet objet.
	 * @param int $width La taille à appliquer.
	 */
	public function setWidth($width = 450) {
		$this->width = $width;
		return $this;
	}
	
	/**
	 * Donne au widget le droit d'afficher les visages ou non.
	 * @param bool $bool True pour affiche les visages, False pour ne pas les afficher.
	 */
	public function setShowFaces($bool = false) {
		$this->showFaces = $bool;
		return $this;
	}
	
	/**
	 * Attribue le verbe de partage à cet objet.
	 * @param string $string Le verbe à utiliser.
	 */
	public function setVerb($string = "like") {
		$this->verb = $string;
		return $this;
	}
	
	/**
	 * Définit si  l'affichage countBox est activé ou non
	 * @param bool $bool
	 * @return AcidFacebook
	 */
	public function setCountBox($bool = false) {
		$this->countBox = $bool;
		return $this;
	}
	
	/**
	 * Retourne l'url après traitement
	 * @return string
	 */
	public function getPrintedURL() {
		return urlencode($this->url);
	}
	
	/**
	 * Retourne la largeur du widget
	 * @return number
	 */
	public function getPrintedWidth() {
		return $this->width;
	}
	
	/**
	 * Retourne l'état de l'affichage des visages des personnes qui ont utilisé ce widget
	 * @return string
	 */
	public function getPrintedShowFaces() {
		return $this->showFaces ? "true" : "false";
	}
	
	/**
	 * Retourne le verbe de partage du widget
	 * @return string
	 */
	public function getPrintedVerb() {
		return $this->verb;
	}
	
	/**
	 * Retourne vrai si l'affichage countBox est actif
	 * @return bool
	 */
	public function isCountBox() {
		return $this->countBox;
	}
	
}
