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
$I->wantTo('perform lang switch');

$I->amOnPage(Acid::get('url:system_lang'));
$I->seeInSource('page-index');
$I->see(AcidRouter::getName('index'));

$cur_lang = Acid::get('lang:current');

if (Acid::get('lang:use_nav_0')) {
    if (count(Acid::get('lang:available')) > 1) {
        foreach (Acid::get('lang:available') as $l) {
            if ($l != $cur_lang) {
                $I->comment('I select lang '.$l);
                $I->click('.switch_lang_to_'.$l,'#footer_flags');
                $I->seeInSource('lang="'.$l.'"');
                $I->see(AcidRouter::getName('index',$l));
            }
        }

    }else{
        $I->comment('No additionnal langage.');
    }

}else{
    $I->comment('Use nav 0 is disabled.');
}
