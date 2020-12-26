<?php

/**
 * 计划任务调用入口.
 */

require '../../../zb_system/function/c_system_base.php';

$zbp->Load();

$polling_results =  ScheduledTasks_Polling();
if (count($polling_results) === 0) {
    echo json_encode(array('msg' => 'No task was executed.', 'results' => array()));
    exit;
}

echo json_encode(array(
    'msg' => ((string) count($polling_results)) . ' tasks were executed.',
    'results' => $polling_results,
));
