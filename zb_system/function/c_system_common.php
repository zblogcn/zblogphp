<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


function GetGuid(){
	$s=trim(uniqid(),'{..}');
	return $s;
}

function CreateDbName(){

	return 'zb_users/data/' . str_replace('-','','#%20' . strtolower(GetGuid()));
}

function RunTime(){

	$endtime = microtime();
	$startime = explode(' ',$GLOBALS['startime']);
	//$endtime = explode(' ',$endtime);
	//$totaltime = $endtime[0]-$startime[0]+$endtime[1]-$startime[1];
	//$timecost = sprintf("%s",$totaltime);
	//return '<!--'.$timecost.'s-->';


	$endtime=microtime(true);
    $totaltime=$endtime-$startime;

    $str_total = var_export($totaltime, true);
    if(substr_count($str_total,"e")){
        $float_total = floatval(substr($str_total,5));
        $totaltime = $float_total/100000;
		return '<!--'.$totaltime.'s-->';
	}
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


	$host.=$_SERVER['HTTP_HOST'].'/';

	$a=$GLOBALS['blogpath'];
	$b=str_replace('\\','/',$_SERVER['DOCUMENT_ROOT']).'/';
	$c=str_replace($b,'',$a);
	$cookiespath=$c==''?'/':'/'.$c;

	return $host . $c;
}


?>