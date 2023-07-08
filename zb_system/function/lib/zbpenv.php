<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-BlogPHP 环境变量读取类.
 *
 * 注：请务必禁止环境变量存放文件 .env 的访问.
 */
class ZbpEnv
{
    
    /**
     * 是否已经过初始化.
     *
     * @var boolean
     */
    private static $IsInitialized = false;

    /**
     * env 文件路径.
     *
     * @var string|null
     */
    private static $EnvPath = null;

    /**
     * 获取环境变量值.
     *
     * @param string $item
     * @param string|null $default
     * @return string|null
     */
    public static function Get($item, $default = null)
    {
        if (self::$IsInitialized === false) {
            self::Initialize();
        }
        
        //$item = strtoupper($item);

        if (array_key_exists($item, $_ENV)) {
            return $_ENV[$item];
        }

        if (getenv($item) !== false) {
            return getenv($item);
        }

        return $default;
    }

    /**
     * 设置环境变量值.
     *
     * @param string $item
     * @param string $value
     * @return void
     */
    public static function Put($item, $value)
    {
        if (function_exists('putenv')) {
            putenv("$item=$value");
        }

        $_ENV[$item] = $value;
    }

    /**
     * 设置 env 文件路径.
     *
     * @param string $path
     * @return boolean
     */
    public static function SetPath($path)
    {
        if (is_readable($path)) {
            self::$EnvPath = $path;
            return true;
        }

        return false;
    }

    /**
     * 初始化类
     *
     * @return void
     */
    private static function Initialize()
    {
        if (is_null(self::$EnvPath)) {
            self::SetPath(ZBP_PATH . '.env');
        }

        // 如果 SetPath 之后 $EnvPath 还是 null，说明没有任何 .env 文件可以被加载
        if (! is_null(self::$EnvPath)) {
            $env = parse_ini_file(self::$EnvPath);
            $env = array_change_key_case($env, CASE_UPPER);
            $_ENV = array_merge($_ENV, $env);
            self::$IsInitialized = true;
        }


    }

}
