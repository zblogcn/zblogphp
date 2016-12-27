<?php

function linkmanage_SubMenu($id)
{
    $arySubMenu = array(
        0 => array('导航菜单管理', 'main.php', 'left', false),
        1 => array('导航链接编辑', '', 'left', false),
        //2 => array('位置管理', 'location.php', 'left', false),
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
        //TODO 生成链接HTML内容
        if (!isset($zbp->modulesbyfilename['linkmanage_'.$nav_name])) {
            $t = new Module();
            $t->Name = '菜单：'.GetVars('name', 'POST');
            $t->FileName = 'linkmanage_'.$nav_name;
            $t->Source = 'plugin_'.$nav_name;
            $t->SidebarID = 0;
            $t->Content = '';
            $t->HtmlID = 'linkmanage_'.$nav_name;
            $t->Type = 'ul';
            $t->Save();
        }
        Redirect('main.php');
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


        //删除模块
        $m = $zbp->modulesbyfilename['linkmanage_'.$nav_name];
        $m->Del();

        Redirect('main.php');
    }
}
// 修改、保存导航排序
function linkmanage_saveNav()
{
    global $zbp;
    $menuID = GetVars('id', 'POST');
    $menuName = GetVars('MenuName', 'POST');
    if ($menuID) {
        $n = linkmanageGetNav();
        //保存菜单名
        $n['data'][$menuID]['name'] = $menuName;
        $zbp->Config('linkmanage')->Nav = json_encode($n);
        //保存菜单链接排序
        $jsonmenuItem = json_encode(GetVars('menuItem', 'POST'));
        $zbp->Config('linkmanage')->$menuID = $jsonmenuItem;
        $zbp->SaveConfig('linkmanage');

         //修改模块
        linkmanage_updataModule($menuID,$menuName);

    }
    echo $jsonmenuItem;
    //$zbp->ShowHint('good', '成功保存菜单设置！');
    //Redirect('menuedit.php?id='.$menuID);
}


function linkmanage_updataModule($menuID,$menuName)
{
    global $zbp;
    //链接组装
    $html = '';

    $Menus = linkmanageGetMenu();
    $menuIDarray = json_decode($zbp->Config('linkmanage')->$menuID, true);
    foreach ($menuIDarray as $key => $value) {
        $menu = $Menus['ID'.$key];

        $html_tmp = '<li class="li-item" id="menuItem_'.$menu['id'].'"><a href="'.$menu['url'].'" title="'.$menu['title'].'">'.$menu['title'].'<a><span id="'.$menu['id'].'"></span></li>';

        if ($value == 'null') {
            $html .= $html_tmp;
        } else {
            $html = str_replace('<span id="'.$value.'"></span>', '<ul class="ul-subitem">'.$html_tmp.'</ul><span id="'.$value.'"></span>', $html);
        }
    }

    $html=preg_replace("/<(span.*?)>(.*?)<(\/span.*?)>/si","",$html); //过滤span标签
    $html=preg_replace("/<\/ul><ul class=\"ul-subitem\">/si","",$html); //过滤ul标签

    //修改模块
    $t = '';
    if (isset($zbp->modulesbyfilename['linkmanage_'.$menuID])) {
        $t = $zbp->modulesbyfilename['linkmanage_'.$menuID];
    }
    else {
        $t = new Module();
    }

    $t->Name = '菜单：'.$menuName;
    $t->FileName = 'linkmanage_'.$menuID;
    $t->Source = 'plugin_'.$menuID;
    $t->SidebarID = 0;
    $t->Content = $html;
    $t->HtmlID = 'linkmanage_'.$menuID;
    $t->Type = 'ul';
    $t->Save();
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
// 编辑保存链接
function linkmanage_saveLink()
{
    global $zbp;

    $menu = json_decode($zbp->Config('linkmanage')->Menu, true);
    $new_menu = array();
    foreach ($_POST as $key => $value) {
        $new_menu[$key] = $value;
    }
    $new_menu['type'] = $menu['ID'.$new_menu['id']]['type'];
    $menu['ID'.$new_menu['id']] = $new_menu;
    // $menu[] = array(
    //  "id" => "123456",
    //  "title" => "导航栏",
    //  "url" => "",
    //  "newtable" => "true",
    //  "img" => "",
    //  "type" => "",
    // );
    $zbp->Config('linkmanage')->Menu = json_encode($menu);
    $zbp->SaveConfig('linkmanage');

    echo json_encode($_POST);
    die();
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

// 编辑按钮
function linkmanage_edit_button($menuID)
{
    global $zbp;
    $edit_button = '<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" onclick="edit_menu(\''.$menuID.'\');return false;"><span class="ui-button-text">编辑</span></button>
        <button class="ui-button-danger ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" onclick="del_menu(\''.$menuID.'\');return false;">删除导航</button>';
    return $edit_button;
}
