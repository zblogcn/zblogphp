<?php

RegisterPlugin("wearingtheme", "ActivePlugin_wearingtheme");

function ActivePlugin_wearingtheme()
{
    Add_Filter_Plugin('Filter_Plugin_Index_Begin', 'wearingtheme_index_begin');
}

function wearingtheme_index_begin()
{
    global $zbp;
    global $usersdir;

    $app = new App();
    $theme = GetVars('theme', 'GET');
    if ($theme == '') {
        $theme = GetVars('theme', 'COOKIE');
    }

    $dir = $usersdir . 'theme/' . $theme;

    if ($theme == '') {
        return;
    }
    if (!is_dir($dir)) {
        return;
    }

    if (!$app->LoadInfoByXml('theme', $theme)) {
        return;
    }

    $zbp->Config('system')->ZC_BLOG_THEME = $theme;
    $zbp->option['ZC_BLOG_THEME'] = $theme;
    $zbp->activeapps[0] = $theme;

    foreach ($app->GetCssFiles() as $key => $value) {
        $value = basename($value, '.css');
        $zbp->Config('system')->ZC_BLOG_CSS = $value;
        $zbp->option['ZC_BLOG_CSS'] = $value;
        break;
    }

    if (is_readable($filename = $dir . '/include.php')) {
        require $filename;
        if (isset($GLOBALS['plugins'][$theme])) {
            $func_name = $GLOBALS['plugins'][$theme];
            if (function_exists($func_name)) {
                $func_name();
            }
        }
    }

    $zbp->LoadTemplate();
    $zbp->MakeTemplatetags();
    $zbp->template = $zbp->PrepareTemplate();

    setcookie('theme', $theme, time() + 24 * 60 * 60);

    /*
        TODO:

        一、安全问题：
            1. 主题任意函数内包含危害服务器安全的代码
            2. 主题任意函数内包含危害其它主题的代码（比如new ZipArchive然后POST到新的服务器）

        二、主题从APP上传到theme
    */
}

function InstallPlugin_wearingtheme()
{
}

function UninstallPlugin_wearingtheme()
{
}
