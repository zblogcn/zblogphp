<?php

// 下载 AppCentre 插件到本地

require dirname(__FILE__) . '/../zb_system/function/c_system_base.php';

function _GetHttpContent($url)
{
    $r = null;
    if (function_exists("curl_init") && function_exists('curl_exec')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if (ini_get("safe_mode") == false && ini_get("open_basedir") == false) {
            curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        if (extension_loaded('zlib')) {
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        $r = curl_exec($ch);
        curl_close($ch);
    } elseif (ini_get("allow_url_fopen")) {
        if (function_exists('ini_set')) {
            ini_set('default_socket_timeout', 300);
        }
        $r = file_get_contents((extension_loaded('zlib') ? 'compress.zlib://' : '') . $url);
    }

    return $r;
}

$zba = _GetHttpContent('https://app.zblogcn.com/?zba=231');
if (! $zba) {
    throw new Exception('Downloaded zba failed.');
}

App::UnPack($zba);
