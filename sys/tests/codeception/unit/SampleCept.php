<?php 
$I = new UnitTester($scenario);
$I->wantTo('check array() is empty');

$stack = array();
$I->assertEmpty($stack);

return $stack;