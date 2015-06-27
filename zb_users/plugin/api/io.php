<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
* @package api/io
 * @php >= 5.2
 */
class API_IO {
	const FORMAT_JSON = 0;
	/**
	 * Instance
	 */
	private static $instance = null;
	/**
	 * Saved object
	 * for input/output
	 * @var array
	 */
	private static $savedObject = array();
	/**
	 * Input/Output format
	 * for input/output
	 * @var array
	 */
	private static $ioFormat = self::FORMAT_JSON;
	/**
	 * To avoid construct outside this class.
	 * @param string $formatString
	 * @private
	 */
	private function __construct($formatString) {

		if ($formatString === "") {
			self::$ioFormat = self::FORMAT_JSON;
		} else if (0 > strpos($formatString, 'json')) {
			self::end(API_ERROR::NON_ACCEPT);
		}

	}

	/**
	 * To return instance
	 * @param string $type
	 * @return API_Route
	 */
	public static function getInstance($formatString) {

		if (is_null(self::$instance)) {
			$class = __CLASS__;
			self::$instance = new $class($formatString);
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
	 * Return outputData or HTTP_GET
	 * @param  $name
	 * @return 
	 */
	public function __get($name) {

		if ($name === "output") {
			return $savedObject[$name];
		}
		return GetVars($name, 'GET');
		
	}

	/**
	 * Set outputData
	 * @param string $name
	 * @param $value
	 */
	public function __set($name, $value) {
		self::$savedObject[$name] = $value;
	}

	/**
	 * Return POST Data
	 * @param  string $name
	 */
	public static function post($name) {
		return GetVars($name, 'POST');
	}

	/**
	 * Write data to page and exit
	 * @param  integer $errorCode  
	 * @param  string  $errorMessage
	 */
	public static function end($errorCode = 0, $errorMessage = "") {

		$returnObject = array(
			'err' => $errorCode
		);

		$err = $errorCode;
		if ($errorCode !== API_ERROR::OK && $errorMessage === "") {
			$returnObject['message'] = API_ERROR::$errorCode[$err];
		} else if ($errorCode !== API_ERROR::OK && $errorMessage !== "") {
			$returnObject['message'] = $errorMessage;
		} else {
			$returnObject['data'] = self::$savedObject;
		}

		$returnObject['info'] = RunTime(); // A ZBP Function
		echo json_encode($returnObject);
		
		exit;
		return true;
	}




}