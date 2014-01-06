<?php

/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Tool
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


/**
 * Outil AcidRss, Gestionnaire Rss
 * @package   Tool
 */
class AcidRss {

	/**
	 * @var string type d'encodage
	 */
	protected $encoding;
	
	/**
	 * @var string titre
	 */
	protected $title;
	
	/**
	 * @var string img
	 */
	protected $img;
	
	/**
	 * @var string lien
	 */
	protected $alink;
	
	/**
	 * @var string description
	 */
	protected $description;
	
	/**
	 * @var array tableau associatif des jours 
	 */
	protected $days_in_letters;

	
	/**
	 * @var string contenu
	 */
	protected $content;


	/**
	 * Constructeur AcidRss
	 *
	 * @param string $title
	 * @param string $alink
	 * @param string $description
	 * @param string $img
	 */
	public function __construct($title,$alink,$description, $img=null) {
		$this->title = $title;
		$this->alink = $alink;
		$this->description = $description;
		$this->img = $img;
		$this->encoding = 'UTF-8';
		$this->days_in_letters = array();
	}

	/**
	 * Retourne la valeur du paramètre $key de l'objet.
	 *
	 * @param string $key
	 */
	public function get($key) {
		try {
			if (!isset($this->$key))
			throw new Exception ('Argument inconnu !');
			return $this->$key;
		}
		catch(Exception $e) {
			trigger_error('Argument inconnu !',E_USER_WARNING);
		}
	}

	/**
	 * Définit l'encodage du Rss.
	 * 
	 * @param string $enc
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
	 */
	public function getValidDate($date_to_convert) {
		$output = 'Fri, 31 Dec 1999 23:59:59 EST';

		try{
			if (preg_match('#^[0-9]{4}(-[0-9]{2}){2} ([0-9]{2}:){2}[0-9]{2}$#',$date_to_convert)) {
				list ($date,$time) = explode(' ',$date_to_convert);
				list ($year,$month,$day) = explode('-',$date);
				list ($hours,$minutes,$seconds) = explode(':',$time);
				$my_time = mktime($hours,$minutes,$seconds,$month,$day,$year);
				$output = date('D, j M Y H:i:s',$my_time) . ' EST';
			}
			elseif (preg_match('#^[0-9]{4}(-[0-9]{2}){2}$#',$date_to_convert)) {
				list ($year,$month,$day) = explode('-',$date_to_convert);
				$my_time = mktime(0,0,0,$month,$day,$year);
				$output = date('D, j M Y H:i:s',$my_time) . ' EST';
			}
			else throw new Exception('Format de date non valide : '.$date_to_convert.'');

			return $output;
		}
		catch(Exception $e) {
			trigger_error($e,E_USER_WARNING);
			Acid::log('error',$e);
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
	 */
	public function add($title,$link,$guid,$description,$pubDate) {
		$this->content .=	'		<item>' . "\n" .
							'			<title>'.htmlspecialchars($title).'</title>' . "\n" . 
							'			<link>'.htmlspecialchars($link).'</link>' . "\n" . 
							'			<guid isPermaLink="false">'.htmlspecialchars($guid).'</guid>' . "\n" . 
							'			<description>' . "\n".
							'				<![CDATA['."\n".
							'				' . $description. "\n" .
							'				]]>' . "\n". 
							'			</description>' . "\n" . 
							'			<pubDate>'.$this->getValidDate($pubDate).'</pubDate>' . "\n" . 
							'		</item>' . "\n\n";
	}


	/**
	 * Ajoute un ensemble de blocs au RSS
	 * 
	 * @param array $tab Tableaux d'éléments.
	 * @param int $max_elts Nombre de blocs à afficher.
	 */
	public function addElements($tab,$max_elts=0) {
		$max_elts = (int) $max_elts;
		if ($max_elts <= 0) {
			foreach ($tab as $elt) {
				$this->add($elt['title'],$elt['link'],$elt['guid'],$elt['description'],$elt['pubDate']);
			}
		}else {
			$i = 0;
			while (isset($tab[$i]) && $i < $max_elts) {
				$elt = $tab[$i];
				$this->add($elt['title'],$elt['link'],$elt['guid'],$elt['description'],$elt['pubDate']);
				$i++;
			}
		}
	}


	/**
	 * Retourne le RSS.
	 *
	 *
	 * @return string
	 */
	public function printRss() {

		//header('Content-Type: text/xml; charset='.$this->encoding);
		
		if($this->img){
			$img = 
				'<image>
					<url>'.$this->img.'</url>
					<link>'.$this->alink.'</link>
					<title>'.$this->title.'</title>
				</image>' . "\n";
		}
		else{
			$img='';
		}
		
		
		$output =	'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n" .
					'	<channel>' . "\n" . 
					'		<atom:link href="'.Acid::get('url:system').'rss'.'" rel="self" type="application/rss+xml" />' . "\n" .
					'		<title>'.$this->title.'</title>' . "\n" . 
					'		<link>'.$this->alink.'</link>' . "\n" .
					$img.
					'		<description>'.$this->description.'</description>' . "\n\n" . 
			
		$this->content .
			
		//'		<atom:link href="'.$this->alink.'" rel="self" type="application/rss+xml" />' . "\n" .
					'	</channel>' . "\n" . 
					'</rss>' . "\n";


		return $output;
	}


}