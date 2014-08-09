<?php
/**
 * 错误调试
 * @package Z-BlogPHP
 * @subpackage System/Debug 错误调试
 * @copyright (C) RainbowSoft Studio
 */

 
/**
 * 错误调度提示
 * @param int $errno 错误级别
 * @param string $errstr 错误信息
 * @param string $errfile 错误文件名
 * @param int $errline 错误行
 * @return bool
 */
function Debug_Error_Handler($errno, $errstr, $errfile, $errline) {
	$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;

	if(is_readable($errfile)){
		$a = array_slice(file($errfile), max(0,$errline-1), 1, true);
		$s = reset($a);
		if(strpos($s,'@')!==false)return true;
	}

	if(ZBlogException::$isdisable==true)return true;
	
	if(ZBlogException::$iswarning==false){
		if( $errno == E_WARNING )return true;
		if( $errno == E_USER_WARNING )return true;
	}
	if(ZBlogException::$isstrict==false){
		if( $errno == E_NOTICE )return true;
		if( $errno == E_STRICT )return true;
		if( $errno == E_USER_NOTICE )return true;
	}

	if( $errno == E_CORE_WARNING  )return true;
	if( $errno == E_COMPILE_WARNING  )return true;
	if( defined('E_DEPRECATED') && $errno== E_DEPRECATED )return true;
	if( defined('E_USER_DEPRECATED ') && $errno== E_USER_DEPRECATED )return true;
	
	$zbe = ZBlogException::GetInstance();
	$zbe->ParseError($errno, $errstr, $errfile, $errline);
	$zbe->Display();
	die();

}

/**
 * 异常处理
 * @param $exception 异常事件
 * @return bool
 */
function Debug_Exception_Handler($exception) {
	$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;
	if(ZBlogException::$isdisable==true)return true;

	$zbe = ZBlogException::GetInstance();
	$zbe->ParseException($exception);
	$zbe->Display();
	die();
}

/**
 * 当机错误处理
 * @return bool
 */
function Debug_Shutdown_Handler() {
	foreach ($GLOBALS['Filter_Plugin_Debug_Shutdown_Handler'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname();
	}
	if ($error = error_get_last()) {
		$_SERVER['_error_count'] = $_SERVER['_error_count'] +1;

		if(ZBlogException::$isdisable==true)return true;

		if(ZBlogException::$iswarning==false){
			if( $error['type'] == E_WARNING )return true;
			if( $error['type'] == E_USER_WARNING )return true;
		}
		if(ZBlogException::$isstrict==false){
			if( $error['type'] == E_NOTICE )return true;
			if( $error['type'] == E_STRICT )return true;
			if( $error['type'] == E_USER_NOTICE )return true;
		}

		if( $error['type'] == E_CORE_WARNING  )return true;
		if( $error['type'] == E_COMPILE_WARNING  )return true;
		if( defined('E_DEPRECATED') && $error['type'] == E_DEPRECATED )return true;
		if( defined('E_USER_DEPRECATED ') && $error['type'] == E_USER_DEPRECATED )return true;

		$zbe = ZBlogException::GetInstance();
		$zbe->ParseShutdown($error);
		$zbe->Display();
		die();
	}
}

/**
 * Class ZBlogException
 *
 */
class ZBlogException {
	private static $_zbe = null;
	public static $isdisable = false;
	private static $_isdisable = null;
	public static $isfull = false;
	public static $isstrict = false;
	public static $iswarning = true;
	public static $error_id=0;
	public static $error_file=null;
	public static $error_line=null;
	public $type;
	public $message;
	public $file;
	public $line;
	public $errarray=array();

	/**
	 * 构造函数，定义常见错误代码
	 */
	function __construct(){
		$this->errarray=array(
			0=>'UNKNOWN',
			1=>'E_ERROR',
			2=>'E_WARNING',
			4=>'E_PARSE',
			8=>'E_NOTICE',
			16=>'E_CORE_ERROR',
			32=>'E_CORE_WARNING',
			64=>'E_COMPILE_ERROR',
			128=>'E_COMPILE_WARNING',
			256=>'E_USER_ERROR',
			512=>'E_USER_WARNING',
			1024=>'E_USER_NOTICE',
			2048=>'E_STRICT',
			4096=>'E_RECOVERABLE_ERROR',
			8192=>'E_DEPRECATED',
			16384=>'E_USER_DEPRECATED',
		);
	}

	/**
	* 获取参数
	* @param $name
	* @return mixed
	*/
	public function __get($name){
		if($name=='typeName'){
			if(isset($this->errarray[$this->type])){
				return $this->errarray[$this->type];
			}else{
				return $this->errarray[0];
			}
		}
	}
	
	/**
	* 获取单一实例
	* @return ZBlogException
	*/
	static public function GetInstance() {
		if (!isset(self::$_zbe)) {
			self::$_zbe = new ZBlogException;
		}

		return self::$_zbe;
	}

	/**
	* 设定错误处理函数
	*/
	static public function SetErrorHook() {
		set_error_handler('Debug_Error_Handler');
		set_exception_handler('Debug_Exception_Handler');
		register_shutdown_function('Debug_Shutdown_Handler');
	}

	/**
	* 清除错误信息
	*/
	static public function ClearErrorHook() {
		#set_error_handler(create_function('', ''));
		#set_exception_handler(create_function('', ''));
		#register_shutdown_function(create_function('', ''));
		self::$isdisable = true;
	}

	/**
	* 禁止错误调度
	*/
	static public function DisableErrorHook() {
		self::$isdisable = true;
	}
	
	static public function SuspendErrorHook() {
		if(self::$_isdisable !== null)return;
		self::$_isdisable = self::$isdisable;
		self::$isdisable = true;
	}
	static public function ResumeErrorHook() {
		if(self::$_isdisable === null)return;
		self::$isdisable = self::$_isdisable;
		self::$_isdisable = null;
	}
	
	static public function EnableErrorHook() {
		self::$isdisable = false;
	}

	static public function DisableStrict() {
		self::$isstrict = false;
	}

	static public function EnableStrict() {
		self::$isstrict = true;
	}
	
	static public function DisableWarning() {
		self::$iswarning = false;
	}

	static public function EnableWarning() {
		self::$iswarning = true;
	}
	
	static public function DisableFull() {
		self::$isfull = false;
	}

	static public function EnableFull() {
		self::$isfull = true;
	}
	
	static public function Trace($s) {
		Logs($s);
	}

	/**
	* 解析错误信息
	* @param $type
	* @param $message
	* @param $file
	* @param $line
	*/
	function ParseError($type, $message, $file, $line) {

		$this->type = $type;
		$this->message = $message . ' (set_error_handler)';
		$this->file = $file;
		$this->line = $line;

	}

	/**
	* 解析错误信息
	* @param $error
	*/
	function ParseShutdown($error) {

		$this->type = $error['type'];
		$this->message = $error['message'] . ' (register_shutdown_function)';
		$this->file = $error['file'];
		$this->line = $error['line'];
	}

	/**
	* 解析异常信息
	* @param $exception
	*/
	function ParseException($exception) {

		$this->message = $exception->getMessage() . ' (set_exception_handler)';
		$this->type = $exception->getCode();
		$this->file = $exception->getFile();
		$this->line = $exception->getLine();

		if (self::$error_file !== null)
			$this->file = self::$error_file;
		if (self::$error_line !== null)
			$this->line = self::$error_line;

	}

	/**
	* 输出错误信息
	*/
	function Display() {

		if (!headers_sent())
		{
			Http500();
			ob_clean();
		}
		
		require dirname(__FILE__) . '/../defend/error.html';
		RunTime();
		die();
	}

	/**
	* 获取出错代码信息
	* @param $file
	* @param $line
	* @return array
	*/
	function get_code($file, $line) {
		if(strcasecmp($file,'Unknown')==0)return array();
		if(!is_readable($file))return array();
		$aFile = array_slice(file($file), max(0, $line - 5), 10, true);
		foreach ($aFile as &$sData) { //&$ = ByRef
			$sData = htmlspecialchars($sData);
		}
		return $aFile;
	}

}
