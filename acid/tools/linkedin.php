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
 * Classe de partage sur le réseau social linkedin.com
 * @package   Acidfarm\Tool
 */
class AcidLinkedIn {

    /**
     * URL à partager
     * @var string
     */
    public $url='';

    /**
     * Titre à partager
     * @var string
     */
    public $title='';

    /**
     * Résumé à partager
     * @var string
     */
    public $summary='';

    /**
     * Source du partage
     * @var string
     */
    public $source='';

	// ========================
	// CONSTRUCTEURS
	// ========================
	
	/**
	 * Constructeur
	 * @param string $type
	 * @return AcidLinked
	 */
	public function __construct($init=array()) {

        if ($init) {
            foreach ($init as $k => $v) {
                $this->$k = isset($this->$k) ? $this->$k : '';
            }
        }

		return $this;
	}

	// ========================
	// METHODES DE CLASSE
	// ========================

    /**
     * Retourne l'url de partage linkedin
     */
    public function share() {
        return static::urlShare($this->url,$this->title,$this->summary,$this->source);
    }

    /**
	 * Retourne l'url de partage linkedin avec le contenu passé en argument
	 * @param string $url
	 * @param string $title
	 * @param string $summary
	 * @param string $source
	 */
	public static function urlShare($url='',$title='',$summary='',$source='') {
		return 'https://www.linkedin.com/shareArticle?mini=true&url='.urlencode($url).'&title='.urlencode($title).'&summary='.urlencode($summary).'&source='.urlencode($source);
	}

}
