<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */




class Template extends Base{


	public $modulesbyfilename=array();
	public $templates=array();
	public $template_includes=array();
	public $templatetags=array();	
	public $templatepath=null;
	public $zbp_path='';
	public $themename='';


	function __construct()
	{

		

	}

	public function LoadTemplates(){
		#先读默认的
		$dir=$this->zbp_path .'zb_system/defend/default/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}
		#再读当前的
		$dir=$this->zbp_path .'zb_users/theme/' . $this->themename . '/template/';
		$files=GetFilesInDir($dir,'html');
		foreach ($files as $sortname => $fullname) {
			$this->templates[$sortname]=file_get_contents($fullname);
		}

	}

	public function MakeTemplatetags(){

		global $zbp;

		foreach ($this->modulesbyfilename as $key => $mod) {
			$this->templatetags[strtoupper($key)]=$mod->Content;
		}

		foreach ($zbp->option as $key => $value) {
			$this->templatetags[strtoupper($key)]=$value;
		}

		$this->templatetags['ZC_BLOG_SUB_NAME']=&$this->templatetags['ZC_BLOG_SUBTITLE'];
		$this->templatetags['ZC_BLOG_NAME']=&$this->templatetags['ZC_BLOG_TITLE'];
		$this->templatetags['BlogTitle']=&$zbp->title;

	}









	public function CompileFile($content){
		foreach ($this->templates as $name => $file) {
			$content=str_ireplace('{template:' . $name . '}', '<?php include $this->IncludeTemplate("' . $name . '");?>', $content);
		}

		foreach ($this->modulesbyfilename as $key => $value) {
			$content=str_ireplace('{module:' . $key . '}', '<?php echo $this->IncludeModule("' . $key . '");?>', $content);
		}

		foreach ($this->templatetags as $key => $value) {
			$content=str_ireplace('<#' . $key . '#>', '<?php echo $this->templatetags["' . $key . '"];?>', $content);
			$content=str_ireplace('{#' . $key . '#}', '<?php echo $this->templatetags["' . $key . '"];?>', $content);			
		}
		#正则替换{$变量}
		$content = preg_replace('#\{\$([^\}]+)\}#', '<?php echo $\\1; ?>', $content);
		return $content;
	}



	public function Compiling(){

		#先生成sidebar1-5
		$sidebars=array(1=>'',2=>'',3=>'',4=>'',5=>'');
		$s=array($this->option['ZC_SIDEBAR_ORDER'],
				$this->option['ZC_SIDEBAR_ORDER2'],
				$this->option['ZC_SIDEBAR_ORDER3'],
				$this->option['ZC_SIDEBAR_ORDER4'],
				$this->option['ZC_SIDEBAR_ORDER5'] );

		foreach ($s as $k =>$v) {
			$a=explode(':', $v);
			foreach ($a as $v2) {
				if(isset($this->modulesbyfilename[$v2])){
					$f='<?php $this->IncludeModuleFull(\'' . $v2 . '\');?>' . "\r\n";
				}
				$sidebars[($k+1)] .=$f ;
			}
		}
		$this->templates['sidebar']=$sidebars[1];
		$this->templates['sidebar2']=$sidebars[2];
		$this->templates['sidebar3']=$sidebars[3];
		$this->templates['sidebar4']=$sidebars[4];
		$this->templates['sidebar5']=$sidebars[5];

		#把所有模板编辑到template目录下
		foreach ($this->templates as $name => $file) {
			$f=$this->CompileFile($file);
			file_put_contents($this->templatepath . $name . '.php', $f, LOCK_EX);
		}

	}

	function IncludeTemplate($name){
		return $this->templatepath . $name . '.php';
	}

	function IncludeModule($name){
		
		global $zbp;
		
		if(isset($this->modulesbyfilename[$name])){
			$c=$this->modulesbyfilename[$name]->Content;
			return str_replace('{#ZC_BLOG_HOST#}', $zbp->host, $c);
		}
	}
	function IncludeModuleFull($name){

		if(isset($this->modulesbyfilename[$name])){
			$module=$this->modulesbyfilename[$name];
		}else{
			$module=new Module;
		}		
		
		$this->display('b_module');
	}


	public function display($name){
		//exit($this->templatepath . $name . '.php');

		include $this->templatepath . $name . '.php';
	}
}
?>