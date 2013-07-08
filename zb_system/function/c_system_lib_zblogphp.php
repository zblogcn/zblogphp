<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class zblogphp{
	// 当前应用的配置
	public $option = array();
	public $lang = array();
	public $path = null;
	
	function __construct() {
		$this->option = $GLOBALS["c_option"];
		$this->lang = $GLOBALS["c_lang"];
		$this->path = $GLOBALS["blogpath"];
		//define();
	}
	
	public function __get($var) {

	}
	
	public function __call($method, $args) {
		throw new Exception('');
	}

	public function run(){
		echo 'hello zblog php!<br>';
	}

}

?>