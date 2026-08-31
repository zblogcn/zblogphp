<?php

/**
 * 杂项之统计函数
 * 注：目前还是走系统的更新.
 */
function zbp_admin2_statistic()
{
    global $zbp;

    // 当模板文件有修改，或者传入强制刷新参数时，重建模板编译
    $zbp->CheckTemplate((bool) GetVars('forced', 'GET'));

    // 获取上次刷新时间
    $reload_statistic_time = $zbp->cache->reload_statistic_time;

    if (!($zbp->CheckRights('root') || (time() - (int) $reload_statistic_time) > (23 * 60 * 60))) {
        echo $zbp->ShowError(6, __FILE__, __LINE__);

        exit();
    }

    // 统计公开文章，对应数据为 $zbp->cache->normal_article_nums
    CountNormalArticleNums(null);

    // 判断是否开启 large_data
    $ZC_LARGE_DATA = $zbp->option['ZC_LARGE_DATA'];

    if (!$ZC_LARGE_DATA) {
        // 统计评论数，对应数据为
        // $zbp->cache->all_comment_nums
        // $zbp->cache->check_comment_nums
        // $zbp->cache->normal_comment_nums
        CountCommentNums(null, null);
        // 统计置顶文章
        CountTopPost(ZC_POST_TYPE_ARTICLE, null, null);
        // 全部文章，页面，用户
        $zbp->cache->all_article_nums = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Post'])->count(['*' => 'num'])->where(['=', 'log_Type', '0'])->query, 'num');
        $zbp->cache->all_page_nums = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Post'])->count(['*' => 'num'])->where(['=', 'log_Type', '1'])->query, 'num');
        $zbp->cache->all_member_nums = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Member'])->count(['*' => 'num'])->query, 'num');
    }
    // 阅读量统计开关
    $ZC_VIEWNUMS_TURNOFF = $zbp->option['ZC_VIEWNUMS_TURNOFF'];
    // 统计文章阅读量
    if (!$ZC_VIEWNUMS_TURNOFF && !$ZC_LARGE_DATA) {
        $zbp->cache->all_view_nums = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Post'])->sum(['log_ViewNums' => 'num'])->query, 'num');
    } else {
        $zbp->cache->all_view_nums = 0;
    }
    // 分类及标签统计
    $zbp->cache->all_category_nums = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Category'])->count(['*' => 'num'])->query, 'num');
    $zbp->cache->all_tag_nums = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Tag'])->count(['*' => 'num'])->query, 'num');

    $zbp->cache->reload_statistic_time = time();
    // 环境信息及当前时间
    $zbp->cache->system_environment = GetEnvironment();

    $zbp->SaveCache();
}

/**
 * 返回站点信息数据.
 */
function zbp_admin2_statistic_data()
{
    global $zbp;
    $data = new stdClass();

    // api xmlrpc
    $data->api_address = "<a href=\"{$zbp->apiurl}\" target=\"_blank\">{$zbp->lang['msg']['api_address']}</a>";
    $data->xmlrpc_address = "<a href=\"{$zbp->xmlrpcurl}\" target=\"_blank\">{$zbp->lang['msg']['xmlrpc_address']}</a>";

    // 系统版本信息，包括应用中心，数据库记录的版本是否最新
    $app = $zbp->LoadApp('plugin', 'AppCentre');
    $sv = ZC_VERSION_FULL;
    if (true == $app->isloaded && $app->IsUsed()) {
        $sv .= '; AppCentre' . $app->version;
    }
    if ($zbp->option['ZC_LAST_VERSION'] < ZC_LAST_VERSION) {
        $sv .= '; Db' . ZC_LAST_VERSION;
    }

    // 系统信息
    $data->current_isroot = '';
    $data->current_member = $zbp->user->Name;
    $data->current_style = $zbp->style;
    $data->current_theme = $zbp->theme;
    $data->current_theme_version = $zbp->themeapp->version;
    $data->current_version = $sv;
    $data->system_environment = $zbp->cache->system_environment;

    $array = explode(';', $data->system_environment);
    $data->system_environment1 = @$array[0] . '; ' . @$array[1] . '; ' . @$array[2];
    $data->system_environment2 = @$array[3] . '; ' . @$array[4] . '; ' . @$array[5];

    // 统计信息
    $data->all_articles = $zbp->cache->all_article_nums;
    $data->all_categories = $zbp->cache->all_category_nums;
    $data->all_comments = $zbp->cache->all_comment_nums;
    $data->all_members = $zbp->cache->all_member_nums;
    $data->all_pages = $zbp->cache->all_page_nums;
    $data->all_tags = $zbp->cache->all_tag_nums;
    $data->all_views = $zbp->cache->all_view_nums;
    $data->all_uploads = 123; //还没做！！！
    // 更新时间及更新链接
    $data->reload_time = date('c', (int) $zbp->cache->reload_statistic_time);
    $data->reload_url = BuildSafeCmdURL('act=misc&type=statistic&forced=1');

    // 公告获取
    $data->reload_updateinfo = $zbp->cache->reload_updateinfo;
    $data->reload_updateinfo_time = date('c', (int) $zbp->cache->reload_updateinfo_time);
    $data->reload_reload_updateinfo_url = BuildSafeCmdURL('act=misc&type=updateinfo');

    // thanks
    $data->thanksInfo = zbp_admin2_get_thanks();
    // 替换语言项
    foreach ($data->thanksInfo as &$group) {
        switch ($group['category']) {
            case '程序':
                $group['category'] = $zbp->lang['msg']['program'];

                break;

            case '界面':
                $group['category'] = $zbp->lang['msg']['interface'];

                break;

            case '支持':
                $group['category'] = $zbp->lang['msg']['support'];

                break;

            case '感谢':
                $group['category'] = $zbp->lang['msg']['thanks'];

                break;

            case '链接':
                $group['category'] = $zbp->lang['msg']['website'];

                break;

            default:
                // 保持原样
        }
    }

    return $data;
}

/**
 * 管理页 SubMenu 统一处理函数
 * 根据 $action 执行相应的 SubMenu 接口，捕获接口内的 echo 内容并返回.
 *
 * @param mixed $action
 */
function zbp_admin2_GenSubMenu($action)
{
    global $zbp;

    // 开启输出缓冲
    ob_start();

    switch ($action) {
    case 'admin':
      HookFilterPlugin('Filter_Plugin_Admin_SiteInfo_SubMenu');

      break;

    case 'ArticleMng':
      HookFilterPlugin('Filter_Plugin_Admin_ArticleMng_SubMenu');

      break;

    case 'PageMng':
      HookFilterPlugin('Filter_Plugin_Admin_PageMng_SubMenu');

      break;

    case 'CategoryMng':
      HookFilterPlugin('Filter_Plugin_Admin_CategoryMng_SubMenu');

      break;

    case 'CategoryEdt':
      HookFilterPlugin('Filter_Plugin_Category_Edit_SubMenu');

      break;

    case 'TagMng':
      HookFilterPlugin('Filter_Plugin_Admin_TagMng_SubMenu');

      break;

    case 'TagEdt':
      HookFilterPlugin('Filter_Plugin_Tag_Edit_SubMenu');

      break;

    case 'CommentMng':
      HookFilterPlugin('Filter_Plugin_Admin_CommentMng_SubMenu');

      break;

    case 'UploadMng':
      HookFilterPlugin('Filter_Plugin_Admin_UploadMng_SubMenu');

      break;

    case 'MemberMng':
      HookFilterPlugin('Filter_Plugin_Admin_MemberMng_SubMenu');

      break;

    case 'MemberNew':
    case 'MemberEdt':
      HookFilterPlugin('Filter_Plugin_Member_Edit_SubMenu');

      break;

    case 'ModuleMng':
      HookFilterPlugin('Filter_Plugin_Admin_ModuleMng_SubMenu');

      break;

    case 'ModuleEdt':
      HookFilterPlugin('Filter_Plugin_Module_Edit_SubMenu');

      break;

    case 'ThemeMng':
      HookFilterPlugin('Filter_Plugin_Admin_ThemeMng_SubMenu');

      break;

    case 'PluginMng':
      HookFilterPlugin('Filter_Plugin_Admin_PluginMng_SubMenu');

      break;

    case 'SettingMng':
      HookFilterPlugin('Filter_Plugin_Admin_SettingMng_SubMenu');

      break;

    default:
      // 如果没有匹配的 action，返回空字符串
      return '';
  }

    // 获取并清空输出缓冲区的内容
    $content = ob_get_clean();

    return $content;
}

/**
 * 安全相关的 header 设置以及 csrf 过期时间调整.
 */
function zbp_admin2_security()
{
    global $zbp;

    if ($zbp->option['ZC_ADDITIONAL_SECURITY']) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Content-Security-Policy: ' . GetBackendCSPHeader());
        if ($zbp->isHttps) {
            header('Upgrade-Insecure-Requests: 1');
        }
    }

    if ('ArticleEdt' === $zbp->action || 'PageEdt' === $zbp->action) {
        $zbp->csrfExpiration = 48;
    }
}

/**
 * 封装一个函数，用于返回 thanks.php.
 */
function zbp_admin2_get_thanks()
{
    return require $GLOBALS['zbp']->path . 'zb_system/defend/thanks.php';
}
