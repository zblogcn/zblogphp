<?php

//注册任务
ScheduledTasks_RegTasks(
    [
        'id' => 'task_test',//唯一的任务ID名称
        'name' => '测试任务' ,
        'function' => 'Task_Test_Run',//需要运行的function
        'interval' => 1,//运行间隔时间（分钟）
        'regtime' => 1680243760,//注册任务的unix时间
        'begintime' => 0,
        'endtime' => 0,
        'lasttime' => 0,
        'lastresult' => '',
        'suspend' => false,
    ]
);


//任务执行函数(函数可以在这里定义，也可以在其它地方定义好只需要调用)
function Task_Test_Run() {
    echo 'task test';
}
