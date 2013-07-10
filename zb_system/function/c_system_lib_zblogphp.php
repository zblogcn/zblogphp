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

		$c_option = include($GLOBALS["blogpath"].'zb_users/c_option.php');	
		$c_lang = include($GLOBALS["blogpath"].'zb_users/language/'.$c_option['ZC_BLOG_LANGUAGEPACK'].'.php');

		$this->option = $c_option;
		$this->lang = $c_lang;
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

	public function run(){
		echo 'hello zblog php!<br>';
	}

	public function initialize(){

	}

	public function terminate(){

	}

}

?>