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

/**
 * Class SampleTest
 */
class SampleTest extends PHPUnit_Framework_TestCase
{

    /**
     * Assertion array vide
     * @return array
     */
    public function testEmpty()
    {

        $stack = array();
        $this->assertEmpty($stack);

        return $stack;
    }

}