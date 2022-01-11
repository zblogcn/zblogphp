<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 网络连接接口.
 *
 * @property int $readyState 状态
 * @property mixed $responseBody 返回的二进制
 * @property string $responseText 返回的字符串
 * @property SimpleXMLElement $responseXML 返回的XML DOM
 * @property int $status 状态码
 * @property string $statusText 状态码文本
 * @property string $responseVersion HTTP版本号
 * @property string[] $responseHeader 返回的 HTTP 响应头
 */
interface Network__Interface
{

    /**
     * @return mixed
     */
    public function abort();

    /**
     * @return mixed
     */
    public function getAllResponseHeaders();

    /**
     * @param $bstrHeader
     *
     * @return mixed
     */
    public function getResponseHeader($bstrHeader);

    /**
     * @param $bstrMethod
     * @param $bstrUrl
     * @param bool   $varAsync
     * @param string $bstrUser
     * @param string $bstrPassword
     *
     * @return mixed
     */
    public function open($bstrMethod, $bstrUrl, $varAsync = true, $bstrUser = '', $bstrPassword = '');

    /**
     * @param string $varBody
     *
     * @return mixed
     */
    public function send($varBody = '');

    /**
     * @param $bstrHeader
     * @param $bstrValue
     *
     * @return mixed
     */
    public function setRequestHeader($bstrHeader, $bstrValue);

    /**
     * @return mixed
     */
    public function enableGzip();

    /**
     * @param int $n
     *
     * @return mixed
     */
    public function setMaxRedirs($n = 0);

    /**
     * @param string $name
     * @param string $entity
     *
     * @return mixed
     */
    public function addBinary($name, $entity);

    /**
     * @param string $name
     * @param string $entity
     *
     * @return mixed
     */
    public function addText($name, $entity);

    /**
     * @param $resolveTimeout
     * @param $connectTimeout
     * @param $sendTimeout
     * @param $receiveTimeout
     *
     * @return mixed
     */
    public function setTimeOuts($resolveTimeout, $connectTimeout, $sendTimeout, $receiveTimeout);

    /**
     * @return mixed
     */
    public function getStatusCode();

    /**
     * @return mixed
     */
    public function getStatusText();

    /**
     * @return mixed
     */
    public function getReasonPhrase();

    /**
     * @return mixed
     */
    public function withStatus($code, $reasonPhrase = '');

    /**
     * @return mixed
     */
    public function getBody();

    /**
     * @return mixed
     */
    public function getHeaders();

    /**
     * @return mixed
     */
    public function getHeader($name);
    /**
     * @return mixed
     */
    public function hasHeader($name);

}
