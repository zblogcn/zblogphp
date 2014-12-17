<?php

function GetRequestUri() {
	$url = '';
	if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])){
		$url = $_SERVER['HTTP_X_ORIGINAL_URL'];
	} elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
		$url = $_SERVER['HTTP_X_REWRITE_URL'];
		if(strpos($url,'?') !== false){
			$querys=GetValueInArray(explode('?',$url),'1');
			foreach (explode('&',$querys) as $query){
				$name=GetValueInArray(explode('=',$query),'0');
				$value=GetValueInArray(explode('=',$query),'1');
				$name=urldecode($name);
				$value=urldecode($value);
				if(!isset($_GET[$name]))$_GET[$name]=$value;
				if(!isset($_GET[$name]))$_REQUEST[$name]=$value;
				$name='';
				$value='';
			}
		}
	} elseif (isset($_SERVER['REQUEST_URI'])) {
		$url = $_SERVER['REQUEST_URI'];
	} elseif (isset($_SERVER['REDIRECT_URL'])) {
		$url = $_SERVER['REDIRECT_URL'];
		if (isset($_SERVER['REDIRECT_QUERY_STRIN']))
			$url .= '?' . $_SERVER['REDIRECT_QUERY_STRIN'];
	} else {
		$url = $_SERVER['PHP_SELF'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
	}

	return $url;
}

echo GetRequestUri();
echo $s=dirname(__FILE__) . GetRequestUri();
var_dump(file_exists($s));


date_default_timezone_set('America/New_York'); 

phpinfo();