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
	set_error_handler(create_function('',''));
	set_exception_handler(create_function('',''));
	register_shutdown_function(create_function('',''));
	set_error_handler('ds_error_handler');
	set_exception_handler('ds_exception_handler');
	register_shutdown_function('ds_shutdown_error_handler');
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

function api_async()
{
	//header('Content-Type: application/javascript');
	
	global $zbp;
	global $duoshuo;
	$result = array();
	$duoshuo->init();
	$_last = (int)$duoshuo->cfg->lastpub;
	$_now = time();
	$result['last'] = $_last;
	$result['now'] = $_now;
	
	if(!DUOSHUO_DEBUG)
	{
		if (($_now-$_last)/1000<60*20)
		{
			$result['status'] = 'waiting...';
			echo json_encode($result);
			exit();
		}
	}
	
	$_last = $_now;
	$duoshuo->cfg->lastpub = $_now;
	$zbp->SaveConfig('duoshuo');
	$return_string = '';
	
	if($duoshuo->cfg->cron_sync_enabled != "async")
	{
		$return_string = 'noasync';
	}
	else
	{
		$return_string = api_run();
	}

	$result['last'] = $_last;
	$result['now'] = $_now;
	$result['status'] = $return_string;
	echo json_encode($result);
	exit();
}

function api_run()
{
	global $duoshuo;
	global $zbp;
	$duoshuo->init();
	$duoshuo->api->sync();
	$zbp->AddBuildModule('comments');
	$zbp->AddBuildModule('statistics');
	$zbp->BuildModule();
	return 'success';
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

function save()
{
	global $duoshuo;
	global $zbp;
	foreach($_POST as $name => $value)
	{
		if(substr($name,0,7)=='duoshuo')
		{
			$name = substr($name,8);
			$duoshuo->cfg->$name = $value;
		}
	}
	$zbp->SaveConfig('duoshuo');
	$zbp->SetHint('good');
	header('Location: main.php?act=setting');
}

function export()
{

	
	$intmin = 0; $intmax = 0;
	$http = Network::Create();
	if(!$http) throw new Exception('主机没有开启网络功能');
	
	$startTime = microtime_float();
	
	require DUOSHUO_PATH . '/export.article.php';
	require DUOSHUO_PATH . '/export.comment.php';
	require DUOSHUO_PATH . '/export.member.php';
	
	switch(GetVars("type",'POST'))
	{
		case 'all':
			export_post_article($http,$intmin,$intmax);
			export_post_comment($http,$intmin,$intmax);
			export_post_member($http,$intmin,$intmax);
			$strSuccess='全部导出完成';
		break;
		case 'article':	
			$intmin=(int)GetVars('articlemin','POST');
			$intmax=(int)GetVars('articlemax','POST');
			export_post_article($http,$intmin,$intmax);
			$strSuccess="文章数据(".$intmin." - ".$intmax.")导出完成";
		break;
		case 'comment':
			$intmin=(int)GetVars('commentmin','POST');
			$intmax=(int)GetVars('commentmax','POST');
			export_post_comment($http,$intmin,$intmax);
			$strSuccess="评论数据(".$intmin." - ".$intmax.")导出完成";
		break;
		case 'member':
			export_post_member($http,$intmin,$intmax);
			$strSuccess="用户数据导出完成";
		break;
		case 'backup':
			$strSuccess = api_run();
			if($strSuccess=='success') $strSuccess = "数据从多说备份到本地完成";
		break;
	}
	$result = array(
		'success' => $strSuccess  . '，用时' . (microtime_float()-$startTime) . 'ms'
	);
	
	echo json_encode($result);
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


function ds_error_handler($errno, $errstr, $errfile, $errline ){

	//ob_clean();
	$zbe=ZBlogException::GetInstance();
	$zbe->ParseError($errno, $errstr, $errfile, $errline);
	Http500();
	$code = $zbe->get_code($zbe->file,$zbe->line);
	$code = $code[$zbe->line-1];
	echo '<br/>Message: ' . $zbe->message . '<br/>File: ' . $zbe->file . '<br/>Line: ' . $zbe->line . '<br/>Code: ' . $code;
	die();
}

function ds_exception_handler($exception){

	//ob_clean();
	$zbe=ZBlogException::GetInstance();
	$zbe->ParseException($exception);
	Http500();
	$code = $zbe->get_code($zbe->file,$zbe->line);
	$code = $code[$zbe->line-1];
	echo '<br/>Message: ' . $zbe->message . '<br/>File:' . $zbe->file . '<br/>Line:' . $zbe->line . '<br/>Code:' . $code;
	die();
}
function ds_shutdown_error_handler(){
	if ($error = error_get_last()) {

		//ob_clean();
		$zbe=ZBlogException::GetInstance();
		$zbe->ParseShutdown($error);
		Http500();
		$code = $zbe->get_code($zbe->file,$zbe->line);
		$code = $code[$zbe->line-1];
		echo '<br/>Message: ' . $zbe->message . '<br/>File:' . $zbe->file . '<br/>Line:' . $zbe->line . '<br/>Code:' . $code;
		die();
	}
}

?>
