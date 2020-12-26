<?php

/*定义一个数据结构

任务
ZblogTasks
{
id: 英文+数字 (ID不可重复，建议用插件ID_事件名)
name: 名称
function: 要执行的无显式参数的函数 （函数返回值是字符串 或是bool值 True，执行失败则返回bool值False）
begintime: 起始时间 int unixtime 秒
endtime: 结束时间 int unixtime 秒
interval: 间隔 （以秒为单位）
lasttime: 最后一次执行的时间 （新任务为空）
lastresult: 最后一次执行的结果 （新任务为空）
suspend：挂起（boolean）暂停该计划
operate:正在执行中(boolean) 为了防止多次刷新执行同一个任务
}

任务队列
List of ZblogTasks array(ZblogTask task)

按间隔时间进行从高到低的排序

function ScheduledTasks_Polling(){} //主函数，轮询任务队列
从间隔最长的任务依次向间隔最短的任务判断下去，如果nowtime > lasttime + interval的话，就执行该任务

function ScheduledTasks_RegTasks(array $array){} //注册或是更新一个任务

function ScheduledTasks_DelTasks($id){} //从任务队列中删除一个任务

function ScheduledTasks_GetTasks($id){} //获取一个任务的详细信息

function ScheduledTasks_GetTasksAll(){} //获取所有任务的array

function ScheduledTasks_Command($function, $interval = 3600, $begintime = 0, $endtime = 2147483647){} //快捷注册计划任务,默认1小时执行一次

function ScheduledTasks_Execute($id){} //执行一个任务

数据存储

任务队列存储在$zbp->cache('ScheduledTasks')里
每一个任务的ID都做为一个键值key（"Tasks_" + task_id），value为数据结构
$zbp->cache('ScheduledTasks')->task_id = array(ZblogTask结构);

*/

// 注册插件
RegisterPlugin("ScheduledTasks", "ActivePlugin_ScheduledTasks");

// 任务函数 function_name => name(description)
$scheduled_task_functions = array();
$scheduled_task_functions_loaded = false;

function ActivePlugin_ScheduledTasks()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'ScheduledTasks_AddMenu');
}

function ScheduledTasks_AddMenu(&$m)
{
    global $zbp;
    $m['nav_ScheduledTasks'] = MakeLeftMenu("root", '计划任务', $zbp->host . "zb_users/plugin/ScheduledTasks/main.php", "nav_ScheduledTasks", "aScheduledTasks", null, "icon-hourglass-split");
}

function ScheduledTasks_Load_Function()
{
    global $scheduled_task_functions_loaded, $scheduled_task_functions;

    if ($scheduled_task_functions_loaded === true) {
        return;
    }

    // 载入系统默认的 functions
    // ...

    foreach ($GLOBALS['hooks']['Filter_Plugin_ScheduledTasks_Reg_Function'] as $fpname => &$fpsignal) {
        $add_funcs = $fpname();

        if (!is_array($add_funcs)) {
            continue;
        }

        foreach ($add_funcs as $func => $name) {
            if (array_key_exists($func, $scheduled_task_functions)) {
                continue;
            }
            if (! function_exists($func)) {
                continue;
            }
    
            $scheduled_task_functions[$func] = $name;
        }
    }

    $scheduled_task_functions_loaded = true;
    return true;
}

function test_abc(){
    $a = 1/0;

    return 'OK';
}

//ScheduledTasks_Command('test_abc');
//ScheduledTasks_Execute('test_abc');

function ScheduledTasks_Polling(){
    // Load functions
    ScheduledTasks_Load_Function();

    //先对key<=>interval数组进行自然排序，从大到小
    $alltasks = ScheduledTasks_GetTasksAll();
    $aryinterval = array();
    $newtasks = array();
    foreach ($alltasks as $key => $value) {
        $aryinterval[$key] = $value['interval'];
    }
    arsort($aryinterval);
    foreach ($aryinterval as $key => $value) {
        $newtasks[$key] = $alltasks[$key];
    }

    $results = array();

    foreach ($newtasks as $key => $task) {
        if ($task['operate'] == false &&
            $task['suspend'] == false &&
            time() > $task['begintime'] &&
            $task['endtime'] > time() &&
            time() >= $task['lasttime'] + $task['interval'] - round(1 + $task['interval']*0.01)) { //减1%时间应对网络波动 
                $result_data = ScheduledTasks_Execute($task['id']);
                $results[$task['id']] = $result_data;
        }
    }
}

function ScheduledTasks_RegTasks(array $array){
    global $zbp, $scheduled_task_functions;

    // Load functions
    ScheduledTasks_Load_Function();

    $tasks = array();
    $tasks['id'] = mb_substr(str_replace(' ', '', GetValueInArray($array, 'id')), 0, 50);
    $tasks['name'] = (string)GetValueInArray($array, 'name');
    $tasks['function'] = (string)trim(GetValueInArray($array, 'function'));
    $tasks['begintime'] = (int)GetValueInArray($array, 'begintime');
    $tasks['endtime'] = (int)GetValueInArray($array, 'endtime');
    $tasks['interval'] = (int)GetValueInArray($array, 'interval');
    $tasks['lasttime'] = (int)GetValueInArray($array, 'lasttime');
    $tasks['lastresult'] = (string)GetValueInArray($array, 'lastresult');
    $tasks['suspend'] = (bool)GetValueInArray($array, 'suspend');
    $tasks['operate'] = false;
    if(empty($tasks['id'])){
        $zbp->ShowError('id不能为空');
    }

    if (! in_array($tasks['function'], $scheduled_task_functions)) {
        $zbp->ShowError('该函数未注册');
    }

    $key = 'Tasks_' . $tasks['id'];
    if ($zbp->Config('ScheduledTasks')->HasKey($key) == false) {

        if(empty($tasks['function'])){
            $zbp->ShowError('函数名不能为空');
        }

        if(empty($tasks['interval'])){
            $zbp->interval = 5;
        }

        $zbp->Config('ScheduledTasks')->$key = $tasks;
        $zbp->Config('ScheduledTasks')->Save();
    } else {
        $tasks = $zbp->Config('ScheduledTasks')->$key;
        if(isset($array['name']))
            $tasks['name'] = (string)GetValueInArray($array, 'name');
        if(isset($array['function']))
            $tasks['function'] = (string)trim(GetValueInArray($array, 'function'));
        if(isset($array['interval']))
            $tasks['begintime'] = (int)GetValueInArray($array, 'begintime');
        if(isset($array['begintime']))
            $tasks['endtime'] = (int)GetValueInArray($array, 'endtime');
        if(isset($array['endtime']))
            $tasks['interval'] = (int)GetValueInArray($array, 'interval');
        if(isset($array['suspend']))
            $tasks['suspend'] = (bool)GetValueInArray($array, 'suspend');
        $tasks['operate'] = false;
        $zbp->Config('ScheduledTasks')->$key = $tasks;
        $zbp->Config('ScheduledTasks')->Save();
    }

}

function ScheduledTasks_DelTasks($id){
    global $zbp;
    $key = 'Tasks_' . $id;
    if($zbp->Config('ScheduledTasks')->HasKey($key)){
        $zbp->Config('ScheduledTasks')->DelKey($key);
        $zbp->Config('ScheduledTasks')->Save();
    }
}

function ScheduledTasks_GetTasks($id){
    global $zbp;
    $key = 'Tasks_' . $id;
    if($zbp->Config('ScheduledTasks')->HasKey($key)){
        return $zbp->Config('ScheduledTasks')->$key;
    }
    return ;
}

function ScheduledTasks_GetTasksAll(){
    global $zbp;
    $config = $zbp->Config('ScheduledTasks');
    $array = array();
    $data = $config->GetData();
    foreach ($data as $key => $value) {
        if (stripos($key, 'Tasks_') === 0) {
            if(is_array($zbp->Config('ScheduledTasks')->$key)) {
                $array[$key] = $zbp->Config('ScheduledTasks')->$key;
            }
        }
    }
    return $array;
}

function ScheduledTasks_Execute($id){
    global $zbp;
    $key = 'Tasks_' . $id;
    $tasks = ScheduledTasks_GetTasks($id);
    $result = null;
    if(is_array($tasks)){
        //set operate true
        $tasks['operate'] = true;
        $zbp->Config('ScheduledTasks')->$key = $tasks;
        $zbp->Config('ScheduledTasks')->Save();

        $tasks['lastresult'] = '';
        $function = $tasks['function'];

        if (function_exists($function)) {

            ZBlogException::SuspendErrorHook();

            $result = call_user_func($function);
            $tasks['lastresult'] = (string)$result;

            ZBlogException::ResumeErrorHook();
        }

        $tasks['lasttime'] = time();
        $tasks['operate'] = false;
        $zbp->Config('ScheduledTasks')->$key = $tasks;
        $zbp->Config('ScheduledTasks')->Save();
    }
    return $result;
}

// 快速注册任务
function ScheduledTasks_Reg($function, $interval = 3600, $begintime = 0, $endtime = 2147483647){
    global $zbp;
    $tasks = array();
    $function = trim($function);
    $tasks['id'] = (string)$function;
    $tasks['name'] = (string)$function;
    $tasks['function'] = (string)$function;
    $tasks['begintime'] = (int)$begintime;
    $tasks['endtime'] = (int)$endtime;
    $tasks['interval'] = (int)$interval;
    $tasks['lasttime'] = (int)0;
    $tasks['lastresult'] = (string)'';
    $tasks['suspend'] = (bool)false;
    return ScheduledTasks_RegTasks($tasks);
}