<?php

/*定义一个数据结构

任务
ZblogTasks
{
id: 英文+数字 (ID不可重复，建议用插件ID_事件名)
name: 名称
function: 要执行的无显式参数的函数 （函数返回值是字符串 或是bool值 True，执行失败则返回bool值False）
regtime: 注册时间 int unixtime 秒
begintime: 任务起始时间 int unixtime 秒
endtime: 任务结束时间 int unixtime 秒
interval: 间隔 （以分为单位）
lasttime: 最后一次执行的时间 （新任务为空）
lastresult: 最后一次执行的结果 （新任务为空）
suspend：挂起（boolean）暂停该计划
operate:正在执行中(boolean) 为了防止多次刷新执行同一个任务
}

任务队列
List of ZblogTasks_Data array(ZblogTask task)

function ScheduledTasks_Polling(){} //主函数，轮询任务队列

function ScheduledTasks_RegTasks(array $array){} //注册或是更新一个任务

function ScheduledTasks_DelTasks($id){} //从任务队列中删除一个任务

function ScheduledTasks_GetTasks($id){} //获取一个任务的详细信息

function ScheduledTasks_GetTasksAll(){} //获取所有任务的array

function ScheduledTasks_Execute($id){} //执行一个任务

function ScheduledTasks_GetToken(){} //获取run.php的token

*/

$ScheduledTasks_Data = array();

//run.php页的token，防止被其它程序调用
$ScheduledTasks_Token = 'rqpDsedLuJhxJav2PNMEdadQzv0PKTdV';

//注册插件
RegisterPlugin("ScheduledTasks", "ActivePlugin_ScheduledTasks");

function ActivePlugin_ScheduledTasks(){

}

function ScheduledTasks_Polling(){
    $alltasks = ScheduledTasks_GetTasksAll();
    $runtasks = array();
    foreach ($alltasks as $key => $tasks) {
        if ($tasks['suspend'] == false) {
            if (time() > $tasks['begintime']) {
                if ($tasks['endtime'] == 0 || $tasks['endtime'] > time()) {
                    $m0 = intdiv($tasks['regtime'], 60);
                    $m1 = intdiv(time(), 60);
                    $interval = (int) fmod($m1 - $m0, $tasks['interval']);
                    //echo $interval;
                    if ($interval == 0) {
                        $runtasks[] = $tasks;
                    }
                }
            }
        }
    }

    foreach ($runtasks as $key => $tasks) {
        ScheduledTasks_Execute($tasks['id']);
    }
}

function ScheduledTasks_RegTasks(array $array){
    global $zbp, $ScheduledTasks_Data;
    $tasks = array();
    $tasks['id'] = mb_substr(str_replace(' ', '', GetValueInArray($array, 'id')), 0, 50);
    $tasks['name'] = (string)GetValueInArray($array, 'name');
    $tasks['function'] = (string)trim(GetValueInArray($array, 'function'));
    $tasks['begintime'] = (int)GetValueInArray($array, 'begintime');
    $tasks['regtime'] = (int)GetValueInArray($array, 'regtime');
    $tasks['endtime'] = (int)GetValueInArray($array, 'endtime');
    $tasks['interval'] = (int)GetValueInArray($array, 'interval');
    $tasks['lasttime'] = (int)GetValueInArray($array, 'lasttime');
    $tasks['lastresult'] = (string)GetValueInArray($array, 'lastresult');
    $tasks['suspend'] = (bool)GetValueInArray($array, 'suspend');
    //$tasks['operate'] = false;

    if(empty($tasks['id'])){
        $zbp->ShowError('id不能为空');
    }
    if ($tasks['interval'] <= 0) {
        $tasks['interval'] = 1;
    }

    $ScheduledTasks_Data[$tasks['id']] = $tasks;
}

function ScheduledTasks_DelTasks($id){
    global $zbp, $ScheduledTasks_Data;
    unset($ScheduledTasks_Data[$tasks['id']]);
}

function ScheduledTasks_GetTasks($id){
    global $zbp, $ScheduledTasks_Data;
    if(isset($ScheduledTasks_Data[$tasks['id']])){
        return $ScheduledTasks_Data[$tasks['id']];
    }
    return array();
}

function ScheduledTasks_GetTasksAll(){
    global $zbp, $ScheduledTasks_Data;
    return $ScheduledTasks_Data;;
}

function ScheduledTasks_Execute($id){
    global $zbp, $ScheduledTasks_Data;

    try {
        $result = call_user_func($ScheduledTasks_Data[$id]['function']);
    } catch (Throwable $e) {
        $result = null;
    }

    return $result;    
}

function ScheduledTasks_GetToken(){
    global $zbp, $ScheduledTasks_Token;
    if (!empty($zbp->Config('ScheduledTasks')->Token)) {
        return $zbp->Config('ScheduledTasks')->Token;
    }
    return md5(md5($ScheduledTasks_Token) . $zbp->guid);
}
