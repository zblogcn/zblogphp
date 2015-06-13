<?php
#注册插件
RegisterPlugin("utf84mb", "ActivePlugin_utf84mb");

function ActivePlugin_utf84mb() {
	Add_Filter_Plugin('Filter_Plugin_DbSql_Filter', 'utf84mb_filter');
	Add_Filter_Plugin('Filter_Plugin_Edit_Begin', 'utf84mb_fixHtmlSpecialChars');
}
function InstallPlugin_utf84mb() {}
function UninstallPlugin_utf84mb() {}

function utf84mb_filter(&$sql) {
	$sql = preg_replace_callback("/[\x{10000}-\x{10FFFF}]/u", 'utf84mb_convertToUCS4', $sql);
}

function utf84mb_fixHtmlSpecialChars() {
	global $article;
	$article->Content = preg_replace_callback("/\&\#x([0-9A-Z]{2,6})\;/u", 'utf84mb_convertToUTF8', $article->Content);
	$article->Intro = preg_replace_callback("/\&\#x([0-9A-Z]{2,6})\;/u", 'utf84mb_convertToUTF8', $article->Intro);
}

// These code is used for fuck PHP 5.3 and lower.
// I love callback!

function utf84mb_convertToUCS4($matches) {
	return sprintf("&#x%s;", ltrim(strtoupper(bin2hex(iconv('UTF-8', 'UCS-4', $matches[0]))), "0"));
}

function utf84mb_convertToUTF8($matches) {
	return iconv('UCS-4', 'UTF-8', hex2bin(str_pad($matches[1], 8, "0", STR_PAD_LEFT)));
}

if (!function_exists('hex2bin')) {
	function hex2bin($str) {
		$sbin = "";
		$len = strlen($str);
		for ($i = 0; $i < $len; $i += 2) {
			$sbin .= pack("H*", substr($str, $i, 2));
		}
		return $sbin;
	}
}