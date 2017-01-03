<?php

function linkmanage_SubMenu($id)
{
    $arySubMenu = array(
        0 => array('菜单管理', 'main.php', 'left', false),
        1 => array('链接编辑', '', 'left', false),
        //2 => array('位置管理', 'location.php', 'left', false),
    );
    foreach ($arySubMenu as $k => $v) {
        echo '<a href="'.$v[1].'" '.($v[3] == true ? 'target="_blank"' : '').'><span class="m-'.$v[2].' '.($id == $k ? 'm-now' : '').'">'.$v[0].'</span></a>';
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
    $t = '{}';//json
    if (isset($zbp->modulesbyfilename['linkmanage_'.$menuID])) {
        $t = $zbp->modulesbyfilename['linkmanage_'.$menuID]->Metas->linkmanage_links;
    }
    return json_decode($t, true);
}

//获取菜单链接排序
function linkmanage_getLink_sort($menuID)
{
    global $zbp;
    $t = '{}';
    if (isset($zbp->modulesbyfilename['linkmanage_'.$menuID])) {
        $t = $zbp->modulesbyfilename['linkmanage_'.$menuID]->Metas->linkmanage_link_sort;
    }
    return json_decode($t, true);
}

//创建导航
function linkmanage_creatMenu($menuID)
{
    global $zbp,$sysMenu;
    $n = linkmanage_getMenus();
    if (preg_match("/$menuID/", $sysMenu)) {
        $zbp->ShowHint('bad', 'ID与系统菜单冲突！请更改');
    } elseif (isset($n['data'][$menuID])) {
        $zbp->ShowHint('bad', 'ID已存在！请更改');
    } else {
        $n['data'][$menuID] = array(
            'id' => $menuID,
            'name' => GetVars('name', 'POST'),
            'location' => '',
        );
        $n['num'] = count($n['data']);
        $zbp->Config('linkmanage')->Menus = json_encode($n);
        $zbp->SaveConfig('linkmanage');

        //创建模块
        if (!isset($zbp->modulesbyfilename['linkmanage_'.$menuID])) {
            $t = new Module();
            $t->Name = '菜单：'.GetVars('name', 'POST');
            $t->FileName = 'linkmanage_'.$menuID;
            $t->Source = 'plugin_'.$menuID;
            $t->SidebarID = 0;
            $t->Content = '';
            $t->HtmlID = 'linkmanage_'.$menuID;
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
    global $zbp,$sysMenu;
    if (preg_match("/$menuID/", $sysMenu)) {
        $zbp->ShowHint('bad', '系统菜单不能删除');
    } else {
        $n = linkmanage_getMenus();
        unset($n['data'][$menuID]);
        $n['num'] = count($n['data']);
        $zbp->Config('linkmanage')->Menus = json_encode($n);
        $zbp->SaveConfig('linkmanage');

        //删除模块
        $m = $zbp->modulesbyfilename['linkmanage_'.$menuID];
        $m->Del();

        Redirect('main.php');
    }
}
// 修改、保存导航排序
function linkmanage_saveMenu()
{
    global $zbp;
    $menuID = GetVars('id', 'POST');
    $menuName = GetVars('menuname', 'POST');

    $links_json = GetVars('links', 'POST');

    if ($menuID) {
        $n = linkmanage_getMenus();
        //保存菜单名
        $n['data'][$menuID]['name'] = $menuName;
        $zbp->Config('linkmanage')->Menus = json_encode($n);
        $zbp->SaveConfig('linkmanage');

        //保存菜单链接排序
        $link_sort = json_encode(GetVars('menuItem', 'POST'));
        linkmanage_updataModule($menuID,$menuName,$links_json,$link_sort);

    }
    echo $link_sort;
    die();
}

//更新模块内容
function linkmanage_updataModule($menuID,$menuName = null,$links_json = null,$link_sort)
{
    global $zbp;
    $html = '';
    if (is_null($links_json)) {
        $links = json_decode($links_json, true);
    } else {
        $links = linkmanage_getLink($menuID);
    }
    if ($link_sort !== 'null'){
        $sort = json_decode($link_sort, true);
        foreach ($sort as $key => $value) {
            $link = $links['ID'.$key];

            $newtable_tmp = '';
            if($link['newtable']){
                $newtable_tmp = 'target="_blank"';
            }

            $html_tmp = '<li class="li-item" id="menuItem_'.$link['id'].'"><a href="'.$link['url'].'" title="'.$link['title'].'" ' .$newtable_tmp .'>'.$link['title'].'<a><span id="'.$link['id'].'"></span></li>';

            if ($value == 'null') {
                $html .= $html_tmp;
            } else {
                $html = str_replace('<span id="'.$value.'"></span>', '<ul class="ul-subitem">'.$html_tmp.'</ul><span id="'.$value.'"></span>', $html);
            }
        }
        $html=preg_replace("/<(span.*?)>(.*?)<(\/span.*?)>/si","",$html); //过滤span标签
        $html=preg_replace("/<\/ul><ul class=\"ul-subitem\">/si","",$html); //过滤ul标签
    } else {
        $html = '';
        $links_json = '{}';
        $link_sort = '{}';
    }

    //修改模块
    $t = '';
    if (isset($zbp->modulesbyfilename['linkmanage_'.$menuID])) {
        $t = $zbp->modulesbyfilename['linkmanage_'.$menuID];
    }
    else {
        $t = new Module();
        $t->SidebarID = 0;
        $t->FileName = 'linkmanage_'.$menuID;
        $t->Source = 'plugin_'.$menuID;
        $t->HtmlID = 'linkmanage_'.$menuID;
        $t->Type = 'ul';
    }

    if(!is_null($menuName)) {
        $t->Name = '菜单：'.$menuName;
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
    if (!isset($links['ID'.$new_link['id']])) {
        $new_check = true;
    }

    $links['ID'.$new_link['id']] = $new_link;

    if (isset($zbp->modulesbyfilename['linkmanage_'.$menuID])) {
        $t = $zbp->modulesbyfilename['linkmanage_'.$menuID];
    }
    else {
        $t = new Module();
        $t->FileName = 'linkmanage_'.$menuID;
        $t->Source = 'plugin_'.$menuID;
        $t->HtmlID = 'linkmanage_'.$menuID;
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
        unset($links['ID'.$linkID]);
        unset($link_sort[$linkID]);
        echo 0;

        linkmanage_updataModule($menuID,null,json_encode($links),json_encode($link_sort));

    }
    echo json_encode($links);
    die();
}

// 获取系统链接
function linkmanage_get_syslink($type)
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
        $array = $zbp->GetTagList('*', '', array('tag_Count' => 'DESC'), '', '');
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
