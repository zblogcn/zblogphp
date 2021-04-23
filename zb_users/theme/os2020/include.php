<?php
#注册插件
RegisterPlugin("os2020", "ActivePlugin_os2020");

function ActivePlugin_os2020()
{
    //Add_Filter_Plugin('Filter_Plugin_Zbp_PreLoad', 'os2020_Change_Url');
    Add_Filter_Plugin('Filter_Plugin_API_Begin', 'os2020_Refresh_ControlPanel_Content');
}

function os2020_Change_Url()
{
    //global $zbp;

    //$zbp->LoadPostType();
    //$zbp->LoadRoutes();
}

function os2020_Refresh_ControlPanel_Content()
{
    global $zbp;

    if ($zbp->islogin) {
        $s = '<span class="cp-hello">' . $zbp->lang['msg']['welcome'] . $zbp->user->StaticName . '(' . $zbp->user->LevelName . ')</span>';
        $s .= '<br/>';
        if ($zbp->CheckRights('admin')) {
            $s .= '<span class="cp-login"><a href="' . $zbp->systemurl . 'cmd.php?act=login">' . $zbp->lang['msg']['admin'] . '</a></span>';
        }
        $s .= '&nbsp;&nbsp;';
        if ($zbp->CheckRights('ArticleEdt')) {
            $s .= '<span class="cp-login"><a href="' . $zbp->systemurl . 'cmd.php?act=ArticleEdt">' . $zbp->lang['msg']['new_article'] . '</a></span>';
        } else {
            $s .= '<span class="cp-vrs"><a href="' . $zbp->systemurl . 'cmd.php?act=vrs">' . $zbp->lang['msg']['view_rights'] . '</a></span>';
        }
        $zbp->modulesbyfilename['controlpanel']->Content = $s;
    }
}

function InstallPlugin_os2020()
{
    global $zbp;

    if ($zbp->Config('os2020')->IsActived == true){
        return true;
    }

    $zbp->Config('os2020')->BAK_ZC_STATIC_MODE = $zbp->option['ZC_STATIC_MODE'];
    $zbp->Config('os2020')->BAK_ZC_ARTICLE_REGEX = $zbp->option['ZC_ARTICLE_REGEX'];
    $zbp->Config('os2020')->BAK_ZC_PAGE_REGEX = $zbp->option['ZC_PAGE_REGEX'];
    $zbp->Config('os2020')->BAK_ZC_INDEX_REGEX = $zbp->option['ZC_INDEX_REGEX'];
    $zbp->Config('os2020')->BAK_ZC_CATEGORY_REGEX = $zbp->option['ZC_CATEGORY_REGEX'];
    $zbp->Config('os2020')->BAK_ZC_TAGS_REGEX = $zbp->option['ZC_TAGS_REGEX'];
    $zbp->Config('os2020')->BAK_ZC_DATE_REGEX = $zbp->option['ZC_DATE_REGEX'];
    $zbp->Config('os2020')->BAK_ZC_AUTHOR_REGEX = $zbp->option['ZC_AUTHOR_REGEX'];
    $zbp->Config('os2020')->BAK_ZC_SEARCH_REGEX = $zbp->option['ZC_SEARCH_REGEX'];

    $zbp->option['ZC_STATIC_MODE'] = 'ACTIVE';
    $zbp->option['ZC_ARTICLE_REGEX'] = '{%host%}?type=acticle&id={%id%}';
    $zbp->option['ZC_PAGE_REGEX'] = '{%host%}?type=page&id={%id%}';
    $zbp->option['ZC_INDEX_REGEX'] = '{%host%}?page={%page%}';
    $zbp->option['ZC_CATEGORY_REGEX'] = '{%host%}?cate={%id%}&page={%page%}';
    $zbp->option['ZC_TAGS_REGEX'] = '{%host%}?tags={%id%}&page={%page%}';
    $zbp->option['ZC_DATE_REGEX'] = '{%host%}?date={%date%}&page={%page%}';
    $zbp->option['ZC_AUTHOR_REGEX'] = '{%host%}?auth={%id%}&page={%page%}';
    $zbp->option['ZC_SEARCH_REGEX'] = '{%host%}?search={%q%}';

    $zbp->Config('os2020')->IsActived = true;
    $zbp->Config('os2020')->Save();

    $zbp->SaveOption();
}

function UninstallPlugin_os2020()
{
    global $zbp;

    $zbp->option['ZC_STATIC_MODE'] = $zbp->Config('os2020')->BAK_ZC_STATIC_MODE;
    if(trim($zbp->option['ZC_STATIC_MODE'])==''){
        $zbp->option['ZC_STATIC_MODE'] = 'ACTIVE';
    }
    $zbp->option['ZC_ARTICLE_REGEX'] = $zbp->Config('os2020')->BAK_ZC_ARTICLE_REGEX;
    if(trim($zbp->option['ZC_ARTICLE_REGEX'])==''){
        $zbp->option['ZC_ARTICLE_REGEX'] = '{%host%}?id={%id%}';
    }
    $zbp->option['ZC_PAGE_REGEX'] = $zbp->Config('os2020')->BAK_ZC_PAGE_REGEX;
    if(trim($zbp->option['ZC_PAGE_REGEX'])==''){
        $zbp->option['ZC_PAGE_REGEX'] = '{%host%}?id={%id%}';
    }
    $zbp->option['ZC_INDEX_REGEX'] = $zbp->Config('os2020')->BAK_ZC_INDEX_REGEX;
    if(trim($zbp->option['ZC_INDEX_REGEX'])==''){
        $zbp->option['ZC_INDEX_REGEX'] = '{%host%}?page={%page%}';
    }
    $zbp->option['ZC_CATEGORY_REGEX'] = $zbp->Config('os2020')->BAK_ZC_CATEGORY_REGEX;
    if(trim($zbp->option['ZC_CATEGORY_REGEX'])==''){
        $zbp->option['ZC_CATEGORY_REGEX'] = '{%host%}?cate={%id%}&page={%page%}';
    }
    $zbp->option['ZC_TAGS_REGEX'] = $zbp->Config('os2020')->BAK_ZC_TAGS_REGEX;
    if(trim($zbp->option['ZC_TAGS_REGEX'])==''){
        $zbp->option['ZC_TAGS_REGEX'] = '{%host%}?tags={%id%}&page={%page%}';
    }
    $zbp->option['ZC_DATE_REGEX'] = $zbp->Config('os2020')->BAK_ZC_DATE_REGEX;
    if(trim($zbp->option['ZC_DATE_REGEX'])==''){
        $zbp->option['ZC_DATE_REGEX'] = '{%host%}?date={%date%}&page={%page%}';
    }
    $zbp->option['ZC_AUTHOR_REGEX'] = $zbp->Config('os2020')->BAK_ZC_AUTHOR_REGEX;
    if(trim($zbp->option['ZC_AUTHOR_REGEX'])==''){
        $zbp->option['ZC_AUTHOR_REGEX'] = '{%host%}?auth={%id%}&page={%page%}';
    }
    $zbp->option['ZC_SEARCH_REGEX'] = $zbp->Config('os2020')->BAK_ZC_SEARCH_REGEX;
    if(trim($zbp->option['ZC_SEARCH_REGEX'])==''){
        $zbp->option['ZC_SEARCH_REGEX'] = '{%host%}search.php?q={%q%}&page={%page%}';
    }
    $zbp->Config('os2020')->Delete();
    $zbp->SaveOption();
}
