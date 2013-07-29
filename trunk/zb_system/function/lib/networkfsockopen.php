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
class Networkfsockopen implements iNetwork
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
	
	public function __set($property_name, $value){
		#$var = strtolower($property_name);
		#$readonly = array('readystate','responsebody');
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
		throw new Exception('fsockopen cannot abort.');
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
		//初始化变量
		$this->reinit();
		$method=strtoupper($bstrMethod);
		$this->option['method'] = $method;
		
		$this->parsed_url = parse_url($bstrUrl);
		if(!$this->parsed_url)
		{
			throw new Exception('URL Syntax Error!');
		}
		else{
			//bstrUser & bstrPassword ?
		}
		
		return true;
	}
	
	public function send($varBody=''){
		$data=$varBody;
		
		if($this->option['method']=='POST')
		{
			
			if($data==''){
				$data=http_build_query($this->postdata);
			}
			$this->option['content'] = $data;
			
			$this->httpheader[] = 'Content-Type: application/x-www-form-urlencoded';
			$this->httpheader[] = 'Content-Length: ' . strlen($data);

		}

		$this->httpheader[] = 'Host: ' . $this->parsed_url['host'];
		$this->httpheader[] = 'Connection: close';
		
		$this->option['header'] = implode("\r\n",$this->httpheader);
		
		$socket = fsockopen(
					$this->parsed_url['host'],
					$this->port,
					$this->errno,
					$this->errstr,
					$this->timeout
				  ); 
				  
		$url = $this->option['method'] . ' ' . $this->parsed_url['path'];
		
		if(isset($this->parsed_url["query"]))
		{
			$url.= "?" . $this->parsed_url["query"];
		}
		fwrite($socket,
			   $url . ' HTTP/1.1' . "\r\n"
	    ); 
		fwrite($socket,$this->option['header']."\r\n"); 
		fwrite($socket,"\r\n");
		fwrite($socket,$this->option['content']."\r\n");
		fwrite($socket,"\r\n");  

		while ($str = trim(fgets($socket,4096)))
		{ 
			$this->responseHeader.=$str;
		} 
		
		while (!feof($socket))
		{ 
			$this->responseText .= fgets($socket,4096); 
		} 
		
		fclose($socket);
				
	}
	public function setRequestHeader($bstrHeader, $bstrValue){
		array_push($this->header,$bstrHeader.': '.$bstrValue);
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
		
	}
}
?>