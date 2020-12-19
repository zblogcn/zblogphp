<?php

/**
 * 计划任务调用入口.
 */

require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

echo ScheduledTasks_Polling();
