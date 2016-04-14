<?php

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