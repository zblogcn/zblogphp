<?php

RegisterPlugin('Scheduler', 'ActivePlugin_Scheduler');

function ActivePlugin_Scheduler()
{
    Add_Filter_Plugin('Filter_Plugin_Autoload', 'Scheduler_Autoload');

    Scheduler::new('plugin1-autoupdate', '插件1自动更新任务')
        ->call('test_function', ['123'])
        ->everySomeMinutes();
    Scheduler::new('plugin2-autocache', '插件2自动缓存任务')
        ->call('test_function', ['123'])
        ->everySomeMinutes();
}

function Scheduler_Autoload($className)
{
    if (substr($className, 0, 17) === 'Scheduler__Cron__') {
        $GLOBALS['hooks']['Filter_Plugin_Autoload'][__FUNCTION__] = PLUGIN_EXITSIGNAL_RETURN;
        $className = substr($className, 17);
        is_readable($file = dirname(__FILE__) . '/lib/cron/' . $className . '.php') && include $file;
    } elseif (substr($className, 0, 9) === 'Scheduler') {
        $GLOBALS['hooks']['Filter_Plugin_Autoload'][__FUNCTION__] = PLUGIN_EXITSIGNAL_RETURN;
        is_readable($file = dirname(__FILE__) . '/lib/' . strtolower($className) . '.php') && include $file;
    }
}

function Scheduler_RandomChars($length)
{
    $returnStr = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $len = strlen($pattern);
    for ($i = 0; $i < $length; $i++) {
        $returnStr .= $pattern[mt_rand(0, $len - 1)];
    }
    return $returnStr;
}


function InstallPlugin_Scheduler()
{
    global $zbp;
    if (! $zbp->Config('Scheduler')->token) {
        $zbp->Config('Scheduler')->token = Scheduler_RandomChars(32);
        $zbp->SaveConfig('Scheduler');
    }
}

function UninstallPlugin_Scheduler()
{
}
