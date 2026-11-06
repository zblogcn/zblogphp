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
    public $networktype;

    /**
     * @var string[]
     */
    public $network_list = [];

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
     * @var null|Network
     */
    private static $private_network;

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
            if (function_exists('stream_socket_client')) {
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
     * @return null|Network__Interface
     */
    public static function Create($extension = '')
    {
        if (!isset(self::$private_network)) {
            self::$private_network = new self();
        }
        if ((!self::$private_network->file_get_contents) && (!self::$private_network->fsockopen) && (!self::$private_network->curl)) {
            return null;
        }

        $extension = ('' == $extension ? self::$private_network->network_list[0] : $extension);
        $type = 'Network__' . $extension;

        return new $type();
    }
}
