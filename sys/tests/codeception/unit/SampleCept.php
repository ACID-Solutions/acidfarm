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

$I = new UnitTester($scenario);
$I->wantTo('check array() is empty');

$stack = array();
$I->assertEmpty($stack);

return $stack;