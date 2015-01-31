<?php
/**
 * 自定义网络连接接口
 */
if ( !interface_exists ( 'iNetwork' )) {
	/**
	 * 网络连接接口
	 *
	 * @package Z-BlogPHP
	 * @subpackage Interface/Network 网络连接
	 */
	interface iNetwork{
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
		 * @return mixed
		 */
		public function getResponseHeader($bstrHeader);
		/**
		 * @param $bstrMethod
		 * @param $bstrUrl
		 * @param bool $varAsync
		 * @param string $bstrUser
		 * @param string $bstrPassword
		 * @return mixed
		 */
		public function open($bstrMethod, $bstrUrl, $varAsync=true, $bstrUser='', $bstrPassword='');

		/**
		 * @param string $varBody
		 * @return mixed
		 */
		public function send($varBody='');

		/**
		 * @param $bstrHeader
		 * @param $bstrValue
		 * @return mixed
		 */
		public function setRequestHeader($bstrHeader, $bstrValue);

		/**
		 * @return mixed
		 */
		public function enableGzip();

		/**
		 * @param int $n
		 * @return mixed
		 */
		public function setMaxRedirs($n=0);

	}

}

/**
 * 网络连接类
 *
 * @package Z-BlogPHP
 * @subpackage ClassLib/Network
 */
class Network {

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
	 * @var null
	 */
	static private $_network = null;

	/**
	 * 构造函数
	 */
	function __construct(){
		if (function_exists('curl_init') && function_exists('curl_exec'))
		{
			$this->network_list[] = 'curl';
			$this->curl = true;
		}
		if ((bool)ini_get('allow_url_fopen') && function_exists('fsockopen'))
		{
			if(function_exists('fsockopen')) $this->network_list[] = 'fsockopen';
			$this->fsockopen = true;
		}
		if ((bool)ini_get('allow_url_fopen'))
		{
			if(function_exists('file_get_contents')) $this->network_list[] = 'file_get_contents';
			$this->file_get_contents = true;
		}
	}

	/**
	 * @param string $extension
	 * @return bool|network
	 */
	static function Create($extension = ''){
		if(!isset(self::$_network)){
			self::$_network=new Network;
		}	
		if ((!self::$_network->file_get_contents) && (!self::$_network->fsockopen) && (!self::$_network->curl)) return false;
		$extension = ($extension == '' ? self::$_network->network_list[0] : $extension);
		$type = 'network' . $extension;
		$network = New $type();
		return $network;
	}

}
