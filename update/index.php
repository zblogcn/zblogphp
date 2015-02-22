<?php
if($_SERVER['QUERY_STRING']=='install'){
	header("Content-type:text/xml; Charset=utf-8");
	echo file_get_contents(__DIR__ . '/Release.xml');
}elseif($_SERVER['QUERY_STRING']=='beta'){
	header('Content-Type: text/plain; charset=utf-8');
	echo file_get_contents(__DIR__ . '/beta.html');
}elseif($_SERVER['QUERY_STRING']!=''){
	$s=__DIR__ . '/' . str_replace('\\','/',$_SERVER['QUERY_STRING']);
	if(is_readable($s) && strpos($s,'./')===false){
		header('Content-Type: application/octet-stream');
		echo file_get_contents($s);
	}
}else{
	header('Content-Type: text/plain; charset=utf-8');
	echo file_get_contents(__DIR__ . '/now.html');
}