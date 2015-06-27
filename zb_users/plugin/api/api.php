<?php
require 'route.php';
class API {
	private static $instance;
	public static $Route;

	private function __construct() {
		var_dump('a');
	}

	public static function getInstance() {

		if (is_null(self::$instance)) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

	public function __clone() {
		throw new Exception("Singleton Class Can Not Be Cloned");
	}

	public static function init() {
		self::$Route = API_Route::getInstance();
	}

}

API::init();
