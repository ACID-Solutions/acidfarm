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
 * Gestionnaire de Sitemap
 * @package   Tool
 */
class AcidSitemap {

	/**
	 * Constructeur
	 */
	public function __construct() {


	}

	/**
	 * Retourne une ligne du sitemap correspondant à une url
	 * @param string $url
	 * @param float $priority
	 * @param string $changefreq
	 * @param string $lastmod
	 * @param string $url_prefix prefix à appliquer à l'url saisie
	 * @return string
	 */
	public static function getElt($url,$priority='1.0',$changefreq='monthly',$lastmod=null,$url_prefix=null) {
		$url_prefix = $url_prefix===null ? Acid::get('url:system_lang') : $url_prefix;
		return	'	<url>' . "\n" .
				'		<loc>'.$url_prefix.$url.'</loc>' . "\n" .
				'		<priority>'.$priority.'</priority>' . "\n" .
				'		<changefreq>'.$changefreq.'</changefreq>' . "\n" .
				($lastmod !== null ? '		<lastmod>'.$lastmod.'</lastmod>' . "\n" : '') .
				'	</url>' . "\n" .
				'';
	}

	/**
	 * Retourne une ligne du sitemap correspondant à une url
	 * @param string $url
	 * @param float $priority
	 * @param string $changefreq
	 * @param string $lastmod
	 * @param string $url_prefix prefix à appliquer à l'url saisie
	 * @return string
	 */
	public static function getMultilangElt($tab_url, $priority='1.0', $changefreq='monthly', $lastmod=null, $url_prefix=null) {
		$url_prefix = $url_prefix===null ? Acid::get('url:system') : $url_prefix;
		$urls = '';
		foreach ($tab_url as $key => $link){
			$url = 	'		<url>' . "\n";
			$url .= '			<loc>'.$url_prefix.$key.'/'.$link['url'].'</loc>' . "\n";
			// en attente de savoir pourquoi le sitemap est cassé avec les nouvelles normes google
			foreach ($tab_url as $other_key => $other_link){
				$url .= '				<xhtml:link'. "\n" .
						'					rel = "alternate"'. "\n" .
						'					hreflang = "'.$other_key.'"'. "\n" .
						'					href="'.$url_prefix.$other_key.'/'.$other_link['url'].'"'. "\n" .
						'				/>' . "\n";
			}
			$url .=	'		<priority>'.$priority.'</priority>' . "\n" .
					'		<changefreq>'.$changefreq.'</changefreq>' . "\n" .
			($lastmod !== null ? '		<lastmod>'.$lastmod.'</lastmod>' . "\n" : '');
			$url .=	'	</url>' . "\n" .
					'';
			$urls .= $url;
		}
		return $urls;
	}


	/**
	 * Génère un sitemap
	 * @param string $content contenu du sitemap
	 * @param boleean $display_source si true, alors ajoute automatiquement l'index du site
	 * @return string
	 */
	public static function printSitemap($content,$display_source=true,$multilingual=null) {
		Acid::set('out','xml');
		$multilingual = $multilingual===null ? Acid::get('lang:use_nav_0') : $multilingual;

		$source = 	$display_source ?
						'	<url>' . "\n" .
						'		<loc>'.Acid::get('url:system').'</loc>' . "\n" .
						'		<priority>1.0</priority>' . "\n" .
						'	</url>' . "\n"
						: '';

		$multilang_add = $multilingual ? 'xmlns:xhtml="http://www.w3.org/TR/xhtml11/xhtml11_schema.html"' : '';
		return	'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '.$multilang_add.' >' . "\n" .
					$source .
					$content .
				'</urlset>' . "\n" .
				'';
	}

}
