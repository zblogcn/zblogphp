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
		if (!$zbp->CheckRights('root')) {echo $zbp->ShowError(6);die();}
		misc_statistic();
		break;
	case 'updateinfo':
		if (!$zbp->CheckRights('root')) {echo $zbp->ShowError(6);die();}
		misc_updateinfo();
		break;
	case 'showtags':
		if (!$zbp->CheckRights('ArticleEdt')) {Http404();die();}
		misc_showtags();
		break;
	case 'vrs':
		if (!$zbp->CheckRights('misc')) {$zbp->ShowError(6);}
		misc_viewrights();
		break;
	default:
		break;
}


function misc_updateinfo(){

	global $zbp;

	$r=null;

	$r = file_get_contents($zbp->option['ZC_UPDATE_INFO_URL']);
	#$r = file_get_contents('http://www.baidu.com/robots.txt');
	$r = '<tr><td>' . $r . '</td></tr>';

	$zbp->LoadConfigs();
	$zbp->LoadCache();
	$zbp->cache->reload_updateinfo=$r;
	$zbp->cache->reload_updateinfo_time=time();
	$zbp->SaveCache();

	echo $r;
}



function misc_statistic(){

	global $zbp;

	$r=null;

	$zbp->BuildTemplate();

	$xmlrpc_address=$zbp->host . 'zb_system/xml-rpc/';
	$current_member=$zbp->user->Name;
	$current_version=$zbp->option['ZC_BLOG_VERSION'];
	$all_artiles=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Post'] . ' WHERE log_Type=0'),'num');
	$all_pages=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Post'] . ' WHERE log_Type=1'),'num');	
	$all_categorys=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(cate_ID) AS num FROM ' . $GLOBALS['table']['Category']),'num');
	$all_comments=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(comm_ID) AS num FROM ' . $GLOBALS['table']['Comment']),'num');
	$all_views=GetValueInArrayByCurrent($zbp->db->Query('SELECT SUM(log_ViewNums) AS num FROM ' . $GLOBALS['table']['Post']),'num');
	$all_tags=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(tag_ID) as num FROM ' . $GLOBALS['table']['Tag']),'num');
	$all_members=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(mem_ID) AS num FROM ' . $GLOBALS['table']['Member']),'num');
	$current_theme=$zbp->theme;
	$current_style=$zbp->style;
	$current_member='{$zbp->user->Name}';

	$system_environment=(getenv('OS')?getenv('OS'):getenv('XAMPP_OS')) . ';' . GetValueInArray(explode('/',GetVars('SERVER_SOFTWARE','SERVER')),0) . ';' . 'PHP ' . phpversion() . ';' . $zbp->option['ZC_DATABASE_TYPE'] . ';';

	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_member']}</td><td class='td30'>{$current_member}</td><td class='td20'>{$zbp->lang['msg']['current_version']}</td><td class='td30'>{$current_version}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_artiles']}</td><td>{$all_artiles}</td><td>{$zbp->lang['msg']['all_categorys']}</td><td>{$all_categorys}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_pages']}</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}</td><td>{$all_tags}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_comments']}</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}</td><td>{$all_views}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_theme']}/{$zbp->lang['msg']['current_style']}</td><td>{$current_theme}/{$current_style}</td><td>{$zbp->lang['msg']['all_members']}</td><td>{$all_members}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['xmlrpc_address']}</td><td>{$xmlrpc_address}</td><td>{$zbp->lang['msg']['system_environment']}</td><td>{$system_environment}</td></tr>";		

	$s=$zbp->db->sql->Count($zbp->table['Post'],array(array('COUNT','*','num')),array(array('=','log_Type',0),array('=','log_IsTop',0),array('=','log_Status',0)));
	$num=GetValueInArrayByCurrent($zbp->db->Query($s),'num');

	$zbp->LoadConfigs();
	$zbp->LoadCache();
	$zbp->cache->reload_statistic=$r;
	$zbp->cache->reload_statistic_time=time();
	$zbp->cache->normal_article_nums=$num;
	
	$zbp->SaveCache();

	$r=str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);

	echo $r;

}


function misc_showtags(){
	global $zbp;

	header('Content-Type: application/x-javascript; Charset=utf-8');

echo '$("#ajaxtags").html("';


$array=$zbp->GetTagList(
	null,
	null,
	array('tag_Count'=>'DESC','tag_ID'=>'ASC'),
	array(100),
	null
);
if(count($array)>0){
	$t=array();
	foreach ($array as $tag) {
		echo '<a href=\"#\">' . $tag->Name . '</a>';
	}
}

echo '");$("#ulTag").tagTo("#edtTag");';

}


function misc_viewrights(){
	global $zbp;

$blogtitle=$zbp->name . '-' . $zbp->lang['msg']['view_rights'];
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
      <dt><?php echo $zbp->lang['msg']['current_member'] . ' : <b>' . $zbp->user->Name;?></b><br/>
      <?php echo $zbp->lang['msg']['member_level'] . ' : <b>' . $zbp->user->LevelName;?></b></dt>
<?php

foreach ($GLOBALS['actions']  as $key => $value) {
	if($GLOBALS['zbp']->CheckRights($key)){
		echo '<dd><b>' . $key . '</b> : ' . ($GLOBALS['zbp']->CheckRights($key)?'<span style="color:green">true</span>':'<span style="color:red">false</span>') . '</dd>';
	}
}

?>
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