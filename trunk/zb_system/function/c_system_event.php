<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


function ViewList($page,$cate,$auth,$date,$tags){
	global $zbp;
	foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($page,$cate,$auth,$date,$tags);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
	}

	$zbp->title=$zbp->option['ZC_BLOG_SUBTITLE'];

	$zbp->template->display($zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']);

}

function ViewArticle(){


}

function ViewPage(){


}








function Login(){
	global $zbp;

	if (isset($zbp->membersbyname[GetVars('username')])) {
		$m=$zbp->membersbyname[GetVars('username')];
		if($m->Password == md5(GetVars('password') . $m->Guid)){
			if(GetVars('savedate')==0){
				setcookie("username", GetVars('username'),0,$zbp->cookiespath);
				setcookie("password", GetVars('password'),0,$zbp->cookiespath);
			}else{
				setcookie("username", GetVars('username'), time()+3600*24*GetVars('savedate'),$zbp->cookiespath);
				setcookie("password", GetVars('password'), time()+3600*24*GetVars('savedate'),$zbp->cookiespath);
			}
			header('Location:admin/');
		}else{
			throw new Exception($GLOBALS['lang']['error'][8]);
		}
	}else{
		throw new Exception($GLOBALS['lang']['error'][8]);
		
	}

}


function Logout(){
	global $zbp;

	setcookie('username', '',time() - 3600,$zbp->cookiespath);
	setcookie('password', '',time() - 3600,$zbp->cookiespath);
}

function CategoryPost(){
	$cate = new Category();
	if($_POST['edtID'] == 0){
		$cate->LoadInfobyArray(
			array(
				$_POST['edtID'],
				$_POST['edtName'],
				$_POST['edtOrder'],
				0,
				$_POST['edtAlias'],
				'',
				0,
				$_POST['edtPareID'],
				$_POST['edtTemplate'],
				$_POST['edtLogTemplate'],
				''
			)
		);
		$cate->Post();
		redirect('admin/category.php');
	}else{
		$cate->LoadInfoByID($_POST['edtID']);
		$cate->Name = $_POST['edtName'];
		$cate->Order = $_POST['edtOrder'];
		$cate->Alias = $_POST['edtAlias'];
		$cate->PareID = $_POST['edtPareID'];
		$cate->Template = $_POST['edtTemplate'];
		$cate->LogTemplate = $_POST['edtLogTemplate'];
		$cate->Post();
		redirect('admin/category.php');
	}
}






?>