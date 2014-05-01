<?php
/*  TODO:
 *  原有配置不考虑进行转移或升级
 *  1. 提取IP和网址
 */
RegisterPlugin("Totoro","ActivePlugin_Totoro");
define('TOTORO_PATH', dirname(__FILE__));
define('TOTORO_INCPATH', TOTORO_PATH . '/inc/');


function Totoro_init()
{
	require(TOTORO_PATH . '/inc/totoro.php');
	global $Totoro;
	$Totoro = new Totoro_Class;
}

function ActivePlugin_Totoro()
{
	Add_Filter_Plugin('Filter_Plugin_Admin_CommentMng_SubMenu','Totoro_Admin_CommentMng_SubMenu');
	Add_Filter_Plugin('Filter_Plugin_PostComment_Core','Totoro_PostComment_Core');
}


function InstallPlugin_Totoro()
{
}


function Totoro_Admin_CommentMng_SubMenu()
{
	global $zbp;
	echo '<a href="'. $zbp->host .'zb_users/plugin/Totoro/main.php"><span class="m-right">Totoro设置</span></a>';
}


function Totoro_PostComment_Core(&$comment)
{
	global $zbp;
	Totoro_init();
	global $Totoro;
	$Totoro->check_comment($comment);
	if (!$comment->IsChecking && !$comment->IsThrow)
	{
		$Totoro->replace_comment($comment);
	}
}

