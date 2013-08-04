<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

function zbp_add(){
	echo '<a href="../cmd.php?act=ArticleEdt&amp;type=1"><span class="m-left">' . $GLOBALS['lang']['msg']['new_page'] . '</span></a>';
}

Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu','aaa');

$zbp->Initialize();
#$zbp->BuildTemplate();

$action=(GetVars('act','GET')=='') ? 'admin' : GetVars('act','GET') ;
if (!$zbp->CheckRights($action)) {throw new Exception($lang['error'][6]);}

$f=null;
switch ($action) {
	case 'ArticleMng':
		if(GetVars('type','GET')==ZC_LOG_TYPE_PAGE){
			$f='Admin_PageMng';
			$blogtitle=$lang['msg']['page_manage'];	
		}else{
			$f='Admin_ArticleMng';
			$blogtitle=$lang['msg']['article_manage'];			
		}
		break;
	case 'CategoryMng':
		$f='Admin_CategoryMng';
		$blogtitle=$lang['msg']['category_manage'];
		break;
	case 'CommentMng':
		$f='Admin_CommentMng';
		$blogtitle=$lang['msg']['comment_manage'];
		break;
	case 'MemberMng':
		$f='Admin_MemberMng';
		$blogtitle=$lang['msg']['member_manage'];
		break;
	case 'UploadMng':
		$f='Admin_UploadMng';
		$blogtitle=$lang['msg']['upload_manage'];
		break;
	case 'TagMng':
		$f='Admin_TagMng';
		$blogtitle=$lang['msg']['tag_manage'];
		break;
	case 'PluginMng':
		$f='Admin_PluginMng';
		$blogtitle=$lang['msg']['plugin_manage'];
		break;
	case 'ThemeMng':
		$f='Admin_ThemeMng';
		$blogtitle=$lang['msg']['theme_manage'];
		break;
	case 'ModuleMng':
		$f='Admin_ModuleMng';
		$blogtitle=$lang['msg']['module_manage'];
		break;
	case 'SettingMng':
		$f='Admin_SettingMng';
		$blogtitle=$lang['msg']['settings'];
		break;		
	default:
		$f='Admin_SiteInfo';
		$blogtitle=$lang['msg']['dashboard'];
		break;
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<?php
$f();
?>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
