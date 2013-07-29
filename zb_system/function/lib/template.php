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

	//public function __get($name) 
	//{
	//	return $this->tags[$name];
	//}



	function SetTags($name,$value){
		$this->tags[$name]=$value;
	}	


	function CompileFiles($filesarray){

		foreach ($filesarray as $name => $content) {
			file_put_contents($this->path . $name . '.php', $this->Compiling($content), LOCK_EX);
		}

	}

	public function Compiling($content)
	{



		$this->replacePHP($content);

		$this->parse_template($content);
		$this->parse_option($content);
		$this->parse_vars($content);
		$this->parse_if($content);

		$this->parsePHP($content);
		

		#正则替换{$变量}
		/*$content = preg_replace('#\{\$([^\}]+)\}#', '<?php echo $this->\\1; ?>', $content);*/
        
		return $content;
	}

	private function replacePHP(&$content)
	{
		//替换<?php，不允许出现。
		$content = preg_replace("/\<\?php[\d\D]+?\?\>/si", '', $content);
	}

	private function parse_template(&$content)
	{
		$content = preg_replace('/\{template:([^\}]+)\}/', '{php} include $this->GetTemplate(\'$1\'); {/php}', $content);
		//$content = preg_replace('/\{include:([^\}]+)\}/', '{php} $this->IncludeCompiled(\'$1\'); {/php}', $content);
	}

	private function parse_option(&$content)
	{
		#zblog asp 特别魔法
		$content = preg_replace('#\{\#([^\}]+)\#\}#', '<?php echo $option[\'\\1\']; ?>', $content);
	}

	private function parsePHP(&$content)
	{
		$content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<?php $1 ?>', $content);
	}

	private function parse_vars(&$content)
	{
		$content = preg_replace_callback('#\{\$([^\}]+)\}#',array($this,'parse_vars_replace_dot'), $content);
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

	//public function IncludeCompiled($name)
	//{
	//	include $this->path . $name . '.php';
	//}

	#模板入口
	public function Display($name)
	{
		#入口处将tags里的变量提升全局!!!
		foreach ($this->tags as $key => &$value) {
			$$key=&$value;
		}
		include $this->path . $name . '.php';
	}

	public function Output($name)
	{

		ob_start();
		$this->Display($name);
		$data = ob_get_contents();
		ob_end_clean();
		return $data;

	}

}
?>