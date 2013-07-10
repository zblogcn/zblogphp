<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

set_error_handler("exception_error_handler");
set_exception_handler('exception_handler');
register_shutdown_function('shutdown_error_handler');



function exception_error_handler($errno, $errstr, $errfile, $errline ){

	#throw new ErrorException($errstr,0,$errno, $errfile, $errline);
	//die();

	#echo "exception_error_handler:".'<br/>';
	#echo $errno, $errstr, $errfile, $errline;
	#die();

	ob_clean();		
	$zbe=new ZblogException();
	$zbe->ParseHandler($errno, $errstr, $errfile, $errline);
	$zbe->Display();
	die();

 }




function exception_handler($exception){

	#echo "exception_handler:".'<br/>';
	#var_dump($exception);
	#die();

	ob_clean();
	$zbe=new ZblogException();
	$zbe->ParseException($exception);
	$zbe->Display();
	die();
}




function shutdown_error_handler(){
	if ($error = error_get_last()) {

		#echo "shutdown_error_handler:".'<br/>';
		#var_dump($error);
		#die();

		ob_clean();
		$zbe=new ZblogException();
		$zbe->ParseError($error);
		$zbe->Display();
		die();
	}
}


/**
* 
*/
class ZblogException
{
	
	public $type;
	public $message;
	public $file;
	public $line;


	function ParseHandler($type,$message,$file,$line){
		$this->type=$type;
		$this->message=$message;
		$this->file=$file;
		$this->line=$line;	
	}	

	function ParseError($error){

		$this->type=$error['type'];
		$this->message=$error['message'];
		$this->file=$error['file'];
		$this->line=$error['line'];	

	}

	function ParseException($exception){
		var_dump($exception);
	}



	function Display(){

		$e='';
		$e.= 'type:<br/>'.$this->type;
		$e.= "<hr/>";
		$e.= 'message:<br/>'.$this->message;
		$e.= "<hr/>";
		$e.= 'file:<br/>'.$this->file;
		$e.= "<hr/>";
		$e.= 'line:<br/>'.$this->line;		

		$h=file_get_contents($GLOBALS['blogpath'] . 'zb_system/defend/error.html');
		$h=str_replace('<#ZC_BLOG_HOST#>', $GLOBALS['bloghost'], $h);
		$h=str_replace('<#ZC_BLOG_TITLE#>', $GLOBALS['c_option']['ZC_BLOG_TITLE'], $h);
		$h=str_replace('<#BlogTitle#>', $GLOBALS['c_lang']['ZC_MSG045'], $h);		
		$h=str_replace('<#ERROR#>', $e, $h);
		echo $h;

	}


}

?>