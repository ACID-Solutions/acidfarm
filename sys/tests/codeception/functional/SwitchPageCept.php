<?php
/**
 * AcidFarm - Yet Another Framework
 *
 * Requires PHP version 5.3
 *
 * @author    ACID-Solutions <contact@acid-solutions.fr>
 * @category  AcidFarm
 * @package   Acidfarm\Tests
 * @version   0.1
 * @since     Version 0.8
 * @copyright 2011 ACID-Solutions SARL
 * @license   http://www.acidfarm.net/license
 * @link      http://www.acidfarm.net
 */

$I = new FunctionalTester($scenario);
$I->wantTo('perform page switch and see result');
$I->comment('From my home page : '.Acid::get('url:system_lang'));
$I->amOnPage(Acid::get('url:system_lang'));
$I->comment('I go to contact page');
$I->click(AcidRouter::getName('contact'));
$I->seeInSource('page-contact');