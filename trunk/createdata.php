<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require 'zb_system/function/c_system_base.php';
error_reporting(0);
ini_set("display_errors",0);
set_error_handler(create_function('',''));
set_exception_handler(create_function('',''));
register_shutdown_function(create_function('',''));

$zbp->Initialize();

function article(){
	for ($i=0; $i < 2000; $i++) { 
		$a=new Post();
		$a->CateID=rand(1,20);
		$a->AuthorID=1;
		$a->Tag=getTagStr(rand(0,19));
		$a->Status=ZC_LOG_STATUS_PUBLIC;
		$a->Type=ZC_LOG_TYPE_ARTICLE;
		$a->Alias='';
		$a->IsTop=false;
		$a->IsLock=false;
		$a->Title=getRandStr(rand(8,14));
		$a->Intro=getRandStr(rand(50,150)) . GetGuid();
		$a->Content=getRandStr(rand(200,300)) . GetGuid() . '<br/>' . GetGuid();
		$a->IP=GetGuestIP();
		$a->PostTime=time();
		$a->CommNums=0;
		$a->ViewNums=0;
		$a->Template='';
		$a->Meta='';
		$a->Save();
	}
}

function page(){
	for ($i=0; $i < 500; $i++) { 
		$a=new Post();
		$a->CateID=0;
		$a->AuthorID=1;
		$a->Tag='';
		$a->Status=ZC_LOG_STATUS_PUBLIC;
		$a->Type=ZC_LOG_TYPE_PAGE;
		$a->Alias='';
		$a->IsTop=false;
		$a->IsLock=false;
		$a->Title=getRandStr(rand(6,10));
		$a->Intro='';
		$a->Content=getRandStr(rand(200,300)) . GetGuid() . '<br/>' . GetGuid();
		$a->IP=GetGuestIP();
		$a->PostTime=time();
		$a->CommNums=0;
		$a->ViewNums=0;
		$a->Template='';
		$a->Meta='';
		$a->Save();  
	}
}

function cate(){
	for ($i=0; $i < 20; $i++) { 
		$cate = new Category();
		$cate->LoadInfobyArray(array(
		0,
		getRandStr(rand(2,4)),
		0,
		0,
		'',
		'',
		0,
		0,
		'',
		'',
		'',
		));
		$cate->Save();
  	}
}

function tag(){
	for ($i=0; $i < 1000; $i++) { 
		$tag = new Tag();
		$tag->LoadInfobyArray(array(
		0,
		getRandStr(rand(2,5)),
		'',
		0,
		0,
		'',
		'',
		'',
		));
		$tag->Save();
  	}
}

function getChineseChar2() {
 $unidec = rand(hexdec('4e00'), hexdec('9fa5'));
 $unichr = '&#' . $unidec . ';';
 $zhcnchr = mb_convert_encoding($unichr, "UTF-8", "HTML-ENTITIES");
 return $zhcnchr;
}

function getChineseChar() {

$i= rand(0xb0, 0xd7);
$j= rand(0xa1, 0xfe);
$s=  chr($i).chr($j);
$s=@iconv('GB2312', 'UTF-8//IGNORE', $s);

return $s;
}



function getRandStr($len) {
 $str = '';
 for($i=0;$i<$len;$i++) {
  $str = $str . getChineseChar();
 }
 return $str;
}

function getTagStr($tagcount) {
 $str = '';
 for($i=0;$i<$tagcount;$i++) {
  $str = $str . '{' . rand(1,1000) . '}';
 }
 return $str;

}



echo "生成分类!<br/>";
cate();

echo "生成文章!<br/>";
article();

echo "生成页面!<br/>";
page();

echo "tag!<br/>";
tag();

$zbp->Terminate();



RunTime();
?>