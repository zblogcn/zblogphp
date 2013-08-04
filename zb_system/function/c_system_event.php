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
	
	$pagebar=new Pagebar();
	$pagebar->PageCount=5;
	$pagebar->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
	$pagebar->PageBarCount=10;
	$pagebar->UrlRule='{%host%}?page={%page%}';

	$articles=$zbp->GetArticleList(
		'',
		array('log_PostTime'=>'DESC'),
		array(($pagebar->PageNow-1) * $pagebar->PageCount,$pagebar->PageCount),
		array('pagebar'=>$pagebar)
	);

	$zbp->template->SetTags('title',$pagebar->PageNow);
	$zbp->template->SetTags('articles',$articles);
	$zbp->template->SetTags('pagebar',$pagebar);

	$zbp->template->display($zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']);

}

function ViewPost($id,$alias){
	global $zbp;


	$article = new Post;
	$article->LoadInfoByID($id);

	$zbp->template->SetTags('title',$article->Title);
	$zbp->template->SetTags('article',$article);

	$zbp->template->display($zbp->option['ZC_ARTICLE_DEFAULT_TEMPLATE']);
}








function Login(){
	global $zbp;

	if (isset($zbp->membersbyname[GetVars('username','POST')])) {
		$m=$zbp->membersbyname[GetVars('username','POST')];
		if($m->Password == md5(GetVars('password','POST') . $m->Guid)){
			if(GetVars('savedate')==0){
				setcookie("username", GetVars('username','POST'),0,$zbp->cookiespath);
				setcookie("password", GetVars('password','POST'),0,$zbp->cookiespath);
			}else{
				setcookie("username", GetVars('username','POST'), time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
				setcookie("password", GetVars('password','POST'), time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
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
		redirect('cmd.php?act=CategoryMng');
	}else{
		$cate->LoadInfoByID($_POST['edtID']);
		$cate->Name = $_POST['edtName'];
		$cate->Order = $_POST['edtOrder'];
		$cate->Alias = $_POST['edtAlias'];
		$cate->ParentID = $_POST['edtPareID'];
		$cate->Template = $_POST['edtTemplate'];
		$cate->LogTemplate = $_POST['edtLogTemplate'];
		$cate->Post();
		redirect('cmd.php?act=CategoryMng');
	}
}






?>