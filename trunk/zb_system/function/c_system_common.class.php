<?php
/**
 * Z-Blog with PHP
 * @author 未寒 <im@imzhou.com>
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

class c_system_common extends c_system_base {

	function __construct(&$c_option) {
		parent::__construct($c_option);
		session_start();
	}

}
?>