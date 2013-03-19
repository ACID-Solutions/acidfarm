<?php
/**
 * AcidFarm - Yet Another Framework
 * 
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Model / View
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

$res = Page::dblist(array(array('active','=',1)));

$sitemap = '';
if (count($res)) {
	$sitemap .= '<ul class="sitemap">'. "\n" .
				'<li><a href="'.Acid::get('url:folder_lang').'">Home</a></li>' . "\n" .
				'<li><a href="'.Actu::buildUrl().'">'.AcidRouter::getName('news').'</a></li>' . "\n" .
				'<li><a href="'.Photo::buildUrl().'">'.AcidRouter::getName('gallery').'</a></li>' . "\n" ;
	
		foreach ($res as $elt) {
			$sitemap .= '<li><a href="'.Page::buildUrl($elt).'">'.htmlspecialchars($elt['title']).'</a></li>' . "\n" ;
		}
		
	$sitemap .= '<li><a href="'.Route::buildUrl('contact').'">'.AcidRouter::getName('contact').'</a></li>' . "\n" .
				'<li><a href="'.Route::buildUrl('search').'">'.AcidRouter::getName('search').'</a></li>' . "\n" .
				'</ul>' . "\n" ;
}
