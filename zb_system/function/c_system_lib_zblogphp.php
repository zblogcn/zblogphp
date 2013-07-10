<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class ZBlogPHP{
	// 当前应用的配置
	public $option = array();
	public $lang = array();
	public $path = null;
	public $host = null;
	public $db = null;
	
	function __construct() {

		$this->option = $GLOBALS["c_option"];
		$this->lang = $GLOBALS["c_lang"];
		$this->path = $GLOBALS["blogpath"];
		$this->host = $GLOBALS["bloghost"];
		//define();
	}

	function __destruct(){
		$c_option = null;
		$c_land = null;
		$path = null;
		$host = null;
		
	}
	
	public function __get($var) {

	}
	
	public function __call($method, $args) {
		throw new Exception('');
	}

	public function Run(){
		echo 'hello zblog php!<br>';
	}

	#初始化连接
	public function Initialize(){

	}

	#终止连接，释放资源
	public function Terminate(){

	}

}

?>