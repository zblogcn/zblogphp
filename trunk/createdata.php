<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version
 */

require_once 'zb_system/function/c_system_base.php';

$zbp->Initialize();

function article(){
	for ($i=0; $i < 1000; $i++) { 
		$a=new Post();
		$a->CateID=1;
		$a->AuthorID=1;
		$a->Tag='';
		$a->Status=ZC_LOG_STATUS_PUBLIC;
		$a->Type=ZC_LOG_TYPE_ARTICLE;
		$a->Alias='';
		$a->IsTop=false;
		$a->IsLock=false;
		$a->Title='随机文章' . GetGuid();
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
		$a->Title='随机页面' . GetGuid();
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


echo "生成文章!<br/>";
#article();

echo "生成页面!<br/>";
#page();



$zbp->Terminate();



RunTime();
?>