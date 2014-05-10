<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

class Template{

	private $tags = array();
	private $path = null;
	private $startpage=null;
	private $parsephpcodes=array();
	
	function __construct()
	{
	}

	public function SetPath($path)
	{
		$this->path = $path;
	}
	
	public function GetPath()
	{
		return $this->path;
	}
	
	public function GetTemplate($name)
	{
		foreach ($GLOBALS['Filter_Plugin_Template_GetTemplate'] as $fpname => &$fpsignal)
		{
			$fpreturn=$fpname($this,$name);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}
		return $this->path . $name . '.php';
	}

	public function SetTemplate($templatename)
	{
		$this->startpage = $templatename;
	}

	function &GetTags($name){
		return $this->tags[$name];
	}

	function SetTags($name,$value){
		$this->tags[$name]=$value;
	}

	function &GetTagsAll(){
		return $this->tags;
	}
	
	function SetTagsAll($array){
		$this->tags=$array;
	}
	
	function CompileFiles($filesarray){

		foreach ($filesarray as $name => $content) {
			$s=RemoveBOM($this->Compiling($content));
			@file_put_contents($this->path . $name . '.php', $s);
			//if(function_exists('chmod')){
			//	@chmod($this->path . $name . '.php',0755);
			//}
		}

	}

	public function Compiling($content)
	{

		foreach ($GLOBALS['Filter_Plugin_Template_Compiling_Begin'] as $fpname => &$fpsignal)
		{
			$fpreturn = $fpname($this,$content);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}

		//Step1:替换<?php块
		$this->replacePHP($content);
		//Step2:解析PHP
		$this->parsePHP($content);	
		//Step3:引入主题
		$this->parse_template($content);
		//Step4:解析module
		$this->parse_module($content);	
		//Step5:替换配置
		$this->parse_option($content);
		//Step6:替换标签
		$this->parse_vars($content);
		//Step6:替换函数
		$this->parse_function($content);
		//Step7:解析If
		$this->parse_if($content);
		//Step8:解析foreach
		$this->parse_foreach($content);
		//Step9:解析for
		$this->parse_for($content);
		//StepN:解析PHP
		$this->parsePHP2($content);

		foreach ($GLOBALS['Filter_Plugin_Template_Compiling_End'] as $fpname => &$fpsignal)
		{
			$fpreturn=$fpname($this,$content);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
		}

		return $content;
	}

	private function replacePHP(&$content)
	{
		$content = preg_replace("/\<\?php[\d\D]+?\?\>/si", '', $content);
	}

	private function parsePHP(&$content)
	{
		$this->parsephpcodes=array();
		$matches=array();
		if($i=preg_match_all ( '/\{php\}([\D\d]+?)\{\/php\}/si' ,  $content ,  $matches )>0){
			if(isset($matches[1]))
				foreach($matches[1] as $j=>$p) {
					$content = str_replace($p,'<!--'.$j.'-->',$content);
					$this->parsephpcodes[$j]=$p;
				}
		}
	}
	private function parsePHP2(&$content)
	{
		foreach($this->parsephpcodes as $j=>$p) {
			$content = str_replace('{php}<!--'.$j.'-->{/php}','<'.'?php '.$p.' ?'.'>',$content);
		}
		$content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<'.'?php $1 ?'.'>', $content);
		$this->parsephpcodes=array();
	}

	private function parse_template(&$content)
	{
		$content = preg_replace('/\{template:([^\}]+)\}/', '{php} include $this->GetTemplate(\'$1\'); {/php}', $content);
	}

	private function parse_module(&$content)
	{
		$content = preg_replace('/\{module:([^\}]+)\}/', '{php} if(isset($modules[\'$1\'])){echo $modules[\'$1\']->Content;} {/php}', $content);
	}

	private function parse_option(&$content)
	{
		$content = preg_replace('#\{\#([^\}]+)\#\}#', '<?php echo $option[\'\\1\']; ?>', $content);
	}

	private function parse_vars(&$content)
	{
		$content = preg_replace_callback('#\{\$(?!\()([^\}]+)\}#',array($this,'parse_vars_replace_dot'), $content);
	}

	private function parse_function(&$content)
	{
		$content = preg_replace_callback('/\{([a-zA-Z0-9_]+?)\((.+?)\)\}/',array($this,'parse_funtion_replace_dot'), $content);
	}

	private function parse_if(&$content)
	{
		while(preg_match('/\{if [^\n\}]+\}.*?\{\/if\}/s', $content))
			$content = preg_replace_callback(
				'/\{if ([^\n\}]+)\}(.*?)\{\/if\}/s',
				array($this,'parse_if_sub'),
				$content
			);
	}

	private function parse_if_sub($matches)
	{

		$content = preg_replace_callback(
			'/\{elseif ([^\n\}]+)\}/',
			array($this, 'parse_elseif'),
			$matches[2]
		);

		$ifexp = str_replace($matches[1],$this->replace_dot($matches[1]),$matches[1]);

		$content = str_replace('{else}', '{php}}else{ {/php}', $content);
		return "<?php if ($ifexp) { ?>$content<?php } ?>";

	}

	private function parse_elseif($matches)
	{
		$ifexp = str_replace($matches[1],$this->replace_dot($matches[1]),$matches[1]);
		return "{php}}elseif($ifexp) { {/php}";
	}


	private function parse_foreach(&$content)
	{
		while(preg_match('/\{foreach(.+?)\}(.+?){\/foreach}/s', $content))
			$content = preg_replace_callback(
				'/\{foreach(.+?)\}(.+?){\/foreach}/s',
				array($this,'parse_foreach_sub'),
				$content
			);
	}

	private function parse_foreach_sub($matches)
	{
		$exp = $this->replace_dot($matches[1]);
		$code = $matches[2];
		return "{php} foreach ($exp) {{/php} $code{php} }  {/php}";
	}
	private function parse_for(&$content)
	{
		while(preg_match('/\{for(.+?)\}(.+?){\/for}/s', $content))
			$content = preg_replace_callback(
				'/\{for(.+?)\}(.+?){\/for}/s',
				array($this,'parse_for_sub'),
				$content
			);
	}

	private function parse_for_sub($matches)
	{
		$exp = $this->replace_dot($matches[1]);
		$code = $matches[2];
		return "{php} for($exp) {{/php} $code{php} }  {/php}";
	}

	private function parse_vars_replace_dot($matches)
	{
		if(strpos($matches[1],'=')===false){
			return '{php} echo $' . $this->replace_dot($matches[1]) . '; {/php}';
		}else{
			return '{php} $' . $this->replace_dot($matches[1]) . '; {/php}';
		}
	}

	private function parse_funtion_replace_dot($matches)
	{
		return '{php} echo ' . $matches[1] . '(' . $this->replace_dot($matches[2]) . '); {/php}';
	}

	private function replace_dot($content)
	{
		$array=array();
		preg_match_all('/".+?"|\'.+?\'/', $content,$array,PREG_SET_ORDER);
		if(count($array)>0){
			foreach($array as $a){
				$a=$a[0];
				if(strstr($a,'.')!=false){
					$b=str_replace('.','{%_dot_%}',$a);
					$content=str_replace($a,$b,$content);
				}
			}
		}
		$content=str_replace(' . ',' {%_dot_%} ',$content);
		$content=str_replace('. ','{%_dot_%} ',$content);
		$content=str_replace(' .',' {%_dot_%}',$content);
		$content=str_replace('.','->',$content);
		$content=str_replace('{%_dot_%}','.',$content);
		return $content;
	}


	#模板入口
	public function Display()
	{
		#强制撤除所有错误监控
		if($GLOBALS['option']['ZC_DEBUG_MODE']==false){
			ZBlogException::ClearErrorHook();
		}
		#入口处将tags里的变量提升全局!!!
		foreach ($this->tags as $key => &$value) {
			$$key=&$value;
		}
		include $this->path .  $this->startpage . '.php';
	}

	public function Output()
	{

		ob_start();
		$this->Display($this->startpage);
		$data = ob_get_contents();
		ob_end_clean();
		return $data;

	}

}
