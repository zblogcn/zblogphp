<?php

// 下载 AppCentre 插件到本地

require dirname(__FILE__) . '/../zb_system/function/c_system_base.php';

$zba = GetHttpContent('https://app.zblogcn.com/?zba=231');
if (! $zba) {
    throw new Exception('Downloaded zba failed.');
}

App::UnPack($zba);
