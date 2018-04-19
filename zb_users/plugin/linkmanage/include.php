<?php

include_once $zbp->usersdir . 'plugin/linkmanage/function.php';
//注册插件
RegisterPlugin("linkmanage", "ActivePlugin_linkmanage");

function ActivePlugin_linkmanage()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', 'linkmanage_TopMenu');
    Add_Filter_Plugin('Filter_Plugin_Cmd_Begin', 'linkmanage_ModuleEdt');
}

function linkmanage_TopMenu(&$m)
{
    global $zbp;
    array_unshift($m, MakeTopMenu("root", '菜单链接管理', $zbp->host . "zb_users/plugin/linkmanage/main.php", "", "topmenu_linkmanage"));
}

function linkmanage_ModuleEdt()
{
    global $zbp;
    $n = linkmanage_getMenus();
    $modid = null;
    $mod = null;
    $action = GetVars('act', 'GET');
    switch ($action) {
        case 'ModuleEdt':
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $modid = (int) GetVars('id', 'GET');
                $mod = $zbp->GetModuleByID($modid);
                if (linkmanage_editSys_str($mod->FileName)) {
                    $menuid = substr($mod->FileName, 11);
                } else {
                    $menuid = $mod->FileName;
                }
                if ($menuid && isset($n['data'][$menuid])) {
                    Redirect($zbp->host . 'zb_users/plugin/linkmanage/menuedit.php?id=' . $menuid);
                }
            } else {
                Redirect('admin/module_edit.php?' . GetVars('QUERY_STRING', 'SERVER'));
            }
        break;
    }
}

function InstallPlugin_linkmanage()
{
    global $zbp;
    if (!$zbp->HasConfig('linkmanage')) {
        $zbp->Config('linkmanage')->Version = '0.3';
        $zbp->Config('linkmanage')->Menus = '{"num":4,"data":{"navbar":{"id":"navbar","name":"导航栏"},"link":{"id":"link","name":"友情链接"},"favorite":{"id":"favorite","name":"网站收藏"},"misc":{"id":"misc","name":"图标汇集"}}}';
        $favorites = array(
            array('Name' => '首页', 'Url' => $zbp->host, 'Sysid' => 'index'),
            array('Name' => '新建文章', 'Url' => $zbp->host . "zb_system/cmd.php?act=ArticleEdt", 'Sysid' => 'newpost'),
            array('Name' => '登录管理', 'Url' => $zbp->host . "zb_system/cmd.php?act=Admin", 'Sysid' => 'login'),
        );
        $zbp->Config('linkmanage')->Favorites = json_encode($favorites);
        $zbp->Config('linkmanage')->showoption = 'page|category|tags|other';
        //$zbp->Config('linkmanage')->Menu = '{}'; //菜单集{[{"id":"123456","title":"导航栏","url":"","newtable":"true","icon":"","type":""}]}
        //$zbp->Config('linkmanage')->Location = '{}';
        $zbp->Config('linkmanage')->Tempid = '0';
        $zbp->SaveConfig('linkmanage');
    }
}

function UninstallPlugin_linkmanage()
{
}
