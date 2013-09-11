<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


set_error_handler('error_handler');
set_exception_handler('exception_handler');
register_shutdown_function('shutdown_error_handler');



 
function error_handler($errno, $errstr, $errfile, $errline ){

	#throw new ErrorException($errstr,0,$errno, $errfile, $errline);
	//die();

	ob_clean();		
	$zbe=ZBlogException::GetInstance();
	$zbe->ParseError($errno, $errstr, $errfile, $errline);
	$zbe->Display();
	die();

 }




function exception_handler($exception){

	ob_clean();
	$zbe=ZBlogException::GetInstance();
	$zbe->ParseException($exception);
	$zbe->Display();
	die();
}




function shutdown_error_handler(){
	if ($error = error_get_last()) {

		ob_clean();
		$zbe=ZBlogException::GetInstance();
		$zbe->ParseShutdown($error);
		$zbe->Display();
		die();
	}
}


/**
* 
*/
class ZBlogException
{
	static private $_zbe=null;
	public $type;
	public $message;
	public $file;
	public $line;

	static public function GetInstance(){
		if(!isset(self::$_zbe)){
			self::$_zbe=new ZBlogException;
		}
		return self::$_zbe;
	}


	static public function Trace($s){

	}


	function ParseError($type,$message,$file,$line){
		$this->type=$type;
		$this->message=$message;
		$this->file=$file;
		$this->line=$line;	
	}	

	function ParseShutdown($error){

		$this->type=$error['type'];
		$this->message=$error['message'];
		$this->file=$error['file'];
		$this->line=$error['line'];	

	}

	function ParseException($exception){

		$this->message=$exception->getMessage();
		$this->type=$exception->getCode();
		$this->file=$exception->getFile();
		$this->line=$exception->getLine();
	}



	function Display(){

		Http500();
		include $GLOBALS['blogpath'] . 'zb_system/defend/error.html';

	}

	function get_code($file, $line) {
		$aFile = array_slice(file($file), max(0, $line - 5), 10, true);
		foreach ($aFile as &$sData) { //&$ = ByRef
				$sData = htmlspecialchars($sData);
		}
		unset($sData);
		return $aFile;
	}


}

?>