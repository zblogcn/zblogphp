<?php

function x2013_SubMenu($id)
{
    $arySubMenu = array(
        0 => array('常规设置', 'main.php', 'left', false),
        1 => array('导航设置', 'navbar.php', 'left', false),
        2 => array('帮助说明', 'about.php', 'right', false),
        3 => array('广告设置', 'ad.php', 'left', false),
    );
    foreach ($arySubMenu as $k => $v) {
        echo '<a href="' . $v[1] . '" ' . ($v[3] == true ? 'target="_blank"' : '') . '><span class="m-' . $v[2] . ' ' . ($id == $k ? 'm-now' : '') . '">' . $v[0] . '</span></a>';
    }
}

function x2013_get_link($type)
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
