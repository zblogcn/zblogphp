<?php

/**
 * 插件接口相关
 * 接口模式复制自Z-Blog ASP版
 * @package Z-BlogPHP
 * @subpackage System/Plugin 操作API
 */

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/*
 * 插件运行中断方式：''
 */
define('PLUGIN_EXITSIGNAL_NONE', '');
/*
 * 插件中断方式：return
 */
define('PLUGIN_EXITSIGNAL_RETURN', 'return');
/*
 * 插件中断方式：break
 */
define('PLUGIN_EXITSIGNAL_BREAK', 'break');

//定义总插件激活列表
$GLOBALS['plugins'] = array();

//定义总接口列表，1.5版启用，逐渐过度到hooks
$GLOBALS['hooks'] = array();

/**
 * 注册插件函数，由每个插件主动调用.
 *
 * @param string $strPluginName           插件ID
 * @param string $strPluginActiveFunction 插件激活时执行的函数名
 *
 * @return void
 */
function RegisterPlugin($strPluginName, $strPluginActiveFunction)
{
    $GLOBALS['plugins'][$strPluginName] = $strPluginActiveFunction;
}

/**
 * 插件安装函数，只在插件安装时运行一次
 *
 * @param string $strPluginName 插件ID
 *
 * @return void
 */
function InstallPlugin($strPluginName)
{
    $f = 'InstallPlugin_' . $strPluginName;
    if (function_exists($f)) {
        $f();
    }
}

/**
 * 插件删除函数，只在插件删除时运行一次
 *
 * @param $strPluginName 插件ID
 *
 * @return void
 */
function UninstallPlugin($strPluginName)
{
    $f = 'UninstallPlugin_' . $strPluginName;
    if (function_exists($f) == true) {
        $f();
    }
}

/**
 * 创建插件接口
 *
 * @param $strPluginFilter 插件接口
 *
 * @return boolean
 */
function DefinePluginFilter($strPluginFilter)
{
    if (!isset($GLOBALS['hooks'][$strPluginFilter])) {
        $GLOBALS['hooks'][$strPluginFilter] = array();
        $GLOBALS[$strPluginFilter] = &$GLOBALS['hooks'][$strPluginFilter];

        return true;
    }

    return false;
}

/**
 * 检查插件接口
 *
 * @param $strPluginFilter 插件接口
 *
 * @return boolean
 */
function ExistsPluginFilter($strPluginFilter)
{
    return isset($GLOBALS['hooks'][$strPluginFilter]);
}

/**
 * 调用插件接口(没用)
 *
 * @param $strPluginFilter 插件接口
 *
 * @return array
 */
function &UsingPluginFilter($strPluginFilter)
{
    if (isset($GLOBALS['hooks'][$strPluginFilter])) {
        return $GLOBALS['hooks'][$strPluginFilter];
    }

    return array();
}

/**
 * 移除插件接口
 *
 * @param $strPluginFilter 插件接口
 *
 * @return boolean
 */
function RemovePluginFilter($strPluginFilter)
{
    if (isset($GLOBALS['hooks'][$strPluginFilter])) {
        unset($GLOBALS[$strPluginFilter]);
        unset($GLOBALS['hooks'][$strPluginFilter]);

        return true;
    }

    return false;
}

/**
 * 设置插件信号
 *
 * @param $plugname
 * @param $function
 * @param $signal
 *
 * @return boolean
 */
function SetPluginSignal($plugname, $function, $signal = 'PLUGIN_EXITSIGNAL_NONE')
{
    $GLOBALS['hooks'][$plugname][$function] = $signal;

    return true;
}

/**
 * 挂上Filter接口
 *
 * @param $plugname 接口名称
 * @param $functionname 要挂接的函数名
 * @param $exitsignal :return,break,continue
 *
 * @return boolean
 */
function Add_Filter_Plugin($plugname, $functionname, $exitsignal = PLUGIN_EXITSIGNAL_NONE)
{
    if (isset($GLOBALS['hooks'][$plugname])) {
        if (!isset($GLOBALS['hooks'][$plugname][$functionname])) {
            $GLOBALS['hooks'][$plugname][$functionname] = $exitsignal;

            return true;
        }
    }

    return false;
}

/**
 * 卸载Filter接口的某项挂载函数
 *
 * @param $plugname 接口名称
 * @param $functionname 要挂接的函数名
 *
 * @return boolean
 */
function Remove_Filter_Plugin($plugname, $functionname)
{
    if (isset($GLOBALS['hooks'][$plugname])) {
        if (isset($GLOBALS['hooks'][$plugname][$functionname])) {
            unset($GLOBALS['hooks'][$plugname][$functionname]);

            return true;
        }
    }

    return false;
}

//###############################################################################################################
//<c_system_dubug,c_system_common里的接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Debug_Handler
'参数:$type 类型(Shutdown|Exception|Error) $error 错误数据(对象或数组)
'说明:定义Debug_Shutdown_Handler,Debug_Exception_Handler,Debug_Error_Handler函数的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Debug_Handler');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Debug_Display
'参数:
'说明:定义ZBlogException的Display函数的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Debug_Display');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Autoload
'参数:$classname
'说明:监控autoload魔术方法
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Autoload');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Logs
'参数:$s,$iserror
'说明:监控记录函数
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Logs');

//###############################################################################################################
//<DbSql类里的接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DbSql_Filter
'参数:$method, $args
'说明:DbSql类的SQL过滤和统计方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DbSql_Filter');

//###############################################################################################################
//<ZBP类里的接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_Call
'参数:$method, $args
'说明:Zbp类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_Call');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_Get
'参数:$name
'说明:Zbp类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_Get');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_Set
'参数:$name,$value
'说明:Zbp类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_Set');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_CheckRights
'参数:$action
'说明:Zbp类的检查权限接口(检查当前用户)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_CheckRights');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_ShowError
'参数:$idortext
'说明:Zbp类的显示错误接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_ShowError');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_ShowValidCode
'参数:$id
'说明:Zbp类的显示验证码接口，具有唯一性；
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_ShowValidCode');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_CheckValidCode
'参数:$vaidcode,$id
'说明:Zbp类的比对验证码接口，具有唯一性；
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_CheckValidCode');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_BuildTemplate
'参数:$template
'说明:Zbp类的重新编译模板接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_BuildTemplate');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_BuildTemplate_End
'参数:$template
'说明:Zbp类的针对多套模板的重编译
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_BuildTemplate_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_MakeTemplatetags
'参数:$template
'说明:Zbp类的生成模板标签接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_MakeTemplatetags');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_BuildModule
'参数:
'说明:Zbp类的生成模块内容的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_BuildModule');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_PreLoad
'参数:
'说明:Zbp类的预加载接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_PreLoad');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_Load
'参数:
'说明:Zbp类的加载接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_Load');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_Load_Pre
'参数:
'说明:Zbp类的加载(预处理)接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_Load_Pre');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_PrepareTemplate
'参数:&$theme, &$template_dirname
'说明:Zbp类的PrepareTemplate接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_PrepareTemplate');


/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_LoadManage
'参数:
'说明:Zbp类的后台管理初始加载接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_LoadManage');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_Terminate
'参数:
'说明:Zbp类的终结接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_Terminate');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Zbp_LoadLanguage
'参数:&$lang
'说明:Zbp类的加载语言包的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Zbp_LoadLanguage');

//###############################################################################################################
//<前台view,index>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Index_Begin
'参数:
'说明:定义index.php接口 起动
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Index_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Index_End
'参数:
'说明:定义index.php接口 结束
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Index_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Html_Js_Add
'参数:
'说明:c_html_js_add.php脚本接口，允许插件在c_html_js_add.php内输出内容
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Html_Js_Add');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Html_Js_ZbpConfig
'参数:
'说明:c_html_js_add.php脚本接口，允许插件设置zbpConfig
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Html_Js_ZbpConfig');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Search_Begin
'参数:
'说明:搜索页接口，可以接管搜索页。
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Search_Begin');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Search_End
'参数:
'说明:搜索接口 结束
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Search_End');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Feed_Begin
'参数:
'说明:Feed页接口，可以接管Feed页。
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Feed_Begin');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Feed_End
'参数:
'说明:Feed页接口 结束
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Feed_End');

//###############################################################################################################
//<cmd.php里的接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Cmd_Begin
'参数:
'说明:cmd.php的启动接口,可以在这里拦截各种action
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Cmd_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Cmd_End
'参数:
'说明:cmd.php的结束接口,可以在这里拦截各种action之后的
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Cmd_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Cmd_Ajax
'参数:
'说明:cmd.php的Ajax命令专用接口，插件需要自行判断权限
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Cmd_Ajax');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Misc_Begin
'参数:$type 类型
'说明:c_system_misc.php的启动接口,可以在这里拦截各种type
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Misc_Begin');

//###############################################################################################################
//<后台管理里的接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Login_Header
'参数:
'说明:定义Login.php首页header接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Login_Header');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Other_Header
'参数:
'说明:定义其它页的header接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Other_Header');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Begin
'参数:
'说明:后台管理页的启动接口,可以拦截后台管理请求实现自己的管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Other_Action
'参数:
'说明:后台管理页的拦截后台管理请求实现自己的Action
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_Other_Action');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_End
'参数:
'说明:后台管理页的终结接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Header
'参数:
'说明:定义后台首页header接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_Header');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Hint
'参数:
'说明:定义后台首页hint接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_Hint');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Footer
'参数:
'说明:定义后台首页footer接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_Footer');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_LeftMenu
'参数:&$leftmenus
'说明:定义后台左侧栏接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_LeftMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_TopMenu
'参数:&$topmenus
'说明:定义后台顶部导航栏接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_TopMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_SiteInfo_SubMenu
'参数:
'说明:后台首页SubMenu
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_SiteInfo_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ArticleMng_SubMenu
'参数:
'说明:文章管理SubMenu
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_ArticleMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_PageMng_SubMenu
'参数:
'说明:页面管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_PageMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CategoryMng_SubMenu
'参数:
'说明:分类管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_CategoryMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CommentMng_SubMenu
'参数:
'说明:评论管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_CommentMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_MemberMng_SubMenu
'参数:
'说明:用户管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_MemberMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_UploadMng_SubMenu
'参数:
'说明:
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_UploadMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_TagMng_SubMenu
'参数:
'说明:标签管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_TagMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_PluginMng_SubMenu
'参数:
'说明:插件管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_PluginMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ThemeMng_SubMenu
'参数:
'说明:主题管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_ThemeMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ModuleMng_SubMenu
'参数:
'说明:模块管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_ModuleMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_SettingMng_SubMenu
'参数:
'说明:设置管理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_SettingMng_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ArticleMng_Core
'参数:$s, $w, $or, $l, $op
'说明:文章管理页的核心接口(1.7新加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_ArticleMng_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_PageMng_Core
'参数:$s, $w, $or, $l, $op
'说明:文章管理页的核心接口(1.7新加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_PageMng_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CommentMng_Core
'参数:$s, $w, $or, $l, $op
'说明:评论管理页的核心接口(1.7新加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_CommentMng_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CategoryMng_Core
'参数:$s, $w, $or, $l, $op
'说明:分类管理页的核心接口(1.7新加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_CategoryMng_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_MemberMng_Core
'参数:$s, $w, $or, $l, $op
'说明:会员管理页的核心接口(1.7新加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_MemberMng_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_UploadMng_Core
'参数:$s, $w, $or, $l, $op
'说明:会员管理页的核心接口(1.7新加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_UploadMng_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_TagMng_Core
'参数:$s, $w, $or, $l, $op
'说明:会员管理页的核心接口(1.7新加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_TagMng_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_SubMenu
'参数:
'说明:编辑页菜单(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Edit_SubMenu
'参数:
'说明:标签编辑页菜单(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Edit_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Module_Edit_SubMenu
'参数:
'说明:模块编辑页菜单(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Module_Edit_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Edit_SubMenu
'参数:
'说明:用户编辑页菜单(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Edit_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Edit_SubMenu
'参数:
'说明:分类编辑页菜单(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Edit_SubMenu');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_ArticleMng_Table
'参数:&$article,&$tabletds,&$tableths
'说明:文章管理页表处理(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_ArticleMng_Table');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_PageMng_Table
'参数:&$article,&$tabletds,&$tableths
'说明:页面管理页表处理(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_PageMng_Table');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CategoryMng_Table
'参数:&$category,&$tabletds,&$tableths
'说明:分类管理页表处理(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_CategoryMng_Table');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_CommentMng_Table
'参数:&$cmt,&$tabletds,&$tableths,$article
'说明:评论管理页表处理(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_CommentMng_Table');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_MemberMng_Table
'参数:&$member,&$tabletds,&$tableths
'说明:会员管理页表处理(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_MemberMng_Table');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_UploadMng_Table
'参数:&$upload,&$tabletds,&$tableths
'说明:附件管理页表处理(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_UploadMng_Table');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_TagMng_Table
'参数:&$tag,&$tabletds,&$tableths
'说明:Tag管理页表处理(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_TagMng_Table');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Begin
'参数:
'说明:文章页面编辑页开始接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_End
'参数:
'说明:文章页面编辑页结束接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response
'参数:
'说明:文章页面编辑1号输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_Response');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response2
'参数:
'说明:文章页面编辑2号输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_Response2');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response4
'参数:
'说明:文章页面编辑4号输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_Response4');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response5
'参数:
'说明:文章页面编辑5号输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_Response5');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Edit_Response3
'参数:
'说明:文章页面编辑3号输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Edit_Response3');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Edit_Response
'参数:
'说明:分类编辑页输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Edit_Response');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Edit_Response
'参数:
'说明:标签编辑页输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Edit_Response');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Edit_Response
'参数:
'说明:会员编辑页输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Edit_Response');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Module_Edit_Response
'参数:
'说明:模块编辑页输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Module_Edit_Response');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Admin_Js_Add
'参数:
'说明:c_admin_js_add.php脚本页的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Admin_Js_Add');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfMemberLevel
'参数:$default,$tz
'说明:定义OutputOptionItemsOfMemberLevel函数里的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfMemberLevel');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfMember_Begin
'参数:$default,$tz
'说明:定义Filter_Plugin_OutputOptionItemsOfMember函数里的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfMember_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfCategories
'参数:$default,$tz
'说明:定义OutputOptionItemsOfCategories函数里的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfCategories');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfPostStatus
'参数:$default,$tz
'说明:定义OutputOptionItemsOfPostStatus函数里的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfPostStatus');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfIsTop
'参数:$default,$tz
'说明:定义OutputOptionItemsOfIsTop函数里的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfIsTop');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfMember
'参数:$default,$tz
'说明:定义OutputOptionItemsOfMember函数里的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfMember');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfTemplate
'参数:$default,$tz
'说明:定义OutputOptionItemsOfTemplate函数里的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfTemplate');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_OutputOptionItemsOfCommon
'参数:$default,$array,$name
'说明:定义OutputOptionItemsOfCommon函数里的接口，因为是通用型的，所以有$name
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_OutputOptionItemsOfCommon');

//###############################################################################################################
//<c_system_event.php里的接口>
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_VerifyLogin_Succeed
'参数:
'说明:VerifyLogin成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_VerifyLogin_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Logout_Succeed
'参数:
'说明:Logout成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Logout_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_GetPost_Result
'参数:&$post
'说明:定义GetPost输出结果接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_GetPost_Result');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_GetList_Result
'参数:&$list
'说明:定义GetList输出结果接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_GetList_Result');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewIndex_Begin
'参数:
'说明:定义ViewIndex输出接口Begin
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewIndex_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewFeed_Begin
'参数:
'说明:定义ViewFeed输出接口Begin
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewFeed_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewSearch_Begin
'参数:
'说明:定义ViewSearch输出接口Begin
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewSearch_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewAuto_Begin
'参数:&$url
'说明:定义ViewAuto输出接口Begin
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewAuto_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewAuto_End
'参数:&$url
'说明:定义ViewAuto输出接口End
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewAuto_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Begin
'参数:&$page,&$cate,&$auth,&$date,&$tags
'说明:定义列表输出接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewList_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Begin_V2
'参数:&$array
'说明:定义列表输出接口(第2版，只传一个$array)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewList_Begin_V2');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Core
'参数:&$type,&$page,&$category,&$author,&$datetime,&$tag,&$w,&$pagebar
'说明:定义列表核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewList_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewSearch_Core
'参数:$q, $page, $w, $pagebar
'说明:定义搜索核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewSearch_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewFeed_Core
'参数:$w
'说明:定义Feed核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewFeed_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewFeed_End
'参数:$rss2
'说明:定义Feed核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewFeed_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewPost_Begin
'参数:&$id,&$alias
'说明:定义POST显示输出begin接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewPost_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewPost_Begin_V2
'参数:&$array
'说明:定义POST显示输出begin接口(第2版，只传入一个$array)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewPost_Begin_V2');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewPost_Core
'参数:&$select,&$w,&$order,&$limit,&$option
'说明:定义POST显示核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewPost_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewList_Template
'参数:&$template
'说明:
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewList_Template');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewPost_Template
'参数:&$template
'说明:
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewPost_Template');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewSearch_Template
'参数:&$template
'说明:
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewSearch_Template');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewComments_Template
'参数:&$template
'说明:
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewComments_Template');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_ViewComment_Template
'参数:&$template
'说明:
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_ViewComment_Template');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostModule_Core
'参数:&$mod
'说明:模块编辑的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostModule_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostMember_Core
'参数:&$mem
'说明:会员编辑的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostMember_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostTag_Core
'参数:&$tag
'说明:标签编辑的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostTag_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostCategory_Core
'参数:&$cate
'说明:分类编辑的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostCategory_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostComment_Core
'参数:&$cmt
'说明:评论发表的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostComment_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_CheckComment_Core
'参数:&$cmt
'说明:评论审核的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_CheckComment_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostArticle_Core
'参数:&$article
'说明:文章编辑的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostArticle_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostPage_Core
'参数:&$article
'说明:页面编辑的核心接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostPage_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostMember_Succeed
'参数:&$mem
'说明:会员编辑成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostMember_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostTag_Succeed
'参数:&$tag
'说明:标签编辑成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostTag_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostCategory_Succeed
'参数:&$cate
'说明:分类编辑成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostCategory_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostComment_Succeed
'参数:&$cmt
'说明:评论发表成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostComment_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_CheckComment_Succeed
'参数:&$cmt
'说明:评论审核成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_CheckComment_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostPage_Succeed
'参数:&$article
'说明:页面编辑成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostPage_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostArticle_Succeed
'参数:&$article
'说明:文章编辑成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostArticle_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostModule_Succeed
'参数:&$mod
'说明:模块编辑成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostModule_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostUpload_Succeed
'参数:&$upload
'说明:附件上传成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostUpload_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelMember_Succeed
'参数:&$mem
'说明:会员删除成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelMember_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelTag_Succeed
'参数:&$tag
'说明:标签删除成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelTag_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelCategory_Succeed
'参数:&$cate
'说明:分类删除成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelCategory_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelComment_Succeed
'参数:&$cmt
'说明:评论删除成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelComment_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelPage_Succeed
'参数:&$article
'说明:页面删除成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelPage_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelArticle_Succeed
'参数:&$article
'说明:文章删除成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelArticle_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelModule_Succeed
'参数:&$mod
'说明:模块删除成功的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelModule_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostPost_Core
'参数:&$post
'说明:Post类对象的通用编辑的核心接口(1.7.0加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostArticle_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelPost_Core
'参数:&$post
'说明:Post类对象的通用删除核心接口(1.7.0加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelPost_Core');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_PostPost_Succeed
'参数:&$post
'说明:Post类对象的通用编辑的成功接口(1.7.0加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_PostArticle_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DelPost_Succeed
'参数:&$post
'说明:Post类对象的通用删除成功接口(1.7.0加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DelPost_Succeed');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_EnablePlugin
'参数:&name
'说明:EnablePlugin(1.6.0加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_EnablePlugin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_DisablePlugin
'参数:&name
'说明:DisablePlugin(1.6.0加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_DisablePlugin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_BatchPost
'参数:&type
'说明:BatchPost(1.6.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_BatchPost');

//###############################################################################################################
//<lib类里的接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Url
'参数:&$this
'说明:干预Post类Url方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Url');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Get
'参数:&$this, $method
'说明:干预Post类Get方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Get');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Set
'参数:&$this, $method, $arg
'说明:干预Post类Set方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Set');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Thumbs
'参数:&$this, &$all_images, &$width, &$height, &$count, &$clip
'说明:干预Post类Thumbs方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Thumbs');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Url
'参数:&$this
'说明:干预Category类Url方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Url');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Get
'参数:&$this, $method
'说明:干预Category类Get方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Get');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Set
'参数:&$this, $method, $arg
'说明:干预Category类Set方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Set');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Url
'参数:&$this
'说明:干预Tag类Url方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Url');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Get
'参数:&$this, $method
'说明:干预Tag类Get方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Get');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Set
'参数:&$this, $method, $arg
'说明:干预Tag类Set方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Set');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Url
'参数:&$this
'说明:干预Member类Url方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Url');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Get
'参数:&$this, $method
'说明:干预Member类Get方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Get');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Set
'参数:&$this, $method, $arg
'说明:干预Member类Set方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Set');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_CommentPostUrl
'参数:$post
'说明:Post类的CommentPostUrl接口
'调用:返回CommentPostUrl值.
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_CommentPostUrl');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Prev
'参数:$post
'说明:Post类的Prev接口
'调用:返回Prev值.
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Prev');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Next
'参数:$post
'说明:Post类的Next接口
'调用:返回Next值.
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Next');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_RelatedList
'参数:$post
'说明:Post类的RelatedList 接口
'调用:返回RelatedList Array.
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_RelatedList');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Call
'参数:&$post,$method,$args
'说明:Post类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Call');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Comment_Call
'参数:&$comment,$method,$args
'说明:Comment类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Comment_Call');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Comment_Get
'参数:&$this, $method
'说明:干预Comment类Get方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Comment_Get');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Comment_Set
'参数:&$this, $method, $arg
'说明:干预Comment类Set方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Comment_Set');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Call
'参数:&$tag,$method,$args
'说明:Tag类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Call');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Call
'参数:&$category,$method,$args
'说明:Category类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Call');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Call
'参数:&$member,$method,$args
'说明:Member类的魔术方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Call');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Del
'参数:&$post
'说明:Post类的Del方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Del');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Del
'参数:&$post
'说明:Tag类的Del方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Del');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Comment_Del
'参数:&$comment
'说明:Comment类的Del方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Comment_Del');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Del
'参数:&$category
'说明:Category类的Del方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Del');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Del
'参数:&$member
'说明:Member类的Del方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Del');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Module_Del
'参数:&$module
'说明:Module类的Del方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Module_Del');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Module_Get
'参数:&$this, $method
'说明:干预Module类Get方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Module_Get');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Module_Set
'参数:&$this, $method, $arg
'说明:干预Module类Set方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Module_Set');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Post_Save
'参数:&$post
'说明:Post类的Save方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Post_Save');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Comment_Save
'参数:&$comment
'说明:Comment类的Save方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Comment_Save');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Tag_Save
'参数:&$tag
'说明:Tag类的Save方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Tag_Save');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Category_Save
'参数:&$category
'说明:Category类的Save方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Category_Save');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Save
'参数:&$member
'说明:Member类的Save方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Save');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Module_Save
'参数:&$module
'说明:Module类的Save方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Module_Save');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Member_Avatar
'参数:$member
'说明:Member类的Avatar接口
'调用:返回Avatar值,可以返回null.
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Member_Avatar');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_SaveFile
'参数:$tmp,$this
'说明:Upload类的SaveFile方法接口
'调用:对$tmp临时文件进行拦截
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_SaveFile');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_SaveBase64File
'参数:$str64,$this
'说明:Upload类的SaveBase64File方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_SaveBase64File');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_Del
'参数:$this
'说明:Upload类的Del方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_Del');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_DelFile
'参数:$this
'说明:Upload类的DelFile方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_DelFile');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_Url
'参数:$upload
'说明:Upload类的Url方法接口
'调用:返回Url的值,可以返回null.
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_Url');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_Get
'参数:&$this, $method
'说明:干预Upload类Get方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_Get');
/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_Set
'参数:&$this, $method, $arg
'说明:干预Upload类Set方法的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_Set');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Upload_Dir
'参数:$upload
'说明:Upload类的Dir方法接口
'调用:返回Dir的值,可以返回null.
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Upload_Dir');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_App_Pack
'参数:$this, $this->dirs, $this->files
'说明:App类的Pack方法接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_App_Pack');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Template_Compiling_Begin
'参数:$this,$content
'说明:Template类编译一个模板前的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Template_Compiling_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Template_Compiling_End
'参数:$this,$content
'说明:Template类编译一个模板后的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Template_Compiling_End');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Template_GetTemplate
'参数:$this,$name
'说明:Template类读取一个模板前的接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Template_GetTemplate');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Template_Display
'参数:$this, $entryPage
'说明:Template类显示接口
'调用:未启用
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Template_Display');

/*
'**************************************************
'类型:Filter
'名称:Filter_Plugin_Template_MakeTemplatetags
'参数:$this,$name
'说明:Template类读取一个模板前的接口
'调用:未启用
'**************************************************
 */
//DefinePluginFilter('Filter_Plugin_Template_MakeTemplatetags');

//<大数据操作相关>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_LargeData_Article
'参数:&$select,&$where,&$order,&$limit,&$option
'说明:大数据文章接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_LargeData_Article');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_LargeData_Page
'参数:&$select,&$where,&$order,&$limit,&$option
'说明:大数据页面接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_LargeData_Page');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_LargeData_Comment
'参数:&$select,&$where,&$order,&$limit,&$option
'说明:大数据评论接口
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_LargeData_Comment');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_LargeData_CountTagArray
'参数:$string, $plus, $articleid
'说明:大数据增减文章标签关联表
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_LargeData_CountTagArray');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_LargeData_GetList
'参数:&$select,&$where,&$order,&$limit,&$option
'说明:大数据GetList函数
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_LargeData_GetList');


//<其它杂项接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_Xmlrpc_Begin
'参数:&xml
'说明:xml-rpc页的begin接口(1.5.1加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_Xmlrpc_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_CSP_Backend
'参数:&$xml
'说明:后台CSP接口(1.5.2加入)
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_CSP_Backend');

//###############################################################################################################
//<api里的接口>

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_API_Response
'参数:&$response
'说明:API 响应内容处理
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_API_Response');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_API_Begin
'参数:
'说明:API 启动
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_API_Begin');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_API_Extend_Mods
'参数:
'说明:API 的应用追加模块机制
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_API_Extend_Mods');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_API_ListCheck
'参数:&$mods_allow, &$mods_disallow
'说明:API 的黑白名单机制
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_API_ListCheck');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_API_Get_Request_Filter
'参数:&$condition
'说明:API 获取约束过滤条件
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_API_Get_Request_Filter');

/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_API_Get_Pagination_Info
'参数:&$info,&$pagebar
'说明:API 获取分页信息
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_API_Get_Pagination_Info');


/*
'**************************************************<
'类型:Filter
'名称:Filter_Plugin_API_Get_Object_Array
'参数:&$object, &$array, &$other_props, &$remove_props, &$with_relations
'说明:API 转换Object到Array
'调用:
'**************************************************>
 */
DefinePluginFilter('Filter_Plugin_API_Get_Object_Array');
