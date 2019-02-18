<?php

define('TPLUGINMAKER_IS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

$message = array('用前须知', '配置内容', '生成插件');
function check_plugin_exists($theme_name)
{
    global $usersdir;

    return is_readable($usersdir . 'theme/' . $theme_name . '/include.php');
}

function get_theme_data($themename)
{
    global $zbp;
    global $usersdir;
    $dir = $usersdir . 'theme/' . $themename . '/include/';
    $return = array();

    if (!is_dir($dir)) {
        mkdir($dir);
    }
    $dir_handle = opendir($dir);

    while (false !== ($filename = readdir($dir_handle))) {
        if (!is_file($dir . $filename) || $filename == 'index.html') {
            continue;
        }
        if (!$zbp->Config('tpluginmaker_' . $themename)->HasKey($filename)) {
            $zbp->Config('tpluginmaker_' . $themename)->$filename = '';
        }

        $return[] = array(
            "name"  => (TPLUGINMAKER_IS_WINDOWS ? iconv('GBK', 'UTF-8', $filename) : $filename),
            "value" => $zbp->Config('tpluginmaker_' . $themename)->$filename,
            "type"  => check_file_ext($filename),
        );
    }

    $zbp->SaveConfig('tpluginmaker_' . $themename);

    return $return;
}

function check_file_ext($filename)
{
    return preg_match("/\.(html?|txt|php|inc|css|less|js|coffee|xml)$/i", $filename) ? "1" : "2";
}
