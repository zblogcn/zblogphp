<?php
/**
 * Z-Blog with PHP
 * @author 未寒 <im@imzhou.com>
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

class c_system_base{
	// 当前应用的配置
	public $c_option = array();
	
	function __construct(&$c_option) {
		$this->c_option = &$c_option;
	}
	
	public function __get($var) {
		if($var == 'view') {
			$this->view = new template($this->c_option);
			return $this->view;
		} else {
			$this->$var = core::model($this->conf, $var);
			if(!$this->$var) {
				throw new Exception('Not found model:'.$var);
			}
			return $this->$var;
		}
	}
	
	public function __call($method, $args) {
		throw new Exception('base_control.class.php: Not implement method：'.$method.': ('.var_export($args, 1).')');
	}
}
?>