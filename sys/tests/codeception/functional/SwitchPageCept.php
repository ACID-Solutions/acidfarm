<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('perform page switch and see result');
$I->comment('From my home page : '.Acid::get('url:system_lang'));
$I->amOnPage(Acid::get('url:system_lang'));
$I->comment('I go to contact page');
$I->click(AcidRouter::getName('contact'));
$I->seeInSource('page-contact');