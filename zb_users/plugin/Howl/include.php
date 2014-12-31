<?php
$actions_name_howl = array(
	'login' => '登录',
	'logout' => '登出',
	'verify' => '验证',
	'admin' => '管理',
	'search' => '搜索',
	'misc' => '杂项',
	'feed' => '查看RSS',
	'cmt' => '发表评论',
	'getcmt' => '获取评论',
	'ArticleEdt' => '文章编辑',
	'ArticlePst' => '文章发布',
	'ArticleDel' => '文章删除',
	'ArticlePub' => '公开发表',
	'PageEdt' => '页面编辑',
	'PagePst' => '页面发布',
	'PageDel' => '页面删除',
	'CategoryEdt' => '分类编辑',
	'CategoryPst' => '分类发布',
	'CategoryDel' => '分类删除',
	'CommentEdt' => '评论编辑',
	'CommentSav' => '评论发布',
	'CommentDel' => '评论删除',
	'CommentChk' => '评论审核',
	'CommentBat' => '批量管理评论',
	'MemberEdt' => '会员编辑',
	'MemberPst' => '会员发布',
	'MemberDel' => '会员删除',
	'MemberNew' => '新建会员',
	'TagEdt' => '标签编辑',
	'TagPst' => '标签发布',
	'TagDel' => '标签删除',
	'TagNew' => '新建标签',
	'PluginEnb' => '插件启用',
	'PluginDis' => '插件禁用',
	'UploadPst' => '上传附件',
	'UploadDel' => '删除附件',
	'ModuleEdt' => '模块编辑',
	'ModulePst' => '模块发布',
	'ModuleDel' => '模块删除',
	'ThemeSet' => '主题设置',
	'SidebarSet' => '侧栏设置',
	'SettingSav' => '配置保存',
	'ArticleMng' => '文章管理',
	'PageMng' => '页面管理',
	'CategoryMng' => '分类管理',
	'SettingMng' => '配置管理',
	'TagMng' => '标签管理',
	'CommentMng' => '评论管理',
	'UploadMng' => '附件管理',
	'MemberMng' => '用户管理',
	'ThemeMng' => '主题管理',
	'PluginMng' => '插件管理',
	'ModuleMng' => '模块管理',
	'ArticleAll' => '所有文章权限',
	'PageAll' => '所有文章权限',
	'CategoryAll' => '所有分类权限',
	'CommentAll' => '所有评论权限',
	'MemberAll' => '所有用户权限',
	'TagAll' => '所有标签权限',
	'UploadAll' => '所有附件权限',
	'root' => '超级权限',
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
	$a = array();
	$a[1] = array();
	$a[2] = array();
	$a[3] = array();
	$a[4] = array();
	$a[5] = array();
	$a[6] = array();
	$a[0] = array();
	
	if($zbp->Config('Howl')->HasKey('Group1')){$a[1]=$zbp->Config('Howl')->Group1;}
	if($zbp->Config('Howl')->HasKey('Group2')){$a[2]=$zbp->Config('Howl')->Group2;}
	if($zbp->Config('Howl')->HasKey('Group3')){$a[3]=$zbp->Config('Howl')->Group3;}	
	if($zbp->Config('Howl')->HasKey('Group4')){$a[4]=$zbp->Config('Howl')->Group4;}
	if($zbp->Config('Howl')->HasKey('Group5')){$a[5]=$zbp->Config('Howl')->Group5;}
	if($zbp->Config('Howl')->HasKey('Group6')){$a[6]=$zbp->Config('Howl')->Group6;}
	if($zbp->Config('Howl')->HasKey('User')){$a[0] = json_decode($zbp->Config('Howl')->User);}
	
	$g = $zbp->user->Level;
	
	if(($g < 6) && isset($a[0] -> $action)) 
	{
		$GLOBALS['Filter_Plugin_Zbp_CheckRights']['Howl_CheckRights'] = PLUGIN_EXITSIGNAL_RETURN;
		$id = $zbp->user->ID;
		return (boolean)isset($a[0] -> $action -> $id);
		/*数据结构：
		$a = array(
			"action1" => array(
				"userid" => "userid",
				"userid" => "userid"
			)
		)
		*/
	}
	
	if(array_key_exists($action, $a[$g])){
		$GLOBALS['Filter_Plugin_Zbp_CheckRights']['Howl_CheckRights'] = PLUGIN_EXITSIGNAL_RETURN;
		return (boolean)$a[$g][$action];
	}

}



?>