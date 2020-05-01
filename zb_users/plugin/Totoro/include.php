<?php
/*  TODO:
 *  原有配置不考虑进行转移或升级
 *  1. 提取IP和网址
 */
RegisterPlugin("Totoro", "ActivePlugin_Totoro");
define('TOTORO_PATH', dirname(__FILE__));
define('TOTORO_INCPATH', TOTORO_PATH . '/inc/');
/** @var Totoro_Class $Totoro */
$Totoro = null;

function Totoro_init()
{
    require_once TOTORO_PATH . '/inc/totoro.php';

    global $Totoro;
    if (!is_object($Totoro)) {
        $Totoro = new Totoro_Class();
    }
}

$Totoro_Include_File = array();

function Totoro_Include($filename)
{
    global $Totoro_Include_File;
    if (!isset($Totoro_Include_File[$filename])) {
        $r = include $filename;
        $Totoro_Include_File[$filename] = $r;
        return $r;
    }
    return $Totoro_Include_File[$filename];
}

function ActivePlugin_Totoro()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_CommentMng_SubMenu', 'Totoro_Admin_CommentMng_SubMenu');
    Add_Filter_Plugin('Filter_Plugin_PostComment_Core', 'Totoro_PostComment_Core');
    Add_Filter_Plugin('Filter_Plugin_Cmd_Begin', 'Totoro_Cmd_Begin');
}

function InstallPlugin_Totoro()
{
}

function Totoro_Admin_CommentMng_SubMenu()
{
    global $zbp;
    echo '<a href="' . $zbp->host . 'zb_users/plugin/Totoro/main.php"><span class="m-right">Totoro设置</span></a>';
    //echo '<script src="' . $zbp->host . 'zb_users/plugin/Totoro/submenu.js"></script>';
}

function Totoro_PostComment_Core(&$comment)
{
    global $zbp;
    Totoro_init();
    global $Totoro;
    $Totoro->check_comment($comment);
    if (!$comment->IsChecking && !$comment->IsThrow) {
        $Totoro->replace_comment($comment);
    }
}

function Totoro_Cmd_Begin()
{
    global $zbp;

    if (!(GetVars('act', 'GET') == 'CommentChk')) {
        return;
    }

    if (function_exists('CheckIsRefererValid')) {
        CheckIsRefererValid();
    } elseif (!$zbp->ValidToken(GetVars('token', 'GET'))) {
        $zbp->ShowError(5, __FILE__, __LINE__);
    }
    $id = (int) GetVars('id', 'GET');
    $ischecking = (bool) GetVars('ischecking', 'GET');
    if (!$ischecking) {
        return;
    }

    Totoro_init();
    global $Totoro;
    $Totoro->add_black_list($id);
}
