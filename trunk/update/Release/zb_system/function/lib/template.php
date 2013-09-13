<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */




class Template{


	public $templates = array();
	public $tags = array();	
	public $path = null;

	function __construct(){
	}
	
	public function SetPath($path)
	{
		 $this->path= $path;
	}

	function GetTags($name){
		return $this->tags[$name];
	}

	function SetTags($name,$value){
		$this->tags[$name]=$value;
	}	


	function CompileFiles($filesarray){

		foreach ($filesarray as $name => $content) {
			@file_put_contents($this->path . $name . '.php', $this->Compiling($content));
			//if(function_exists('chmod ')){
			//	@chmod($this->path . $name . '.php',0777);
			//}
		}

	}

	public function Compiling($content)
	{


		//Step1:替换<?php块
		$this->replacePHP($content);
		//Step2:引入主题
		$this->parse_template($content);
		//Step3:替换配置
		$this->parse_option($content);
		//Step4:替换标签
		$this->parse_vars($content);
		//Step5:解析If
		$this->parse_if($content);
		//Step6:解析foreach
		$this->parse_foreach($content);
		//Step7:解析for
		$this->parse_for($content);
		//StepN:解析PHP
		$this->parsePHP($content);

		#正则替换{$变量}
		/*$content = preg_replace('#\{\$([^\}]+)\}#', '<?php echo $this->\\1; ?>', $content);*/
        
		return $content;
	}

	private function replacePHP(&$content)
	{
		$content = preg_replace("/\<\?php[\d\D]+?\?\>/si", '', $content);
	}

	private function parse_template(&$content)
	{
		$content = preg_replace('/\{template:([^\}]+)\}/', '{php} include $this->GetTemplate(\'$1\'); {/php}', $content);
		//$content = preg_replace('/\{include:([^\}]+)\}/', '{php} $this->IncludeCompiled(\'$1\'); {/php}', $content);
	}

	private function parse_option(&$content)
	{
		$content = preg_replace('#\{\#([^\}]+)\#\}#', '<?php echo $option[\'\\1\']; ?>', $content);
	}

	private function parsePHP(&$content)
	{
		$content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<?php $1 ?>', $content);
	}

	private function parse_vars(&$content)
	{
		$content = preg_replace_callback('#\{\$(?!\()([^\}]+)\}#',array($this,'parse_vars_replace_dot'), $content);
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
		return '{php} echo $' . $this->replace_dot($matches[1]) . '; {/php}';
	}

	private function replace_dot($content)
	{
		return str_replace('.','->',$content);
	}

	

	public function GetTemplate($name)
	{
		return $this->path . $name . '.php';
	}

	
	private $templatename=null;
	public function SetTemplate( $templatename)
	{
		 $this->templatename= $templatename;
	}

	#模板入口
	public function Display()
	{
		#强制撤除所有错误监控
		if($GLOBALS['option']['ZC_DEBUG_MODE']==false){
			set_error_handler(create_function('',''));
			set_exception_handler(create_function('',''));
			register_shutdown_function(create_function('',''));
		}
		#入口处将tags里的变量提升全局!!!
		foreach ($this->tags as $key => &$value) {
			$$key=&$value;
		}
		include $this->path .  $this->templatename . '.php';
	}

	public function Output()
	{

		ob_start();
		$this->Display($this->templatename);
		$data = ob_get_contents();
		ob_end_clean();
		return $data;

	}

}
?>