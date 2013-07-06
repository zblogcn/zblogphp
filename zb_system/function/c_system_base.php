<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

$c_option = include('zb_users/c_option.php');
$c_lang = include('zb_users/language/'.$c_option['ZC_BLOG_LANGUAGEPACK'].'.php');

class zblogphp{
	// 当前应用的配置
	//public $option = array();
	
	function __construct() {
		//$this->option = include('zb_users/c_option.php');
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