<?php

namespace Adadgio\DoctrineDQLBundle\DQL;

class Limit
{
    const LIMIT_NONE = 'NONE';
    const MAX_SECURITY_LIMIT = 1000;

    public static function enforce($limit, $disable = false)
    {
        if ($disable === static::LIMIT_NONE) {

            return $limit;

        } else if (null === $limit) {

            return static::MAX_SECURITY_LIMIT;

        } else {

            return (self::isTooHigh($limit)) ? static::MAX_SECURITY_LIMIT : (int) $limit;
        }
    }

    public static function getMaxSecurityLimit()
    {
        return static::MAX_SECURITY_LIMIT;
    }

    public static function isTooHigh($limit)
    {
        return ($limit > static::MAX_SECURITY_LIMIT) ? true : false;
    }
}
