<?php
require './function/c_system_base.php';

$zbp->Load();

$action=GetVars('act','GET');

if(!$zbp->CheckRights($action)){$zbp->ShowError(6);die();}

switch ($action) {
	case 'login':
		if ($zbp->CheckRights('admin')) {
			Redirect('cmd.php?act=admin');
		}
		Redirect('login.php');
		break;
	case 'logout':
		Logout();
		Redirect('../');
		break;
	case 'admin':
		Redirect('admin/');
		break;	
	case 'verify':
		if(VerifyLogin()){
			header('Location:admin/');
		}else{
			Redirect('../');
		}
		break;
	case 'search':
		Redirect('../search.php?q=' . urlencode(trim(GetVars('q','POST'))));	
		break;
	case 'misc':
		require './function/c_system_misc.php';
		#echo Reload(GetVars('QUERY_STRING','SERVER'));
		break;
	case 'cmt':
		if(GetVars('isajax','POST')){
			Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','RespondError',PLUGIN_EXITSIGNAL_RETURN);
		}
		PostComment();
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
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ArticleMng');
		break;
	case 'ArticleMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ArticlePst':
		PostArticle();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ArticleMng');
		break;
	case 'PageEdt':
		Redirect('admin/edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PageDel':
		DelPage();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=PageMng');
		break;
	case 'PageMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PagePst':
		PostPage();
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
		$zbp->SetHint('good');
		Redirect('cmd.php?act=CategoryMng');
		break;
	case 'CommentBat':
		var_dump($_POST['id']);
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
		$zbp->SetHint('good');
		Redirect('cmd.php?act=MemberMng');
		break;
	case 'MemberDel':
		if(DelMember()){
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
		$zbp->SetHint('good');
		Redirect('cmd.php?act=TagMng');
		break;
	case 'TagDel':
		DelTag();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=TagMng');
		break;
	case 'PluginMng':
		if(GetVars('install','GET')){
			InstallPlugin(GetVars('install','GET'));
		}
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'PluginDisable':
		UninstallPlugin(GetVars('name','GET'));
		DisablePlugin(GetVars('name','GET'));
		$zbp->SetHint('good');
		Redirect('cmd.php?act=PluginMng');
		break;
	case 'PluginEnable':
		$install='&install=';
		$install .= EnablePlugin(GetVars('name','GET'));
		$zbp->SetHint('good');
		Redirect('cmd.php?act=PluginMng' . $install);
		break;
	case 'ThemeMng':
		Redirect('admin/?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ThemeSet':
		SetTheme(GetVars('theme','POST'),GetVars('style','POST'));
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ThemeMng');
		break;
	case 'SidebarSet':
		SetSidebar();
		break;
	case 'ModuleEdt':
		Redirect('admin/module_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ModulePst':
		PostModule();
		$zbp->SetHint('good');
		Redirect('cmd.php?act=ModuleMng');
		break;
	case 'ModuleDel':
		DelModule();
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
		$zbp->SetHint('good');
		Redirect('cmd.php?act=SettingMng');
		break;	
	default:
		# code...
		break;
}

?>