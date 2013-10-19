<?php
$actions_name_howl = array(
	'login' => '登陆',
	'logout' => '退出登陆',
	'verify' => '验证',
	'admin' => '',
	'search' => '搜索',
	'misc' => '',
	'feed' => '查看RSS',
	'cmt' => '',
	'getcmt' => '',
	'ArticleEdt' => '文章编辑',
	'ArticlePst' => '文章发布',
	'ArticleDel' => '文章删除',
	'ArticlePub' => '',
	'PageEdt' => '页面编辑',
	'PagePst' => '页面发布',
	'PageDel' => '页面删除',
	'CategoryEdt' => '分类编辑',
	'CategoryPst' => '分类发布',
	'CategoryDel' => '分类删除',
	'CommentEdt' => '评论编辑',
	'CommentSav' => '评论发布',
	'CommentDel' => '评论删除',
	'CommentChk' => '',
	'CommentBat' => '',
	'MemberEdt' => '',
	'MemberPst' => '',
	'MemberDel' => '',
	'MemberNew' => '',
	'TagEdt' => '',
	'TagPst' => '',
	'TagDel' => '',
	'TagNew' => '',
	'PluginEnb' => '',
	'PluginDis' => '',
	'UploadPst' => '',
	'UploadDel' => '',
	'ModuleEdt' => '',
	'ModulePst' => '',
	'ModuleDel' => '',
	'ThemeSet' => '',
	'SidebarSet' => '',
	'SettingSav' => '',
	'ArticleMng' => '',
	'PageMng' => '',
	'CategoryMng' => '',
	'SettingMng' => '',
	'TagMng' => '',
	'CommentMng' => '',
	'UploadMng' => '',
	'MemberMng' => '',
	'ThemeMng' => '',
	'PluginMng' => '',
	'ModuleMng' => '',
	'ArticleAll' => '',
	'PageAll' => '',
	'CategoryAll' => '',
	'CommentAll' => '',
	'MemberAll' => '',
	'TagAll' => '',
	'UploadAll' => '',
	'root' => '',
);

#注册插件
RegisterPlugin("Howl","ActivePlugin_Howl");


function ActivePlugin_Howl() {

	Add_Filter_Plugin('Filter_Plugin_Zbp_CheckRights','Howl_CheckRights');

}

function InstallPlugin_Howl(){
	global $zbp;
	$zbp->Config('Howl')->version='1.0';
	$zbp->SaveConfig('Howl');
}

function UninstallPlugin_Howl(){
	global $zbp;
	//$zbp->DelConfig('Howl');
}


function Howl_CheckRights(&$action){
	global $zbp;
$a=array();
$a[1]=array();
$a[2]=array();
$a[3]=array();
$a[4]=array();
$a[5]=array();
$a[6]=array();

if($zbp->Config('Howl')->HasKey('Group1')){$a[1]=$zbp->Config('Howl')->Group1;}
if($zbp->Config('Howl')->HasKey('Group2')){$a[2]=$zbp->Config('Howl')->Group2;}
if($zbp->Config('Howl')->HasKey('Group3')){$a[3]=$zbp->Config('Howl')->Group3;}	
if($zbp->Config('Howl')->HasKey('Group4')){$a[4]=$zbp->Config('Howl')->Group4;}
if($zbp->Config('Howl')->HasKey('Group5')){$a[5]=$zbp->Config('Howl')->Group5;}
if($zbp->Config('Howl')->HasKey('Group6')){$a[6]=$zbp->Config('Howl')->Group6;}


$g=$zbp->user->Level;
if(array_key_exists($action, $a[$g])){
	$GLOBALS['Filter_Plugin_Zbp_CheckRights']['Howl_CheckRights']=PLUGIN_EXITSIGNAL_RETURN;
	return (boolean)$a[$g][$action];
}

}



?>