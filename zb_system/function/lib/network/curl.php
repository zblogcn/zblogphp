<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * curl类.
 *
 * 自定义网络连接接口代替curl
 */
class Network__curl implements Network__Interface
{

    private $readyState = 0; //状态

    private $responseBody = null; //返回的二进制

    private $responseStream = null; //返回的数据流

    private $responseText = ''; //返回的数据

    private $responseXML = null; //尝试把responseText格式化为XMLDom

    private $status = 0; //状态码

    private $statusText = ''; //状态码文本

    private $responseVersion = ''; //返回的HTTP版体

    private $option = array();

    private $url = '';

    private $postdata = array();

    private $httpheader = array();

    private $responseHeader = array();

    private $parsed_url = array();

    private $timeout = 30;

    private $set_timeouts = false;

    private $errstr = '';

    private $errno = 0;

    private $ch = null;

    private $isgzip = false;

    private $maxredirs = 0;

    private $private_isBinary = false;

    private $curl = null;

    /**
     * @ignore
     */
    public function __construct()
    {
        $this->curl = &$this->ch;
    }

    /**
     * @param $property_name
     * @param $value
     *
     * @throws Exception
     */
    public function __set($property_name, $value)
    {
        throw new Exception($property_name . ' readonly');
    }

    /**
     * @param $property_name
     *
     * @return mixed
     */
    public function __get($property_name)
    {
        if (strtolower($property_name) == 'responsexml') {
            $w = new DOMDocument();

            return $w->loadXML($this->responseText);
        } elseif (strtolower($property_name) == 'scheme'
            || strtolower($property_name) == 'host'
            || strtolower($property_name) == 'port'
            || strtolower($property_name) == 'user'
            || strtolower($property_name) == 'pass'
            || strtolower($property_name) == 'path'
            || strtolower($property_name) == 'query'
            || strtolower($property_name) == 'fragment'
        ) {
            if (isset($this->parsed_url[strtolower($property_name)])) {
                return $this->parsed_url[strtolower($property_name)];
            } else {
                return;
            }
        } else {
            return $this->$property_name;
        }
    }

    /**
     * 取消.
     */
    public function abort()
    {
    }

    /**
     * @return string
     */
    public function getAllResponseHeaders()
    {
        return implode("\r\n", $this->responseHeader);
    }

    /**
     * 获取返回头.
     *
     * @param $bstrHeader
     *
     * @return string
     */
    public function getResponseHeader($bstrHeader)
    {
        $name = strtolower($bstrHeader);
        foreach ($this->responseHeader as $w) {
            if (strtolower(substr($w, 0, strpos($w, ':'))) == $name) {
                return substr(strstr($w, ': '), 2);
            }
        }

        return '';
    }

    /**
     * 链接远程接口.
     *
     * @param $bstrMethod
     * @param $bstrUrl
     * @param bool   $varAsync
     * @param string $bstrUser
     * @param string $bstrPassword
     *
     * @throws Exception
     *
     * @return bool
     */
    public function open($bstrMethod, $bstrUrl, $varAsync = true, $bstrUser = '', $bstrPassword = '')
    {
        //Async无用
        $this->reinit();
        $method = strtoupper($bstrMethod);
        $this->option['method'] = $method;
        $this->parsed_url = parse_url($bstrUrl);
        if (!$this->parsed_url) {
            throw new Exception('URL Syntax Error!');
        }

        if (!isset($this->parsed_url['port'])) {
            if (isset($this->parsed_url['scheme']) && $this->parsed_url['scheme'] == 'https') {
                $this->parsed_url['port'] = 443;
            } else {
                $this->parsed_url['port'] = 80;
            }
        }

        curl_setopt($this->ch, CURLOPT_URL, $bstrUrl);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($this->ch, CURLOPT_REFERER, 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        curl_setopt($this->ch, CURLOPT_POST, ($method == 'POST' ? 1 : 0));

        return true;
    }

    /**
     * 设置超时时间.
     *
     * @param $resolveTimeout
     * @param $connectTimeout
     * @param $sendTimeout
     * @param $receiveTimeout
     */
    public function setTimeOuts($resolveTimeout, $connectTimeout, $sendTimeout, $receiveTimeout)
    {
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $resolveTimeout);
        $this->set_timeouts = true;
    }

    /**
     * 发送数据.
     *
     * @param string $varBody
     */
    public function send($varBody = '')
    {
        $data = $varBody;
        if (is_array($data)) {
            $data = http_build_query($data);
        }
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->httpheader);

        if ($this->option['method'] == 'POST') {
            if (is_string($varBody) && count($this->postdata) > 0) {
                parse_str($varBody, $data);
                $data = ($data + $this->postdata);
            } elseif (is_array($varBody) && count($this->postdata) > 0) {
                $data = ($varBody + $this->postdata);
            }
            if ($this->private_isBinary) {
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
            } else {
                curl_setopt($this->ch, CURLOPT_POST, 1);
            }
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $this->option['method']);
        }

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        if ($this->maxredirs > 0) {
            if (ini_get("safe_mode") == false && ini_get('open_basedir') == '') {
                curl_setopt($this->ch, CURLOPT_MAXREDIRS, $this->maxredirs);
                @curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
            }
        }

        if ($this->isgzip == true) {
            curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip');
        }

        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($this->set_timeouts == false) {
            if (!isset($this->option['timeout'])) {
                $this->option['timeout'] = $this->timeout;
            }
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->option['timeout']);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->option['timeout']);
        }

        $result = curl_exec($this->ch);
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);

        $this->responseHeader = explode("\r\n", substr($result, 0, ($header_size - 4)));
        $this->responseText = substr($result, $header_size);
        if (is_bool($this->responseText) && !$this->responseText) {
            $this->responseText = '';
        }
        curl_close($this->ch);

        foreach ($this->responseHeader as $key => $value) {
            if (strpos($value, 'HTTP/') === 0) {
                if (isset($this->responseHeader[$key])) {
                    $this->statusText = $this->responseHeader[$key];
                    $a = explode(' ', $this->statusText);
                    if (isset($a[0])) {
                        $this->responseVersion = $a[0];
                    }

                    if (isset($a[1])) {
                        $this->status = $a[1];
                    }
                    unset($this->responseHeader[$key]);
                }
            }
        }
    }

    /**
     * 设置请求HTTP头.
     *
     * @param $bstrHeader
     * @param $bstrValue
     * @param bool $append
     *
     * @return bool
     */
    public function setRequestHeader($bstrHeader, $bstrValue, $append = false)
    {
        if ($append == false) {
            $this->httpheader[$bstrHeader] = $bstrHeader . ': ' . $bstrValue;
        } else {
            if (isset($this->httpheader[$bstrHeader])) {
                $this->httpheader[$bstrHeader] = $this->httpheader[$bstrHeader] . $bstrValue;
            } else {
                $this->httpheader[$bstrHeader] = $bstrHeader . ': ' . $bstrValue;
            }
        }

        return true;
    }

    /**
     * 添加数据.
     *
     * @param string $bstrItem  参数
     * @param mixed  $bstrValue 值
     */
    public function add_postdata($bstrItem, $bstrValue)
    {
        $this->postdata[$bstrItem] = $bstrValue;
    }

    /**
     * @param string $name
     * @param string $entity
     *
     * @return mixed
     */
    public function addBinary($name, $entity, $filename = null, $mime = '')
    {
        $this->private_isBinary = true;

        if (!is_file($entity)) {
            $filename = ($filename === null ? $name : $filename);
            $key = "$name\"; filename=\"$filename\"\r\nContent-Type: " . ($mime == '' ? 'application/octet-stream' : $mime) . "\r\n";
            $this->postdata[$key] = $entity;

            return;
        }

        if ($mime == '') {
            if (function_exists('mime_content_type')) {
                $mime = mime_content_type($entity);
            } elseif (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME);
                $mime = finfo_file($finfo, $name);
                finfo_close($finfo);
            } else {
                $mime = 'application/octet-stream';
            }
        }

        $filename = ($filename === null ? basename($entity) : $filename);
        if (class_exists('CURLFile')) {
            $this->postdata[$name] = new CURLFile(realpath($entity), $mime, $filename);

            return;
        }

        $entity = realpath($entity);
        $value = "@{$entity}";
        if (!empty($mime)) {
            $value .= ';type=' . $mime;
        }
        $value .= ';filename=' . $filename;

        $this->postdata[$name] = $value;

        return true;
    }

    /**
     * @param string $name
     * @param string $entity
     *
     * @return mixed
     */
    public function addText($name, $entity)
    {
        return $this->add_postdata($name, $entity);
    }

    /**
     * 重置.
     */
    private function reinit()
    {
        $this->readyState = 0; //状态
        $this->responseBody = null; //返回的二进制
        $this->responseStream = null; //返回的数据流
        $this->responseText = ''; //返回的数据
        $this->responseXML = null; //尝试把responseText格式化为XMLDom
        $this->status = 0; //状态码
        $this->statusText = ''; //状态码文本

        $this->private_isBinary = false;

        $this->option = array();
        $this->url = '';
        $this->postdata = array();
        $this->httpheader = array();
        $this->responseHeader = array();
        $this->parsed_url = array();
        $this->timeout = 30;
        $this->errstr = '';
        $this->errno = 0;
        $this->set_timeouts = false;

        $this->ch = curl_init();

        if (defined('ZBP_PATH')) {
            $this->setRequestHeader('User-Agent', 'Mozilla/5.0 (' . $GLOBALS['zbp']->cache->system_environment . ') Z-BlogPHP/' . $GLOBALS['zbp']->version);
        } else {
            $this->setRequestHeader('User-Agent', 'Mozilla/5.0 (compatible; ZBP_NetWork)');
        }
        $this->setMaxRedirs(1);
    }

    /**
     * 启用Gzip.
     */
    public function enableGzip()
    {
        if (extension_loaded('zlib')) {
            $this->isgzip = true;
        }
    }

    /**
     * @param int $n
     */
    public function setMaxRedirs($n = 0)
    {
        $this->maxredirs = (int) $n;
    }

    public function getStatusCode()
    {
        return $this->status;
    }

    public function getStatusText()
    {
        return $this->statusText;
    }

    public function getReasonPhrase()
    {
        return substr($this->statusText, 13);
    }

    public function withStatus($code, $reasonPhrase = '')
    {
    }

    public function getBody()
    {
        return $this->responseText;
    }

    public function getHeaders()
    {
        $headers = array();
        foreach ($this->responseHeader as $h) {
            $array = explode(': ', $h, 2);
            if (count($array) > 1) {
                if (isset($headers[$array[0]]) == false) {
                    $headers[$array[0]] = array();
                }
                $headers[$array[0]][] = $array[1];
            }
        }
        return $headers;
    }

    public function getHeader($name)
    {
        $headers = $this->getHeaders();
        if (isset($headers[$name])) {
            return $headers[$name];
        }
        return array();
    }

    public function hasHeader($name)
    {
        $headers = $this->getHeaders();
        return isset($headers[$name]);
    }

}
