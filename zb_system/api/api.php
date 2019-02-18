<?php
/**
 * api.
 *
 * @php >= 5.2
 *
 * @author zsx<zsx@zsxsoft.com>
 */
define('API_PATH', dirname(__FILE__));
require API_PATH . '/route.php';
require API_PATH . '/io.php';
require API_PATH . '/user.php';

/**
 * API Singleton.
 */
class API
{
    /**
     * Instance.
     */
    private static $instance;
    /**
     * Route.
     */
    public static $Route;
    /**
     * I/O.
     */
    public static $IO;
    /**
     * User.
     */
    public static $User;

    /**
     * To avoid construct outside this class.
     *
     * @private
     */
    private function __construct()
    {
        // Do nothing
    }

    /**
     * To return instance.
     *
     * @return API
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * To avoid clone.
     */
    public function __clone()
    {
        throw new Exception("Singleton Class Can Not Be Cloned");
    }

    /**
     * Init class.
     *
     * @return true
     */
    public static function init()
    {
        global $zbp;
        // Set Z-BlogPHP Enviroment
        $zbp->option['ZC_RUNINFO_DISPLAY'] = false;

        self::$Route = API_Route::getInstance();
        self::$IO = API_IO::getInstance(isset($_SERVER['ACCEPT']) ? $_SERVER['ACCEPT'] : 'application/json');
        self::$User = API_User::getInstance();

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(API_PATH . '/route'), RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $path) {
            $fileName = $path->__toString();
            if ($path->isFile()) {
                include $fileName;
            }
        }

        return true;
    }
}
