<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

function exception_error_handler($errno, $errstr, $errfile, $errline ){

    // if (error_reporting() === 0)
    // {
    //     return;
     //}

     //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	ob_clean();
	echo "string";
 }

set_error_handler("exception_error_handler");

function catchException($error){
     // Do some stuff
	ob_clean();
	var_dump($error);
}

set_exception_handler('catchException');


function shutdown_error_handler(){
    if ($error = error_get_last()) {
    	ob_clean();
        var_dump($error);
    }
}


register_shutdown_function('shutdown_error_handler');


?>