<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

/**
*
*/
class Networkcurl implements iNetwork
{
	private $readyState = 0;        #状态
	private $responseBody = NULL;   #返回的二进制
	private $responseStream = NULL; #返回的数据流
	private $responseText = '';     #返回的数据
	private $responseXML = NULL;    #尝试把responseText格式化为XMLDom
	private $status = 0;            #状态码
	private $statusText = '';       #状态码文本

	private $option = array();
	private $url = '';
	private $postdata = array();
	private $httpheader = array();
	private $responseHeader = array();
	private $parsed_url = array();
	private $port = 80;
	private $timeout = 30;
	private $errstr = '';
	private $errno = 0;
	private $ch = NULL;

	function __construct()
	{
		$this->ch = curl_init();
	}

	public function __set($property_name, $value){
		throw new Exception($property_name.' readonly');
	}

	public function __get($property_name){
		if(strtolower($property_name)=='responsexml')
		{
			$w = new DOMDocument();
			return $w->loadXML($this->responseText);
		}
		else
		{
			return $this->$property_name;
		}
	}

	public function abort(){

	}

	public function getAllResponseHeaders(){
		return implode("\r\n",$this->responseHeader);
	}

	public function getResponseHeader($bstrHeader){
		$name=strtolower($bstrHeader);
		foreach($this->responseHeader as $w){
			if(strtolower(substr($w,0,strpos($w,':')))==$name){
				return substr(strstr($w,': '),2);
			}
		}
		return '';
	}

	public function open($bstrMethod, $bstrUrl, $varAsync=true, $bstrUser='', $bstrPassword=''){ //Async无用
		$this->reinit();
		$method = strtoupper($bstrMethod);
		$this->option['method'] = $method;
		$this->parsed_url = parse_url($bstrUrl);
		if (!$this->parsed_url) throw new Exception('URL Syntax Error!');

		curl_setopt($this->ch, CURLOPT_URL, $bstrUrl);
		curl_setopt($this->ch, CURLOPT_HEADER, 1);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_POST, ($method == 'POST' ? 1 : 0));
		return true;
	}

	public function setTimeOuts($resolveTimeout,$connectTimeout,$sendTimeout,$receiveTimeout)
	{
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $resolveTimeout);
	}

	public function send($varBody = ''){

		$data = $varBody;

		if($this->option['method'] == 'POST')
		{
			if($data=='') $data = http_build_query($this->postdata);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS,$data);
		}

		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$this->httpheader);

		$result = curl_exec($this->ch);
		$header_size = curl_getinfo($this->ch,CURLINFO_HEADER_SIZE);
		$this->responseHeader = explode("\n",substr($result,0,$header_size));
		$this->responseText = substr($result,$header_size);
		curl_close($this->ch);

	}
	public function setRequestHeader($bstrHeader, $bstrValue){
		array_push($this->httpheader,$bstrHeader.': '.$bstrValue);
		return true;
	}

	public function add_postdata($bstrItem, $bstrValue){
		array_push($this->postdata,array(
			$bstrItem => $bstrValue
		));
	}

	private function reinit(){
		$this->readyState = 0;        #状态
		$this->responseBody = NULL;   #返回的二进制
		$this->responseStream = NULL; #返回的数据流
		$this->responseText = '';     #返回的数据
		$this->responseXML = NULL;    #尝试把responseText格式化为XMLDom
		$this->status = 0;            #状态码
		$this->statusText = '';       #状态码文本

		$this->option = array();
		$this->url = '';
		$this->postdata = array();
		$this->httpheader = array();
		$this->responseHeader = array();
		$this->parsed_url = array();
		$this->port = 80;
		$this->timeout = 30;
		$this->errstr = '';
		$this->errno = 0;

		$this->ch = curl_init();
		$this->setRequestHeader('User-Agent','Z-Blog PHP curl module');

	}
}
