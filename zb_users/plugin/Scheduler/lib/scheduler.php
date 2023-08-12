<?php

/**
 * 计划任务 - 操作类.
 * 
 * @author zhouzishu
 */
class Scheduler
{
    protected static $jobs = array();

    public static function new($name, $description = '')
    {
        return static::$jobs[] = new Scheduler_Job($name, $description);
    }

    public static function getJobs()
    {
        return static::$jobs;
    }

    public static function run()
    {
        global $zbp;

        foreach (static::$jobs as $job) {
            if (HasNameInString($zbp->Config('Scheduler')->disabled_jobs, md5($job->getName()))) {
                continue;
            }
            if ($job->isDue()) {
                try {
                    $ret = $job->run();
                    $status = true;
                } catch (Exception $e) {
                    $ret = false;
                    $status = false;
                }
            }
        }
    }
}



/*

Usage:

Scheduler::new('plugin1-autoupdate', '这是描述')
        ->call('test_function', ['arg1', 'arg2'])
        ->cron('* * * * *')                                              // 直接设置 cron 表达式
    或  ->everySomeMinutes($minutes = 1)                                 // 每几分钟执行一次
    或  ->everySomeHoursAt($hours = 1, $minute = 0)                      // 每几小时执行一次，在几点几分执行
    或  ->everySomeMonthsAt($months, $days = 1, $hour = 0, $minute = 0)  // 每几月执行一次，每几月的第几天几点几分执行
    或  ->everySomeDaysAt($days = 1, $hour = 0, $minute = 0)             // 每几天执行一次，每几天的几点几分执行
    或  ->weeklyOn($week_day, $hour = 0, $minute = 0)                    // 每周的几点几分执行 

 */
