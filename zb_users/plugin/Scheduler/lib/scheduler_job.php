<?php

/**
 * 计划任务 - 任务类.
 * 
 * @author zhouzishu
 */
class Scheduler_Job
{
    /**
     * @var Scheduler__Cron__CronExpression
     */
    protected $execTime;

    /**
     * @var string
     */
    public $name = 'unnamed-job-0';

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var callable
     */
    protected $callable;

    /**
     * @var array
     */
    protected $args = array();

    /**
     * 构造新对象.
     */
    public function __construct($name, $description = '')
    {
        $this->execTime = Scheduler__Cron__CronExpression::factory('@daily');
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * 获取名称.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 获取描述.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * 是否到达执行时间.
     *
     * @return boolean
     */
    public function isDue()
    {
        return $this->execTime->isDue();
    }

    /**
     * 设置待调用函数
     *
     * @param callable $callable
     * @param array $args
     */
    public function call($callable, $args)
    {
        $this->callable = $callable;
        $this->args = $args;

        return $this;
    }

    /**
     * 执行.
     */
    public function exec()
    {
        if (is_callable($this->callable)) {
            return call_user_func($this->callable, $this->args);
        }
        return null;
    }


    /*
     ==========================================================================
                                Frequencies collection
     ==========================================================================
     */

    /**
     * 直接设置 cron 表达式.
     *
     * @param string $expression
     * @return $this
     */
    public function cron($expression)
    {
        $this->execTime->setExpression($expression);

        return $this;
    }

    /**
     * 每几分钟.
     *
     * @param integer $minutes
     * @return $this
     */
    public function everySomeMinutes($minutes = 1)
    {
        return $this->cron("*/$minutes * * * *");
    }

    /**
     * 每几小时.
     *
     * @param integer $hours
     * @return $this
     */
    public function everySomeHoursAt($hours = 1, $minute = 0)
    {
        return $this->cron("$minute */$hours * * *");
    }

    /**
     * 每几天.
     *
     * @param integer $days
     * @return $this
     */
    public function everySomeMonthsAt($months, $days = 1, $hour = 0, $minute = 0)
    {
        return $this->cron("$minute $hour */$days */$months *");
    }

    /**
     * 每几天.
     *
     * @param integer $days
     * @return $this
     */
    public function everySomeDaysAt($days = 1, $hour = 0, $minute = 0)
    {
        return $this->cron("$minute $hour */$days * *");
    }

    /**
     * 每个星期几.
     *
     * @param string|int $week_day
     * @param integer $hour
     * @param integer $minute
     * @return void
     */
    public function weeklyOn($week_day, $hour = 0, $minute = 0)
    {
        return $this->cron("$minute $hour 0 * $week_day");
    }
}
