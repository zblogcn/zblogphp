<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

class c_system_base{
	// 当前应用的配置
	public $c_option = array();
	
	function __construct(&$c_option) {
		$this->c_option = &$c_option;
		define();
	}
	
	public function __get($var) {

	}
	
	public function __call($method, $args) {
		throw new Exception('');
	}
}
?>