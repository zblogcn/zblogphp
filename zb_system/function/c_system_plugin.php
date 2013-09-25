<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */

#接口模式复制自Z-Blog ASP版

define('PLUGIN_EXITSIGNAL_NONE', '');
define('PLUGIN_EXITSIGNAL_RETURN', 'return');
define('PLUGIN_EXITSIGNAL_BREAK', 'break');


#定义总插件激活函数列表
$plugins=array();



/*
'*********************************************************
' 目的： 注册插件函数，由每个插件主动调用
'*********************************************************
*/
function RegisterPlugin($strPluginName,$strPluginActiveFunction){

	$GLOBALS['plugins'][$strPluginName]=$strPluginActiveFunction;

}







/*
'*********************************************************
' 目的： 激活插件函数
'*********************************************************
*/
function ActivePlugin(){

	foreach ($GLOBALS['plugins'] as &$sPluginActiveFunctions) {
		$sPluginActiveFunctions();
	}

}




/*
'*********************************************************
' 目的： 安装插件函数，只运行一次
'*********************************************************
*/
function InstallPlugin($strPluginName){

	if(function_exists('InstallPlugin_' . $strPluginName)==true){
		$f='InstallPlugin_' . $strPluginName;
		$f();
	}

}




/*
'*********************************************************
' 目的： 删除插件函数，只运行一次
'*********************************************************
*/
function UninstallPlugin($strPluginName){

	if(function_exists('UninstallPlugin_' . $strPluginName)==true){
		$f='UninstallPlugin_' . $strPluginName;
		$f();
	}

}





/*
'*********************************************************
' 目的：挂上Action接口
' 参数：'plugname:接口名称
		'actioncode:要执行的语句，要转义为Execute可执行语句
'*********************************************************
*/
//function Add_Action_Plugin($plugname,$actioncode){
//	$GLOBALS[$plugname][]=$actioncode;
//}




/*
'*********************************************************
' 目的：挂上Filter接口
' 参数：'plugname:接口名称
		'functionname:要挂接的函数名
		'exitsignal:return,break,continue
'*********************************************************
*/
function Add_Filter_Plugin($plugname,$functionname,$exitsignal=PLUGIN_EXITSIGNAL_NONE){
	$GLOBALS[$plugname][$functionname]=$exitsignal;
}




/*
'*********************************************************
' 目的：挂上Response接口
' 参数：'plugname:接口名称
		'parameter:要写入的内容
'*********************************************************
*/
//function Add_Response_Plugin($plugname,$functionname){
//	$GLOBALS[$plugname][]=$functionname;
//}











################################################################################################################
#base里的





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Autoload
'参数:
'说明:定义autoload魔术方法
'调用:
'**************************************************>
*/
$Filter_Plugin_Autoload=array();







################################################################################################################
#ZBP类里的接口





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_Call
'参数:
'说明:Zbp类的魔术方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_Call=array();







/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_CheckRights
'参数:
'说明:Zbp类的检查权限接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_CheckRights=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_ShowError
'参数:
'说明:Zbp类的显示错误接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_ShowError=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_BuildTemplate
'参数:
'说明:Zbp类的重新编译模板接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_BuildTemplate=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_MakeTemplatetags
'参数:
'说明:Zbp类的生成模板标签接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_MakeTemplatetags=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_BuildModule
'参数:
'说明:Zbp类的生成模块内容的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_BuildModule=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_BuildModule
'参数:
'说明:Zbp类的加载接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_Load=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_BuildModule
'参数:
'说明:Zbp类的终结接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Zbp_Terminate=array();






################################################################################################################
#前台view,index





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewAuto_Begin
'参数:
'说明:定义列表输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_ViewAuto_Begin=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Begin
'参数:
'说明:定义列表输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_ViewList_Begin=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewPost_Begin
'参数:
'说明:定义列表输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_ViewPost_Begin=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Template
'参数:
'说明:
'调用:
'**************************************************>
*/
$Filter_Plugin_ViewList_Template=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewPost_Template
'参数:
'说明:
'调用:
'**************************************************>
*/
$Filter_Plugin_ViewPost_Template=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewComments_Template
'参数:
'说明:
'调用:
'**************************************************>
*/
$Filter_Plugin_ViewComments_Template=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Index_Begin
'参数:
'说明:定义index.php接口 起动
'调用:
'**************************************************>
*/
$Filter_Plugin_Index_Begin=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Index_End
'参数:
'说明:定义index.php接口 结束
'调用:
'**************************************************>
*/
$Filter_Plugin_Index_End=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Html_Js_Add
'参数:
'说明:c_html_js_add.php脚本调用
'调用:
'**************************************************>
*/
$Filter_Plugin_Html_Js_Add=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Search_Begin
'参数:
'说明:搜索页接口，可以接管搜索页。
'调用:
'**************************************************>
*/
$Filter_Plugin_Search_Begin=array();





################################################################################################################
#CMD里的接口





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Cmd_Begin
'参数:
'说明:cmd.php的启动接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Cmd_Begin=array();





################################################################################################################
#后台里的接口





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Begin
'参数:
'说明:后台管理页的启动接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_Begin=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Header
'参数:
'说明:定义后台首页header接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_Header=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Footer
'参数:
'说明:定义后台首页footer接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_Footer=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_LeftMenu
'参数:
'说明:定义后台左侧栏接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_LeftMenu=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_TopMenu
'参数:
'说明:定义后台顶部导航栏接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_TopMenu=array();


/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_SiteInfo_SubMenu
'参数:
'说明:后台首页SubMenu
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_SiteInfo_SubMenu=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ArticleMng_SubMenu
'参数:
'说明:文章管理SubMenu
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_ArticleMng_SubMenu=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_PageMng_SubMenu
'参数:
'说明:页面管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_PageMng_SubMenu=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CategoryMng_SubMenu
'参数:
'说明:分类管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_CategoryMng_SubMenu=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CommentMng_SubMenu
'参数:
'说明:评论管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_CommentMng_SubMenu=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_MemberMng_SubMenu
'参数:
'说明:用户管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_MemberMng_SubMenu=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_UploadMng_SubMenu
'参数:
'说明:
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_UploadMng_SubMenu=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_TagMng_SubMenu
'参数:
'说明:标签管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_TagMng_SubMenu=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_PluginMng_SubMenu
'参数:
'说明:插件管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_PluginMng_SubMenu=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ThemeMng_SubMenu
'参数:
'说明:主题管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_ThemeMng_SubMenu=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ModuleMng_SubMenu
'参数:
'说明:模块管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_ModuleMng_SubMenu=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_SettingMng_SubMenu
'参数:
'说明:设置管理
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_SettingMng_SubMenu=array();




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Begin
'参数:
'说明:文章页面编辑页开始接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Edit_Begin=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_End
'参数:
'说明:文章页面编辑页结束接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Edit_End=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response
'参数:
'说明:文章页面编辑1号输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Edit_Response=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response2
'参数:
'说明:文章页面编辑2号输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Edit_Response2=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response3
'参数:
'说明:文章页面编辑3号输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Edit_Response3=array();







/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Edit_Response
'参数:
'说明:分类编辑页输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Category_Edit_Response=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Edit_Response
'参数:
'说明:标签编辑页输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Tag_Edit_Response=array();







/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Edit_Response
'参数:
'说明:会员编辑页输出接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Member_Edit_Response=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Js_Add
'参数:
'说明:c_admin_js_add.php脚本页的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Admin_Js_Add=array();






################################################################################################################
#Event里的接口




/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostModule_Core
'参数:
'说明:模块编辑的核心接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostModule_Core=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostMember_Core
'参数:
'说明:会员编辑的核心接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostMember_Core=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostTag_Core
'参数:
'说明:标签编辑的核心接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostTag_Core=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostCategory_Core
'参数:
'说明:分类编辑的核心接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostCategory_Core=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostComment_Core
'参数:
'说明:评论发表的核心接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostComment_Core=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostArticle_Core
'参数:
'说明:文章编辑的核心接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostArticle_Core=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostPage_Core
'参数:
'说明:页面编辑的核心接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostPage_Core=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostMember_Succeed
'参数:
'说明:会员编辑成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostMember_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostTag_Succeed
'参数:
'说明:标签编辑成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostTag_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostCategory_Succeed
'参数:
'说明:分类编辑成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostCategory_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostComment_Succeed
'参数:
'说明:评论发表成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostComment_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostPage_Succeed
'参数:
'说明:页面编辑成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostPage_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostArticle_Succeed
'参数:
'说明:文章编辑成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostArticle_Succeed=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostModule_Succeed
'参数:
'说明:模块编辑成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_PostModule_Succeed=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelMember_Succeed
'参数:
'说明:会员删除成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_DelMember_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelTag_Succeed
'参数:
'说明:标签删除成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_DelTag_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelCategory_Succeed
'参数:
'说明:分类删除成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_DelCategory_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelComment_Succeed
'参数:
'说明:评论删除成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_DelComment_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelPage_Succeed
'参数:
'说明:页面删除成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_DelPage_Succeed=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelArticle_Succeed
'参数:
'说明:文章删除成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_DelArticle_Succeed=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelModule_Succeed
'参数:
'说明:模块删除成功的接口
'调用:
'**************************************************>
*/
$Filter_Plugin_DelModule_Succeed=array();






################################################################################################################
#类里的接口






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Call
'参数:
'说明:Post类的魔术方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Post_Call=array();



/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Comment_Call
'参数:
'说明:Comment类的魔术方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Comment_Call=array();



/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Call
'参数:
'说明:Tag类的魔术方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Tag_Call=array();



/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Call
'参数:
'说明:Category类的魔术方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Category_Call=array();



/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Call
'参数:
'说明:Member类的魔术方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Member_Call=array();





/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Mebmer_Avatar
'参数:
'说明:Mebmer类的Avatar接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Mebmer_Avatar=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_SaveFile
'参数:
'说明:Upload类的SaveFile方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Upload_SaveFile=array();







/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_SaveBase64File=
'参数:
'说明:Upload类的SaveBase64File方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Upload_SaveBase64File=array();






/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_Url
'参数:
'说明:Upload类的Url方法接口
'调用:
'**************************************************>
*/
$Filter_Plugin_Upload_Url=array();






?>