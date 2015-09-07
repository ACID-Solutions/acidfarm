<?php

/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Controller
 * @version   0.1
 * @since     Version 0.1
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */


Acid::mod('MyTemplate');

$template = new MyTemplate();
$template->cssPrepare();

//Checking for SEO
$log_meta_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

if ($check_for_keywords = Conf::get('meta:check_for_keywords')) {
	$check = is_numeric($check_for_keywords) ? $check_for_keywords : 5;
	$tab_kw = Lib::getWordsCount($html,$check);
	foreach ($tab_kw as $key => $elt) {
		if (!in_array($key,$meta_keys)) {
			Conf::addToMetaKeys($key);
		}
	}
}

if ( (!Conf::getMetaDesc()) && (!Conf::getMetaDescStart()) &&  (!Conf::getMetaDescBase()) ) {
	Acid::log('META','Meta description is not set for url '.$log_meta_url);
}

/*
if (!Conf::getMetaKeys()) {
	Acid::log('META','Meta keywords are not set for url '.$log_meta_url);
}
*/

if (!Conf::getPageTitle()) {
	Acid::log('META','Page title is not set for url '.$log_meta_url);
}


//Setting custom head
if (Conf::getPageTitle()) {
    $template->setTitle(Conf::getPageTitle(),Conf::getPageTitleAlone());
}



Acid::set('meta:description', Conf::getMetaDescStart() . (Conf::getMetaDesc()  ? Conf::getMetaDesc() : Conf::getMetaDescBase()) );

if (Conf::getMetaKeys() && is_array(Conf::getMetaKeys())) {
	Acid::set('meta:keywords',Conf::getMetaKeys());
}

if (Conf::getMetaImage()) {
	$template->addInHead(
			'<link rel="image_src" type="image/jpeg" href="'.Conf::getMetaImage().'" />' . "\n" .
			'<meta property="og:image" content="'.Conf::getMetaImage().'" />' . "\n"
	);
}



$template->add(Conf::getContent());
$template->printPage();


if (!empty($start_time)) {
	$timer = new AcidTimer();
	list($usec, $sec) = explode(' ', $start_time);
	$timer->start((float)$usec + (float)$sec);
	$tps_gen_page = $timer->fetch(3)*1000;

	$tdbc = Acid::timerSum('db-connect');
	$tdb = Acid::timerSum('db');
	$ttpl = Acid::timerSum('tpl');

	$db_c = Acid::counter('db-connect').' db connections ('.round($tdbc,3).' ms)';
	$db = Acid::counter('db').' db requests ('.round($tdb,3).' ms)';
	$tpl = Acid::counter('tpl').' tpl inclusions ('.round($ttpl,3).' ms)';
	$total = 'total : '.round($tdbc+$tdb+$ttpl).'ms / '.$tps_gen_page.'ms';

	Acid::log('TIMER',$db_c.' - '.$db.' - '.$tpl.' - '.$total);
}

include ACID_PATH . 'stop.php';