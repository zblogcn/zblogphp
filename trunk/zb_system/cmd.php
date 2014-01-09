<?php
require './function/c_system_base.php';

$zbp->Load();

$action=GetVars('act','GET');

foreach ($GLOBALS['Filter_Plugin_Cmd_Begin'] as $fpname => &$fpsignal) {$fpname();}

if(!$zbp->CheckRights($action)){$zbp->ShowError(6);die();}

switch ($action) {
	case 'login':
		if ($zbp->user->ID>0 && GetVars('redirect','GET')) {
			Redirect(GetVars('redirect','GET'));
		}
		if ($zbp->CheckRights('admin')) {
			Redirect('cmd.php?act=admin');
		}
		if ($zbp->user->ID==0 && GetVars('redirect','GET')) {
			setcookie("redirect", GetVars('redirect','GET'),0,$zbp->cookiespath);
		}
		Redirect('login.php');
		break;
	case 'logout':
		Logout();
		Redirect('../');
		break;
	case 'admin':
		Redirect('admin/?act=admin');
		break;	
	case 'verify':
		if(VerifyLogin()){
			if ($zbp->user->ID>0 && GetVars('redirect','COOKIE')) {
				Redirect(GetVars('redirect','COOKIE'));
			}
			Redirect('admin/?act=admin');
		}else{
			Redirect('../');
		}
		break;
	case 'search':
		$q=urlencode(trim(strip_tags(GetVars('q','POST'))));
		Redirect('../search.php?q=' . $q);	
		break;
	case 'misc':
		require './function/c_system_misc.php';
		break;
	case 'cmt':
		if(GetVars('isajax','POST')){
			Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','RespondError',PLUGIN_EXITSIGNAL_RETURN);
		}
		PostComment();
		$zbp->BuildModule();
		if(GetVars('isajax','POST')){
			die();
		}else{
			Redirect(GetVars('HTTP_REFERER','SERVER'));
		}
		break;
	case 'getcmt':
		ViewComments((int)GetVars('postid','GET'),(int)GetVars('page','GET'));
		die();
		break;
	case 'ArticleEdt':
		Redirect('admin/edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ArticleDel':
		DelArticle();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ArticleMng');
		break;
	case 'ArticleMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ArticlePst':
		PostArticle();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ArticleMng');
		break;
	case 'PageEdt':
		Redirect('admin/edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PageDel':
		DelPage();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=PageMng');
		break;
	case 'PageMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PagePst':
		PostPage();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=PageMng');
		break;
	case 'CategoryMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'CategoryEdt':
		Redirect('admin/category_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'CategoryPst':
		PostCategory();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=CategoryMng');
		break;
	case 'CategoryDel':
		DelCategory();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=CategoryMng');
		break;	
	case 'CommentDel':
		DelComment();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect($_SERVER["HTTP_REFERER"]);
		break;	
	case 'CommentChk':
		CheckComment();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect($_SERVER["HTTP_REFERER"]);
		break;
	case 'CommentBat':
		var_dump($_POST['id']);
		$zbp->BuildModule();
		break;
	case 'CommentMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberEdt':
		Redirect('admin/member_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberNew':
		Redirect('admin/member_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'MemberPst':
		PostMember();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=MemberMng');
		break;
	case 'MemberDel':
		if(DelMember()){
			$zbp->BuildModule();
			$zbp->SetHint('good');
		}else{
			$zbp->SetHint('bad');			
		}
		Redirect('cmd.php?act=MemberMng');
		break;
	case 'UploadMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'UploadPst':
		PostUpload();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=UploadMng');
		break;
	case 'UploadDel':
		DelUpload();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=UploadMng');
		break;		
	case 'TagMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'TagEdt':
		Redirect('admin/tag_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'TagPst':
		PostTag();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=TagMng');
		break;
	case 'TagDel':
		DelTag();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=TagMng');
		break;
	case 'PluginMng':
		if(GetVars('install','GET')){
			InstallPlugin(GetVars('install','GET'));
		}
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PluginDis':
		UninstallPlugin(GetVars('name','GET'));
		DisablePlugin(GetVars('name','GET'));
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=PluginMng');
		break;
	case 'PluginEnb':
		$install='&install=';
		$install .= EnablePlugin(GetVars('name','GET'));
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=PluginMng' . $install);
		break;
	case 'ThemeMng':
		if(GetVars('install','GET')){
			InstallPlugin(GetVars('install','GET'));
		}
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ThemeSet':
		$install='&install=';
		$install .=SetTheme(GetVars('theme','POST'),GetVars('style','POST'));
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ThemeMng' . $install);
		break;
	case 'SidebarSet':
		$zbp->BuildModule();
		SetSidebar();
		break;
	case 'ModuleEdt':
		Redirect('admin/module_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ModulePst':
		PostModule();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ModuleMng');
		break;
	case 'ModuleDel':
		DelModule();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ModuleMng');
		break;
	case 'ModuleMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'SettingMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;	
	case 'SettingSav':
		SaveSetting();
		$zbp->BuildModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=SettingMng');
		break;	
	default:
		# code...
		break;
}
