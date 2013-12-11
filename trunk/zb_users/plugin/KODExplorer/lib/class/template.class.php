<?php

/**
 * 模版引擎父类。
 * 模版引擎父类，用来声明所有子类所需要的固定变量。
 */
class Template {

	var $logger;							//日志
	var $template_dir		= 'templates';	//存放模版目录
	var $compile_dir		= 'templates_c';//存放模版临时编译文件目录
	var $compile_check		= true;			//是否检查模版临时编译文件
	var $force_compile		= false;		//是否强制编译
	var $caching			= 0;			//是否缓存
	var $cache_dir			= 'cache';		//存放缓存目录
	var $cache_life_time	= 2;			//缓存存活期
	var $client_caching		= false;		//是否采用客户端缓存
	var $lang_dir			= '';			//语言包路径
	var $global_lang_name	= 'global';		//全局语言变量名
	var $compile_lang		= false;		//是否加载语言包
	var $left_delimiter		= '<';			//模板标签左定界符
	var $right_delimiter	= '>';			//模板标签右定界符
	var $compilefile_prefix = '~';			//模版临时编译文件前缀
	var $tag_left_delim		= '[';			//模板变量左定界符
	var $tag_right_delim	= ']';			//模板变量右定界符
	var $parse_first_function	= array();	//模板预先解析函数
	var $parse_filter_function	= array();	//模板预后解析函数

	var $tpl_vars			= array();		//模板变量存放数组
	var $check_tpl_modify	= true;			//是否修复模板
	var $auto_repair		= false;		//是否自动修复
	var $source				= null;			//模板内容源取出函数
	var $compiler_file		= 'template.class.php';//编译器文件名
	var $compiler_class		= 'Template';	//编译器类名
	var $cache_expire_time;					//缓存过期时间

	var $parse_fun_array = array(//解析函数
		'parsePhp',
		'parseIf',
		'parseLoop',
		'parseTag',
		'parseTagFunc',
		'parseInclude',
		'parseIncludeSubtpl',
		'parseGet',
		"parseVar",
		"parseOp",
		"parseDebug",
		"parseHeader",
		'clearWhitespace', 
		// "clearComment",
	);

	private static $instance;
	public static function getInstance()
	{
		if (!self :: $instance) {
			self :: $instance = new self(); 
			self :: $instance -> template_dir	= TEMPLATE_DIR; //指定模板路径
			self :: $instance -> compile_dir	= COMPILE_DIR;	//指定中间文件路径
			self :: $instance -> is_cache		= true;			//是否开启缓存
			self :: $instance -> cache_dir		= CACHE_DIR;	//指定缓存目录
			self :: $instance -> client_caching = true;			//是否进行客户端缓存
			self :: $instance -> cache_life_time = CACHE_LIFE_TIME; //指定缓存过期时间
		}
		return self :: $instance;
	} 

	/**
	 * constructor
	 */
	private function __construct()
	{
		if(defined('CACHE_LIFE_TIME')){
			$cache_life_time = CACHE_LIFE_TIME;
		}
	} 

	/**
	 * assign a {@link Logger} object to the database
	 * 
	 * @see Logger
	 * @param object $logger reference to a {@link Logger} object
	 */
	function setLogger(&$logger)
	{
		$this -> logger = &$logger;
	} 
	/**
	 * 分配模板变量
	 * 
	 * @var $tpl_var,$value
	 */
	function assign($tpl_var, $value = null)
	{
		if (is_array($tpl_var)) {
			foreach ($tpl_var as $key => $val) {
				if ($key != '') {
					$this -> tpl_vars[$key] = $val;
				} 
			} 
		} else {
			if ($tpl_var != '') {
				$this -> tpl_vars[$tpl_var] = $value;
			} 
		} 
	} 

	/**
	 * 以引用方式分配模板变量
	 * 
	 * @var $tpl_var,&$value
	 */
	function assignByRef($tpl_var, &$value)
	{
		if ($tpl_var != '')

			$this -> tpl_vars[$tpl_var] = &$value;
	} 
	/**
	 * 清空所有模板变量
	 */
	function clearAllAssign()
	{
		$this -> tpl_vars = array();
	} 

	/**
	 * 注册模板预先解析函数
	 * 
	 * @var $function_name
	 */
	function registerFirstFunction($function_name)
	{
		$this -> parse_first_function[] = $function_name;
	} 
	/**
	 * 注册模板预先解析函数
	 * 
	 * @var $function_name
	 */
	function registerFilterFunction($function_name)
	{
		$this -> parse_filter_function[] = $function_name;
	} 
	/**
	 * 是否编译过
	 * 
	 * @var $function_name
	 */
	function isCompiled()
	{
		if (!file_exists($this -> compile_name)) return false;
		$expire = (filemtime($this -> compile_name) == filemtime($this -> template_name)) ? true : false;
		if ($expire) return true;
		else return false;
	} 

	/**
	 * 生成哈希数
	 * 
	 * @param string $str 
	 * @return int 
	 */
	function hash($str)
	{
		$hash = 0;
		$n = strlen($str);
		for ($i = 0; $i < $n; $i++) {
			$hash ^= (ord($str[$i]) << ($i &0x0f));
		} 
		return $hash % 701819;
	} 

	/**
	 * 得到缓存子目录
	 * 
	 * @param string $file_name 文件名
	 * @return string 
	 */
	function getCacheDir($file_name)
	{
		$hash = $this -> hash($file_name);
		$hash = sprintf('%06u', $hash);
		$dir_1 = substr($hash, -4, 2);
		$dir_2 = substr($hash, -2);
		$cache_dir = $this -> cache_dir . 'cache/' . $dir_1 . '/' . $dir_2 . '/';
		if (!is_dir($cache_dir)) {
			mkdir($cache_dir , 0755 , 1);
		}
		return $cache_dir;
	} 

	/**
	 * 是否生成过缓存
	 * 
	 * @var $file_name,$cache_id
	 */
	function isCached($file_name, $cache_id = null)
	{
		if (isset($this -> cached) && $this -> cached) return true;
		$this -> cache_name = $this -> getCacheDir($this -> template_dir . $file_name . $cache_id) 
		. md5($this -> template_dir . $file_name . $cache_id) . '.cache';

		if (!file_exists($this -> cache_name)) return false;
		if (!($mtime = filemtime($this -> cache_name))) return false;
		$this -> cache_expire_time = $mtime + $this -> cache_life_time - time();
		if (($mtime + $this -> cache_life_time) < time()) {
			unlink($this -> cache_name);
			return false;
		} else {
			$this -> cached = true;
			return true;
		} 
	} 
	/**
	 * 格式化编译文件名
	 * 
	 * @var $file_name
	 */
	function format($file_name)
	{
		return str_replace(array(TEMPLATE_DIR, DS, '..'), array('', '%', '#'), $file_name);
	} 
	/**
	 * 取出待编译内容
	 * 
	 * @var $file_name,$compile
	 */
	function fetch($file_name, $compile = 0)
	{
		$this -> template_name = $this -> template_dir . $file_name;
		$this -> compile_name = $this -> compile_dir . $this -> compilefile_prefix
		. $this -> format($this -> template_name);
		
		ob_start();
		if (!$this -> isCompiled()) {
			if ($this -> compile($this -> template_name)) {
				include($this -> compile_name);
			} 
		} else {
			include($this -> compile_name);
		}
		$contents = ob_get_contents();
		ob_end_clean();

		if ($compile) {
			$contents = $this -> compileOutput($contents);
		} 
		

		if (!empty($this -> parse_filter_function)) {
			
			foreach($this -> parse_filter_function as $var) {
				if (function_exists($var)) {
					$contents = $var($contents);
				} 
			} 
		}
		return $contents;
	} 

	/**
	 * 取出缓存文件
	 * 
	 * @var $file_name,$cache_id,$compile
	 */
	function fetchCache($file_name, $cache_id, $compile = 0)
	{
		$this -> cache_name = $this -> getCacheDir($this -> template_dir . $file_name . $cache_id) . md5($this -> template_dir . $file_name . $cache_id) . '.cache';

		if (file_exists($this -> cache_name) && $fp = @fopen($this -> cache_name, 'r')) {
			$contents = fread($fp, filesize($this -> cache_name));
			fclose($fp);
			return $contents;
		} else {
			$contents = $this -> fetch($file_name, $compile);

			if (!file_exists($this -> cache_name)) {
				touch($this -> cache_name);
			} 

			if ($fp = @fopen($this -> cache_name, 'w')) {
				fwrite($fp, $contents);
				fclose($fp);
			} else {
				die('Unable to write cache.');
			} 
			return $contents;
		} 
	} 

	/**
	 * 删除缓存文件
	 * 
	 * @var $file_name,$cache_id
	 */
	function clear_cache($file_name, $cache_id)
	{
		$this -> cache_name = $this -> getCacheDir($this -> template_dir . $file_name . $cache_id) 
		.md5($this -> template_dir . $file_name . $cache_id) . '.cache';
		return @unlink($this -> cache_name);
	} 

	/**
	 * 显示
	 * 
	 * @var $file_name 压缩技术
	 */
	function display($file_name)
	{
		if (ENABLE_GZIP) {
			$buffer = $this -> fetch($file_name);
			ob_start('ob_gzhandler');
			print $buffer;
		} else {
			print($this -> fetch($file_name));
		} 
	} 
	/**
	 * 显示缓存
	 * 
	 * @var $file_name,$cache_id 压缩技术
	 */
	function displayCache($file_name, $cache_id = null)
	{
		global $g_config;
		if ($this -> client_caching && $g_config['system']['cache_life_time']) {
			header('Cache-Control: public');
			header('Pragma:');

			$lastmod = time() + $this -> cache_expire_time;
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $lastmod) . " GMT");
			header("Expires: " . gmdate ("D, d M Y H:i:s", time() + $this -> cache_expire_time) . " GMT");

			$cache_file = $g_config['system']['root'] . $this -> getCacheDir($this -> template_dir . $file_name . $cache_id) 
			. md5($this -> template_dir . $file_name . $cache_id) . '.cache';

			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) 
			&& strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastmod 
			&& file_exists($cache_file) 
			&& filemtime($cache_file) <= $lastmod) {
				@header('Status: 304 Not Modified', true, 304);
				System :: toEnd();
			} 
		} 

		if (ENABLE_GZIP) {
			$buffer = $this -> fetchCache($file_name, $cache_id);
			ob_start('ob_gzhandler');
			print $buffer;
		} else {
			print($this -> fetchCache($file_name, $cache_id));
		} 
	} 

	/**
	 * 运行缓存
	 * 
	 * @var $file_name,$cache_id, 压缩技术
	 */
	function runCache($file_name, $cache_id = null)
	{
		if (ENABLE_GZIP) {
			$buffer = $this -> fetchCache($file_name, $cache_id);

			ob_start('ob_gzhandler');
			print $buffer;
		} else {
			print($this -> fetchCache($file_name, $cache_id));
		} 
	} 

	/**
	 */

	/**
	 * 取出模板内容
	 * 
	 * @param string $file_name 
	 * @return string or die
	 */
	function readTemplate($file_name)
	{
		if (empty($this -> source)) {
			if ($this -> templateExists($file_name)) {
				$fp = fopen($file_name, 'r');
				$contents = fread($fp, filesize($file_name));
				fclose($fp);
				return $contents;
			} else {
				$dir = pathinfo($file_name);

				if ($this -> autoRepair) {
					if (@opendir($dir["dirname"])) {
						die('<b>Template error:</b>template file does not exits: <b>' . $file_name . '</b>');
					} else {
						if ($this -> makeDir($dir["dirname"])) {
							die('<b>Template Notice:</b>template_dir does not exits, Template engine repair the template_dir: <b>' . $dir["dirname"] . '</b> successfully, please refresh your page');
						} else {
							die('<b>Template error:</b>template_dir does not exits, but Template engine fail to  repair the template_dir: <b>' . $dir["dirname"] . '</b>,please connect to your administrator to solve the problem');
						} 
					} 
				} else {
					die('<b>Template error:</b> Unable to read template file: <b>' . $file_name . '</b>');
				} 
			} 
		} else {
			$source = &$this -> source;
			return $this -> $source($file_name);
		} 
	} 

	/**
	 * 创建目录
	 * 
	 * @var $directory,$mode
	 */
	function makeDir($directory, $mode = 0777)
	{
		if (@opendir($directory)) {
			return true;
		} else {
			if (@mkdir($directory, $mode)) {
				return true;
			} else {
				// try to repair the path
				$pathInfo = explode("/", $directory);
				$basedir = "";

				foreach($pathInfo as $var) {
					if ($var == ".") {
						$basedir = $basedir . "./";
						$begin = false;
					} elseif ($var == "..") {
						$basedir = $basedir . "../";
						$begin = false;
					} else {
						if (!$begin) {
							$var = $var;
							$begin = true;
						} else {
							$var = '/' . $var;
						} 

						if ($this -> makeDir($basedir . $var, $mode)) {
							$repair = true;
							$basedir = $basedir . $var;
						} else {
							$repair = false;
						} 
					} 
				} 

				return $repair;
			} 
		} 
	} 

	/**
	 * 模板是否存在
	 * 
	 * @var $file_name
	 */

	function templateExists($file_name)
	{
		if (file_exists($file_name)) {
			return true;
		} else {
			return false;
		} 
	} 

	/**
	 * 编译内容并生成中间文件
	 * 
	 * @var $file_name,$compile_name
	 */
	function compile($file_name, $compile_name = null)
	{
		$basename = $file_name;

		if (!$this -> templateExists($file_name)) {
			// echo '<b>Template error:</b>no exists template file  : <b>' . $file_name  .'</b>';
			return false;
		} else {
			if (!empty($compile_name)) {
				if (file_exists($this -> compile_dir . $compile_name)) {
					$expire = (filemtime($file_name) == filemtime($this -> compile_dir . $compile_name)) ? true : false;
					if ($expire) return true;
				} 
			} else {
				if (file_exists($this -> compile_dir . $this -> compilefile_prefix . $this -> format($basename))) {
					$expire = (filemtime($file_name) == filemtime($this -> compile_dir 
					. $this -> compilefile_prefix . $this -> format($basename))) ? true : false;
					if ($expire) return true;
				} 
			} 
		} 

		$content = $this -> readTemplate($file_name);
		$content = $this -> compileFile($content); 
		// 自动创建编译存放路径文件夹
		if (!is_dir($this -> compile_dir)) {
			$this -> makeDir($this -> compile_dir);
		}
		if (!empty($compile_name)) {
			if ($fp = fopen($this -> compile_dir . $compile_name, 'w')) {
				fwrite($fp, $content);
				fclose($fp);
				touch($this -> compile_dir . $compile_name, filemtime($file_name));
				return true;
			} else {
				die('<b>Template error:</b> Unable to write compiled file : <b>' . $this -> compile_dir . $compile_name . '</b>');
			} 
		} else {
			if ($fp = fopen($this -> compile_dir . $this -> compilefile_prefix . $this -> format($basename), 'w')) {
				fwrite($fp, $content);
				fclose($fp);
				touch($this -> compile_dir . $this -> compilefile_prefix . $this -> format($basename), filemtime($file_name));
				return true;
			} else {
				die('<b>Template error:</b> Unable to write compiled file : <b>' 
				. $this -> compile_dir . $this -> compilefile_prefix . $file_name . '</b>');
			} 
		} 
	} 

	/**
	 * 编译内容
	 * 
	 * @var $contents
	 */
	function compileFile($contents)
	{
		if (!empty($this -> parse_first_function)) {
			foreach($this -> parse_first_function as $var) {
				if (function_exists($var)) {
					$contents = $var($contents);
				} 
			} 
		} 

		if (defined('SYSTEM_CONFIG_ATTR') && SYSTEM_CONFIG_ATTR === 'release') {
			$this -> parse_fun_array[] = 'clearComment';
		} 

		foreach($this -> parse_fun_array as $var) {
			$contents = $this -> $var($contents);
		} 

		if ($this -> compile_lang) {
			$this -> parse_lang($contents);
		} 

		return $contents;
	} 
	// -------------------------------开始内容解析函数---------------------------//
	/**
	 * compile language label to php code
	 * {lang:can_not_connected_to_you}
	 */

	function parse_lang(&$contents)
	{
		$patt = '/\{LANG:([a-zA-Z0-9_-]+)\}/siU';

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $this -> lang_tag_format($var), $contents);
			} 
		} 
		$patt = '/\{LANG_GLOBAL:([a-zA-Z0-9_-\s]+)\}/siU';

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $this -> lang_global_tag_format($var), $contents);
			} 
		} 
	} 

	function lang_tag_format($var)
	{
		$var = "<?php echo \$LANG['{$var}'];?>";
		return $var;
	} 

	function lang_global_tag_format($var)
	{
		if (strpos($var, ' ')) {
			$vars = explode(' ', $var);

			foreach($vars as $key => $var1) {
				$return .= "<?php echo \$LANG_GLOBAL['{$var1}'];?>";
			} 
		} else {
			$return = "<?php echo \$LANG_GLOBAL['{$var}'];?>";
		} 
		return $return;
	} 

	function parseTag($contents)
	{
		$patt = '/' . preg_quote($this -> tag_left_delim) . '\\$([\S]+)' . preg_quote($this -> tag_right_delim) . '/siU';

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $this -> parse_tag_format_display($var), $contents);
			} 
		} 
		$patt = '/' . preg_quote('{') . '\\$(.*)' . preg_quote('}') . '/siU';
		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				if (strpos($matches[0][$key], 'this->tpl_vars')) continue;

				$contents = str_replace($matches[0][$key], $this -> parse_tag_format_var($var), $contents);
			} 
		} 
		$patt = '/' . preg_quote($this -> tag_left_delim) . '\\*(.*)' . preg_quote($this -> tag_right_delim) . '/siU';
		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $this -> parse_tag_format_global_display($var), $contents);
			} 
		} 
		$patt = '/' . preg_quote('{') . '\\*(.*)' . preg_quote('}') . '/siU';
		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				if (strpos($matches[0][$key], 'this->tpl_vars')) continue;
				$contents = str_replace($matches[0][$key], $this -> parse_tag_format_global_var($var), $contents);
			} 
		} 

		return $contents;
	} 

	function parseParameter($Parameter)
	{
		$Parameter = trim($Parameter);
		$patt = "/([A-Za-z0-9_\\-]+)=[\"\\']([^\"\\']*)[\"\\']/siU";
		if (preg_match_all($patt, $Parameter, $matches)) {
			foreach ($matches[0] as $key => $var) {
				$returnKey = strtolower($matches[1][$key]);
				$return[$returnKey] = $matches[2][$key];
			} 
		} 
		return $return;
	} 

	function parse_tag_format_var($string)
	{
		$header = "{\$this->tpl_vars";

		if (strpos($string, '.')) {
			$data = explode('.', $string);
			$string = '';

			foreach($data as $key => $var) {
				$string .= "['" . $var . "']";
			} 

			$string = $header . $string . '}';
		} else {
			$string = $header . "['" . $string . "']}";
		} 

		return $string;
	} 

	function parse_tag_format_var2($string)
	{
		$header = "\$this->tpl_vars";

		if (strpos($string, '.')) {
			$data = explode('.', $string);
			$string = '';

			foreach($data as $key => $var) {
				$var = $this -> parse_tag_format_varIN($var);
				$string .= "[\"" . $var . "\"]";
			} 
			$string = $header . $string;
		} else {
			$string = $header . "['" . $string . "']";
		} 

		return $string;
	} 

	function parse_tag_format_varIN($string)
	{
		$header = "{\$this->tpl_vars";
		$substr = substr($string, 0, 1);

		if (strpos($string, ':') && $substr == '$') {
			$string = substr($string, 1);
			$data = explode(':', $string);
			$string = '';

			foreach($data as $key => $var) {
				$string .= "['" . $var . "']";
			} 

			$string = $header . $string . '}';
		} 
		return $string;
	} 

	function parse_tag_format_display($string)
	{
		$header = "<?php echo \$this->tpl_vars";

		if (strpos($string, '.')) {
			$data = explode('.', $string);
			$string = '';

			foreach($data as $key => $var) {
				$var = $this -> parse_tag_format_varIN($var);
				$string .= "[\"" . $var . "\"]";
			} 
			$string = $header . $string . ';?>';
		} else {
			$string = $header . "[\"" . $string . "\"];?>";
		} 

		return $string;
	} 

	function parse_tag_format_global_var($string)
	{
		$header = "{\$GLOBALS";

		if (strpos($string, '.')) {
			$data = explode('.', $string);
			$string = '';

			foreach($data as $key => $var) {
				$string .= "[\"" . $var . "\"]";
			} 

			$string = $header . $string . ";?>";
		} else {
			$string = $header . "[\"" . $string . "\"];?>";
		} 

		return $string . "}";
	} 

	function parse_tag_format_global_display($string)
	{
		$header = "<?php echo \$GLOBALS";

		if (strpos($string, '.')) {
			$data = explode('.', $string);
			$string = '';

			foreach($data as $key => $var) {
				$string .= "['" . $var . "']";
			} 

			$string = $header . $string . ";?>";
		} else {
			$string = $header . "['" . $string . "'];?>";
		} 
		return $string;
	} 

	function parsePhp(&$contents)
	{
		$patt = "'" . preg_quote($this -> left_delimiter) . "php" . preg_quote($this -> right_delimiter) . "(.*)" . preg_quote($this -> left_delimiter) . "/php" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$contents = str_replace($matches[1][$key] , '<?php ' . $matches[1][$key] . '?>', $contents);
			} 
		} 
		return $contents;
	} 

	function parseIf(&$contents)
	{
		$patt = '/' . preg_quote($this -> left_delimiter) . 'if[\s]+([^\n]+)' . preg_quote($this -> right_delimiter) . '/si';

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php if(" . $matches[1][$key] . "): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = '/' . preg_quote($this -> left_delimiter) . 'elseif[\s]+([^\n]+)' . preg_quote($this -> right_delimiter) . '/si';

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php elseif(" . $matches[1][$key] . "): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = '/' . preg_quote($this -> left_delimiter) . 'else' . preg_quote($this -> right_delimiter) . '/siU';

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$data = "<?php else: ?>";
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = '/' . preg_quote($this -> left_delimiter) . '\/if' . preg_quote($this -> right_delimiter) . '/siU';

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$data = "<?php endif;?>";
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		return $contents;
	} 

	function format_var($data)
	{
		$patt = '/([^[])\\$([a-zA-Z0-9_\.]+)/';

		if (preg_match_all($patt, $data, $matches)) {
			$matches[2] = array_unique($matches[2]);
			foreach($matches[2] as $key => $var) {
				if ($var == 'this') continue;
				if ($var == 'GLOBALS') continue;
				$data = preg_replace('/\\$' . preg_quote($matches[2][$key]) . '([^a-zA-Z0-9_\.])/', $this -> format_control_local($var) . '\\1', $data);
			} 
		} 

		$patt = '/([^[])\\*([a-zA-Z0-9_\.]+)/';

		if (preg_match_all($patt, $data, $matches)) {
			foreach($matches[2] as $key => $var) {
				$data = preg_replace('/\\$' . preg_quote($matches[2][$key]) . '([^a-zA-Z0-9_\.])/', $this -> format_control_local($var) . '\\1', $data);
				$data = str_replace($matches[0][$key] . ' ', $matches[1][$key] . $this -> format_control_global($var) . ' ', $data);
			} 
		} 
		return $data;
	} 

	function format_control_local($string)
	{
		$header = "\$this->tpl_vars";

		if (strpos($string, '.')) {
			$data = explode('.', $string);
			$string = '';

			foreach($data as $key => $var) {
				$var = $this -> parse_tag_format_varIN($var);
				$string .= "['" . $var . "']";
			} 
			$string = $header . $string;
		} else {
			$string = $header . "['" . $string . "']";
		} 

		return $string;
	} 

	function format_control_global($string)
	{
		$header = "\$GLOBALS";

		if (strpos($string, '.')) {
			$data = explode('.', $string);
			$string = '';

			foreach($data as $key => $var) {
				$string .= "['" . $var . "']";
			} 
			$string = $header . $string;
		} else {
			$string = $header . "['" . $string . "']";
		} 

		return $string;
	} 

	function parseLoop(&$contents)
	{
		$patt = "'" . preg_quote($this -> left_delimiter) . "loop[\s]+([\S]+)[\s]+var=([a-zA-Z0-9_]+)[\s]*" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php if(!empty(" . $matches[1][$key] . " )): \n foreach (" . $matches[1][$key] . " as  \$this->tpl_vars['" . $matches[2][$key] . "']): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = "'" . preg_quote($this -> left_delimiter) . "loop[\s]+([\S]+)[\s]+key=([a-zA-Z0-9_]+)[\s]+var=([a-zA-Z0-9_]+)[\s]*" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php if(!empty(" . $matches[1][$key] . " )): \n foreach (" . $matches[1][$key] . " as  \$this->tpl_vars['" . $matches[2][$key] . "']=>\$this->tpl_vars['" . $matches[3][$key] . "']): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = "'" . preg_quote($this -> left_delimiter) . "loop[\s]+([\S]+)[\s]+var=([a-zA-Z0-9_]+)[\s]+key=([a-zA-Z0-9_]+)[\s]*" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php if(!empty(" . $matches[1][$key] . " )): \n foreach (" . $matches[1][$key] . " as  \$this->tpl_vars['" . $matches[3][$key] . "']=>\$this->tpl_vars['" . $matches[2][$key] . "']): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = "'" . preg_quote($this -> left_delimiter) . "/loop" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$data = "<?php endforeach; endif;?>";
				$contents = str_replace($var , $data, $contents);
			} 
		} 
		// ------------------------------------foreach------------------------------------
		$patt = "'" . preg_quote($this -> left_delimiter) . "foreach[\s]+name=\"([\S]+)\"[\s]+var=\"([a-zA-Z0-9_]+)\"[\s]*" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php if(!empty(\$" . $matches[1][$key] . " )): \n foreach (\$" . $matches[1][$key] . " as  \$this->tpl_vars['" . $matches[2][$key] . "']): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = "'" . preg_quote($this -> left_delimiter) . "foreach[\s]+name=\"([\S]+)\"[\s]+key=\"([a-zA-Z0-9_]+)\"[\s]+var=\"([a-zA-Z0-9_]+)\"[\s]*" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php if(!empty(\$" . $matches[1][$key] . " )): \n foreach (\$" . $matches[1][$key] . " as  \$this->tpl_vars['" . $matches[2][$key] . "']=>\$this->tpl_vars['" . $matches[3][$key] . "']): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = "'" . preg_quote($this -> left_delimiter) . "foreach[\s]+name=\"([\S]+)\"[\s]+var=\"([a-zA-Z0-9_]+)\"[\s]+key=\"([a-zA-Z0-9_]+)\"[\s]*" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$str = "<?php if(!empty(\$" . $matches[1][$key] . " )): \n foreach (\$" . $matches[1][$key] . " as  \$this->tpl_vars['" . $matches[3][$key] . "']=>\$this->tpl_vars['" . $matches[2][$key] . "']): ?>";
				$data = $this -> format_var($str);
				$contents = str_replace($var , $data, $contents);
			} 
		} 

		$patt = "'" . preg_quote($this -> left_delimiter) . "/foreach" . preg_quote($this -> right_delimiter) . "'siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[0] as $key => $var) {
				$data = "<?php endforeach; endif;?>";
				$contents = str_replace($var , $data, $contents);
			} 
		} 
		return $contents;
	} 

	function parseInclude($contents)
	{
		$patt = "/" . preg_quote($this -> left_delimiter) . "include:[\s]*file[\s]*=[\s]*\"(.*)\"[\s]*" . preg_quote($this -> right_delimiter) . "/siU";

		if (preg_match_all($patt, $contents, $matches)) { // print_r($matches);
			foreach($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $this -> parse_include_format($var), $contents);
			} 
		} 
		// System::toEnd();
		return $contents;
	} 

	function parseVar(&$contents)
	{
		$patt = "/<var[\\s]*(.*)[\\s]*[\\/]?>/siU";
		if (preg_match_all($patt, $contents, $matches)) {
			foreach ($matches[1] as $key => $var) {
				$attributes = $this -> parseParameter($matches[1][$key]);
				if ($attributes['value'][0] == "\$") {
					$attributes['value'] = substr($attributes['value'], 1);
					$replace = "<?php \$this->tpl_vars['{$attributes['name']}'] = \$this->tpl_vars['{$attributes['value']}'] ;?>";
				} else {
					$replace = "<?php \$this->tpl_vars['{$attributes['name']}'] = \"{$attributes['value']}\";?>";
				} 
				$contents = str_replace($matches[0][$key], $replace, $contents);
			} 
		} 
		return $contents;
	} 

	function parseOp(&$contents)
	{
		$patt = "/<op[\\s]+exp=\"([^\n]*)\"[\\s]*[\\/]?>/si";
		if (preg_match_all($patt, $contents, $matches)) {
			$starter = "<?php ";
			$ender = " ; ?>\n";
			foreach ($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $starter . $this -> format_var($matches[1][$key]) . $ender, $contents);
			} 
		} 
		return $contents;
	} 

	function parseDebug(&$contents)
	{
		$patt = "/<debug[\\s]+name=\"([^\"\n]*)\"[\\s]*[\\/]?>/si";
		if (preg_match_all($patt, $contents, $matches)) {
			$starter = "<?php debug();";
			$ender = "  ?>\n";
			foreach ($matches[1] as $key => $var) {
				$command = "\t\$v = htmlspecialchars(stripslashes(var_export(\$this->tpl_vars[\"{$matches[1][$key]}\"], TRUE)));\r\n\techo \"<TEXTAREA style='width:500px;height:300px;border:5px solid #EAF3FA; padding:5px;font-size:12px;font-family:Verdana,Arial,Helvetica, sans-serif;'>{\$v}</TEXTAREA>\";\r\n\t\t\t\t";
				$contents = str_replace($matches[0][$key], $starter . $command . $ender, $contents);
			} 
		} 
		return $contents;
	} 

	function parseHeader(&$contents)
	{
		$patt = "/<header[\\s]+name=\"(.*)\"[\\s]+\\/>/siU";
		if (preg_match_all($patt, $contents, $matches)) {
			foreach ($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], "<?php header(\"" . $matches[1][$key] . "\"); ?>", $contents);
			} 
		} 
		return $contents;
	} 

	function clearComment($contents)
	{
		return preg_replace("/<!--(.*)-->/isU", "", $contents);
	} 

	function clearWhitespace($contents)
	{
		$contents = preg_replace('/[ \t]+/', ' ', $contents);
		$contents = preg_replace('/[\r\n]+/', "\n", $contents);
		return $contents;
	} 

	function parseIncludeSubtpl($contents)
	{
		$patt = "/<include[\\s]+(.*)[\\s]*[\\/]?>/siU";
		if (preg_match_all($patt, $contents, $matches)) {
			foreach ($matches[1] as $key => $var) {
				$attributes = $this -> _parseRawParameter($matches[1][$key]);
				$replace = "";
				foreach ($attributes as $keyIn => $varIn) {
					if ($keyIn == "file") {
						$file_replace = $this -> parse_include_format($varIn);
					} else if ($varIn[0] == "\$") {
						$varIn = substr($varIn, 1);
						$replace .= "\n<?php \$this->tpl_vars['{$keyIn}'] = " . $this -> parse_tag_format_var2($varIn) . " ;?>\n";
					} else {
						$replace .= "\n<?php \$this->tpl_vars['{$keyIn}'] = \"{$varIn}\";?>\n";
					} 
				} 
				$contents = str_replace($matches[0][$key], $replace . $file_replace, $contents);
			} 
		} 
		return $contents;
	} 
	// 解析标签结束
	function parse_include_format($string)
	{
		$header = "<?php include(\$this->compile_dir.\"";
		$string = str_replace('"', '', $string);
		$string = str_replace("'", '', $string);
		$string = str_replace(' ', '', $string);

		if (strpos('Jerry' . $string, "file:")) {
			$string = str_replace("file:", '', $string);

			$new_name = $this -> compilefile_prefix . $this -> format($this -> template_dir . $string);

			if ($this -> compile($string, $new_name)) {
				$string = "<?php include(\"" . $new_name . "\");?>";
			} else {
				$string = "<b>Template error : </b>unable to compile template: <b>{$string}</b>";
			} 
		} elseif (strpos('Jerry' . $string, "http://")) {
			$string = "<?php include(\"" . $string . "\");?>";
		} elseif (strpos('Jerry' . $string, "{\$")) {
			eval("\$string = \"$string\";");
			$new_name = $this -> compilefile_prefix . $this -> format($this -> template_dir . $string);

			if ($this -> compile($string, $new_name)) {
				$string = "<?php include(\"" . $new_name . "\");?>";
			} else {
				$string = "<b>Template error : </b>unable to compile template: <b>{$string}</b>";
			} 
		} elseif (strpos('Jerry' . $string, "../")) {
			$num = 0;
			$dir = '';
			$data = explode('/', $string);

			foreach($data as $var) {
				if ($var == '..') $num++;
			} 

			$string = str_replace('../', '', $string);
			$string = str_replace('./', '', $string);
			$data = explode('/', $this -> template_dir);

			$num = count($data) - $num-1;

			for($i = 0;$i < $num;$i++) {
				$dir .= $data[$i] . '/';
			} 
			$new_name = $this -> compilefile_prefix . $this -> format($dir . $string);

			if ($this -> compile($dir . $string, $new_name)) {
				$string = "<?php include(\"" . $new_name . "\");?>";
			} else {
				$string = "<b>Template error : </b>unable to compile template: <b>{$string}</b>";
			} 
		} else {
			$new_name = $this -> compilefile_prefix . $this -> format($this -> template_dir . $string);

			if ($this -> compile($this -> template_dir . $string, $new_name)) {
				$string = "<?php include(\"" . $this -> compile_dir . $new_name . "\");?>";
			} else {
				$string = "<b>Template error : </b>unable to compile template: <b>{$string}</b>";
			} 
		} 

		return $string;
	} 

	function parseGet($contents)
	{
		$patt = "/" . preg_quote($this -> left_delimiter) . "get:[\s]+file=(.*)[\s]*" . preg_quote($this -> right_delimiter) . "/siU";

		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $this -> parse_get_format($var), $contents);
			} 
		} 
		return $contents;
	} 
	function parse_get_format($string)
	{
		$header = "<?php include(\"" . $this -> template_dir;
		$string = str_replace('"', '', $string);
		$string = str_replace("'", '', $string);
		$string = str_replace(' ', '', $string);

		$num = 0;
		$dir = '';

		if (strpos('Jerry' . $string, "file:")) {
			$string = str_replace("file:", '', $string);
			$string = "<?php include(\"" . $string . "\");?>";
		} elseif (strpos('Jerry' . $string, "http://")) {
			$string = str_replace("http:\/\/", '', $string);
			$string = "<?php include(\"" . $string . "\");?>";
		} elseif (strpos('Jerry' . $string, "{\$")) {
			$string = "<?php include(\"" . $string . "\");?>";
		} elseif (strpos('Jerry' . $string, "../")) {
			$data = explode('/', $string);

			foreach($data as $var) {
				if ($var == '..') $num++;
			} 

			$string = str_replace('../', '', $string);
			$string = str_replace('./', '', $string);
			$data = explode('/', $this -> template_dir);
			$num = count($data) - $num-1;

			for($i = 0;$i < $num;$i++) {
				$dir .= $data[$i] . '/';
			} 
			$string = "<?php include('" . $dir . $string . "');?>";
		} else {
			$string = str_replace('./', '', $string);
			$string = $header . $string . "\");?>";
		} 

		return $string;
	} 

	function parseTagFunc($contents)
	{
		$patt = "/" . preg_quote('[') . "@([\S^(]+)\(([^]]*)\)" . preg_quote(']') . "/siU";
		if (preg_match_all($patt, $contents, $matches)) {
			foreach($matches[1] as $key => $var) {
				$contents = str_replace($matches[0][$key], $this -> parse_tag_func_format($var, $matches[2][$key]), $contents);
			} 
		} 
		return $contents;
	} 

	function parse_tag_func_format($funName, $params)
	{
		$header = "<?php echo ";
		$patt = "/\\$([a-zA-Z0-9_\.]+)/si";

		if (preg_match_all($patt, $params, $matches)) {
			foreach($matches[1] as $key => $var) {
				$params = str_replace($matches[0][$key], $this -> parse_tag_format_var2($var), $params);
			} 
		} 
		$string = $header . $funName . '(' . $params . ");?>";
		return $string;
	} 
} 
