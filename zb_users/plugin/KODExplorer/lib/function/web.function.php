<?php
/**
 * 获取客户端IP地址
 * 
 * @param boolean $s_type ip类型[ip|long]
 * @return string $ip
 */
function get_client_ip($b_ip = true){
	if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip = 'unknown';
	} 
	$ip = trim($ip, ':f');
	return $b_ip ? $ip : ip2long($ip);
} 

// url检查
function check_url($url){
	$array = get_headers($url, 1);
	if (preg_match('/404/', $array[0])) {
		return false;
	} elseif (preg_match('/403/', $array[0])) {
		return false;
	} else {
		return true;
	} 
} 

/**
 * 获取网络url文件内容，加入ua，以解决防采集的站
 */
function curl_get_contents($url){
	$ch = curl_init();
	$timeout = 5;
	$user_agent = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; WOW64; Trident/4.0; SLCC1)";
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	curl_close($ch);
	return $file_contents;
}

// 返回refer URL 地址
function refer_url(){
	return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
} 
// 返回当前页面的 URL 地址
function this_url(){
	$s_url = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] ? 'https' : 'http';
	$s_url .= '://';
	return $s_url . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
} 

function select_var($array){
	if (!is_array($array)) return -1;
	ksort($array);
	$chosen = -1;
	foreach ($array as $k => $v) {
		if (isset($v)) {
			$chosen = $v;
			break;
		} 
	} 
	return $chosen;
}


function stripslashes_deep($value){ 
	$value = is_array($value) ? array_map('stripslashes_deep', $value) : (isset($value) ? stripslashes($value) : null); 
	return $value; 
}

/**
 * GET/POST数据统一入口
 * 将GET和POST的数据进行过滤，去掉非法字符以及hacker code，返回一个数组
 * 注意如果GET和POST有相同的Key，POST优先
 * 
 * @return array $_GET和$_POST数据过滤处理后的值
 */
function parse_incoming(){
	global $_GET, $_POST,$_COOKIE;

	$_COOKIE = stripslashes_deep($_COOKIE);
	$_GET	 = stripslashes_deep($_GET);
	$_POST	 = stripslashes_deep($_POST);
	$return = array();
	$return = array_merge($_GET,$_POST);	
	$remote = array_get($return,0);
	$remote = explode('/',trim($remote[0],'/'));
	$return['URLremote'] = $remote;
	return $return;
} 

function url2absolute($index_url, $preg_url){
	if (preg_match('/[a-zA-Z]*\:\/\//', $preg_url)) return $preg_url;
	preg_match('/([a-zA-Z]*\:\/\/.*)\//', $index_url, $match);
	$index_url_temp = $match[1];

	foreach(explode('/', $preg_url) as $key => $var) {
		if ($key == 0 && $var == '') {
			preg_match('/([a-zA-Z]*\:\/\/[^\/]*)\//', $index_url, $match);
			$index_url_temp = $match[1] . $preg_url;
			break;
		} 
		if ($var == '..') {
			preg_match('/([a-zA-Z]*\:\/\/.*)\//', $index_url_temp, $match);
			$index_url_temp = $match[1];
		} elseif ($var != '.') $index_url_temp .= '/' . $var;
	} 
	return $index_url_temp;
}

// 将字符串转换成URL的编码，gbk的和utf8的 $to="gbk" 或"utf8"
function urlcode($str, $to){
	if ($to == "gbk") {
		$result = RawUrlEncode($str); //gbk字符(主要是中文)转换为url %BA%EC形式
	} else {
		$key = mb_convert_encoding($str, "utf-8", "gbk"); //对于百度utf8中文url
		$result = urlencode($key);
	} 
	return $result;
} 

// 输出js
function exec_js($js){
	echo "<script language='JavaScript'>\n" . $js . "</script>\n";
} 
// 禁止缓存
function no_cache(){
	header("Pragma:no-cache\r\n");
	header("Cache-Control:no-cache\r\n");
	header("Expires:0\r\n");
} 
// 生成javascript转向
function go_url($url, $msg = ''){
	header("Content-type: text/html; charset=utf-8\r\n");
	echo "<script type='text/javascript'>\n";
	echo "window.location.href='$direction';";
	echo "</script>\n";
	exit;
} 

/**
 * 消息框。eg
 * msg("falied","/",10);
 * msg("ok");
 */
function show_msg($message, $url = '#', $time = 3, $isgo = 1)
{
	$goto = "content='$time;url=$url'";
	if ($isgo != "1") {
		$goto = "";
	} //是否自动跳转
	echo<<<END
<html>
	<meta http-equiv='refresh' $goto charset="utf-8">
	<style>
	#msgbox{width:400px;border: 1px solid #ddd;font-family:微软雅黑;color:888;font-size:13px;margin:0 auto;margin-top:150px;}
	#msgbox #title{background:#3F9AC6;color:#fff;line-height:30px;height:30px;padding-left:20px;font-weight:800;}
	#msgbox #message{text-align:center;padding:20px;}
	#msgbox #info{text-align:center;padding:5px;border-top:1px solid #ddd;background:#f2f2f2;color:#888;}
	</style>
	<body>
	<div id="msgbox">
	<div id="title">提示信息</div>
	<div id="message">$message</div>
	<div id="info">$time 秒后自动跳转，如不想等待可 <a href='$url'>点击这里</a></div></center>
	</body>
</html>
END;
	exit;
} 

function send_http_status($i_status, $s_message = ''){
	$a_status = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols', 
		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content', 
		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy', // 306 is deprecated but reserved
		307 => 'Temporary Redirect', 
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed', 
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		509 => 'Bandwidth Limit Exceeded'
		);

	if (array_key_exists($i_status, $a_status)) {
		header('HTTP/1.1 ' . $i_status . ' ' . $a_status[$i_status]);
	} 
	if ($s_message) {
		echo $s_message;
		exit();
	} 
} 

// 获取操作系统信息
function getOS (){
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$os = false;
	if (eregi('win', $agent) && strpos($agent, '95')) {
		$os = 'Windows 95';
	} else if (eregi('win 9x', $agent) && strpos($agent, '4.90')) {
		$os = 'Windows ME';
	} else if (eregi('win', $agent) && ereg('98', $agent)) {
		$os = 'Windows 98';
	} else if (eregi('win', $agent) && eregi('nt 5.1', $agent)) {
		$os = 'Windows XP';
	} else if (eregi('win', $agent) && eregi('nt 5', $agent)) {
		$os = 'Windows 2000';
	} else if (eregi('win', $agent) && eregi('nt', $agent)) {
		$os = 'Windows NT';
	} else if (eregi('win', $agent) && ereg('32', $agent)) {
		$os = 'Windows 32';
	} else if (eregi('linux', $agent)) {
		$os = 'Linux';
	} else if (eregi('unix', $agent)) {
		$os = 'Unix';
	} else if (eregi('sun', $agent) && eregi('os', $agent)) {
		$os = 'SunOS';
	} else if (eregi('ibm', $agent) && eregi('os', $agent)) {
		$os = 'IBM OS/2';
	} else if (eregi('Mac', $agent) && eregi('PC', $agent)) {
		$os = 'Macintosh';
	} else if (eregi('PowerPC', $agent)) {
		$os = 'PowerPC';
	} else if (eregi('AIX', $agent)) {
		$os = 'AIX';
	} else if (eregi('HPUX', $agent)) {
		$os = 'HPUX';
	} else if (eregi('NetBSD', $agent)) {
		$os = 'NetBSD';
	} else if (eregi('BSD', $agent)) {
		$os = 'BSD';
	} else if (ereg('OSF1', $agent)) {
		$os = 'OSF1';
	} else if (ereg('IRIX', $agent)) {
		$os = 'IRIX';
	} else if (eregi('FreeBSD', $agent)) {
		$os = 'FreeBSD';
	} else if (eregi('teleport', $agent)) {
		$os = 'teleport';
	} else if (eregi('flashget', $agent)) {
		$os = 'flashget';
	} else if (eregi('webzip', $agent)) {
		$os = 'webzip';
	} else if (eregi('offline', $agent)) {
		$os = 'offline';
	} else {
		$os = 'Unknown';
	} 
	return $os;
} 