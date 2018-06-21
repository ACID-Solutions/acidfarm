<?php

// SEO
//************************************************************************************//

//--keywords

/**
 * Configuration des meta keywords de base
 */
//Conf::set('meta:keywords:fr:default',['exemple', 'de', 'mots-clé']);
//Conf::set('meta:keywords:fr:default',[]);
//Conf::set('meta:keywords:en:default',[]);
//Conf::set('meta:keywords:es:default',[]);
//Conf::set('meta:keywords:de:default',[]);
//Conf::set('meta:keywords:it:default',[]);

//--description

/**
 * Configuration des meta description de base
 */

//Conf::set('meta:description:fr:default',"exemple de metadesc");
//Conf::set('meta:description:fr:default',"");
//Conf::set('meta:description:en:default',"");
//Conf::set('meta:description:es:default',"");
//Conf::set('meta:description:de:default',"");
//Conf::set('meta:description:it:default',"");

//--image

/**
 * Configuration des meta image de base
 */

//Conf::set('meta:image:fr:default',"/ascreen.jpg");
//Conf::set('meta:image:fr:default',"");
//Conf::set('meta:image:en:default',"");
//Conf::set('meta:image:es:default',"");
//Conf::set('meta:image:de:default',"");
//Conf::set('meta:image:it:default',"");

//--title

/**
 * Configuration des meta title de base
 */

//Conf::set('meta:title:fr:default', "exemple");

Conf::setMany('meta:title:fr', [
    'news'    => "Actualités",
    'contact' => "Contact",
    'search'  => "Recherche",
]);

Conf::setMany('meta:title:en', [
    'news'    => "News",
    'contact' => "Contact",
    'search'  => "Search",
]);

Conf::setMany('meta:title:es', [
    'news'    => "Noticias",
    'contact' => "Contacto",
    'search'  => "Búsqueda",
]);

Conf::setMany('meta:title:de', [
    'news'    => "Aktualitäten",
    'contact' => "Kontakt",
    'search'  => "Suche",
]);

Conf::setMany('meta:title:it', [
    'news'    => "Notizie",
    'contact' => "Contatto",
    'search'  => "Ricerca",
]);