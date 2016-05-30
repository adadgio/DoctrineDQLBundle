<?php

namespace Adadgio\DoctrineDQLBundle\Tests;

use Adadgio\DoctrineDQLBundle\Common\Offset;

class OffsetTest extends \PHPUnit_Framework_TestCase
{
    public function testOffset()
    {
        $offsetA = 0;
        $offsetB = 5;
        $offsetC = null;
        $offsetD = 'Some error value';

        $this->assertEquals(Offset::offset($offsetA), 0);
        $this->assertEquals(Offset::offset($offsetB), 5);

        $this->assertEquals(Offset::offset($offsetC), 0);
        $this->assertEquals(Offset::offset($offsetD), 0);
    }
}
