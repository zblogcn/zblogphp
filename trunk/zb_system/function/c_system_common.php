<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

$_SERVER['_start_time'] = microtime(1); //RunTime
function RunTime(){
	echo '<!--'.number_format(microtime(1) - $_SERVER['_start_time'], 6).'s-->';
}


function GetGuid(){
	$s=str_replace('.','',trim(uniqid('zbp',true),'zbp'));
	return $s;
}

function GetVars($name,$type='REQUEST'){
	if ($type=='ENV') {$array=&$_ENV;}
	if ($type=='GET') {$array=&$_GET;}
	if ($type=='POST') {$array=&$_POST;}
	if ($type=='COOKIE') {$array=&$_COOKIE;}
	if ($type=='REQUEST') {$array=&$_REQUEST;}
	if ($type=='SERVER') {$array=&$_SERVER;}
	if ($type=='SESSION') {$array=&$_SESSION;}
	if ($type=='FILES') {$array=&$_FILES;}

	if(isset($array[$name])){
		return $array[$name];
	}else{
		return '';
	}
}

function CreateDbName(){

	return 'zb_users/data/' . str_replace('-','','#%20' . strtolower(GetGuid())) . '.db';
}


function GetCurrentHost(&$cookiespath){
	if (array_key_exists('HTTPS',$_SERVER)) {
		if ($_SERVER['HTTPS']=='off') {
			$host='http://';
		} else {
			$host='https://';
		}
	} else {
		$host='http://';
	}


	$host.=$_SERVER['HTTP_HOST'];

	$y=strtolower($GLOBALS['blogpath']);
	$x=strtolower($_SERVER['SCRIPT_NAME']);

	for ($i=strlen($x); $i >0 ; $i--) { 
		$z=substr($x,0,$i);
		if(substr($y,strlen($y)-$i)==$z){
			break;
		}
	}

	$cookiespath=$z;

	return $host . $z;
}


function GetPassWordByGuid($ps,$guid){

	return md5(md5($ps).$guid);

}


function GetFilesInDir($dir,$type){

	$files=array();

	foreach (scandir($dir) as $f) {
		if (is_file($dir . $f)) {
			foreach (explode("|",$type) as $t) {
				$t='.' . $t;
				$i=strlen($t);
				if (substr($f,-$i,$i)==$t) {
					$sortname=substr($f,0,strlen($f)-$i);
					$files[$sortname]=$dir . $f;
					break;
				}
			}

		}
	}

	return $files;

}


function Redirect($url){
	header("HTTP/1.1 302 Found");
	header('Location: '.$url);
}

?>