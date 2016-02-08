<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that frontpage works');

$I->amOnPage(Acid::get('url:folder'));
//$I->amOnPage(Acid::get('url:system'));

$I->see('Welcome to the home page AcidFarm !');
$I->seeInSource('page-index');