<?php

class UpYun
{
    const VERSION = '2.2.0';

    const ED_AUTO = 'v0.api.upyun.com';
    const ED_TELECOM = 'v1.api.upyun.com';
    const ED_CNC = 'v2.api.upyun.com';
    const ED_CTT = 'v3.api.upyun.com';

    const CONTENT_TYPE = 'Content-Type';
    const CONTENT_MD5 = 'Content-MD5';
    const CONTENT_SECRET = 'Content-Secret';

    // 缩略图
    const X_GMKERL_THUMBNAIL = 'x-gmkerl-thumbnail';
    const X_GMKERL_TYPE = 'x-gmkerl-type';
    const X_GMKERL_VALUE = 'x-gmkerl-value';
    const X_GMKERL_QUALITY = 'x­gmkerl-quality';
    const X_GMKERL_UNSHARP = 'x­gmkerl-unsharp';

    private $_bucketname;
    private $_username;
    private $_password;
    private $_timeout = 30;
    private $_file_secret = null;
    private $_content_md5 = null;

    protected $endpoint;

    /**
     * @var string: UPYUN 请求唯一id, 出现错误时, 可以将该id报告给 UPYUN,进行调试
     */
    private $x_request_id;

    /**
     * 初始化 UpYun 存储接口.
     *
     * @param $bucketname string 空间名称
     * @param $username string 操作员名称
     * @param $password string 密码
     * @param null $endpoint
     * @param int  $timeout
     */
    public function __construct($bucketname, $username, $password, $endpoint = null, $timeout = 30)
    {
        $this->_bucketname = $bucketname;
        $this->_username = $username;
        $this->_password = md5($password);
        $this->_timeout = $timeout;

        $this->endpoint = is_null($endpoint) ? self::ED_AUTO : $endpoint;
    }

    /**
     * 获取当前SDK版本号.
     */
    public function version()
    {
        return self::VERSION;
    }

    /**
     * 创建目录.
     *
     * @param $path string 路径
     * @param $auto_mkdir bool 是否自动创建父级目录，最多10层次
     *
     * @return mixed
     */
    public function makeDir($path, $auto_mkdir = true)
    {
        $headers = array('Folder' => 'true');
        if ($auto_mkdir) {
            $headers['Mkdir'] = 'true';
        }

        return $this->_do_request('PUT', $path, $headers);
    }

    /**
     * 删除目录和文件.
     *
     * @param string $path 路径
     *
     * @return bool
     */
    public function delete($path)
    {
        return $this->_do_request('DELETE', $path);
    }

    /**
     * 上传文件.
     *
     * @param string $path       存储路径
     * @param mixed  $file       需要上传的文件，可以是文件流或者文件内容
     * @param bool   $auto_mkdir 自动创建目录
     * @param array  $opts       可选参数
     *
     * @return mixed|null
     */
    public function writeFile($path, $file, $auto_mkdir = true, $opts = null)
    {
        if (is_null($opts)) {
            $opts = array();
        }

        if (!is_null($this->_content_md5)) {
            $opts[self::CONTENT_MD5] = $this->_content_md5;
        }
        if (!is_null($this->_file_secret)) {
            $opts[self::CONTENT_SECRET] = $this->_file_secret;
        }

        if ($auto_mkdir === true) {
            $opts['Mkdir'] = 'true';
        }

        return $this->_do_request('PUT', $path, $opts, $file);
    }

    /**
     * 下载文件.
     *
     * @param string $path        文件路径
     * @param mixed  $file_handle
     *
     * @return mixed
     */
    public function readFile($path, $file_handle = null)
    {
        return $this->_do_request('GET', $path, null, null, $file_handle);
    }

    /**
     * 获取目录文件列表.
     *
     * @param string $path 查询路径
     *
     * @return mixed
     */
    public function getList($path = '/')
    {
        $rsp = $this->_do_request('GET', $path);

        $list = array();
        if ($rsp) {
            $rsp = explode("\n", $rsp);
            foreach ($rsp as $item) {
                @list($name, $type, $size, $time) = explode("\t", trim($item));
                if (!empty($time)) {
                    $type = ($type == 'N') ? 'file' : 'folder';
                }

                $item = array(
                    'name' => $name,
                    'type' => $type,
                    'size' => intval($size),
                    'time' => intval($time),
                );
                array_push($list, $item);
            }
        }

        return $list;
    }

    /**
     * 获取文件、目录信息.
     *
     * @param string $path 路径
     *
     * @return mixed
     */
    public function getFileInfo($path)
    {
        $rsp = $this->_do_request('HEAD', $path);

        return $rsp;
    }

    /**
     * 获取空间使用情况.
     *
     * @param string $bucket
     *
     * @throws UpYunAuthorizationException
     * @throws UpYunException
     * @throws UpYunForbiddenException
     * @throws UpYunNotAcceptableException
     * @throws UpYunNotFoundException
     * @throws UpYunServiceUnavailable
     *
     * @return mixed
     */
    public function getFolderUsage($bucket = '/')
    {
        return $this->_do_request('GET', "{$bucket}?usage");
    }

    /**
     * 获取空间存储使用量，单位 byte.
     */
    public function getBucketUsage()
    {
        return $this->getFolderUsage('/');
    }

    public function getXRequestId()
    {
        return $this->x_request_id;
    }

    /**
     * 设置文件访问密钥.
     */
    public function setFileSecret($str)
    {
        $this->_file_secret = $str;
    }

    /**
     * 这是文件 md5 校验值
     */
    public function setContentMd5($str)
    {
        $this->_content_md5 = $str;
    }

    /**
     * 连接签名方法.
     *
     * @param $method string 请求方式 {GET, POST, PUT, DELETE}
     *
     * @return string 签名字符串
     */
    private function sign($method, $uri, $date, $length)
    {
        //$uri = urlencode($uri);
        $sign = "{$method}&{$uri}&{$date}&{$length}&{$this->_password}";

        return 'UpYun ' . $this->_username . ':' . md5($sign);
    }

    /**
     * HTTP REQUEST 封装.
     *
     * @param string $method      HTTP REQUEST方法，包括PUT、POST、GET、OPTIONS、DELETE
     * @param string $path        除Bucketname之外的请求路径，包括get参数
     * @param array  $headers     请求需要的特殊HTTP HEADERS
     * @param array  $body        需要POST发送的数据
     * @param null   $file_handle
     *
     * @throws UpYunAuthorizationException
     * @throws UpYunException
     * @throws UpYunForbiddenException
     * @throws UpYunNotAcceptableException
     * @throws UpYunNotFoundException
     * @throws UpYunServiceUnavailable
     *
     * @return mixed
     */
    protected function _do_request($method, $path, $headers = null, $body = null, $file_handle = null)
    {
        $uri = "/{$this->_bucketname}{$path}";
        $ch = curl_init("http://{$this->endpoint}{$uri}");

        $_headers = array('Expect:');
        if (!is_null($headers) && is_array($headers)) {
            foreach ($headers as $k => $v) {
                array_push($_headers, "{$k}: {$v}");
            }
        }

        $length = 0;
        $date = gmdate('D, d M Y H:i:s \G\M\T');

        if (!is_null($body)) {
            if (is_resource($body)) {
                fseek($body, 0, SEEK_END);
                $length = ftell($body);
                fseek($body, 0);

                array_push($_headers, "Content-Length: {$length}");
                curl_setopt($ch, CURLOPT_INFILE, $body);
                curl_setopt($ch, CURLOPT_INFILESIZE, $length);
            } else {
                $length = @strlen($body);
                array_push($_headers, "Content-Length: {$length}");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        } else {
            array_push($_headers, "Content-Length: {$length}");
        }

        array_push($_headers, "Authorization: {$this->sign($method, $uri, $date, $length)}");
        array_push($_headers, "Date: {$date}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, $_headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method == 'PUT' || $method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        if ($method == 'GET' && is_resource($file_handle)) {
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FILE, $file_handle);
        }

        if ($method == 'HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        }

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code == 0) {
            throw new UpYunException('Connection Failed', $http_code);
        }

        curl_close($ch);

        $header_string = '';
        $body = '';

        if ($method == 'GET' && is_resource($file_handle)) {
            $header_string = '';
            $body = $response;
        } else {
            list($header_string, $body) = explode("\r\n\r\n", $response, 2);
        }
        $this->setXRequestId($header_string);
        if ($http_code == 200) {
            if ($method == 'GET' && is_null($file_handle)) {
                return $body;
            } else {
                $data = $this->_getHeadersData($header_string);

                return count($data) > 0 ? $data : true;
            }
        } else {
            $message = $this->_getErrorMessage($header_string);
            if (is_null($message) && $method == 'GET' && is_resource($file_handle)) {
                $message = 'File Not Found';
            }
            switch ($http_code) {
                case 401:
                    throw new UpYunAuthorizationException($message);
                    break;
                case 403:
                    throw new UpYunForbiddenException($message);
                    break;
                case 404:
                    throw new UpYunNotFoundException($message);
                    break;
                case 406:
                    throw new UpYunNotAcceptableException($message);
                    break;
                case 503:
                    throw new UpYunServiceUnavailable($message);
                    break;
                default:
                    throw new UpYunException($message, $http_code);
            }
        }
    }

    /**
     * 处理HTTP HEADERS中返回的自定义数据.
     *
     * @param string $text header字符串
     *
     * @return array
     */
    private function _getHeadersData($text)
    {
        $headers = explode("\r\n", $text);
        $items = array();
        foreach ($headers as $header) {
            $header = trim($header);
            if (stripos($header, 'x-upyun') !== false) {
                list($k, $v) = explode(':', $header);
                $items[trim($k)] = in_array(substr($k, 8, 5), array('width', 'heigh', 'frame')) ? intval($v) : trim($v);
            }
        }

        return $items;
    }

    /**
     * 获取返回的错误信息.
     *
     * @param string $header_string
     *
     * @return mixed
     */
    private function _getErrorMessage($header_string)
    {
        list($status, $stash) = explode("\r\n", $header_string, 2);
        list($v, $code, $message) = explode(" ", $status, 3);

        return $message . " X-Request-Id: " . $this->getXRequestId();
    }

    private function setXRequestId($header_string)
    {
        preg_match('~^X-Request-Id: ([0-9a-zA-Z]{32})~ism', $header_string, $result);
        $this->x_request_id = isset($result[1]) ? $result[1] : '';
    }
}

class UpYunException extends Exception
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code);   // For PHP 5.2.x
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class UpYunAuthorizationException extends UpYunException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}

class UpYunForbiddenException extends UpYunException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}

class UpYunNotFoundException extends UpYunException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}

class UpYunNotAcceptableException extends UpYunException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, 406, $previous);
    }
}

class UpYunServiceUnavailable extends UpYunException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, 503, $previous);
    }
}
