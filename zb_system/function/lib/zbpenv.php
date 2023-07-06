<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

class ZbpEnv
{

    public static $isinitialized = false;

    public static function Get($item, $default = null)
    {
        if (self::$isinitialized == false) {
            self::initialize();
        }
        $item = strtoupper($item);
        if (array_key_exists($item, $_ENV)) {
            return $_ENV[$item];
        }
        return $default;
    }

    private static function Initialize()
    {
        if (is_readable(ZBP_PATH . '.env')) {
            $env = parse_ini_file(ZBP_PATH . '.env');
            $env = array_change_key_case($env, CASE_UPPER);
            $_ENV = array_merge($_ENV, $env);
        }
        self::$isinitialized = true;
    }

}
