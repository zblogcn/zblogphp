<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require 'zb_system/function/c_system_base.php';

$zbp->Initialize();

function article(){
	for ($i=0; $i < 10000; $i++) { 
		$a=new Post();
		$a->CateID=rand(1,200);
		$a->AuthorID=1;
		$a->Tag=getTagStr(rand(0,19));
		$a->Status=ZC_LOG_STATUS_PUBLIC;
		$a->Type=ZC_LOG_TYPE_ARTICLE;
		$a->Alias='';
		$a->IsTop=false;
		$a->IsLock=false;
		$a->Title=getRandStr(rand(8,14));
		$a->Intro='欢迎使用Z-BlogPHP' . GetGuid();
		$a->Content='欢迎使用Z-BlogPHP' . GetGuid() . '<br/>' . GetGuid();
		$a->IP=GetGuestIP();
		$a->PostTime=time();
		$a->CommNums=0;
		$a->ViewNums=0;
		$a->Template='';
		$a->Meta='';
		$a->Post();
	}
}

function page(){
	for ($i=0; $i < 1000; $i++) { 
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
		$a->Content='这是一个留言本.' . GetGuid() . '<br/>' . GetGuid();
		$a->IP=GetGuestIP();
		$a->PostTime=time();
		$a->CommNums=0;
		$a->ViewNums=0;
		$a->Template='';
		$a->Meta='';
		$a->Post();  
	}
}

function cate(){
	for ($i=0; $i < 200; $i++) { 
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
		$cate->Post();
  	}
}

function tag(){
	for ($i=0; $i < 5000; $i++) { 
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
		$tag->Post();
  	}
}

function getChineseChar() {
 $unidec = rand(hexdec('4e00'), hexdec('9fa5'));
 $unichr = '&#' . $unidec . ';';
 $zhcnchr = mb_convert_encoding($unichr, "UTF-8", "HTML-ENTITIES");
 return $zhcnchr;
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
  $str = $str . '{' . rand(1,5000) . '}';
 }
 return $str;

}

#echo chr(0xE4).chr(0xB8).chr(0x80);
#echo chr(0xE8).chr(0xB7).chr(0x8B);

echo "生成分类!<br/>";
#cate();

echo "生成文章!<br/>";
article();

echo "生成页面!<br/>";
#page();

echo "tag!<br/>";
#tag();

$zbp->Terminate();



RunTime();
?>