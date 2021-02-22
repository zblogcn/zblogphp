<?php
#注册插件
RegisterPlugin("os2020", "ActivePlugin_os2020");

function ActivePlugin_os2020()
{
    Add_Filter_Plugin('Filter_Plugin_Zbp_PreLoad', 'os2020_Change_Url');
}

function os2020_Change_Url()
{
    global $zbp;

    $zbp->option['ZC_STATIC_MODE'] = 'ACTIVE';
    $zbp->option['ZC_ARTICLE_REGEX'] = '{%host%}?type=acticle&id={%id%}';
    $zbp->option['ZC_PAGE_REGEX'] = '{%host%}?type=page&id={%id%}';
    $zbp->option['ZC_INDEX_REGEX'] = '{%host%}?page={%page%}';
    $zbp->option['ZC_CATEGORY_REGEX'] = '{%host%}?cate={%id%}&page={%page%}';
    $zbp->option['ZC_TAGS_REGEX'] = '{%host%}?tags={%id%}&page={%page%}';
    $zbp->option['ZC_DATE_REGEX'] = '{%host%}?date={%date%}&page={%page%}';
    $zbp->option['ZC_AUTHOR_REGEX'] = '{%host%}?auth={%id%}&page={%page%}';

    $zbp->LoadPostType();
    $zbp->LoadRoutes();
}

function InstallPlugin_os2020()
{
}

function UninstallPlugin_os2020()
{
}
