<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$right = 'root';
function event_init()
{
	global $zbp;global $right;global $duoshuo;
	$zbp->Load();
	if (!$zbp->CheckRights($right)) {$zbp->ShowError(6);exit();}
	if (!$zbp->CheckPlugin('duoshuo')) {$zbp->ShowError(48);exit();}
	$duoshuo->init();
}

switch(GetVars('act','GET'))
{
	case 'callback':
		event_init();
		callback();
	break;
	case "export":
		event_init();
		export();
	break;
	case "specfg":
		event_init();
		specialconfig();
	break;
	case "fac":
		event_init();
		fac();
	break;
	case "api":
		api();
	break;
	case "api_async":
		$right = 'cmt';
		api_async();
	break;
	case "save":
		event_init();
		save();
	break;
	case "login":
		$right = 'login';
		event_init();
		login();
	break;
	case "logout":
		event_init();
}

function api()
{
	echo '多说 for Z-Blog PHP插件暂不支持Ping请求，请静待版本更新';
}

function callback()
{
	global $zbp;
	global $duoshuo;
	$short_name = GetVars("short_name",'GET');
	$secret = GetVars("secret",'GET');
	if (isset($short_name))
	{
		$zbp->config('duoshuo')->short_name = $short_name;
		$zbp->config('duoshuo')->secret = $secret;
		$zbp->SaveConfig('duoshuo');
	}
	$zbp->SetHint('good','现在，您必须导出数据到多说，否则可能会出现一些奇怪的问题。');
	echo "<script>top.location.href='export.php?firstrun'</script>";
}

?>
