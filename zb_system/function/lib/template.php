<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */




class Template{


	public $modulesbyfilename = array();
	public $templates = array();
	public $template_includes = array();
	public $tags = array();	
	public $path = null;

	function __construct()
	{

		

	}

	public function LoadTemplates($path)
	{

		$files=GetFilesInDir($path,'php');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		return true;
	}

	public function SaveTemplate($name,$content)
	{
		$this->templates[$name] = $content;
		return file_put_contents($this->path . $name . '.php', $content, LOCK_EX);
	}



	public function CompileFile($name)
	{

		$content = $this->templates[$name];

		foreach ($this->templates as $name => $file) {
			$content=str_ireplace('{$template:' . $name . '}', '{php} include $this->GetTemplate("' . $name . '"); {/php}', $content);
		}

		foreach ($this->tags as $key => $value) {
			$content=str_ireplace('{$' . $key . '}', '{php} echo $this->tags["' . $key . '"]; {/php}', $content);
		}
		
		//替换<?php，不允许出现。
		$content = preg_replace("/\<\?php[\d\D]+?\?\>/si", '', $content);
		#替换template和include
		$content = preg_replace('/\{\$template:([^\}]+)\}/', '{php} include $this->GetTemplate(\'$1\'); {/php}', $content);
		$content = preg_replace('/\{\$include:([^\}]+)\}/', '{php} $this->IncludeCompiled(\'$1\'); {/php}', $content);
		#正则替换{$变量}

		$content = preg_replace('#\{\$([^\}]+)\}#', '<?php echo $\\1; ?>', $content);

		$content = preg_replace('/\{php\}([\D\d]+?)\{\/php\}/', '<?php$1?>', $content);

		return $content;
	}

	public function CompileAll(){
		foreach ($this->templates as $name => $file) {
			$content=$this->CompileFile($name);
			$this->SaveTemplate($name,$content);
		}
	}



	public function GetTemplate($name)
	{
		return $this->path . $name . '.php';
	}

	public function IncludeCompiled($name)
	{
		include $this->path . $name . '.php';
	}

	public function Display($name)
	{

		include $this->path . $name . '.php';
	}

	public function Output($name)
	{

		ob_start();
		include $this->path . $name . '.php';
		$data = ob_get_contents();
		ob_end_clean();
		return $data;

	}

}
?>