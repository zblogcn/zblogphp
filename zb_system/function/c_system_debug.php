<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function exception_error_handler($errno, $errstr, $errfile, $errline ){

    if (error_reporting() === 0){
		return;
	}

    #throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	ob_clean();
	echo "exception_error_handler:".'<br/>';
	echo $errno, $errstr, $errfile, $errline;
	die();
 }

set_error_handler("exception_error_handler");

function exception_handler($error){
     // Do some stuff
	ob_clean();
	echo "exception_handler:".'<br/>';
	var_dump($error);
	die();
}

set_exception_handler('exception_handler');


function shutdown_error_handler(){
	if ($error = error_get_last()) {
		ob_clean();
		echo "shutdown_error_handler:".'<br/>';
		var_dump($error);
		die();
	}
}

register_shutdown_function('shutdown_error_handler');


?>