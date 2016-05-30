<?php

namespace Adadgio\DoctrineDQLBundle\Tests;

use Adadgio\DoctrineDQLBundle\DQL\Limit;

class LimitTest extends \PHPUnit_Framework_TestCase
{
    public function testLimit()
    {
        $limitA = 0;
        $limitB = 5;
        $limitC = 99999;
        $limitD = null;

        $this->assertEquals(Limit::enforce($limitA), 0);
        $this->assertEquals(Limit::enforce($limitB), 5);
        $this->assertEquals(Limit::enforce($limitC), 1000);
        $this->assertEquals(Limit::enforce($limitC, Limit::LIMIT_NONE), 99999);
        $this->assertEquals(Limit::enforce($limitD), Limit::getMaxSecurityLimit());
    }
}
