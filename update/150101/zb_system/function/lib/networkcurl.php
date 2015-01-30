<?php
/**
 * curl类
 *
 * 自定义网络连接接口代替curl
 * @package Z-BlogPHP
 * @subpackage ClassLib/Network 网络连接
 */
class Networkcurl implements iNetwork
{
	private $readyState = 0;		#状态
	private $responseBody = NULL;   #返回的二进制
	private $responseStream = NULL; #返回的数据流
	private $responseText = '';	 #返回的数据
	private $responseXML = NULL;	#尝试把responseText格式化为XMLDom
	private $status = 0;			#状态码
	private $statusText = '';	   #状态码文本
	private $responseVersion = '';  #返回的HTTP版体
	
	private $option = array();
	private $url = '';
	private $postdata = array();
	private $httpheader = array();
	private $responseHeader = array();
	private $parsed_url = array();
	private $timeout = 30;
	private $errstr = '';
	private $errno = 0;
	private $ch = NULL;
	private $isgzip = false;
	private $maxredirs = 0;

	/**
	 * @ignore
	 */
	function __construct()
	{
		//$this->ch = curl_init();
	}

	/**
	 * @param $property_name
	 * @param $value
	 * @throws Exception
	 */
	public function __set($property_name, $value){
		throw new Exception($property_name.' readonly');
	}

	/**
	 * @param $property_name
	 * @return mixed
	 */
	public function __get($property_name){
		if(strtolower($property_name)=='responsexml'){
			$w = new DOMDocument();
			return $w->loadXML($this->responseText);
		}elseif(strtolower($property_name)=='scheme'||
				strtolower($property_name)=='host'||
				strtolower($property_name)=='port'||
				strtolower($property_name)=='user'||
				strtolower($property_name)=='pass'||
				strtolower($property_name)=='path'||
				strtolower($property_name)=='query'||
				strtolower($property_name)=='fragment'){
			if(isset($this->parsed_url[strtolower($property_name)]))return $this->parsed_url[strtolower($property_name)];
		}
		else{
			return $this->$property_name;
		}
	}

	/**
	 * 取消
	 */
	public function abort(){

	}

	/**
	 * @return string
	*/
	public function getAllResponseHeaders(){
		return implode("\r\n",$this->responseHeader);
	}

	/**
	 * 获取返回头
	 * @param $bstrHeader
	 * @return string
	 */
	public function getResponseHeader($bstrHeader){
		$name=strtolower($bstrHeader);
		foreach($this->responseHeader as $w){
			if(strtolower(substr($w,0,strpos($w,':')))==$name){
				return substr(strstr($w,': '),2);
			}
		}
		return '';
	}

	/**
	 * 链接远程接口
	 * @param $bstrMethod
	 * @param $bstrUrl
	 * @param bool $varAsync
	 * @param string $bstrUser
	 * @param string $bstrPassword
	 * @return bool
	 * @throws Exception
	 */
	public function open($bstrMethod, $bstrUrl, $varAsync=true, $bstrUser='', $bstrPassword=''){ //Async无用
		$this->reinit();
		$method = strtoupper($bstrMethod);
		$this->option['method'] = $method;
		$this->parsed_url = parse_url($bstrUrl);
		if (!$this->parsed_url) throw new Exception('URL Syntax Error!');
		if(!isset($this->parsed_url['port'])){
			if($this->parsed_url['scheme']=='https'){
				$this->parsed_url['port'] = 443;
			}else{
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
	 * 设置超时时间
	 * @param $resolveTimeout
	 * @param $connectTimeout
	 * @param $sendTimeout
	 * @param $receiveTimeout
	 */
	public function setTimeOuts($resolveTimeout,$connectTimeout,$sendTimeout,$receiveTimeout)
	{
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, $resolveTimeout);
	}

	/**
	* 发送数据
	* @param string $varBody
	*/
	public function send($varBody = ''){

		$data = $varBody;
		if(is_array($data)){
			$data=http_build_query($data);
		}

		if($this->option['method'] == 'POST')
		{
			if($data=='') $data = http_build_query($this->postdata);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS,$data);
			curl_setopt($this->ch, CURLOPT_POST, 1);
		}

		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$this->httpheader);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

		if($this->maxredirs>0){
			if(ini_get("safe_mode")==false && ini_get("open_basedir")==false){
				curl_setopt($this->ch, CURLOPT_MAXREDIRS, $this->maxredirs);
				curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION,true);
			}
		}
		
		if($this->isgzip == true){
			curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip');
		}
		
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);

		$result = curl_exec($this->ch);
		$header_size = curl_getinfo($this->ch,CURLINFO_HEADER_SIZE);
		$this->responseHeader = explode("\r\n",substr($result,0,$header_size-4));

		$this->responseText = substr($result,$header_size);
		curl_close($this->ch);
		if(isset($this->responseHeader[0])){
			$this->statusText=$this->responseHeader[0];
			$a=explode(' ',$this->statusText);
			if(isset($a[0]))$this->responseVersion=$a[0];
			if(isset($a[1]))$this->status=$a[1];
			unset($this->responseHeader[0]);
		}

	}

	/**
	 * 设置请求HTTP头
	 * @param $bstrHeader
	 * @param $bstrValue
	 * @param bool $append
	 * @return bool
	 */
	 public function setRequestHeader($bstrHeader, $bstrValue, $append=false){
		if($append==false){
			$this->httpheader[$bstrHeader]=$bstrHeader.': '.$bstrValue;
		}else{
			if(isset($this->httpheader[$bstrHeader])){
				$this->httpheader[$bstrHeader] = $this->httpheader[$bstrHeader].$bstrValue;
			}else{
				$this->httpheader[$bstrHeader]=$bstrHeader.': '.$bstrValue;
			}
		}
		return true;
	}

	/**
	 * 添加数据
	 * @param string $bstrItem 参数
	 * @param mixed $bstrValue 值
	 */
	public function add_postdata($bstrItem, $bstrValue){
		array_push($this->postdata,array(
			$bstrItem => $bstrValue
		));
	}

	/**
	 * 重置
	 */
	private function reinit(){
		global $zbp;
		$this->readyState = 0;		#状态
		$this->responseBody = NULL;   #返回的二进制
		$this->responseStream = NULL; #返回的数据流
		$this->responseText = '';	 #返回的数据
		$this->responseXML = NULL;	#尝试把responseText格式化为XMLDom
		$this->status = 0;			#状态码
		$this->statusText = '';	   #状态码文本

		$this->option = array();
		$this->url = '';
		$this->postdata = array();
		$this->httpheader = array();
		$this->responseHeader = array();
		$this->parsed_url = array();
		$this->timeout = 30;
		$this->errstr = '';
		$this->errno = 0;

		$this->ch = curl_init();
		$this->setRequestHeader('User-Agent','Mozilla/5.0 ('.$zbp->cache->system_environment.') Z-BlogPHP/' . ZC_BLOG_VERSION);
		$this->setMaxRedirs(1);
	}

	/**
	  * 启用Gzip
	 */
	public function enableGzip(){
		if( extension_loaded('zlib') ){
			$this->isgzip = true;
		}
	}

	/**
	 * @param int $n
	 */
	public function setMaxRedirs($n=0){
		$this->maxredirs=(int)$n;
	}
}
