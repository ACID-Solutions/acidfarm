<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('perform page switch and see result');

$I->amOnPage(Acid::get('url:folder'));
$I->click('Contact');
$I->seeInSource('page-contact');