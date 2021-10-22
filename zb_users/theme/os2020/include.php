<?php
#注册插件
RegisterPlugin("os2020", "ActivePlugin_os2020");

function ActivePlugin_os2020()
{
    Add_Filter_Plugin('Filter_Plugin_Zbp_PreLoad', 'os2020_Change_Url');
    Add_Filter_Plugin('Filter_Plugin_API_Begin', 'os2020_Refresh_ControlPanel_Content');
}

function os2020_Change_Url()
{
    global $zbp;

    $zbp->routes['active_post_article_single']['urlrule'] = '{%host%}?type=acticle&id={%id%}';
    $zbp->routes['active_post_page_single']['urlrule'] = '{%host%}?type=page&id={%id%}';
    $zbp->routes['default_post_article_list']['urlrule'] = '{%host%}?page={%page%}';
    $zbp->routes['active_post_article_list_category']['urlrule'] = '{%host%}?cate={%id%}&page={%page%}';
    $zbp->routes['active_post_article_list_tag']['urlrule'] = '{%host%}?tags={%id%}&page={%page%}';
    $zbp->routes['active_post_article_list_date']['urlrule'] = '{%host%}?date={%date%}&page={%page%}';
    $zbp->routes['active_post_article_list_author']['urlrule'] = '{%host%}?auth={%id%}&page={%page%}';

    $zbp->RegRoute(
      array(
        'posttype' => 0,
        'type' => 'active',
        'name' => 'post_article_search',
        'call' => 'ViewSearch',
        'get' => 
        array (
          'q' => '[^\\/_]+',
        ),
        'urlrule' => '{%host%}?search={%q%}',
      )
    );

    $zbp->option['ZC_STATIC_MODE']= 'ACTIVE';

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
}

function UninstallPlugin_os2020()
{
    global $zbp;
}
