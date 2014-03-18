<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author ACID-Solutions <contact@acid-solutions.fr>
 * @category AcidFarm
 * @package Tool
 * @version 0.2
 * @since Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license http://www.acidfarm.net/license
 * @link http://www.acidfarm.net
 *      
 */

/**
 * Outil AcidRss, Gestionnaire Rss
 *
 * @package Tool
 *         
 */
class AcidRss {
	
	/**
	 *
	 * @var string type d'encodage
	 *     
	 */
	protected $encoding;
	/**
	 *
	 * @var string titre
	 *     
	 */
	protected $title;
	/**
	 *
	 * @var string img
	 *     
	 */
	protected $img;
	/**
	 *
	 * @var string lien
	 *     
	 */
	protected $alink;
	/**
	 *
	 * @var string description
	 *     
	 */
	protected $description;
	/**
	 *
	 * @var array tableau associatif des jours
	 *     
	 */
	protected $days_in_letters;
	/**
	 *
	 * @var string contenu
	 *     
	 */
	protected $content;
	/**
	 *
	 * @var string configuration du type de media
	 *
	 */
	protected $media_mode='std';
	
	/**
	 * Constructeur AcidRss
	 *
	 * @param string $title        	
	 * @param string $alink        	
	 * @param string $description        	
	 * @param string $img        	
	 *
	 */
	public function __construct($title, $alink, $description, $img = null, $media_mode = null) {
		$this->title = $title;
		$this->alink = $alink;
		$this->description = $description;
		$this->img = $img;
		$this->encoding = 'UTF-8';
		$this->days_in_letters = array ();
		if ($media_mode) {
			$this->media_mode = $media_mode;
		}
	}
	
	/**
	 * Retourne la valeur du paramètre $key de l'objet.
	 *
	 * @param string $key        	
	 *
	 */
	public function get($key) {
		try {
			if (! isset ( $this->$key ))
				throw new Exception ( 'Argument inconnu !' );
			return $this->$key;
		} catch ( Exception $e ) {
			trigger_error ( 'Argument inconnu !', E_USER_WARNING );
		}
	}
	
	/**
	 * Définit l'encodage du Rss.
	 *
	 * @param string $enc        	
	 *
	 */
	public function setEncoding($enc) {
		$this->encoding = $enc;
	}
	
	/**
	 * Retourne une version formatée de la date en entrée.
	 *
	 * @param string $date_to_convert        	
	 *
	 * @return string
	 *
	 */
	public function getValidDate($date_to_convert) {
		$output = 'Fri, 31 Dec 1999 23:59:59 EST';
		
		try {
			if (preg_match ( '#^[0-9]{4}(-[0-9]{2}){2} ([0-9]{2}:){2}[0-9]{2}$#', $date_to_convert )) {
				list ( $date, $time ) = explode ( ' ', $date_to_convert );
				list ( $year, $month, $day ) = explode ( '-', $date );
				list ( $hours, $minutes, $seconds ) = explode ( ':', $time );
				$my_time = mktime ( $hours, $minutes, $seconds, $month, $day, $year );
				$output = date ( 'D, j M Y H:i:s', $my_time ) . ' EST';
			} elseif (preg_match ( '#^[0-9]{4}(-[0-9]{2}){2}$#', $date_to_convert )) {
				list ( $year, $month, $day ) = explode ( '-', $date_to_convert );
				$my_time = mktime ( 0, 0, 0, $month, $day, $year );
				$output = date ( 'D, j M Y H:i:s', $my_time ) . ' EST';
			} else
				throw new Exception ( 'Format de date non valide : ' . $date_to_convert . '' );
			
			return $output;
		} catch ( Exception $e ) {
			trigger_error ( $e, E_USER_WARNING );
			Acid::log ( 'error', $e );
		}
	}
	
	/**
	 * Ajoute un bloc au RSS
	 *
	 * @param string $title        	
	 * @param string $link        	
	 * @param string $guid        	
	 * @param string $description        	
	 * @param string $pubDate        	
	 *
	 */
	public function add($title, $link, $guid, $description, $pubDate, $img=null, $media=null) {
		if(file_exists($img)){
			$size = filesize($img);
			$type = exif_imagetype($img);
			switch($type){
				case IMAGETYPE_GIF:
					$ext = "gif";
				break;
				case IMAGETYPE_JPEG:
					$ext = "jpeg";
				break;
				case IMAGETYPE_PNG:
					$ext = "png";
				break;
			}
			
			if ($media_mode=='yahoo')  {
				$media = '<media:content
						 	url="'.Acid::get("url:prefix").Actu::genUrlKey("img", $img, "bloc").'"
						  	fileSize="'.$size.'"
						  	type= "image/'.$ext.'"
						  />' . "\n";
			}else{
				$media = '<enclosure url="'.Acid::get("url:scheme").Acid::get("url:domain").Actu::genUrlKey("img", $img, "bloc").'" length="'.$size.'" type= "image/'.$ext.'" ></enclosure>'. "\n";
			}	
			
		}
		else{
			$media ='';
		}
		
		$this->content .=
		' <item>' . "\n" . 
		' <title>' . htmlspecialchars ( $title ) . '</title>' . "\n" . 
		' <link>' . htmlspecialchars ( $link ) . '</link>' . "\n" .
		' <description>' . "\n" .
			' <![CDATA[' . "\n" . ' ' . 
				$description . "\n" .
			' ]]>' .
		' </description>' . "\n" .
		$media .
		' <pubDate>' . $this->getValidDate ( $pubDate ) . '</pubDate>' . "\n" . 
		' <guid isPermaLink="false">' . htmlspecialchars ( $guid ) . '</guid>' . "\n" .
		' </item>' . "\n\n";
	}
	
	/**
	 * Ajoute un ensemble de blocs au RSS
	 *
	 * @param array $tab
	 *        	Tableaux d'éléments.
	 * @param int $max_elts
	 *        	Nombre de blocs à afficher.
	 *        	
	 */
	public function addElements($tab, $max_elts = 0) {
		$max_elts = ( int ) $max_elts;
		if ($max_elts <= 0) {
			foreach ( $tab as $elt ) {
				$img = isset($elt['img']) ? $elt['img'] : null; 
				$media = isset($elt['media']) ? $elt['media'] : null;
				
				$this->add ( $elt ['title'], $elt ['link'], $elt ['guid'], $elt ['description'], $elt ['pubDate'], $img, $media );
			}
		} else {
			$i = 0;
			while ( isset ( $tab [$i] ) && $i < $max_elts ) {
				$elt = $tab [$i];
				
				$img = isset($elt['img']) ? $elt['img'] : null;
				$media = isset($elt['media']) ? $elt['media'] : null;
				
				$this->add ( $elt ['title'], $elt ['link'], $elt ['guid'], $elt ['description'], $elt ['pubDate'], $img, $media );
				$i ++;
			}
		}
	}
	
	/**
	 * Retourne le RSS.
	 *
	 *
	 * @return string
	 *
	 */
	public function printRss() {		
		if ($this->img) {
			$img =
			'<image>
			<title>' . htmlspecialchars($this->title) . '</title>
			<url>' . htmlspecialchars($this->img) . '</url>
			<link>' . htmlspecialchars($this->alink) . '</link>
			</image>' . "\n";
		} else {
			$img = '';
		}
		
		$rss_start = $this->media_mode == 'yahoo' ? '<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">' : '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
		$rss_stop = '</rss>';
		
		$rss_atom = $this->media_mode == 'yahoo' ? '' : ('<atom:link href="' . Acid::get ( 'url:system' ) . 'rss' . '" rel="self" type="application/rss+xml" />' . "\n") ;
		
		$output =
		'<?xml version="1.0" encoding="'.$this->encoding.'" ?>' . "\n" . 
			$rss_start . "\n" .
			' <channel>' . "\n" . 
				$rss_atom .
				' <title>' . htmlspecialchars($this->title) . '</title>' . "\n" . 
				' <link>' . htmlspecialchars($this->alink) . '</link>' . "\n" . 
				$img . 
				' <description>' . htmlspecialchars($this->description) . '</description>' . "\n\n" . 
				' <language>' . Acid::get("lang:current") . '</language> ' . "\n" . 
				$this->content .
			' </channel>' . "\n" . 
			$rss_stop . "\n";	
		return $output;
	}
}