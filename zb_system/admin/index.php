<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require_once '../function/c_system_base.php';
require_once '../function/c_system_admin.php';

$zbp->Initialize();
#$zbp->BuildTemplate();

$action=(GetVars('act','GET')=='') ? 'admin' : GetVars('act','GET') ;
if (!CheckRights($action)) {throw new Exception($GLOBALS['lang']['error'][6]);}

$blogtitle='后台管理';

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<?php

switch ($action) {
	case 'ArticleMng':
		Admin_ArticleMng();	
		break;
	case 'CategoryMng':
		Admin_CategoryMng();
		break;
	case 'CommentMng':
		Admin_CommentMng();
		break;
	case 'MemberMng':
		Admin_MemberMng();
		break;
	case 'UploadMng':
		Admin_UploadMng();
		break;
	case 'TagMng':
		Admin_TagMng();
		break;
	case 'PluginMng':
		Admin_PluginMng();
		break;
	case 'ThemeMng':
		Admin_ThemeMng();
		break;
	case 'ModuleMng':
		Admin_ModuleMng();
		break;
	default:
		Admin_SiteInfo();
		break;
}



?>
</div>
<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
