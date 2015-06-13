<?php
#注册插件
RegisterPlugin("utf84mb", "ActivePlugin_utf84mb");

function ActivePlugin_utf84mb() {
	Add_Filter_Plugin('Filter_Plugin_DbSql_Filter', 'utf84mb_filter');
}
function InstallPlugin_utf84mb() {}
function UninstallPlugin_utf84mb() {}

function utf84mb_filter(&$sql) {
	$sql = preg_replace_callback("/[\x{10000}-\x{10FFFF}]/u", function ($matches) {
		return sprintf("&#x%s;", ltrim(strtoupper(bin2hex(iconv('UTF-8', 'UCS-4', $matches[0]))), "0"));
	}, $sql);
}