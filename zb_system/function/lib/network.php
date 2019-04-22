<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 网络连接类.
 */
class Network
{
    /**
     * @var null
     */
    public $networktype = null;
    /**
     * @var array
     */
    public $network_list = array();
    /**
     * @var bool
     */
    public $curl = false;
    /**
     * @var bool
     */
    public $fsockopen = false;
    /**
     * @var bool
     */
    public $file_get_contents = false;
    /**
     * @var Network__Interface[]
     */
    private static $_network = null;

    /**
     * 构造函数.
     */
    public function __construct()
    {
        if (function_exists('curl_init') && function_exists('curl_exec')) {
            $this->network_list[] = 'curl';
            $this->curl = true;
        }
        if ((bool) ini_get('allow_url_fopen')) {
            if (function_exists('fsockopen')) {
                $this->network_list[] = 'fsockopen';
            }
            $this->fsockopen = true;
        }
        if ((bool) ini_get('allow_url_fopen')) {
            if (function_exists('file_get_contents')) {
                $this->network_list[] = 'filegetcontents';
            }
            $this->file_get_contents = true;
        }
    }

    /**
     * @param string $extension
     *
     * @return Network__Interface
     */
    public static function Create($extension = '')
    {
        if (!isset(self::$_network)) {
            self::$_network = new self();
        }
        if ((!self::$_network->file_get_contents) && (!self::$_network->fsockopen) && (!self::$_network->curl)) {
            return;
        }

        $extension = ($extension == '' ? self::$_network->network_list[0] : $extension);
        $type = 'Network__' . $extension;
        $network = new $type();

        return $network;
    }
}
