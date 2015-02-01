<?php
/**
 * 辅助通用函数
 * @package Z-BlogPHP
 * @subpackage System/CommonFunction 辅助通用函数
 * @copyright (C) RainbowSoft Studio
 */

/**
 * Operation System
 */
define('IS_WINDOWS', in_array(strtoupper(PHP_OS), array('WINNT', 'WIN32', 'WINDOWS')));
define('IS_UNIX', (strtoupper(PHP_OS) === 'UNIX'));
define('IS_LINUX', (strtoupper(PHP_OS) === 'LINUX'));
define('IS_DARWIN', (strtoupper(PHP_OS) === 'DARWIN'));
define('IS_CYGWIN', (strtoupper(substr(PHP_OS, 0, 6)) === 'CYGWIN'));
define('IS_BSD', in_array(strtoupper(PHP_OS), array('NETBSD', 'OPENBSD', 'FREEBSD')));

/**
 * Web Server
 */
define('IS_APACHE', preg_match("/apache/i", $_SERVER['SERVER_SOFTWARE']));
define('IS_IIS', preg_match("/microsoft-iis/i", $_SERVER['SERVER_SOFTWARE']));
define('IS_NGINX', preg_match("/nginx/i", $_SERVER['SERVER_SOFTWARE']));
define('IS_LIGHTTPD', preg_match("/lighttpd/i", $_SERVER['SERVER_SOFTWARE']));
define('IS_KANGLE', preg_match("/kangle/i", $_SERVER['SERVER_SOFTWARE']));

/**
 * 自动加载类文件
 * @api Filter_Plugin_Autoload
 * @param string $classname 类名
 * @return mixed
 */
function AutoloadClass($classname){
	foreach ($GLOBALS['Filter_Plugin_Autoload'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($classname);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
	}
	if (is_readable($f=ZBP_PATH . 'zb_system/function/lib/' . strtolower($classname) .'.php'))
		require $f;
}

/**
 * 记录日志
 * @param string $s
 * @param bool $iserror
 */
function Logs($s,$iserror=false) {
	global $zbp;
	foreach ($GLOBALS['Filter_Plugin_Logs'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($s,$iserror);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
	}
	if($zbp->guid){
		if($iserror)
			$f = $zbp->usersdir . 'logs/' . $zbp->guid . '-error' . date("Ymd") . '.txt';
		else
			$f = $zbp->usersdir . 'logs/' . $zbp->guid . '-log' . date("Ymd") . '.txt';
	}else{
		if($iserror)
			$f = $zbp->usersdir . 'logs/' . md5($zbp->path) . '-error.txt';
		else
			$f = $zbp->usersdir . 'logs/' . md5($zbp->path) . '.txt';
	}
	ZBlogException::SuspendErrorHook();
	if($handle = @fopen($f, 'a+')){
		$t=date('Y-m-d') . ' ' . date('H:i:s') . ' ' . substr(microtime(),1,9) . ' ' . date('P');
		@fwrite($handle, '[' . $t . ']' . "\r\n" . $s . "\r\n");
		@fclose($handle);
	}
	ZBlogException::ResumeErrorHook();
}

/**
 * 页面运行时长
 * @return array
 */
function RunTime() {
	global $zbp;

	$rt=array();
	$rt['time']=number_format(1000 * (microtime(1) - $_SERVER['_start_time']), 2);
	$rt['query']=$_SERVER['_query_count'];
	$rt['memory']=$_SERVER['_memory_usage'];
	$rt['error']=$_SERVER['_error_count'];
	if(function_exists('memory_get_usage')){
		$rt['memory']=(int)((memory_get_usage()-$_SERVER['_memory_usage'])/1024);
	}

	if(isset($zbp->option['ZC_RUNINFO_DISPLAY'])&&$zbp->option['ZC_RUNINFO_DISPLAY']==false){
		$_SERVER['_runtime_result']=$rt;
		return $rt;
	}

	echo '<!--' . $rt['time'] . ' ms , ';
	echo  $rt['query'] . ' query';
	if(function_exists('memory_get_usage'))
		echo ' , ' . $rt['memory'] . 'kb memory';
	echo  ' , ' . $rt['error'] . ' error';
	echo '-->';
	return $rt;
}

/**
 * 获得系统信息
 * @return string 系统信息
 * @since 1.4
 */
function GetEnvironment(){
	global $zbp;
	$ajax = Network::Create();
	if ($ajax) $ajax = substr(get_class($ajax), 7);

	$system_environment = PHP_OS . '; ' .
							GetValueInArray(
								explode(' ',str_replace(array('Microsoft-','/'), array('',''), GetVars('SERVER_SOFTWARE', 'SERVER'))), 0
							) . '; ' .
							'PHP ' . phpversion() . '; ' . $zbp->option['ZC_DATABASE_TYPE'] . '; ' . 	$ajax ;
	return $system_environment;
}

/**
 * 通过文件获取应用URL地址
 * @param string $file 文件名
 * @return string 返回URL地址
 */
function plugin_dir_url($file) {
	global $zbp;
	$s1=$zbp->path;
	$s2=str_replace('\\','/',dirname($file).'/');
	$s3='';
	$s=substr($s2,strspn($s1,$s2,0));
	if(strpos($s,'zb_users/plugin/')!==false){
		$s=substr($s,strspn($s,$s3='zb_users/plugin/',0));
	}else{
		$s=substr($s,strspn($s,$s3='zb_users/theme/',0));
	}
	$a=explode('/',$s);
	$s=$a[0];
	$s=$zbp->host . $s3 . $s . '/';
	return $s;
}

/**
 * 通过文件获取应用目录路径
 * @param $file
 * @return string
 */
function plugin_dir_path($file) {
	global $zbp;
	$s1=$zbp->path;
	$s2=str_replace('\\','/',dirname($file).'/');
	$s3='';
	$s=substr($s2,strspn($s1,$s2,0));
	if(strpos($s,'zb_users/plugin/')!==false){
		$s=substr($s,strspn($s,$s3='zb_users/plugin/',0));
	}else{
		$s=substr($s,strspn($s,$s3='zb_users/theme/',0));
	}
	$a=explode('/',$s);
	$s=$a[0];
	$s=$zbp->path . $s3 . $s . '/';
	return $s;
}

/**
 * 通过Key从数组获取数据
 * @param string $array 数组名
 * @param string $name 下标key
 * @return mixed
 */
function GetValueInArray($array, $name) {
	if (is_array($array)) {
		if (array_key_exists($name, $array)) {
			return $array[$name];
		}
	}
}

/**
 * 获取数组中的当前元素数据
 * @param string $array 数组名
 * @param string $name 下标key
 * @return mixed
 */
function GetValueInArrayByCurrent($array, $name) {
	if (is_array($array)) {
		$array = current($array);

		return GetValueInArray($array, $name);
	}
}

/**
 * 获取Guid
 * @return string
 */
function GetGuid() {
	$s = str_replace('.', '', trim(uniqid('zbp', true), 'zbp'));

	return $s;
}

/**
 * 获取参数值
 * @param string $name 数组key名
 * @param string $type 默认为REQUEST
 * @return mixed|null
 */
function GetVars($name, $type = 'REQUEST') {
	$array = &$GLOBALS[strtoupper("_$type")];

	if (isset($array[$name])) {
		return $array[$name];
	} else {
		return null;
	}
}

/**
 * 获取参数值（可设置默认返回值）
 * @param string $name 数组key名
 * @param string $type 默认为REQUEST
 * @param string $default 默认为null
 * @return mixed|null
 * @since 1.3.140614
 */
function GetVarsByDefault($name, $type = 'REQUEST',$default = null) {
	$g = GetVars($name, $type);
	if($g==null||$g==''){
		return $default;
	}
	return $g;
}

/**
 * 获取数据库名
 * @return string  返回一个随机的SQLite数据文件名
 */
function GetDbName() {

	return str_replace('-', '', '#%20' . strtolower(GetGuid())) . '.db';
}

/**
 * 获取当前网站地址
 * @param string $blogpath 网站域名
 * @param string &$cookiespath 返回cookie作用域值，要传引入
 * @return string  返回网站完整地址，如http://localhost/zbp/
 */
function GetCurrentHost($blogpath,&$cookiespath) {

	$host='';
	if (array_key_exists('REQUEST_SCHEME', $_SERVER)) {
		if ($_SERVER['REQUEST_SCHEME'] == 'https') {
			$host = 'https://';
		} else {
			$host = 'http://';
		}
	}elseif (array_key_exists('HTTPS', $_SERVER)) {
		if ($_SERVER['HTTPS'] == 'off') {
			$host = 'http://';
		} else {
			$host = 'https://';
		}
	} else {
		$host = 'http://';
	}

	$host .= $_SERVER['HTTP_HOST'];

	$x = $_SERVER['SCRIPT_NAME'];
	$y = $blogpath;
	if(isset($_SERVER["CONTEXT_DOCUMENT_ROOT"]) && isset($_SERVER["CONTEXT_PREFIX"]))
		if($_SERVER["CONTEXT_DOCUMENT_ROOT"] && $_SERVER["CONTEXT_PREFIX"]){
			$y= $_SERVER["CONTEXT_DOCUMENT_ROOT"] . $_SERVER["CONTEXT_PREFIX"] . '/';
		}
	$z = '';

	for ($i = strlen($x); $i > 0; $i--) {
		$z = substr($x, 0, $i);
		if (strtolower(substr($y, strlen($y) - $i)) == strtolower($z)) {
			break;
		}
	}

	$cookiespath = $z;

	return $host . $z;
}

/**
 * 通过URL获取远程页面内容
 * @param string $url URL地址
 * @return string  返回页面文本内容，默认为null
 */
function GetHttpContent($url) {

	if(class_exists('Network')){
		$ajax = Network::Create();
		if(!$ajax) return null;

		$ajax->open('GET',$url);
		$ajax->enableGzip();
		$ajax->setTimeOuts(60,60,0,0);
		$ajax->send();

		if($ajax->status==200){
			return $ajax->responseText;
		}else{
			return null;
		}
	}

	$r = null;
	if (function_exists("curl_init") && function_exists('curl_exec')) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if(ini_get("safe_mode")==false && ini_get("open_basedir")==false){
			curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		}
		if(extension_loaded('zlib')){
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		}
		$r = curl_exec($ch);
		$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($code==200)return $r;
	} elseif (ini_get("allow_url_fopen")) {
		ZBlogException::SuspendErrorHook();
		$http_response_header = null;
		$r = file_get_contents((extension_loaded('zlib')?'compress.zlib://':'') . $url);
		if(is_array($http_response_header) && in_array('HTTP/1.1 200 OK',$http_response_header)){
			if($r!==false)return $r;
		}
		ZBlogException::ResumeErrorHook();
	}

	return null;
}

/**
 * 获取目录下文件夹列表
 * @param string $dir 目录
 * @return array 文件夹列表
 */
function GetDirsInDir($dir) {
	$dirs = array();

	if (function_exists('scandir')) {
		foreach (scandir($dir,0) as $d) {
			if (is_dir($dir . $d)) {
				if (($d <> '.') && ($d <> '..')) {
					$dirs[] = $d;
				}
			}
		}
	} else {
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (is_dir($dir . $file)) {
						$dirs[] = $file;
					}
				}
			}
			closedir($handle);
		}
	}

	return $dirs;
}

/**
 * 获取目录下指定类型文件列表
 * @param string $dir 目录
 * @param string $type 文件类型，以｜分隔
 * @return array 文件列表
 */
function GetFilesInDir($dir, $type) {

	$files = array();

	if (!file_exists($dir))
		return $files;

	if (function_exists('scandir')) {
		foreach (scandir($dir) as $f) {
			if (is_file($dir . $f)) {
				foreach (explode("|", $type) as $t) {
					$t = '.' . $t;
					$i = strlen($t);
					if (substr($f, -$i, $i) == $t) {
						$sortname = substr($f, 0, strlen($f) - $i);
						$files[$sortname] = $dir . $f;
						break;
					}
				}

			}
		}
	} else {
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if (is_file($dir . $file)) {
						foreach (explode("|", $type) as $t) {
							$t = '.' . $t;
							$i = strlen($t);
							if (substr($file, -$i, $i) == $t) {
								$sortname = substr($file, 0, strlen($file) - $i);
								$files[$sortname] = $dir . $file;
								break;
							}
						}
					}
				}
			}
			closedir($handle);
		}
	}

	return $files;

}

/**
 * 设置http状态头
 * @param int $number HttpStatus
 * @internal param string $status 成功获取状态码设置静态参数status
 * @return bool
 */
function SetHttpStatusCode($number) {
	static $status = '';
	if ($status != '')
		return false;

	$codes = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',

		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',

		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',  // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
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
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
	);

	if(isset($codes[$number])) {
		header('HTTP/1.1 '.$number.' '.$codes[$number]);
		$status = $number;
		return true;
	}

}

/**
 * 302跳转
 * @param string $url 跳转链接
*/
function Redirect($url) {
	SetHttpStatusCode(302);
	header('Location: ' . $url);
	die();
}

/**
 * 301跳转
 * @param string $url 跳转链接
 */
function Redirect301($url) {
	SetHttpStatusCode(301);
	header('Location: ' . $url);
	die();
}
 /**
  * @ignore
  */
function Http404() {
	SetHttpStatusCode(404);
	header("Status: 404 Not Found");
}
 /**
  * @ignore
  */
function Http500() {
	SetHttpStatusCode(500);
}
 /**
  * @ignore
  */
function Http503() {
	SetHttpStatusCode(503);
}

/**
 * 设置304缓存头
 * @param string $filename 文件名
 * @param string $time 缓存时间
 */
function Http304($filename, $time) {
	$url = $filename;
	$md5 = md5($url . $time);
	$etag = '"' . $md5 . '"';
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
	header("ETag: $etag");
	if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)) {
		SetHttpStatusCode(304);
		die();
	}
}

/**
 * 获取客户端IP
 * @return string  返回IP地址
 */
function GetGuestIP() {
	return $_SERVER["REMOTE_ADDR"];
}

/**
 * 获取客户端Agent
 * @return string  返回Agent
 */
function GetGuestAgent() {
	return $_SERVER["HTTP_USER_AGENT"];
}

/**
 * 获取请求来源URL
 * @return string  返回URL
 */
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

/**
 * 获取文件后缀名
 * @param string $f 文件名
 * @return string  返回小写的后缀名
*/
function GetFileExt($f) {
	if (strpos($f, '.') === false)
		return '';
	$a = explode('.', $f);

	return strtolower(end($a));
}

/**
 * 获取文件权限
 * @param string $f 文件名
 * @return string|null  返回文件权限，数值格式，如0644
*/
function GetFilePermsOct($f) {
	if (!file_exists($f)) {
		return null;
	}

	return substr(sprintf('%o', fileperms($f)), -4);
}

/**
 * 获取文件权限
 * @param string $f 文件名
 * @return string|null  返回文件权限，字符表达格式，如-rw-r--r--
*/
function GetFilePerms($f) {

	if (!file_exists($f)) {
		return null;
	}

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
	$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));

	// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));

	// Other
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

	return $info;
}

/**
 * 向字符串型的参数表加入一个新参数
 * @param string $s 字符串型的参数表，以|符号分隔
 * @param string $name 参数名
 * @return string  返回新字符串，以|符号分隔
*/
function AddNameInString($s, $name) {
	$pl = $s;
	$name = (string)$name;
	$apl = explode('|', $pl);
	if (in_array($name, $apl) == false) {
		$apl[] = $name;
	}
	$pl = trim(implode('|', $apl), '|');

	return $pl;
}

/**
 * 从字符串型的参数表中删除一个参数
 * @param string $s 字符串型的参数表，以|符号分隔
 * @param string $name 参数名
 * @return string  返回新字符串，以|符号分隔
*/
function DelNameInString($s, $name) {
	$pl = $s;
	$name = (string)$name;
	$apl = explode('|', $pl);
	for ($i = 0; $i <= Count($apl) - 1; $i++) {
		if ($apl[$i] == $name) {
			unset($apl[$i]);
		}
	}
	$pl = trim(implode('|', $apl), '|');

	return $pl;
}

/**
 * 在字符串参数值查找参数
 * @param string $s 字符串型的参数表，以|符号分隔
 * @param string $name 参数名
 * @return bool
*/
function HasNameInString($s, $name) {
	$pl = $s;
	$name = (string)$name;
	$apl = explode('|', $pl);

	return in_array($name, $apl);
}


/**
 *  XML-RPC应答错误页面
 * @param string $faultString 错误提示字符串
 * @return void
*/
function RespondError($faultString) {

	$strXML = '<?xml version="1.0" encoding="UTF-8"?><methodResponse><fault><value><struct><member><name>faultCode</name><value><int>$1</int></value></member><member><name>faultString</name><value><string>$2</string></value></member></struct></value></fault></methodResponse>';
	$faultCode = time();
	$strError = $strXML;
	$strError = str_replace("$1", TransferHTML($faultCode, "[html-format]"), $strError);
	$strError = str_replace("$2", TransferHTML($faultString, "[html-format]"), $strError);

	ob_clean();
	echo $strError;
	die();

}

/**
 *  XML-RPC脚本错误页面
 * @param string $faultString 错误提示字符串
 * @return void
*/
function ScriptError($faultString) {
	header('Content-type: application/x-javascript; Charset=utf-8');
	ob_clean();
	echo 'alert("' . str_replace('"', '\"', $faultString) . '")';
	die();
}

/**
 *  验证字符串是否符合正则表达式
 * @param string $source 字符串
 * @param string $para 正则表达式，可用[username]|[password]|[email]|[homepage]或自定义表达式
 * @return bool
*/
function CheckRegExp($source, $para) {
	if (strpos($para, '[username]') !== false) {
		$para = "/^[\.\_A-Za-z0-9·\x{4e00}-\x{9fa5}]+$/u";
	}
	elseif (strpos($para, '[password]') !== false) {
		$para = "/^[A-Za-z0-9`~!@#\$%\^&\*\-_]+$/u";
	}
	elseif (strpos($para, '[email]') !== false) {
		$para = "/^([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*\.)+[a-zA-Z]*)$/u";
	}
	elseif (strpos($para, '[homepage]') !== false) {
		$para = "/^[a-zA-Z]+:\/\/[a-zA-Z0-9\_\-\.\&\?\/:=#\x{4e00}-\x{9fa5}]+$/u";
	}
	elseif (!$para)
		return false;

	return (bool)preg_match($para, $source);
}

/**
 *  通过正则表达式格式化字符串
 * @param string $source 字符串
 * @param string $para 正则表达式，可用[html-format]|[nohtml]|[noscript]|[enter]|[noenter]|[filename]|[normalname]或自定义表达式
 * @return string
*/
function TransferHTML($source, $para) {

	if (strpos($para, '[html-format]') !== false) {
		$source = htmlspecialchars($source);
	}

	if (strpos($para, '[nohtml]') !== false) {
		$source = preg_replace("/<([^<>]*)>/si", "", $source);
		$source = str_replace("<", "˂", $source);
		$source = str_replace(">", "˃", $source);
	}

	if (strpos($para, '[noscript]') !== false) {
		$source = preg_replace("/<(script.*?)>(.*?)<(\/script.*?)>/si", "", $source);
		$source = preg_replace("/<(\/?script.*?)>/si", "", $source);
		$source = preg_replace("/javascript/si", "", $source);
		$source = preg_replace("/vbscript/si", "", $source);
		$source = preg_replace("/on([a-z]+)\s*=/si", "on\\=", $source);
	}
	if (strpos($para, '[enter]') !== false) {
		$source = str_replace("\r\n", "<br/>", $source);
		$source = str_replace("\n", "<br/>", $source);
		$source = str_replace("\r", "<br/>", $source);
		$source = preg_replace("/(<br\/>)+/", "<br/>", $source);
	}
	if (strpos($para, '[noenter]') !== false) {
		$source = str_replace("\r\n", "", $source);
		$source = str_replace("\n", "", $source);
		$source = str_replace("\r", "", $source);
	}
	if (strpos($para, '[filename]') !== false) {
		$source = str_replace(array("/", "#", "$", "\\", ":", "?", "*", "\"", "<", ">", "|", " "), array(""), $source);
	}
	if (strpos($para, '[normalname]') !== false) {
		$source = str_replace(array("#", "$", "(", ")", "*", "+", "[", "]", "{", "}", "?", "\\", "^", "|", ":", "'", "\"", ";", "@", "~", "=", "%", "&"), array(""), $source);
	}

	return $source;
}

/**
 *  封装HTML标签
 * @param string $html html源码
 * @return string
*/
function CloseTags($html) {

	// strip fraction of open or close tag from end (e.g. if we take first x characters, we might cut off a tag at the end!)
	$html = preg_replace('/<[^>]*$/', '', $html); // ending with fraction of open tag

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
	$sc = array('br', 'input', 'img', 'hr', 'meta', 'link');
	// ,'frame','iframe','param','area','base','basefont','col'
	// should not skip tags that can have content inside!

	for ($i = 0; $i < $len_opened; $i++) {
		$ot = strtolower($opentags[$i]);

		if (!in_array($opentags[$i], $closetags) && !in_array($ot, $sc)) {
			$html .= '</' . $opentags[$i] . '>';
		} else {
			unset($closetags[array_search($opentags[$i], $closetags)]);
		}
	}

	return $html;

}

/**
 *  获取UTF8格式的字符串的子串
 * @param string $sourcestr 源字符串
 * @param int $start 起始位置
 * @param int $cutlength 子串长度
 * @return string
*/
function SubStrUTF8_Start($sourcestr, $start, $cutlength) {
	if( function_exists('mb_substr') && function_exists('mb_internal_encoding') ){
		mb_internal_encoding('UTF-8');
		return mb_substr($sourcestr, $start, $cutlength);
	}

	if( function_exists('iconv_substr') && function_exists('iconv_set_encoding') ){
		iconv_set_encoding ( "internal_encoding" ,  "UTF-8" );
		iconv_set_encoding ( "output_encoding" ,  "UTF-8" );
		return iconv_substr($sourcestr, $start, $cutlength);
	}

	return substr($sourcestr, $start, $cutlength);
}

/**
 *  获取UTF8格式的字符串的子串
 * @param string $sourcestr 源字符串
 * @param int $cutlength 子串长度
 * @return string
*/
function SubStrUTF8($sourcestr, $cutlength) {

	if( function_exists('mb_substr') && function_exists('mb_internal_encoding') ){
		mb_internal_encoding('UTF-8');
		return mb_substr($sourcestr, 0, $cutlength);
	}

	if( function_exists('iconv_substr') && function_exists('iconv_set_encoding') ){
		iconv_set_encoding ( "internal_encoding" ,  "UTF-8" );
		iconv_set_encoding ( "output_encoding" ,  "UTF-8" );
		return iconv_substr($sourcestr, 0, $cutlength);
	}

	$returnstr = '';
	$i = 0;
	$n = 0;

	$str_length = strlen($sourcestr); //字符串的字节数

	while (($n < $cutlength) and ($i <= $str_length)) {
		$temp_str = substr($sourcestr, $i, 1);
		$ascnum = Ord($temp_str); //得到字符串中第$i位字符的ascii码
		if ($ascnum >= 224) //如果ASCII位高与224，
		{

			$returnstr = $returnstr . substr($sourcestr, $i, 3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
			$i = $i + 3; //实际Byte计为3
			$n++; //字串长度计1
		} elseif ($ascnum >= 192) //如果ASCII位高与192，
		{
			$returnstr = $returnstr . substr($sourcestr, $i, 2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
			$i = $i + 2; //实际Byte计为2
			$n++; //字串长度计1
		} elseif ($ascnum >= 65 && $ascnum <= 90) //如果是大写字母，
		{

			$returnstr = $returnstr . substr($sourcestr, $i, 1);
			$i = $i + 1; //实际的Byte数仍计1个
			$n++; //但考虑整体美观，大写字母计成一个高位字符

		} else //其他情况下，包括小写字母和半角标点符号，
		{

			$returnstr = $returnstr . substr($sourcestr, $i, 1);
			$i = $i + 1; //实际的Byte数计1个
			$n = $n + 0.5; //小写字母和半角标点等与半个高位字符宽...

		}
	}
	if ($str_length > $cutlength) {
		$returnstr = $returnstr;
	}

	return $returnstr;

}

/**
 *  删除文件BOM头
 * @param string $s 文件内容
 * @return string
*/
function RemoveBOM($s){
	$charset=array();
	$charset[1] = substr($s, 0, 1);
	$charset[2] = substr($s, 1, 1);
	$charset[3] = substr($s, 2, 1);
	if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
		$s = substr($s, 3);
	}
	return $s;
}

/**
 * 获取指定时区名
 * @param int $z 时区号
 * @return string 时区名
 * @since 1.3.140614
 */
function GetTimeZonebyGMT($z){
	$timezones = array(
		-12 => 'Etc/GMT+12',
		-11 => 'Pacific/Midway',
		-10 => 'Pacific/Honolulu',
		-9 => 'America/Anchorage',
		-8 => 'America/Los_Angeles',
		-7 => 'America/Denver',
		-6 => 'America/Tegucigalpa',
		-5 => 'America/New_York',
		-4 => 'America/Halifax',
		-3 => 'America/Argentina/Buenos_Aires',
		-2 => 'Atlantic/South_Georgia',
		-1 => 'Atlantic/Azores',
		0 => 'UTC',
		1 => 'Europe/Berlin',
		2 => 'Europe/Sofia',
		3 => 'Africa/Nairobi',
		4 => 'Europe/Moscow',
		5 => 'Asia/Karachi',
		6 => 'Asia/Dhaka',
		7 => 'Asia/Bangkok',
		8 => 'Asia/Shanghai',
		9 => 'Asia/Tokyo',
		10 => 'Pacific/Guam',
		11 => 'Australia/Sydney',
		12 => 'Pacific/Fiji',
		13 => 'Pacific/Tongatapu',
	);
	if(!isset($timezones[$z]))return 'UTC';
	return $timezones[$z];
}

/**
 * 对数组内的字符串进行htmlspecialchars
 * @param array $array 待过滤字符串
 * @return array
 * @since 1.4
 */
function htmlspecialchars_array($array) {

	foreach ($array as $key => &$value) {
		if (is_array($value)) {
			$value = htmlspecialchars_array($value);
		}
		else if (is_string($value)) {
			$value = htmlspecialchars($value);
		}
	}

	return $array;

}

/**
 * 获得一个只含数字字母和_线的string
 * @param string $s 待过滤字符串
 * @return s
 * @since 1.4
 */
function FilterCorrectName($s) {
	return preg_replace('|[^0-9a-zA-Z_]|','',$s);
}
