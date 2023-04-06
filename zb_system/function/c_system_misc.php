<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 吏新官网信息.
 */
function misc_updateinfo()
{
    global $zbp;

    CheckIsRefererValid();
    if (!$zbp->CheckRights('root')) {
        echo $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }

    $r = GetHttpContent($zbp->option['ZC_UPDATE_INFO_URL']);
    $r = TransferHTML($r, '[noscript]');

    $r = '<tr><td>' . $r . '</td></tr>';

    $zbp->LoadConfigs();
    $zbp->LoadCache();
    $zbp->cache->reload_updateinfo = $r;
    $zbp->cache->reload_updateinfo_time = time();
    $zbp->SaveCache();

    echo $r;
}

/**
 * 杂项之统计函数
 */
function misc_statistic()
{
    global $zbp;

    if (!defined('ZBP_IN_API')) {
        CheckIsRefererValid();
    }
    if (!$zbp->CheckRights('admin')) {
        echo $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }

    //当需要rebuild才会rebuild,如果forced=1就强制rebuild
    $zbp->CheckTemplate(false, (bool) GetVars('forced', 'GET'));

    if (!($zbp->CheckRights('root') || (time() - (int) $zbp->cache->reload_statistic_time) > (23 * 60 * 60))) {
        echo $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }

    $r = null;

    CountNormalArticleNums(null);

    //按条件统计或不统计
    if ($zbp->option['ZC_LARGE_DATA'] == false) {
        CountCommentNums(null, null);
        CountTopPost(ZC_POST_TYPE_ARTICLE, null, null);
        $all_articles = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Post'])->count(array('*' => 'num'))->where(array('=', 'log_Type', '0'))->query, 'num');
        $all_pages = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Post'])->count(array('*' => 'num'))->where(array('=', 'log_Type', '1'))->query, 'num');
        $all_members = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Member'])->count(array('*' => 'num'))->query, 'num');
        $all_comments = $zbp->cache->all_comment_nums;
        $check_comment_nums = $zbp->cache->check_comment_nums;
    } else {
        $all_articles = $zbp->cache->all_article_nums;
        $all_pages = $zbp->cache->all_page_nums;
        $all_members = $zbp->cache->all_member_nums;
        $all_comments = $zbp->cache->all_comment_nums;
        $check_comment_nums = $zbp->cache->check_comment_nums;
    }
    if ($zbp->option['ZC_LARGE_DATA'] == true || $zbp->option['ZC_VIEWNUMS_TURNOFF'] == true) {
        $all_views = 0;
    } else {
        $all_views = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Post'])->sum(array('log_ViewNums' => 'num'))->query, 'num');
    }
    //一直统计
    $all_categories = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Category'])->count(array('*' => 'num'))->query, 'num');
    $all_tags = GetValueInArrayByCurrent($zbp->db->sql->get()->select($GLOBALS['table']['Tag'])->count(array('*' => 'num'))->query, 'num');

    $xmlrpc_address = '<a href="' . $zbp->xmlrpcurl . '" target="_blank">' . $zbp->lang['msg']['xmlrpc_address'] . '</a>';
    $api_address = '<a href="' . $zbp->apiurl . '" target="_blank">' . $zbp->lang['msg']['api_address'] . '</a>';
    $current_member = $zbp->user->Name;
    $current_version = ZC_VERSION_FULL;
    $current_theme = '{$zbp->theme}';
    $current_style = '{$zbp->style}';
    $current_member = '{$zbp->user->Name}';
    $current_version = '{$zbp->version}';
    $current_isroot = '{$zbp->user->IsGod}';
    $system_environment = '{$system_environment}';
    $current_theme_version = '{$theme_version}';

    $r .= '<!--debug_mode_note-->';
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_member']}</td><td class='td30'>{$current_isroot}<a href='../cmd.php?act=misc&type=vrs' target='_blank'>{$current_member}</a></td><td class='td20'>{$zbp->lang['msg']['current_version']}</td><td class='td30'>{$current_version}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_artiles']}</td><td>{$all_articles}</td><td>{$zbp->lang['msg']['all_categorys']}</td><td>{$all_categories}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_pages']}</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}</td><td>{$all_tags}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_comments']}</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}</td><td>{$all_views}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_theme']}</td><td>{$current_theme}/{$current_style} {$current_theme_version}</td><td>{$zbp->lang['msg']['all_members']}</td><td>{$all_members}</td></tr>";
    $r .= '<!--debug_mode_moreinfo-->';
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['protocol_address']}</td><td>{$api_address} , {$xmlrpc_address}</td><td>{$zbp->lang['msg']['system_environment']}</td><td><a href='../cmd.php?act=misc&type=phpinfo' target='_blank'>{$system_environment}</a></td></tr>";
    $r .= "<script type=\"text/javascript\">$('#statistic').attr('title','" . date("c", (int) $zbp->cache->reload_statistic_time) . "');</script>";

    $zbp->cache->reload_statistic = $r;
    $zbp->cache->reload_statistic_time = time();
    $zbp->cache->system_environment = GetEnvironment();
    $zbp->cache->all_article_nums = $all_articles;
    $zbp->cache->all_page_nums = $all_pages;
    $zbp->cache->all_category_nums = $all_categories;
    $zbp->cache->all_view_nums = $all_views;
    $zbp->cache->all_tag_nums = $all_tags;
    $zbp->cache->all_comment_nums = $all_comments;
    $zbp->cache->all_member_nums = $all_members;
    $zbp->cache->check_comment_nums = $check_comment_nums;

    $r = str_replace('{#ZC_BLOG_HOST#}', $zbp->host, $r);
    $r = str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
    $r = str_replace('{$zbp->user->IsGod}', '', $r);
    $r = str_replace('{$zbp->theme}', $zbp->theme, $r);
    $r = str_replace('{$zbp->style}', $zbp->style, $r);
    $app = $zbp->LoadApp('plugin', 'AppCentre');
    $sv = ZC_VERSION_FULL;
    if ($app->isloaded == true && $app->IsUsed()) {
        $sv .= '; AppCentre' . $app->version;
    }
    $r = str_replace('{$zbp->version}', $sv, $r);
    $r = str_replace('{$system_environment}', $zbp->cache->system_environment, $r);
    $r = str_replace('{$theme_version}', '(v' . $zbp->themeapp->version . ')', $r);

    if ($zbp->isdebug) {
        $r = str_replace('<!--debug_mode_note-->', "<tr><td colspan='4' style='text-align: center'>{$zbp->lang['msg']['debugging_warning']}</td></tr>", $r);
    }

    //增加模块内容（因模块模板改变）而刷新的机制
    try {
        if ($zbp->build_system_module) {
            $zbp->AddBuildModule('statistics', array($all_articles, $all_pages, $all_categories, $all_tags, $all_views, $all_comments));
            $zbp->AddBuildModule('previous');
            $zbp->AddBuildModule('calendar');
            $zbp->AddBuildModule('comments');
            $zbp->AddBuildModule('tags');
            $zbp->AddBuildModule('authors');
            $zbp->AddBuildModule('catalog');
            $zbp->AddBuildModule('navbar');
        }
    } catch (Throwable $t) {
    } catch (Exception $e) {
        //echo $e->getMessage();
    }
    $zbp->BuildModule();
    $zbp->SaveCache();

    echo $r;
}

/**
 * 杂项之显示标签
 */
function misc_showtags()
{
    global $zbp;

    $type = (int) GetVars('type');
    $actions = $zbp->GetPostType($type, 'actions');

    $zbp->csrfExpiration = 48;
    CheckIsRefererValid();
    if (!$zbp->CheckRights($actions['new']) || !$zbp->CheckRights($actions['edit'])) {
        Http404();
        die();
    }

    header('Content-Type: application/x-javascript; Charset=utf-8');
    header('Cache-Control: private');
    echo '$("#ajaxtags").html("';

    $array = $zbp->GetTagList(null, array('=', 'tag_Type', $type), array('tag_Count' => 'DESC', 'tag_ID' => 'ASC'), array(100), null);
    if (count($array) > 0) {
        $t = array();
        foreach ($array as $tag) {
            echo '<a href=\"#\">' . $tag->Name . '</a>';
        }
    }

    echo '");$("#ulTag").tagTo("#edtTag");';
}

/**
 * 杂项之显示权项
 */
function misc_vrs()
{
    global $zbp, $blogtitle;

    if (!$zbp->CheckRights('misc')) {
        $zbp->ShowError(6, __FILE__, __LINE__);
    }

    $blogtitle = $zbp->name . '-' . $zbp->lang['msg']['view_rights']; ?>
    <!DOCTYPE HTML>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php
        if (strpos(GetVars('HTTP_USER_AGENT', 'SERVER'), 'Trident/')) {
            ?>
            <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
            <?php
        }
        ?>
        <meta name="robots" content="none" />
        <meta name="generator" content="<?php echo $GLOBALS['option']['ZC_BLOG_PRODUCT_FULL']; ?>" />
        <link rel="stylesheet" href="css/admin.css?<?php echo $GLOBALS['blogversion']; ?>" type="text/css" media="screen" />
        <script src="script/common.js?<?php echo $GLOBALS['blogversion']; ?>"></script>
        <script src="script/c_admin_js_add.php?hash=<?php echo $zbp->html_js_hash; ?>&<?php echo $GLOBALS['blogversion']; ?>"></script>
        <?php
        HookFilterPlugin('Filter_Plugin_Other_Header');
        ?>
        <title><?php echo $blogtitle; ?></title>
    </head>

    <body class="short">
        <div class="bg">
            <div id="wrapper">
                <div class="logo"><img src="image/admin/none.gif" title="Z-BlogPHP" alt="Z-BlogPHP" /></div>
                <div class="login">
                    <form method="post" action="#">
                        <dl>
                            <dt><?php echo $zbp->lang['msg']['current_member'] . ' : <b>' . $zbp->user->Name; ?></b><br />
                                <?php echo $zbp->lang['msg']['member_level'] . ' : <b>' . $zbp->user->LevelName . ($zbp->user->Status > 0 ? '(' . $zbp->lang['user_status_name'][$zbp->user->Status] . ')' : ''); ?></b></dt>
    <?php
    foreach ($GLOBALS['actions'] as $key => $value) {
        if ($GLOBALS['zbp']->CheckRights($key)) {
            echo '<dd><b>' . $zbp->GetActionName($key) . '</b> : ' . ($zbp->CheckRights($key) ? '<span style="color:green">true</span>' : '<span style="color:red">false</span>') . '</dd>';
        }
    }
    ?>
                        </dl>
                    </form>
                </div>
            </div>
        </div>
    </body>

    </html>
    <?php
    RunTime();
}

/**
 * 杂项之显示PhpInfo
 */
function misc_php_zbp_info()
{
    global $zbp, $blogtitle;

    if (!$zbp->CheckRights('root')) {
        echo $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }

    $match = array();
    $blogtitle = $zbp->name . '-phpinfo';
    ob_start();
    $pi = 'php' . "info";
    $pi();
    $s = ob_get_clean();

    if (PHP_ENGINE !== ENGINE_HHVM) {
        preg_match("/<body.*?>(.*?)<\/body>/is", $s, $match);
    }
    ?>
    <!DOCTYPE HTML>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <?php
        if (strpos(GetVars('HTTP_USER_AGENT', 'SERVER'), 'Trident/')) {
            ?>
            <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
            <?php
        }
        ?>
        <meta name="robots" content="none" />
        <meta name="generator" content="<?php echo $GLOBALS['option']['ZC_BLOG_PRODUCT_FULL']; ?>" />
        <link rel="stylesheet" href="css/admin.css?<?php echo $GLOBALS['blogversion']; ?>" type="text/css" media="screen" />
        <script src="script/common.js?<?php echo $GLOBALS['blogversion']; ?>"></script>
        <script src="script/c_admin_js_add.php?hash=<?php echo $zbp->html_js_hash; ?>&<?php echo $GLOBALS['blogversion']; ?>"></script>
        <?php
        HookFilterPlugin('Filter_Plugin_Other_Header');
        ?>
        <title><?php echo $blogtitle; ?></title>
        <style type="text/css">
            * {
                color: #000;
            }

            pre {
                margin: 0;
                font-family: monospace;
            }

            a:link {
                color: #009;
                text-decoration: none;
                background-color: #fff;
            }

            a:hover {
                text-decoration: underline;
            }

            table {
                border-collapse: collapse;
                border: 0;
                width: 934px;
                box-shadow: 1px 2px 3px #ccc;
            }

            .center {
                text-align: center;
            }

            .center table {
                margin: 1em auto;
                text-align: left;
            }

            .center th {
                text-align: center !important;
            }

            td,
            th {
                border: 1px solid #666;
                font-size: 75%;
                vertical-align: baseline;
                padding: 4px 5px;
            }

            h1 {
                font-size: 150%;
            }

            h2 {
                font-size: 125%;
            }

            .p {
                text-align: left;
            }

            .e {
                background-color: #ccf;
                width: 300px;
                font-weight: bold;
            }

            .h {
                background-color: #99c;
                font-weight: bold;
            }

            .v {
                background-color: #ddd;
                max-width: 300px;
                overflow-x: auto;
            }

            .v i {
                color: #999;
            }

            img {
                float: right;
                border: 0;
            }

            hr {
                display: none;
            }

            div.bg {
                background: #3a6ea5 !important;
            }

            .h {
                background: #78a2ce !important;
            }

            .e {
                background: #c6dcf3 !important;
            }

            table a img {
                filter: hue-rotate(-30deg);
            }
        </style>
    </head>

    <body class="short">
        <div class="bg">
            <div id="wrapper">
                <div class="logo"><a href="#zblogphp"><img src="image/admin/none.gif" title="Z-BlogPHP" alt="Z-BlogPHP" /></a></div>
                <?php
                if (PHP_ENGINE === ENGINE_HHVM) {
                    echo '<p style="text-align: center;">' . GetEnvironment() . '</p>';
                } else {
                    echo $match[0];
                }
                echo '<div class="center"><div><br/><br/><a name="zblogphp"><h1>Z-BlogPHP</h1></a><br/></div>';

                $ca = get_defined_constants(true);
                $ca = $ca['user'];
                echo '<table class="table_striped table_hover"><tbody><tr class="h"><th colspan="2">Z-BlogPHP Constants</th></tr>';
                foreach ($ca as $key => $value) {
                    echo '<tr><td class="e">' . $key . '</td><td class="v">' . TransferHTML($value, '[nohtml]') . '</td></tr>';
                }
                echo '</tbody></table>';

                echo '</tbody></table>';

                $ca = array();
                $badfilter = array();
                foreach ($GLOBALS as $n => $v) {
                    if (strpos($n, 'Filter_Plugin_') === false) {
                        if (gettype($v) == 'integer' || gettype($v) == 'double' || gettype($v) == 'string' || gettype($v) == 'boolean') {
                            $ca['$' . $n] = '(' . gettype($v) . ') ' . TransferHTML($v, '[nohtml]');
                        } else {
                            $ca['$' . $n] = '(' . gettype($v) . ')';
                        }
                    } else {
                        $badfilter[$n] = $n;
                    }
                }
                echo '<table class="table_striped table_hover"><tbody><tr class="h"><th colspan="2">Globals Variables</th></tr>';
                foreach ($ca as $key => $value) {
                    echo '<tr><td class="e">' . $key . '</td><td class="v">' . $value . '</td></tr>';
                }
                echo '</tbody></table>';

                echo '</tbody></table>';

                $ca = array();
                foreach (get_included_files() as $n => $v) {
                    $ca[] = $v;
                }
                echo '<table class="table_striped table_hover"><tbody><tr class="h"><th colspan="2">Include Files</th></tr>';
                $i = 0;
                foreach ($ca as $key => $value) {
                    $i++;
                    echo '<tr><td class="e">' . $i . '</td><td class="v">' . $value . '</td></tr>';
                }
                echo '</tbody></table>';

                echo '</tbody></table>';

                $ca = array();
                foreach ($GLOBALS['hooks'] as $n => $v) {
                    $ca[$n] = $n;
                }
                $i = 0;
                $badfilter = array_diff_key($badfilter, $ca);
                echo '<table class="table_striped table_hover"><tbody><tr class="h"><th colspan="2">Plugin Filters</th></tr>';
                foreach ($ca as $key => $value) {
                    $i++;
                    $s = '';
                    foreach ($GLOBALS['hooks'][$value] as $function => $sg) {
                        $s .= '<br/>&nbsp;&nbsp;→' . $function;
                    }
                    echo '<tr><td class="e">' . $i . '</td><td class="v">' . $value . $s . '</td></tr>';
                }
                foreach ($badfilter as $key => $value) {
                    $i++;
                    echo '<tr><td class="e">' . $i . '(no specification)' . '</td><td class="v">' . $value . '</td></tr>';
                }
                echo '</tbody></table>';

                $ca = get_defined_functions();
                $i = 0;
                echo '<table class="table_striped table_hover"><tbody><tr class="h"><th colspan="2">User Functions</th></tr>';
                foreach ($ca as $key => $value) {
                    if ($key !== 'user') {
                        continue;
                    }
                    foreach ($value as $key2 => $value2) {
                        $i++;
                        echo '<tr><td class="e">' . $i . '</td><td class="v">' . $value2 . '</td></tr>';
                    }
                }
                echo '</tbody></table>';

                echo '<table class="table_striped table_hover"><tbody><tr class="h"><th colspan="2">Others</th></tr>';
                if (function_exists('php_uname')) {
                    $pu = 'php' . "_" . 'uname';
                    echo '<tr><td class="e">' . 'php_uname()' . '</td><td class="v">' . $pu() . '</td></tr>';
                    echo '<tr><td class="e">' . 'php_uname(s)' . '</td><td class="v">' . $pu('s') . '</td></tr>';
                    echo '<tr><td class="e">' . 'php_uname(n)' . '</td><td class="v">' . $pu('n') . '</td></tr>';
                    echo '<tr><td class="e">' . 'php_uname(r)' . '</td><td class="v">' . $pu('r') . '</td></tr>';
                    echo '<tr><td class="e">' . 'php_uname(v)' . '</td><td class="v">' . $pu('v') . '</td></tr>';
                    echo '<tr><td class="e">' . 'php_uname(m)' . '</td><td class="v">' . $pu('m') . '</td></tr>';
                }

                $a = array();
                if (function_exists('get_declared_classes')) {
                    $a = get_declared_classes();
                }
                foreach ($a as $key => $value) {
                    echo '<tr><td class="e">' . 'classes' . '</td><td class="v">' . $value . '</td></tr>';
                }
                $a = array();
                if (function_exists('get_declared_interfaces')) {
                    $a = get_declared_interfaces();
                }
                foreach ($a as $key => $value) {
                    echo '<tr><td class="e">' . 'interfaces' . '</td><td class="v">' . $value . '</td></tr>';
                }

                $a = array();
                if (function_exists('get_declared_traits')) {
                    $a = get_declared_traits();
                }
                foreach ($a as $key => $value) {
                    echo '<tr><td class="e">' . 'traits' . '</td><td class="v">' . $value . '</td></tr>';
                }

                echo '</tbody></table>';


                $c = 'PHP_VERSION , PHP_VERSION_ID , PHP_OS , PHP_SAPI , PHP_EOL ,  PHP_INT_MAX ,  PHP_INT_SIZE ,  DEFAULT_INCLUDE_PATH , PEAR_INSTALL_DIR , PEAR_EXTENSION_DIR , PHP_EXTENSION_DIR , PHP_PREFIX , PHP_BINDIR , PHP_LIBDIR , PHP_DATADIR , PHP_SYSCONFDIR , PHP_LOCALSTATEDIR , PHP_CONFIG_FILE_PATH , PHP_CONFIG_FILE_SCAN_DIR , PHP_SHLIB_SUFFIX ,  PHP_OUTPUT_HANDLER_START , PHP_OUTPUT_HANDLER_CONT , PHP_OUTPUT_HANDLER_END , E_ERROR , E_WARNING , E_PARSE , E_NOTICE , E_CORE_ERROR , E_CORE_WARNING , E_COMPILE_ERROR , E_COMPILE_WARNING , E_USER_ERROR , E_USER_WARNING , E_USER_NOTICE , E_ALL , E_STRICT , __COMPILER_HALT_OFFSET__ ,  EXTR_OVERWRITE , EXTR_SKIP , EXTR_PREFIX_SAME , EXTR_PREFIX_ALL , EXTR_PREFIX_INVALID , EXTR_PREFIX_IF_EXISTS , EXTR_IF_EXISTS , SORT_ASC , SORT_DESC , SORT_REGULAR , SORT_NUMERIC , SORT_STRING , CASE_LOWER , CASE_UPPER , COUNT_NORMAL , COUNT_RECURSIVE , ASSERT_ACTIVE , ASSERT_CALLBACK , ASSERT_BAIL , ASSERT_WARNING , ASSERT_QUIET_EVAL , CONNECTION_ABORTED , CONNECTION_NORMAL , CONNECTION_TIMEOUT , INI_USER , INI_PERDIR , INI_SYSTEM , INI_ALL , M_E , M_LOG2E , M_LOG10E , M_LN2 , M_LN10 , M_PI , M_PI_2 , M_PI_4 , M_1_PI , M_2_PI , M_2_SQRTPI , M_SQRT2 , M_SQRT1_2 , CRYPT_SALT_LENGTH , CRYPT_STD_DES , CRYPT_EXT_DES , CRYPT_MD5 , CRYPT_BLOWFISH , DIRECTORY_SEPARATOR , SEEK_SET , SEEK_CUR , SEEK_END , LOCK_SH , LOCK_EX , LOCK_UN , LOCK_NB , HTML_SPECIALCHARS , HTML_ENTITIES , ENT_COMPAT , ENT_QUOTES , ENT_NOQUOTES , INFO_GENERAL , INFO_CREDITS , INFO_CONFIGURATION , INFO_MODULES , INFO_ENVIRONMENT , INFO_VARIABLES , INFO_LICENSE , INFO_ALL , CREDITS_GROUP , CREDITS_GENERAL , CREDITS_SAPI , CREDITS_MODULES , CREDITS_DOCS , CREDITS_FULLPAGE , CREDITS_QA , CREDITS_ALL , STR_PAD_LEFT , STR_PAD_RIGHT , STR_PAD_BOTH , PATHINFO_DIRNAME , PATHINFO_BASENAME , PATHINFO_EXTENSION , PATH_SEPARATOR , CHAR_MAX , LC_CTYPE , LC_NUMERIC , LC_TIME , LC_COLLATE , LC_MONETARY , LC_ALL , LC_MESSAGES , ABDAY_1 , ABDAY_2 , ABDAY_3 , ABDAY_4 , ABDAY_5 , ABDAY_6 , ABDAY_7 , DAY_1 , DAY_2 , DAY_3 , DAY_4 , DAY_5 , DAY_6 , DAY_7 , ABMON_1 , ABMON_2 , ABMON_3 , ABMON_4 , ABMON_5 , ABMON_6 , ABMON_7 , ABMON_8 , ABMON_9 , ABMON_10 , ABMON_11 , ABMON_12 , MON_1 , MON_2 , MON_3 , MON_4 , MON_5 , MON_6 , MON_7 , MON_8 , MON_9 , MON_10 , MON_11 , MON_12 , AM_STR , PM_STR , D_T_FMT , D_FMT , T_FMT , T_FMT_AMPM , ERA , ERA_YEAR , ERA_D_T_FMT , ERA_D_FMT , ERA_T_FMT , ALT_DIGITS , INT_CURR_SYMBOL , CURRENCY_SYMBOL , CRNCYSTR , MON_DECIMAL_POINT , MON_THOUSANDS_SEP , MON_GROUPING , POSITIVE_SIGN , NEGATIVE_SIGN , INT_FRAC_DIGITS , FRAC_DIGITS , P_CS_PRECEDES , P_SEP_BY_SPACE , N_CS_PRECEDES , N_SEP_BY_SPACE , P_SIGN_POSN , N_SIGN_POSN , DECIMAL_POINT , RADIXCHAR , THOUSANDS_SEP , THOUSEP , GROUPING , YESEXPR , NOEXPR , YESSTR , NOSTR , CODESET , LOG_EMERG , LOG_ALERT , LOG_CRIT , LOG_ERR , LOG_WARNING , LOG_NOTICE , LOG_INFO , LOG_DEBUG , LOG_KERN , LOG_USER , LOG_MAIL , LOG_DAEMON , LOG_AUTH , LOG_SYSLOG , LOG_LPR , LOG_NEWS , LOG_UUCP , LOG_CRON , LOG_AUTHPRIV , LOG_LOCAL0 , LOG_LOCAL1 , LOG_LOCAL2 , LOG_LOCAL3 , LOG_LOCAL4 , LOG_LOCAL5 , LOG_LOCAL6 , LOG_LOCAL7 , LOG_PID , LOG_CONS , LOG_ODELAY , LOG_NDELAY , LOG_NOWAIT , LOG_PERROR ,  PCRE_VERSION';
                echo '<table class="table_striped table_hover"><tbody><tr class="h"><th colspan="2">PHP Constants</th></tr>';
                $ca = explode(",", $c);
                foreach ($ca as $key => $value) {
                    echo '<tr><td class="e">' . $value . '</td><td class="v">';
                    if (defined(trim($value))) {
                        echo constant(trim($value));
                    }
                    echo '</td></tr>';
                }
                echo '</tbody></table>';



                echo '</div>';
                ?>
            </div>
        </div>
    </body>

    </html>
    <?php
    RunTime();
}

/**
 * 杂项之响应Ping
 */
function misc_respondping()
{
    $token = GetVars('token', 'GET');
    if (VerifyWebToken($token, "")) {
        echo 'ok';
        die;
    }
}

/**
 * 杂项之Ping
 */
function misc_ping()
{
    global $zbp;
    $data = array();
    $token = GetVars('token', 'GET');

    if (VerifyWebToken($token, "")) {
        $url = GetVars('url') . 'zb_system/cmd.php?act=misc&type=respondping&token=' . $token;
        $http = Network::Create();
        $http->open('GET', $url);
        $http->setTimeOuts(10, 10, 0, 0);
        $http->send();
        if ($http->status == 200) {
            $s = $http->responseText;
            if ($s == 'ok') {
                JsonError(0, '<em>' . $zbp->lang['msg']['verify_succeed'] . '</em>', $data);
            }

            return;
        }
    }
    JsonError(1, $zbp->lang['error'][5], $data);
}

/**
 * 杂项之执之更新应用之后的操作
 */
function misc_updatedapp()
{
    global $zbp;

    header('Content-Type: application/x-javascript; Charset=utf-8');

    if (!$zbp->CheckRights('admin')) {
        echo $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }
    if ($zbp->cache->success_updated_app !== '') {
        $appid = $zbp->cache->success_updated_app;

        $zbp->cache->success_updated_app = '';
        $zbp->SaveCache();

        $fn = 'UpdatePlugin_' . $appid;
        if (function_exists($fn)) {
            $fn();
        } else {
            $fn = $appid . '_Updated';
            if (function_exists($fn)) {
                $fn();
            }
        }
        die;
    }
}

/**
 * 杂项之清除缩略图目录
 */
function misc_clearthumbcache()
{
    global $zbp;

    if (! $zbp->CheckRights('root')) {
        return;
    }
    CheckIsRefererValid();

    rrmdir($zbp->usersdir . '/cache/thumbs');
    $zbp->SetHint('good');
    if (isset($_SERVER["HTTP_REFERER"])) {
        Redirect302($_SERVER["HTTP_REFERER"]);
    }
}
