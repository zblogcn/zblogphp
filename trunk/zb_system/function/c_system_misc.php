<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


ob_clean();

switch (GetVars('type','GET')) {
	case 'statistic':
		if (!$zbp->CheckRights(4)) {throw new Exception($lang['error'][6]);}
		misc_statistic();
		break;
	case 'updateinfo':
		if (!$zbp->CheckRights(4)) {throw new Exception($lang['error'][6]);}
		misc_updateinfo();
		break;
	case 'commontags':
		if (!$zbp->CheckRights(4)) {throw new Exception($lang['error'][6]);}
		misc_commontags();
		break;
	case 'vrs':
		if (!$zbp->CheckRights(5)) {throw new Exception($lang['error'][6]);}
		misc_viewrights();
		break;		
	default:
		break;
}

die();


function misc_updateinfo(){

	global $zbp;

	$r=null;

	$r = file_get_contents($zbp->option['ZC_UPDATE_INFO_URL']);
	#$r = file_get_contents('http://www.baidu.com/robots.txt');
	$r = '<tr><td>' . $r . '</td></tr>';

	$zbp->SetCache('reload_updateinfo',$r);
	$zbp->SaveCache(true);
	
	echo $r;
}



function misc_statistic(){

	global $zbp;

	$r=null;
	

	$zbp->BuildTemplate();

	$xmlrpc_address=$zbp->host . 'zb_system/xml-rpc/';
	$current_member=$zbp->user->Name;
	$current_version=$zbp->option['ZC_BLOG_VERSION'];
	$all_artiles=GetValueInArray(current($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Log'] . ' WHERE log_Type=0')),'num');
	$all_pages=GetValueInArray(current($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Log'] . ' WHERE log_Type=0')),'num');	
	$all_categorys=GetValueInArray(current($zbp->db->Query('SELECT COUNT(cate_ID) AS num FROM ' . $GLOBALS['table']['Category'])),'num');
	$all_comments=GetValueInArray(current($zbp->db->Query('SELECT COUNT(comm_ID) AS num FROM ' . $GLOBALS['table']['Comment'])),'num');
	$all_views=GetValueInArray(current($zbp->db->Query('SELECT SUM(log_ViewNums) AS num FROM ' . $GLOBALS['table']['Log'])),'num');
	$all_tags=GetValueInArray(current($zbp->db->Query('SELECT COUNT(tag_ID) as num FROM ' . $GLOBALS['table']['Tag'])),'num');
	$all_members=GetValueInArray(current($zbp->db->Query('SELECT COUNT(mem_ID) AS num FROM ' . $GLOBALS['table']['Member'])),'num');
	$current_theme=$zbp->option['ZC_BLOG_THEME'];
	$current_style=$zbp->option['ZC_BLOG_CSS'];

	$system_environment=(getenv('OS')?getenv('OS'):getenv('XAMPP_OS')) . ';' . current(explode('/',GetVars('SERVER_SOFTWARE','SERVER'))) . ';' . 'PHP ' . phpversion();

	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_member']}</td><td class='td30'>{$current_member}</td><td class='td20'>{$zbp->lang['msg']['current_version']}</td><td class='td30'>{$current_version}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_artiles']}</td><td>{$all_artiles}</td><td>{$zbp->lang['msg']['all_categorys']}</td><td>{$all_categorys}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_pages']}</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}</td><td>{$all_tags}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_comments']}</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}</td><td>{$all_views}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_theme']}/{$zbp->lang['msg']['current_style']}</td><td>{$current_theme}/{$current_style}</td><td>{$zbp->lang['msg']['all_members']}</td><td>{$all_members}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['xmlrpc_address']}</td><td>{$xmlrpc_address}</td><td>{$zbp->lang['msg']['system_environment']}</td><td>{$system_environment}</td></tr>";		

	$zbp->SetCache('reload_statistic',$r);


	echo $r;

}


function misc_commontags(){

}


function misc_viewrights(){
	global $zbp;

$blogtitle=$zbp->option['ZC_BLOG_TITLE'] . '-' . $zbp->lang['msg']['view_rights'];
?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if(strpos(GetVars('HTTP_USER_AGENT','SERVERS'),'MSIE')){?>
	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<?php }?>
	<meta name="robots" content="none" />
	<meta name="generator" content="<?php echo $GLOBALS['option']['ZC_BLOG_PRODUCT_FULL']?>" />
	<link rel="stylesheet" href="css/admin.css" type="text/css" media="screen" />
	<title><?php echo $blogtitle;?></title>
</head>
<body class="short">
<div class="bg">
<div id="wrapper">
  <div class="logo"><img src="image/admin/none.gif" title="Z-BlogPHP" alt="Z-BlogPHP"/></div>
  <div class="login">
    <form method="post" action="#">
    <dl>
      <dt><?php echo $zbp->lang['msg']['current_member'] . ':' . $zbp->user->Name;?></dt>
      <dd><?php echo $zbp->lang['msg']['member_level'] . ':' . $zbp->user->LevelName;?></dd>
    </dl>
    </form>
  </div>
</div>
</div>
</body>
</html>
<?php

}



?>