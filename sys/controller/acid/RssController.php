<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Controller
 * @version   0.1
 * @since     Version 0.4
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

/**
 *
 * Contrôleur des Actualités
 *
 * @package Controller
 *
 *
 */
class RSSController {

	/**
	 * Liste les actualités si aucun argument ou un numéro de page est donné à l'url, sinon affiche l'actualité ciblée par l'url
	 */
	public function index() {
		Acid::set ( 'out', 'empty' );

		$rss_list = array ();

		$actu_list = Actu::getLast ( 15 );

		$rss_flux = new AcidRss ( Acid::get('site:name'), Acid::get ( 'url:system' ), Acid::get('site:name').' - '.Conf::get('site:accroche')  , Acid::get ( 'url:img_abs' ) . 'site/logo.png' );

		if ($actu_list) {
			foreach ($actu_list as $actu) {

				$title = $actu->trad('title');

				$url = Acid::get('url:prefix') . $actu->url();

				if ($actu->get('src')) {

					$img = $actu->urlSrc('large');
				} else {

					$img = null;
				}

				$to_desc = $actu->trad('head') ? $actu->trad('head') : $actu->trad('content');

				$desc = AcidVarString::split($to_desc, 350, '...');

				$rssDate = $actu->get('adate');

				$rss_flux->add($title, $url, $url, $desc, $rssDate, $img);

			}
		}
		Conf::addToContent ( $rss_flux->printRss () );
	}
}

