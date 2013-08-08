<?php
require './function/c_system_base.php';

$zbp->Initialize();

$action=GetVars('act','GET');

if(!$zbp->CheckRights($action)){throw new Exception($lang['error'][6]);}

switch ($action) {
	case 'login':
		redirect('login.php');
		break;
	case 'logout':
		Logout();
		redirect('../');
		break;
	case 'admin':
		redirect('admin/');
		break;	
	case 'verify':
		Login();
		break;
	case 'search':
		redirect('../search.php?q=' . urlencode(GetVars('q','POST')));	
		break;
	case 'misc':
		require './function/c_system_misc.php';
		#echo Reload(GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ArticleEdt':
		redirect('admin/edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ArticleDel':
		DelArticle();
		$zbp->SetHint('good');
		redirect('cmd.php?act=ArticleMng');
		break;
	case 'ArticleMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ArticlePst':
		PostArticle();
		$zbp->SetHint('good');
		redirect('cmd.php?act=ArticleMng');
		break;
	case 'PageEdt':
		redirect('admin/edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PageDel':
		DelPage();
		$zbp->SetHint('good');
		redirect('cmd.php?act=PageMng');
		break;
	case 'PageMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PagePst':
		PostPage();
		$zbp->SetHint('good');
		redirect('cmd.php?act=PageMng');
		break;
	case 'CategoryMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'CategoryEdt':
		redirect('admin/category_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'CategoryPst':
		PostCategory();
		$zbp->SetHint('good');
		redirect('cmd.php?act=CategoryMng');
		break;
	case 'CommentMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberEdt':
		redirect('admin/member_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberNew':
		redirect('admin/member_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberPst':
		PostMember();
		$zbp->SetHint('good');
		redirect('cmd.php?act=MemberMng');
		break;
	case 'UploadMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'TagMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'TagEdt':
		redirect('admin/tag_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'TagPst':
		PostTag();
		$zbp->SetHint('good');
		redirect('cmd.php?act=TagMng');
		break;
	case 'TagDel':
		DelTag();
		$zbp->SetHint('good');
		redirect('cmd.php?act=TagMng');
		break;
	case 'PluginMng':
		if(GetVars('install','GET')){
			$f='InstallPlugin_' . GetVars('install','GET');
			if(function_exists($f)){$f();}
		}
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PluginDisable':
		$f='UninstallPlugin_' . GetVars('name','GET');
		if(function_exists($f)){$f();}
		DisablePlugin(GetVars('name','GET'));
		$zbp->SetHint('good');
		redirect('cmd.php?act=PluginMng');
		break;
	case 'PluginEnable':
		$install='&install=';
		$install .= EnablePlugin(GetVars('name','GET'));
		$zbp->SetHint('good');
		redirect('cmd.php?act=PluginMng' . $install);
		break;
	case 'ThemeMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ThemeSet':
		SetTheme(GetVars('theme','POST'),GetVars('style','POST'));
		$zbp->SetHint('good');
		redirect('cmd.php?act=ThemeMng');
		break;		
	case 'ModuleMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'SettingMng':
		redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;		
	default:
		# code...
		break;
}

$zbp->Terminate();

/*
Select Case strAct

	'命令列表

	Case "login" 

		Call BlogLogin()

	Case "verify"

		Call BlogVerify()

	Case "logout"

		Call BlogLogout()

	Case "admin" 

		Call BlogAdmin()

	Case "cmt"

		Call CommentPost()

	Case "tb"
		Call TrackBackPost()

	Case "vrs"
		Call ViewRights()

	Case "ArticleMng"

		Call ArticleMng()

	Case "ArticleEdt"

		Call ArticleEdt()

	Case "ArticlePst"

		Call ArticlePst()

	Case "ArticleDel"

		Call ArticleDel()

	Case "CategoryMng"

		Call CategoryMng()

	Case "CategoryEdt"

		Call CategoryEdt()

	Case "CategoryPst"

		Call CategoryPst()

	Case "CategoryDel"

		Call CategoryDel()

	Case "CommentMng"

		Call CommentMng()

	Case "CommentDel"

		Call CommentDel()

	Case "CommentEdt"

		Call CommentEdt()

	Case "CommentSav"

		Call CommentSav()

	Case "CommentGet"

		Call CommentGet()

	Case "CommentAudit"
		
		Call CommentAudit()

	Case "TrackBackMng"

		Call TrackBackMng()

	Case "TrackBackDel"

		Call TrackBackDel()

	Case "TrackBackSnd"

		Call TrackBackSnd()

	Case "UserMng"

		Call UserMng()

	Case "UserCrt"

		Call UserCrt()

	Case "UserEdt"

		Call UserEdt()

	Case "UserMod"

		Call UserMod()

	Case "UserDel"

		Call UserDel()

	Case "FileMng"

		Call FileMng()

	Case "FileSnd"

		Call FileSnd()

	Case "FileUpload"

		Call FileUpload()

	Case "FileDel"

		Call FileDel()

	Case "Search"

		Call Search()

	Case "SettingMng"

		Call SettingMng()

	Case "SettingSav"

		Call SettingSav()

	Case "TagMng"

		Call TagMng()

	Case "TagEdt"

		Call TagEdt()

	Case "TagPst"

		Call TagPst()

	Case "TagDel"

		Call TagDel()

	Case "PlugInMng"

		Call PlugInMng()

	Case "SiteInfo"

		Call SiteInfo()

	Case "SiteFileMng"

		Call SiteFileMng()

	Case "SiteFileEdt"

		Call SiteFileEdt()

	Case "SiteFilePst"

		Call SiteFilePst()

	Case "SiteFileDel"

		Call SiteFileDel()


	Case "gettburl"
		Call TrackBackUrlGet()

	Case "CommentDelBatch"

		Call CommentDelBatch()

	Case "TrackBackDelBatch"

		Call TrackBackDelBatch()

	Case "FileDelBatch"

		Call FileDelBatch()

	Case "ThemeMng"

		Call ThemeMng()

	Case "ThemeSav"

		Call ThemeSav()


	Case "LinkMng"

		Call LinkMng()

	Case "LinkSav"

		Call LinkSav()


	Case "PlugInActive"

		Call PlugInActive()

	Case "PlugInDisable"

		Call PlugInDisable()

	Case "FunctionMng"

		Call FunctionMng()

	Case "FunctionEdt"

		Call FunctionEdt()

	Case "FunctionSav"

		Call FunctionSav()

	Case "FunctionDel"

		Call FunctionDel()

	Case "AskFileReBuild"

		Call AskFileReBuild()

	Case "BlogReBuild"

		Call BlogReBuild()

	Case "FileReBuild"

		Call FileReBuild()

	Case "batch"

		Call Batch()

End Select
*/
?>