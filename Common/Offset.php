<?php

namespace Adadgio\DoctrineDQLBundle\Common;

class Offset
{
    public static function offset($offset)
    {
        return (empty($offset) OR (int) $offset === 0) ? 0 : (int) $offset;
    }
}
