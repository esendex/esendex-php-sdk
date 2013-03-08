<?php
namespace Esendex\Model;

class Api
{
    const NS = "http://api.esendex.com/ns/";

    private static $major = 1;
    private static $minor = 0;
    private static $patch = 0;

    public static function getVersion()
    {
        return sprintf("%d.%d.%d", self::$major, self::$minor, self::$patch);
    }

    public static function getApiVersion()
    {
        return sprintf("%d.%d.0", self::$major, self::$minor);
    }
}
