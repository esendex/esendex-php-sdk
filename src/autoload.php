<?php
namespace Esendex;

define('ESENDEX_HOME', dirname(__FILE__));

class AutoLoader
{
    const SPLIT_DIR = DIRECTORY_SEPARATOR;
    const SPLIT_NS = '\\';

    public static function load($class)
    {
        if (substr($class, 0, 7) != "Esendex") {
            return false;
        }
        $parts = explode(self::SPLIT_NS, $class);
        $path = ESENDEX_HOME . self::SPLIT_DIR . implode(self::SPLIT_DIR, $parts) . '.php';

        if (file_exists($path)) {
            require_once($path);
        }
    }
}

\spl_autoload_register(array('\Esendex\AutoLoader', 'load'));
