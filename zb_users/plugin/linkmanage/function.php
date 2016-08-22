<?php

function linkmanage_SubMenu($id)
{
    $arySubMenu = array(
        0 => array('链接编辑', 'main.php', 'left', false),
        1 => array('位置管理', 'location.php', 'left', false),
    );
    foreach ($arySubMenu as $k => $v) {
        echo '<a href="'.$v[1].'" '.($v[3] == true ? 'target="_blank"' : '').'><span class="m-'.$v[2].' '.($id == $k ? 'm-now' : '').'">'.$v[0].'</span></a>';
    }
}

function linkmanageGetNav()
{
    global $zbp;

    return json_decode($zbp->Config('linkmanage')->Nav, true);
}
function linkmanageGetMenu()
{
    global $zbp;

    return json_decode($zbp->Config('linkmanage')->Menu, true);
}
//创建导航
function linkmanage_creatNav($nav_name)
{
    global $zbp,$sysMenu;
    $n = linkmanageGetNav();
    if (preg_match("/$nav_name/", $sysMenu)) {
        $zbp->ShowHint('bad', 'ID与系统导航冲突！请更改');
    } elseif (isset($n['data'][$nav_name])) {
        $zbp->ShowHint('bad', 'ID已存在！请更改');
    } else {
        $n['data'][$nav_name] = array(
            'id' => $nav_name,
            'name' => GetVars('name', 'POST'),
            'location' => '',
        );
        $n['num'] = count($n['data']);
        $zbp->Config('linkmanage')->Nav = json_encode($n);
        $zbp->Config('linkmanage')->$nav_name = '{}'; //排序值
        $zbp->SaveConfig('linkmanage');

        //创建模块
        if (!isset($zbp->modulesbyfilename[$nav_name])) {
            $t = new Module();
            $t->Name = '菜单：'.GetVars('name', 'POST');
            $t->FileName = $nav_name;
            $t->Source = 'plugin_'.$nav_name;
            $t->SidebarID = 0;
            $t->Content = '';
            $t->HtmlID = $nav_name;
            $t->Type = 'ul';
            $t->Save();
        }
        Redirect('main.php?id='.$nav_name);
    }
}
// 删除导航
function linkmanage_deleteNav($nav_name)
{
    global $zbp,$sysMenu;
    if (preg_match("/$nav_name/", $sysMenu)) {
        $zbp->ShowHint('bad', '系统链接不能删除');
    } else {
        $n = linkmanageGetNav();
        unset($n['data'][$nav_name]);
        $n['num'] = count($n['data']);
        $zbp->Config('linkmanage')->Nav = json_encode($n);
        $links = json_decode($zbp->Config('linkmanage')->Menu, true);
        foreach (json_decode($zbp->Config('linkmanage')->$nav_name, true) as $key => $value) {
            unset($links['ID'.$key]);
        }
        $zbp->Config('linkmanage')->Menu = json_encode($links); //链接
        $zbp->Config('linkmanage')->Del($nav_name); //排序值
        $zbp->SaveConfig('linkmanage');

// TODO:删除链接

        //删除模块
        $m = $zbp->modulesbyfilename[$nav_name];
        $m->Del();

        Redirect('main.php');
    }
}
// 修改、保存导航排序
function linkmanage_saveNav()
{
    global $zbp;
    if (GetVars('id', 'POST')) {
        $n = linkmanageGetNav();
        $n['data'][GetVars('id', 'POST')]['name'] = GetVars('MenuName', 'POST');
        $zbp->Config('linkmanage')->Nav = json_encode($n);
        $zbp->Config('linkmanage')->$n['data'][GetVars('id', 'POST')]['id'] = json_encode(GetVars('menuItem', 'POST'));
        $zbp->SaveConfig('linkmanage');

        //TODO:编译导航
    }
    echo json_encode(GetVars('menuItem', 'POST'));
    die();
}
// 添加链接
function linkmanage_creatLink()
{
    global $zbp;
    $menu = linkmanageGetMenu();
    $new_menu = array();
    foreach ($_POST as $key => $value) {
        $new_menu[$key] = $value;
    }
    $menu['ID'.$new_menu['id']] = $new_menu;
    $zbp->Config('linkmanage')->Menu = json_encode($menu);
    $zbp->SaveConfig('linkmanage');

    echo json_encode($_POST);
    die();
}
// 删除链接函数
function linkmanage_deleteLink($linkid, $nav_name)
{
    global $zbp;

    $menu = linkmanageGetMenu();
    $nav = json_decode($zbp->Config('linkmanage')->$nav_name, true);
    $have_sun = false;
    foreach ($nav as $key => $value) {
        if ($value == $linkid) {
            $have_sun = true;
            break;
        }
    }
    if ($have_sun) {
        echo 1;
    } else {
        unset($menu['ID'.$linkid]);
        unset($nav[$linkid]);
        echo 0;

        $zbp->Config('linkmanage')->Menu = json_encode($menu);
        $zbp->Config('linkmanage')->$nav_name = json_encode($nav);
        $zbp->SaveConfig('linkmanage');
    }

    die();
}
// 编辑修改链接
function linkmanage_saveLink($linkid)
{
    global $zbp;
}

function linkmanage_GetLocation()
{
    global $zbp;

    return json_decode($zbp->Config('linkmanage')->Location, true);
}
function linkmanage_get_link($type)
{
    global $zbp;
    switch ($type) {
    case 'post':
        $array = $zbp->GetArticleList('', '', array('log_PostTime' => 'DESC'), '', '');
        foreach ($array as $article) {
            echo "<option value=\"{$article->Url}\">{$article->Title}</option>";
        }
        break;
    case 'page':
        $array = $zbp->GetPageList('', '', array('log_PostTime' => 'DESC'), '', '');
        foreach ($array as $article) {
            echo "<option value=\"{$article->Url}\">{$article->Title}</option>";
        }
        break;
    case 'category':
        foreach ($zbp->categorysbyorder as $category) {
            echo "<option value=\"{$category->Url}\">{$category->Name}</option>";
        }
        break;
    case 'tags':
        $array = $zbp->GetTagList('', '', array('tag_ID' => 'ASC'), '', '');
        foreach ($array as $tag) {
            echo "<option value=\"{$tag->Url}\">{$tag->Name}</option>";
        }
        break;
    }
}
