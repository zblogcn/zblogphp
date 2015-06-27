<?php
/**
 * api
 * @package api
 * @php >= 5.2
 * @author zsx<zsx@zsxsoft.com>
 */
require 'route.php';
/**
 * API Singleton
 */
class API {
	/**
	 * Instance
	 */
	private static $instance;
	/**
	 * Route
	 */
	public static $Route;

	/**
	 * To avoid construct outside this class.
	 * @private
	 */
	private function __construct() {
		// Do nothing
	}

	/**
	 * To return instance
	 * @return API
	 */
	public static function getInstance() {

		if (is_null(self::$instance)) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * To avoid clone
	 */
	public function __clone() {
		throw new Exception("Singleton Class Can Not Be Cloned");
	}

	/**
	 * Init class
	 * @return true
	 */
	public static function init() {
		self::$Route = API_Route::getInstance();
		return true;
	}

}

API::init();
