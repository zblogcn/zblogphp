<?php

require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

//先加载所有tasks目录下的文件
foreach (glob(__DIR__ . "/tasks/*.php") as $filename) {
    include($filename);
}

//判断token是不是正确的
$token = GetVars('token', 'GET');
if ($token !== ScheduledTasks_GetToken()) {
	Http503();
    die('Token Error');
}

//最后调用ScheduledTasks_Polling
//var_dump($ScheduledTasks_Data);
ScheduledTasks_Polling();

echo 'OK';
