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

$lang['router']['index']     = array(
                                'fr'=>array('key'=>'index','name'=>'Index'),
                                'en'=>array('key'=>'index','name'=>'Home'),
								'es'=>array('key'=>'index','name'=>'Home'),
								'it'=>array('key'=>'index','name'=>'Home'),
								'de'=>array('key'=>'index','name'=>'Empfang')
                            );

$lang['router']['page'] 	= array(
								'fr'=>array('key'=>'page','name'=>'Pages'),
								'en'=>array('key'=>'page','name'=>'Pages'),
								'es'=>array('key'=>'page','name'=>'Páginas'),
								'it'=>array('key'=>'page','name'=>'Pagine'),
								'de'=>array('key'=>'page','name'=>'Seiten')
							);

$lang['router']['news'] 	= array(
								'fr'=>array('key'=>'actu','name'=>'Actualité'),
								'en'=>array('key'=>'news','name'=>'News'),
								'es'=>array('key'=>'noticias','name'=>'Noticias'),
								'it'=>array('key'=>'notizie','name'=>'Notizie'),
								'de'=>array('key'=>'aktualitaten','name'=>'Aktualitäten')
						 	);

$lang['router']['gallery'] 	= array(
								'fr'=>array('key'=>'galerie','name'=>'Galerie'),
								'en'=>array('key'=>'gallery','name'=>'Gallery'),
								'es'=>array('key'=>'galeria','name'=>'galería'),
								'it'=>array('key'=>'galleria','name'=>'Galleria'),
								'de'=>array('key'=>'fotos','name'=>'Fotos')
						 	);

$lang['router']['search'] 	= array(
								'fr'=>array('key'=>'recherche','name'=>'Recherche'),
								'en'=>array('key'=>'search','name'=>'Search'),
								'es'=>array('key'=>'busqueda','name'=>'Búsqueda'),
								'it'=>array('key'=>'ricerca','name'=>'Ricerca'),
								'de'=>array('key'=>'suche','name'=>'Suche')
							);

$lang['router']['policy'] = array(
                                'fr' => array('key' => 'polique-sur-les-données', 'name' => 'Politique sur les données'),
                                'en' => array('key' => 'data-policy', 'name' => 'Data policy'),
                                'es' => array('key' => 'politica-de-datos', 'name' => 'Política de datos'),
                                'it' => array('key' => 'politica-dei-dati', 'name' => 'Politica dei dati'),
                                'de' => array('key' => 'datenpolitik', 'name' => 'Datenpolitik')
);

$lang['router']['account'] 	= array(
								'fr'=>array('key'=>'espace-membre','name'=>'Espace Membre'),
								'en'=>array('key'=>'account','name'=>'Account'),
								'es'=>array('key'=>'miembro','name'=>'Miembro'),
								'it'=>array('key'=>'membro','name'=>'Membro'),
								'de'=>array('key'=>'mitglied','name'=>'Mitglied')
						 	);

$lang['router']['contact'] 	= array(
								'fr'=>array('key'=>'contact','name'=>'Contact'),
								'en'=>array('key'=>'contact','name'=>'Contact'),
								'es'=>array('key'=>'contacto','name'=>'Contacto'),
								'it'=>array('key'=>'contatto','name'=>'Contatto'),
								'de'=>array('key'=>'kontakt','name'=>'Kontakt')
						 	);

$lang['router']['rss'] 	= array(
								'fr'=>array('key'=>'rss','name'=>'RSS'),
								'en'=>array('key'=>'rss','name'=>'RSS'),
								'es'=>array('key'=>'rss','name'=>'RSS'),
								'it'=>array('key'=>'rss','name'=>'RSS'),
								'de'=>array('key'=>'rss','name'=>'RSS')
);

$lang['router']['pagination_key'] 	= array(
		'fr'=>array('key'=>'page','name'=>'Page'),
		'en'=>array('key'=>'page','name'=>'Page'),
		'es'=>array('key'=>'pagina','name'=>'Página'),
		'it'=>array('key'=>'pagina','name'=>'Pagina'),
		'de'=>array('key'=>'seite','name'=>'Seite')
);

//Hooks
AcidHook::call('lang_router');