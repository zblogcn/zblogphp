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

$action=(GetVars('act','GET')=='') ? 'admin' : GetVars('act','GET') ;
if (!CheckRights($action)) {throw new Exception("没有权限！！！");}


require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
<?php

switch ($action) {
	case 'ArticleMng':
		break;
	case 'CategoryMng':
		break;
	case 'CommentMng':
		break;
	case 'UserMng':
		break;
	case 'FileMng':
		break;
	case 'TagMng':
		break;
	case 'PlugInMng':
		break;
	case 'ThemeMng':
		break;
	case 'ModuleMng':
		break;
	default:
		ExportSiteInfo();
		break;
}

?>
</div>
<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

echo RunTime();
?>
