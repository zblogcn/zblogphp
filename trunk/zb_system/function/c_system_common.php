<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

$_SERVER['_start_time'] = microtime(1); //RunTime
function RunTime(){
	echo '<!--'. (1000 * number_format(microtime(1) - $_SERVER['_start_time'], 6)) .'ms-->';
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
		return null;
	}
}

function GetDbName(){

	return str_replace('-','','#%20' . strtolower(GetGuid())) . '.db';
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

function GetDirsInDir($dir){
	$dirs=array();

	foreach (scandir($dir) as $d) {
		if (is_dir($dir .  $d)) {
			if( ($d<>'.') && ($d<>'..') ){
				$dirs[]=$d;
			}
		}
	}

	return $dirs;

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
	die();
}


function Http404(){
	header('HTTP/1.1 404 Not Found');
	header("Status: 404 Not Found");
	die();
}


function Http304($filename,$time){
	$url = $filename;
	$md5 = md5($url . $time);
	$etag = '"' . $md5 . '"';
	header('Last-Modified: '.gmdate('D, d M Y H:i:s',$time ).' GMT');
	header("ETag: $etag");
	if((isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)){
		header("HTTP/1.1 304 Not Modified"); 
		die();
	}
}

function Logs($s){
	$f=$GLOBALS['usersdir'] . 'logs/'. $GLOBALS['option']['ZC_BLOG_CLSID'] .'-log' . date("Ymd"). '.txt';
	$handle = @fopen($f, 'a+');
	@fwrite($handle,"[" . date('c') . "~" . current(explode(" ", microtime()))  . "]" . $s . "\r\n");
	@fclose($handle);	
}

function GetGuestIP(){
	return $_SERVER["REMOTE_ADDR"];
}

function GetGuestAgent(){
	return $_SERVER["HTTP_USER_AGENT"];
}

function GetValueInArray($array,$name){
	if(is_array($array)){
		if(array_key_exists($name,$array)){
			return $array[$name];
		}
	}
}

function GetFilePermsOct($f){
    if(!file_exists($f)){return null;}
    return substr(sprintf('%o', fileperms($f)), -4);
}


function GetFilePerms($f){

    if(!file_exists($f)){return null;}

    $perms = fileperms($f);

    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } else {
        // Unknown
        $info = 'u';
    }
    
    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
                (($perms & 0x0800) ? 's' : 'x' ) :
                (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
                (($perms & 0x0400) ? 's' : 'x' ) :
                (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
                (($perms & 0x0200) ? 't' : 'x' ) :
                (($perms & 0x0200) ? 'T' : '-'));
    
    return $info;
}


function AddNameInString($s,$name){
	$pl=$s;
	$apl=explode('|',$pl);
	if(in_array($name,$apl)==false){
		$apl[]=$name;
	}
	$pl=trim(implode('|',$apl),'|');
	return $pl;
}

function DelNameInString($s,$name){
	$pl=$s;
	$apl=explode('|',$pl);
	for ($i=0; $i <= Count($apl)-1; $i++) { 
		if($apl[$i]==$name){
			unset($apl[$i]);
		}
	}
	$pl=trim(implode('|',$apl),'|');
	return $pl;
}

function HasNameInString($s,$name){
	$pl=$s;
	$apl=explode('|',$pl);
	return in_array($name,$apl);
}






#*********************************************************
# 目的：    XML-RPC显示错误页面
#'*********************************************************
function RespondError($faultString){

	$strXML='<?xml version="1.0" encoding="UTF-8"?><methodResponse><fault><value><struct><member><name>faultCode</name><value><int>$1</int></value></member><member><name>faultString</name><value><string>$2</string></value></member></struct></value></fault></methodResponse>';
	$faultCode=time();
	$strError=$strXML;
	$strError=str_replace("$1",TransferHTML($faultCode,"[html-format]"),$strError);
	$strError=str_replace("$2",TransferHTML($faultString,"[html-format]"),$strError);

	ob_clean();
	echo $strError;
	die();

}


function TransferHTML(&$source,$para){
	return $source;
}


function CloseTags($html){

	// strip fraction of open or close tag from end (e.g. if we take first x characters, we might cut off a tag at the end!)
	$html = preg_replace('/<[^>]*$/','',$html); // ending with fraction of open tag

	// put open tags into an array
	preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
	$opentags = $result[1];


	// put all closed tags into an array
	preg_match_all('#</([a-z]+)>#iU', $html, $result);
	$closetags = $result[1];

	$len_opened = count($opentags);

	// if all tags are closed, we can return
	if (count($closetags) == $len_opened) {
		return $html;
	}

	// close tags in reverse order that they were opened
	$opentags = array_reverse($opentags);

	// self closing tags
	$sc = array('br','input','img','hr','meta','link');
	// ,'frame','iframe','param','area','base','basefont','col'
	// should not skip tags that can have content inside!

	for ($i=0; $i < $len_opened; $i++)
	{
		$ot = strtolower($opentags[$i]);

		if (!in_array($opentags[$i], $closetags) && !in_array($ot,$sc)){
			$html .= '</'.$opentags[$i].'>';
		}else{
			unset($closetags[array_search($opentags[$i], $closetags)]);
		}
	}

	return $html;

}


?>
