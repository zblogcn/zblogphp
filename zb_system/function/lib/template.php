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


	function Compiling($filesarray){

		foreach ($filesarray as $name => $content) {
			file_put_contents($this->path . $name . '.php', $this->CompileFile($content), LOCK_EX);
		}

	}

	public function CompileFile($content)
	{


		//替换<?php，不允许出现。
		$content = preg_replace("/\<\?php[\d\D]+?\?\>/si", '', $content);
		#替换template和include
		$content = preg_replace('/\{template:([^\}]+)\}/', '{php} include $this->GetTemplate(\'$1\'); {/php}', $content);
		//$content = preg_replace('/\{include:([^\}]+)\}/', '{php} $this->IncludeCompiled(\'$1\'); {/php}', $content);

		#zblog asp 特别魔法
		$content = preg_replace('#\{\#([^\}]+)\#\}#', '<?php echo $option[\'\\1\']; ?>', $content);

		$content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<?php $1 ?>', $content);

		#正则替换{$变量}
		/*$content = preg_replace('#\{\$([^\}]+)\}#', '<?php echo $this->\\1; ?>', $content);*/

		$content = preg_replace_callback('#\{\$([^\}]+)\}#',create_function('$matches','return \'<?php echo $\' . str_replace(\'.\',\'->\',$matches[1]) . \';?>\';'), $content);
        
		return $content;
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