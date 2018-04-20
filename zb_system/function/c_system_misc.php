<?php if (!defined('ZBP_PATH')) {
    exit('Access denied');
}
/**
 * 系统信息.
 *
 * @copyright (C) RainbowSoft Studio
 */
function misc_updateinfo()
{
    global $zbp;

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
 * @throws Exception
 */
function misc_statistic()
{
    global $zbp;

    if ($zbp->CheckRights('root') || $zbp->CheckTemplate(true) == false) {
        $zbp->CheckTemplate(false, true);
    }
    if (!($zbp->CheckRights('root') || (time() - (int) $zbp->cache->reload_statistic_time) > (23 * 60 * 60))) {
        echo $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }

    $r = null;

    CountNormalArticleNums();
    CountTopArticle(0, null, null);
    CountCommentNums(null, null);
    $all_comments = $zbp->cache->all_comment_nums;

    $xmlrpc_address = $zbp->xmlrpcurl;
    $current_member = $zbp->user->Name;
    $current_version = ZC_VERSION_FULL;
    $all_articles = GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(*) AS num FROM ' . $GLOBALS['table']['Post'] . ' WHERE log_Type=\'0\''), 'num');
    $all_pages = GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(*) AS num FROM ' . $GLOBALS['table']['Post'] . ' WHERE log_Type=\'1\''), 'num');
    $all_categories = GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(*) AS num FROM ' . $GLOBALS['table']['Category']), 'num');
    $all_views = $zbp->option['ZC_VIEWNUMS_TURNOFF'] == true ? 0 : GetValueInArrayByCurrent($zbp->db->Query('SELECT SUM(log_ViewNums) AS num FROM ' . $GLOBALS['table']['Post']), 'num');
    $all_tags = GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(*) as num FROM ' . $GLOBALS['table']['Tag']), 'num');
    $all_members = GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(*) AS num FROM ' . $GLOBALS['table']['Member']), 'num');
    $current_theme = '{$zbp->theme}';
    $current_style = '{$zbp->style}';
    $current_member = '{$zbp->user->Name}';
    $current_version = '{$zbp->version}';
    $system_environment = '{$system_environment}';

    if ($zbp->option['ZC_DEBUG_MODE']) {
        $r .= "<tr><td colspan='4' style='text-align: center'>{$zbp->lang['msg']['debugging_warning']}</td></tr>";
    }
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_member']}</td><td class='td30'><a href='../cmd.php?act=misc&type=vrs' target='_blank'>{$current_member}</a></td><td class='td20'>{$zbp->lang['msg']['current_version']}</td><td class='td30'>{$current_version}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_artiles']}</td><td>{$all_articles}</td><td>{$zbp->lang['msg']['all_categorys']}</td><td>{$all_categories}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_pages']}</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}</td><td>{$all_tags}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_comments']}</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}</td><td>{$all_views}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_theme']} / {$zbp->lang['msg']['current_style']}</td><td>{$current_theme}/{$current_style}</td><td>{$zbp->lang['msg']['all_members']}</td><td>{$all_members}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['xmlrpc_address']}</td><td>{$xmlrpc_address}</td><td>{$zbp->lang['msg']['system_environment']}</td><td><a href='../cmd.php?act=misc&type=phpinfo' target='_blank'>{$system_environment}</a></td></tr>";
    $r .= "<script type=\"text/javascript\">$('#statistic').attr('title','" . date("c", $zbp->cache->reload_statistic_time) . "');</script>";

    $zbp->cache->reload_statistic = $r;
    $zbp->cache->reload_statistic_time = time();
    $zbp->cache->system_environment = GetEnvironment();
    $zbp->cache->all_article_nums = $all_articles;
    $zbp->cache->all_page_nums = $all_pages;
    $zbp->cache->all_category_nums = $all_categories;
    $zbp->cache->all_view_nums = $all_views;
    $zbp->cache->all_tag_nums = $all_tags;

    $zbp->AddBuildModule('statistics', array($all_articles, $all_pages, $all_categories, $all_tags, $all_views, $all_comments));
    $zbp->BuildModule();
    $zbp->SaveCache();

    $r = str_replace('{#ZC_BLOG_HOST#}', $zbp->host, $r);
    $r = str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
    $r = str_replace('{$zbp->theme}', $zbp->theme, $r);
    $r = str_replace('{$zbp->style}', $zbp->style, $r);
    $r = str_replace('{$zbp->version}', ZC_VERSION_FULL, $r);
    $r = str_replace('{$system_environment}', $zbp->cache->system_environment, $r);

    echo $r;
}

function misc_showtags()
{
    global $zbp;

    header('Content-Type: application/x-javascript; Charset=utf-8');

    echo '$("#ajaxtags").html("';

    $array = $zbp->GetTagList(null, null, array('tag_Count' => 'DESC', 'tag_ID' => 'ASC'), array(100), null);
    if (count($array) > 0) {
        $t = array();
        foreach ($array as $tag) {
            echo '<a href=\"#\">' . $tag->Name . '</a>';
        }
    }

    echo '");$("#ulTag").tagTo("#edtTag");';
}

function misc_viewrights()
{
    global $zbp, $blogtitle;

    $blogtitle = $zbp->name . '-' . $zbp->lang['msg']['view_rights']; ?><!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?php if (strpos(GetVars('HTTP_USER_AGENT', 'SERVER'), 'Trident/')) {
        ?>
        <meta http-equiv="X-UA-Compatible" content="IE=EDGE"/>
    <?php
    } ?>
    <meta name="robots" content="none"/>
    <meta name="generator" content="<?php echo $GLOBALS['option']['ZC_BLOG_PRODUCT_FULL'] ?>"/>
    <link rel="stylesheet" href="css/admin.css" type="text/css" media="screen"/>
    <script src="script/common.js" type="text/javascript"></script>
    <script src="script/c_admin_js_add.php" type="text/javascript"></script>
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Other_Header'] as $fpname => &$fpsignal) {
        $fpname();
    } ?>
    <title><?php echo $blogtitle; ?></title>
</head>
<body class="short">
<div class="bg">
    <div id="wrapper">
        <div class="logo"><img src="image/admin/none.gif" title="Z-BlogPHP" alt="Z-BlogPHP"/></div>
        <div class="login">
            <form method="post" action="#">
                <dl>
                    <dt><?php echo $zbp->lang['msg']['current_member'] . ' : <b>' . $zbp->user->Name; ?></b><br/>
                        <?php echo $zbp->lang['msg']['member_level'] . ' : <b>' . $zbp->user->LevelName; ?></b></dt>
                    <?php
                    foreach ($GLOBALS['actions'] as $key => $value) {
                        if ($GLOBALS['zbp']->CheckRights($key)) {
                            echo '<dd><b>' . $zbp->GetActionDescription($key) . '</b> : ' . ($zbp->CheckRights($key) ? '<span style="color:green">true</span>' : '<span style="color:red">false</span>') . '</dd>';
                        }
                    } ?>
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

function misc_phpif()
{
    global $zbp, $blogtitle;
    $match = array();
    $blogtitle = $zbp->name . '-phpinfo';
    ob_start();
    $pi = 'php' . "info";
    $pi();
    $s = ob_get_clean();

    if (PHP_ENGINE !== ENGINE_HHVM) {
        preg_match("/<body.*?>(.*?)<\/body>/is", $s, $match);
    } ?><!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?php if (strpos(GetVars('HTTP_USER_AGENT', 'SERVER'), 'Trident/')) {
        ?>
        <meta http-equiv="X-UA-Compatible" content="IE=EDGE"/>
    <?php
    } ?>
    <meta name="robots" content="none"/>
    <meta name="generator" content="<?php echo $GLOBALS['option']['ZC_BLOG_PRODUCT_FULL'] ?>"/>
    <link rel="stylesheet" href="css/admin.css" type="text/css" media="screen"/>
    <script src="script/common.js" type="text/javascript"></script>
    <script src="script/c_admin_js_add.php" type="text/javascript"></script>
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Other_Header'] as $fpname => &$fpsignal) {
        $fpname();
    } ?>
    <title><?php echo $blogtitle; ?></title>
    <style type="text/css">
*{color:#000;}
pre {margin: 0; font-family: monospace;}
a:link {color: #009; text-decoration: none; background-color: #fff;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse; border: 0; width: 934px; box-shadow: 1px 2px 3px #ccc;}
.center {text-align: center;}
.center table {margin: 1em auto; text-align: left;}
.center th {text-align: center !important;}
td, th {border: 1px solid #666; font-size: 75%; vertical-align: baseline; padding: 4px 5px;}
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccf; width: 300px; font-weight: bold;}
.h {background-color: #99c; font-weight: bold;}
.v {background-color: #ddd; max-width: 300px; overflow-x: auto;}
.v i {color: #999;}
img {float: right; border: 0;}
hr {display:none;}
div.bg {background: #777bb4!important;}
    </style>
</head>
<body class="short">
<div class="bg">
    <div id="wrapper">
        <div class="logo"><a href="#zblogphp"><img src="image/admin/none.gif" title="Z-BlogPHP" alt="Z-BlogPHP"/></a></div>
        <?php
        if (PHP_ENGINE === ENGINE_HHVM) {
            echo '<p style="text-align: center;">' . GetEnvironment() . '</p>';
        } else {
            echo $match[0];
        }

    $c = 'PHP_VERSION , PHP_OS , PHP_SAPI , PHP_EOL ,  PHP_INT_MAX ,  PHP_INT_SIZE ,  DEFAULT_INCLUDE_PATH , PEAR_INSTALL_DIR , PEAR_EXTENSION_DIR , PHP_EXTENSION_DIR , PHP_PREFIX , PHP_BINDIR , PHP_LIBDIR , PHP_DATADIR , PHP_SYSCONFDIR , PHP_LOCALSTATEDIR , PHP_CONFIG_FILE_PATH , PHP_CONFIG_FILE_SCAN_DIR , PHP_SHLIB_SUFFIX ,  PHP_OUTPUT_HANDLER_START , PHP_OUTPUT_HANDLER_CONT , PHP_OUTPUT_HANDLER_END , E_ERROR , E_WARNING , E_PARSE , E_NOTICE , E_CORE_ERROR , E_CORE_WARNING , E_COMPILE_ERROR , E_COMPILE_WARNING , E_USER_ERROR , E_USER_WARNING , E_USER_NOTICE , E_ALL , E_STRICT , __COMPILER_HALT_OFFSET__ ,  EXTR_OVERWRITE , EXTR_SKIP , EXTR_PREFIX_SAME , EXTR_PREFIX_ALL , EXTR_PREFIX_INVALID , EXTR_PREFIX_IF_EXISTS , EXTR_IF_EXISTS , SORT_ASC , SORT_DESC , SORT_REGULAR , SORT_NUMERIC , SORT_STRING , CASE_LOWER , CASE_UPPER , COUNT_NORMAL , COUNT_RECURSIVE , ASSERT_ACTIVE , ASSERT_CALLBACK , ASSERT_BAIL , ASSERT_WARNING , ASSERT_QUIET_EVAL , CONNECTION_ABORTED , CONNECTION_NORMAL , CONNECTION_TIMEOUT , INI_USER , INI_PERDIR , INI_SYSTEM , INI_ALL , M_E , M_LOG2E , M_LOG10E , M_LN2 , M_LN10 , M_PI , M_PI_2 , M_PI_4 , M_1_PI , M_2_PI , M_2_SQRTPI , M_SQRT2 , M_SQRT1_2 , CRYPT_SALT_LENGTH , CRYPT_STD_DES , CRYPT_EXT_DES , CRYPT_MD5 , CRYPT_BLOWFISH , DIRECTORY_SEPARATOR , SEEK_SET , SEEK_CUR , SEEK_END , LOCK_SH , LOCK_EX , LOCK_UN , LOCK_NB , HTML_SPECIALCHARS , HTML_ENTITIES , ENT_COMPAT , ENT_QUOTES , ENT_NOQUOTES , INFO_GENERAL , INFO_CREDITS , INFO_CONFIGURATION , INFO_MODULES , INFO_ENVIRONMENT , INFO_VARIABLES , INFO_LICENSE , INFO_ALL , CREDITS_GROUP , CREDITS_GENERAL , CREDITS_SAPI , CREDITS_MODULES , CREDITS_DOCS , CREDITS_FULLPAGE , CREDITS_QA , CREDITS_ALL , STR_PAD_LEFT , STR_PAD_RIGHT , STR_PAD_BOTH , PATHINFO_DIRNAME , PATHINFO_BASENAME , PATHINFO_EXTENSION , PATH_SEPARATOR , CHAR_MAX , LC_CTYPE , LC_NUMERIC , LC_TIME , LC_COLLATE , LC_MONETARY , LC_ALL , LC_MESSAGES , ABDAY_1 , ABDAY_2 , ABDAY_3 , ABDAY_4 , ABDAY_5 , ABDAY_6 , ABDAY_7 , DAY_1 , DAY_2 , DAY_3 , DAY_4 , DAY_5 , DAY_6 , DAY_7 , ABMON_1 , ABMON_2 , ABMON_3 , ABMON_4 , ABMON_5 , ABMON_6 , ABMON_7 , ABMON_8 , ABMON_9 , ABMON_10 , ABMON_11 , ABMON_12 , MON_1 , MON_2 , MON_3 , MON_4 , MON_5 , MON_6 , MON_7 , MON_8 , MON_9 , MON_10 , MON_11 , MON_12 , AM_STR , PM_STR , D_T_FMT , D_FMT , T_FMT , T_FMT_AMPM , ERA , ERA_YEAR , ERA_D_T_FMT , ERA_D_FMT , ERA_T_FMT , ALT_DIGITS , INT_CURR_SYMBOL , CURRENCY_SYMBOL , CRNCYSTR , MON_DECIMAL_POINT , MON_THOUSANDS_SEP , MON_GROUPING , POSITIVE_SIGN , NEGATIVE_SIGN , INT_FRAC_DIGITS , FRAC_DIGITS , P_CS_PRECEDES , P_SEP_BY_SPACE , N_CS_PRECEDES , N_SEP_BY_SPACE , P_SIGN_POSN , N_SIGN_POSN , DECIMAL_POINT , RADIXCHAR , THOUSANDS_SEP , THOUSEP , GROUPING , YESEXPR , NOEXPR , YESSTR , NOSTR , CODESET , LOG_EMERG , LOG_ALERT , LOG_CRIT , LOG_ERR , LOG_WARNING , LOG_NOTICE , LOG_INFO , LOG_DEBUG , LOG_KERN , LOG_USER , LOG_MAIL , LOG_DAEMON , LOG_AUTH , LOG_SYSLOG , LOG_LPR , LOG_NEWS , LOG_UUCP , LOG_CRON , LOG_AUTHPRIV , LOG_LOCAL0 , LOG_LOCAL1 , LOG_LOCAL2 , LOG_LOCAL3 , LOG_LOCAL4 , LOG_LOCAL5 , LOG_LOCAL6 , LOG_LOCAL7 , LOG_PID , LOG_CONS , LOG_ODELAY , LOG_NDELAY , LOG_NOWAIT , LOG_PERROR ,  PCRE_VERSION';
    echo '<div class="center"><div><br/><br/><a name="zblogphp"><h1>Z-BlogPHP</h1></a><br/></div>';
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
        echo '<tr><td class="e">' . $i . '</td><td class="v">' . $value . '</td></tr>';
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
    echo '</div>'; ?>
    </div>
</div>
</body>
</html>
<?php
RunTime();
}
