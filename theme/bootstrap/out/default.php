<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\ModelView
 * @version   0.1
 * @since     Version 0.3
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

$output = '';

$site_class = !empty($GLOBALS['site_class']) ? $GLOBALS['site_class'] : '';

$page_class = AcidRouter::getCurrentRouteName() ? AcidUrl::normalize('page_'.AcidRouter::getCurrentRouteName()) : '';
$nav_class = trim(AcidUrl::normalize(Lib::mobileDevice('')).' '.AcidUrl::normalize(Lib::navDevice('')));

if ($page_class&&$nav_class) {
	$body = $this->getBodyAttrs();
	$body_class = isset($body['class']) ? $body['class'] : '';
	$body_class = trim($body_class.' '.$page_class.' '.$nav_class.' '.$site_class);
}

if (isset($_SERVER['HTTP_USER_AGENT'])) {
	$nav_old = false;
	foreach (array('MSIE 8','MSIE 7','MSIE 6') as $search) {
		$nav_old = $nav_old || (stripos($_SERVER['HTTP_USER_AGENT'],$search) !== false);
	}
	if ($nav_old) {
		$body_class .= ' nav-old';
	}
}

if ($body_class) {
	$this->setBodyAttrs(array('class'=>$body_class));
}

//RESPONSIVE MOBILE
$this->addInHead('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
$this->addInHead('<meta name="viewport" content="width=device-width, initial-scale=1">');

//include Acid::outPath('debug.php');
//$output .= Debug::templateTools();


$output .=  <<<OUTPUT

{$this->getDialog()}
{$this->getBwin()}
{$this->getCookieWarning()}

{$this->getBody(array('nav_old'=>$nav_old))}


OUTPUT;


$this->output = $output;