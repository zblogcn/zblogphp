<?php

function linkmanage_SubMenu($id)
{
    $arySubMenu = array(
        0 => array('菜单管理', 'main.php', 'left', false),
        1 => array('链接编辑', '', 'left', false),
        2 => array('配置选项', 'config.php', 'left', false),
    );
    foreach ($arySubMenu as $k => $v) {
        echo '<a href="' . $v[1] . '" ' . ($v[3] == true ? 'target="_blank"' : '') . '><span class="m-' . $v[2] . ' ' . ($id == $k ? 'm-now' : '') . '">' . $v[0] . '</span></a>';
    }
}

//获取菜单列表
function linkmanage_getMenus()
{
    global $zbp;

    return json_decode($zbp->Config('linkmanage')->Menus, true);
}

//获取菜单链接列表
function linkmanage_getLink($menuID)
{
    global $zbp;
    $t = '{}'; //json
    $linkmanage_str = linkmanage_editSys_str($menuID);
    if (isset($zbp->modulesbyfilename[$linkmanage_str . $menuID])) {
        $t = $zbp->modulesbyfilename[$linkmanage_str . $menuID]->Metas->linkmanage_links;
    }

    return json_decode($t, true);
}

//获取菜单链接排序
function linkmanage_getLink_sort($menuID)
{
    global $zbp;
    $t = '{}';
    $linkmanage_str = linkmanage_editSys_str($menuID);
    if (isset($zbp->modulesbyfilename[$linkmanage_str . $menuID])) {
        $t = $zbp->modulesbyfilename[$linkmanage_str . $menuID]->Metas->linkmanage_link_sort;
    }

    return json_decode($t, true);
}

//获取菜单自定义链接序号
function linkmanage_getTempid()
{
    global $zbp;
    $t = '0';
    if ($zbp->Config('linkmanage')->Tempid) {
        $t = $zbp->Config('linkmanage')->Tempid;
    }

    return $t;
}

//option
//检查是否与系统链接模块同名
function linkmanage_isSys($menuID)
{
    global $zbp;
    $sysMenu = 'navbar|link|favorite|misc';

    $t = false;
    if (preg_match("/$menuID/", $sysMenu)) {
        $t = true;
    }

    return $t;
}

//是否直接编辑系统模块,返回模块名前缀
function linkmanage_editSys_str($menuID)
{
    global $zbp;
    $t = 'linkmanage_';
    if ($zbp->Config('linkmanage')->editsystem && linkmanage_isSys($menuID)) {
        $t = '';
    }

    return $t;
}

//创建导航
function linkmanage_creatMenu($menuID)
{
    global $zbp;
    $n = linkmanage_getMenus();
    if (linkmanage_isSys($menuID)) {
        $zbp->SetHint('bad', 'ID与系统菜单冲突！请更改');
    } elseif (isset($n['data'][$menuID])) {
        $zbp->SetHint('bad', 'ID已存在！请更改');
    } else {
        $n['data'][$menuID] = array(
            'id'   => $menuID,
            'name' => GetVars('name', 'POST'),
            //'location' => '',
        );
        $n['num'] = count($n['data']);
        $zbp->Config('linkmanage')->Menus = json_encode($n);
        $zbp->SaveConfig('linkmanage');

        //创建模块
        if (!isset($zbp->modulesbyfilename['linkmanage_' . $menuID])) {
            $t = new Module();
            $t->Name = '菜单：' . GetVars('name', 'POST');
            $t->FileName = 'linkmanage_' . $menuID;
            $t->Source = 'plugin_' . $menuID;
            $t->SidebarID = 0;
            $t->Content = '';
            $t->HtmlID = 'linkmanage_' . $menuID;
            $t->Type = 'ul';

            $t->Metas->linkmanage_links = '{}';
            $t->Metas->linkmanage_link_sort = '{}';

            $t->Save();
        }
        Redirect('main.php');
    }
}

// 删除导航
function linkmanage_deleteMenu($menuID)
{
    global $zbp;
    if (linkmanage_isSys($menuID)) {
        $zbp->SetHint('bad', '系统菜单不能删除！');
    } elseif (!$zbp->Config('linkmanage')->forcedel && linkmanage_getLink_sort($menuID)) {
        $zbp->SetHint('bad', '自定义菜单需要清空链接后才可删除！');
    } else {
        $n = linkmanage_getMenus();
        unset($n['data'][$menuID]);
        $n['num'] = count($n['data']);
        $zbp->Config('linkmanage')->Menus = json_encode($n);
        $zbp->SaveConfig('linkmanage');

        //删除模块
        $m = $zbp->modulesbyfilename['linkmanage_' . $menuID];
        $m->Del();

        $zbp->SetHint('good', '菜单已删除，请将模板中相关引用代码手动清除！');
        Redirect('main.php');
    }
}

// 修改、保存导航排序
function linkmanage_saveMenu()
{
    global $zbp;
    $menuID = GetVars('id', 'POST');
    $menuName = GetVars('menuname', 'POST');
    $tempID = GetVars('tempid', 'POST');

    $links_json = GetVars('links', 'POST');

    if ($menuID) {
        $n = linkmanage_getMenus();
        //保存菜单名
        $n['data'][$menuID]['name'] = $menuName;
        $zbp->Config('linkmanage')->Menus = json_encode($n);
        $zbp->Config('linkmanage')->Tempid = $tempID;
        $zbp->SaveConfig('linkmanage');

        //保存菜单链接排序
        $link_sort = json_encode(GetVars('menuItem', 'POST'));
        linkmanage_updataModule($menuID, $menuName, $links_json, $link_sort);
    }
    echo $link_sort;
    die();
}

//更新模块内容
function linkmanage_updataModule($menuID, $menuName, $links_json, $link_sort)
{
    global $zbp;
    $html = '';

    $linkmanage_str = linkmanage_editSys_str($menuID);

    if (!is_null($links_json)) {
        $links = json_decode($links_json, true);
    } else {
        $links = linkmanage_getLink($menuID);
    }
    if ($link_sort !== 'null') {
        $sort = json_decode($link_sort, true);
        foreach ($sort as $key => $value) {
            $link = $links['ID' . $key];

            $newtable_tmp = '';
            if ($link['newtable']) {
                $newtable_tmp = 'target="_blank"';
            }

            $html_tmp = '<li class="li-item" id="' . $linkmanage_str . $menuID . '-' . $link['type'] . '-' . $link['sysid'] . '"><a href="' . $link['url'] . '" title="' . $link['title'] . '" ' . $newtable_tmp . '>' . $link['title'] . '</a><span id="' . $link['id'] . '"></span></li>';

            if ($value == 'null') {
                $html .= $html_tmp;
            } else {
                $html = str_replace('<span id="' . $value . '"></span>', '<ul class="ul-subitem">' . $html_tmp . '</ul><span id="' . $value . '"></span>', $html);
            }
        }
        $html = preg_replace("/<(span.*?)>(.*?)<(\/span.*?)>/si", "", $html); //过滤span标签
        $html = preg_replace("/<\/ul><ul class=\"ul-subitem\">/si", "", $html); //过滤ul标签
    } else {
        $html = '';
        $links_json = '{}';
        $link_sort = '{}';
    }

    //修改模块

    $t = '';
    if (isset($zbp->modulesbyfilename[$linkmanage_str . $menuID])) {
        $t = $zbp->modulesbyfilename[$linkmanage_str . $menuID];
    } else {
        $t = new Module();
        $t->SidebarID = 0;
        $t->FileName = 'linkmanage_' . $menuID;
        $t->Source = 'plugin_' . $menuID;
        $t->HtmlID = 'linkmanage_' . $menuID;
        $t->Type = 'ul';
    }

    if (!is_null($menuName)) {
        if (linkmanage_isSys($menuID)) {
            $t->Name = $menuName;
        } else {
            $t->Name = '菜单：' . $menuName;
        }
    }
    $t->Content = $html;

    $t->Metas->linkmanage_links = $links_json;
    $t->Metas->linkmanage_link_sort = $link_sort;

    $t->Save();
}

// 创建、编辑保存单链接
function linkmanage_saveLink_s($menuID)
{
    global $zbp;

    //modulelink
    $links = linkmanage_getLink($menuID);
    $new_link = array();
    $new_check = false;

    foreach ($_POST as $key => $value) {
        $new_link[$key] = $value;
    }
    //$new_menu['type'] = $links['ID'.$new_menu['id']]['type'];
    if (!isset($links['ID' . $new_link['id']])) {
        $new_check = true;
    }

    $links['ID' . $new_link['id']] = $new_link;

    $linkmanage_str = linkmanage_editSys_str($menuID);

    if (isset($zbp->modulesbyfilename[$linkmanage_str . $menuID])) {
        $t = $zbp->modulesbyfilename[$linkmanage_str . $menuID];
    } else {
        $t = new Module();
        $t->FileName = 'linkmanage_' . $menuID;
        $t->Source = 'plugin_' . $menuID;
        $t->HtmlID = 'linkmanage_' . $menuID;
        $t->Type = 'ul';
    }
    $t->Metas->linkmanage_links = json_encode($links);
    $t->save();

    $new_link['new_check'] = $new_check;
    echo json_encode($new_link);
    die();
}

// 删除链接函数
function linkmanage_deleteLink($linkID, $menuID)
{
    global $zbp;

    $links = linkmanage_getLink($menuID);
    $link_sort = linkmanage_getLink_sort($menuID);

    $have_sun = false;
    foreach ($link_sort as $key => $value) {
        if ($value == $linkID) {
            $have_sun = true;
            break;
        }
    }
    if ($have_sun) {
        echo 1;
    } else {
        unset($links['ID' . $linkID]);
        unset($link_sort[$linkID]);
        echo 0;

        linkmanage_updataModule($menuID, null, json_encode($links), json_encode($link_sort));
    }
    echo json_encode($links);
    die();
}

// 定义系统链接类型
function linkmanage_showtype()
{
    global $zbp;
    $showtype = array(
        array('post', '文章'),
        array('page', '页面'),
        array('category', '分类'),
        array('tags', '标签'),
        array('author', '作者'),
        array('other', '其它'),
    );

    return $showtype;
}
// 获取系统链接
function linkmanage_get_syslink($type)
{
    global $zbp;
    switch ($type) {
    case 'post':
        $array = $zbp->GetArticleList('', '', array('log_PostTime' => 'DESC'), '', '');
        foreach ($array as $article) {
            echo "<option sysid=\"{$article->ID}\" value=\"{$article->Url}\">{$article->Title}</option>";
        }
        break;
    case 'page':
        $array = $zbp->GetPageList('', '', array('log_PostTime' => 'DESC'), '', '');
        foreach ($array as $article) {
            echo "<option sysid=\"{$article->ID}\" value=\"{$article->Url}\">{$article->Title}</option>";
        }
        break;
    case 'category':
        foreach ($zbp->categorysbyorder as $category) {
            echo "<option sysid=\"{$category->ID}\" value=\"{$category->Url}\">{$category->Name}</option>";
        }
        break;
    case 'tags':
        $array = $zbp->GetTagList('*', '', array('tag_Count' => 'DESC'), '', '');
        foreach ($array as $tag) {
            echo "<option sysid=\"{$tag->ID}\" value=\"{$tag->Url}\">{$tag->Name}</option>";
        }
        break;
   case 'author':
        foreach ($zbp->members as $author) {
            if ($zbp->CheckRightsByLevel('ArticleEdt', $author->Level)) {
                echo "<option sysid=\"{$author->ID}\" value=\"{$author->Url}\">{$author->Name}</option>";
            }
        }
        break;
    case 'other':
        $array = json_decode($zbp->Config('linkmanage')->Favorites);
        //echo var_dump($array);
        foreach ($array as $link) {
            echo "<option sysid=\"{$link->Sysid}\" value=\"{$link->Url}\">{$link->Name}</option>";
        }
        break;
    }
}

// 编辑按钮
function linkmanage_edit_button($menuID)
{
    global $zbp;
    $edit_button = '';
    $del_button = '';
    if (!linkmanage_isSys($menuID)) {
        $del_button = '<button class="ui-button-danger ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" onclick="del_menu(\'' . $menuID . '\');return false;">删除导航</button>';
    }
    $edit_button = '<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false" onclick="edit_menu(\'' . $menuID . '\');return false;"><span class="ui-button-text">编辑</span></button>';

    return $edit_button . '    ' . $del_button;
}

// 保存配置
function linkmanage_saveConfig()
{
    global $zbp;
    foreach ($_POST as $key => $value) {
        if ($key == "showoption") {
            $zbp->Config('linkmanage')->showoption = implode('|', $value);
        } else {
            $zbp->Config('linkmanage')->$key = $value;
        }
    }
    $zbp->SaveConfig('linkmanage');
    $zbp->SetHint('good', '配置已保存！');
    Redirect('config.php');
}
