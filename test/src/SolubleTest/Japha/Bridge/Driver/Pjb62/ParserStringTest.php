<?php

namespace SolubleTest\Japha\Bridge\Driver\Pjb62;

use Soluble\Japha\Bridge\Driver\Pjb62\ParserString;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-11-13 at 10:21:03.
 */
class ParserStringTest extends \PHPUnit_Framework_TestCase
{
    public function testParserString()
    {
        $pe = new ParserString();
        $pe->string = '1234';
        $pe->off = 0;
        $pe->length = 20;
        $this->assertEquals('1234', $pe->getString());
        $this->assertEquals('1234', $pe->toString());

        $pe->off = 1;
        $pe->length = 2;

        $this->assertEquals('23', $pe->getString());
        $this->assertEquals('23', $pe->toString());
    }
}
