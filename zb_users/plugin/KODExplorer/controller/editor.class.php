<?php 
class editor extends Controller{
    /**
     * 构造函数
     */
    function __construct()    {
        parent::__construct();
        $this->tpl = TEMPLATE . 'editor/';
    }

    // 多文件编辑器
    function index(){
		$this->display('editor.php');
    }
    // 单文件编辑
    function edit(){
        $this->display('edit.php');
    }

    // 获取文件数据
    function fileGet(){
		$filename=iconv_system(urldecode($this->in['filename']));
		$filecontents=file_get_contents($filename);//文件内容
		$filecontents=str_replace('</textarea>','<\/textarea>',$filecontents);
		$charset=$this->_get_charset($filecontents);
		if ($charset=='gbk'){
			$filecontents=iconv($charset,'utf-8',$filecontents);
		}
		$data = array(			
			'ext'		=> end(explode('.',$filename)),
            'name'      => iconv_app(end(explode('/',$filename))),
			'filename'	=> iconv_app($filename),
			'charset'	=> $charset,
			'content'	=> $filecontents			
		);
		echo json_encode($data);
    }
    function fileSave(){
        $charset=$this->in['charset'];
        $filestr=$this->in['filestr'];
        $path=iconv_system(urldecode($this->in['path']));        
        if ($charset=='gbk'){
            $filestr=iconv('utf-8','gbk',$filestr);
        }
        $replace_from = array('-@$@-','-($)-',"\x0a",'<\/textarea>');
        $replace_to   = array('&','+',"\x0d\x0a",'</textarea>');
        $filestr=str_replace($replace_from,$replace_to,$filestr);
        $fp=fopen($path,'wb');
        fwrite($fp,stripslashes($filestr));
        fclose($fp);
    }


	//-----------------------------------------------
	/*
	* 获取字符串编码
	* @param:$ext 传入字符串
	*/
	function _get_charset(&$str) {
		$charset=strtolower(
			mb_detect_encoding($str,
			array('ASCII','UTF-8','GBK')//前面检测成功则，自动忽略后面
		));
		if (substr($str,0,3)==chr(0xEF).chr(0xBB).chr(0xBF)){
			$charset='utf-8';
		}
		else if($charset=='cp936'){
			$charset='gbk';
		}
		return $charset;
	}
}
