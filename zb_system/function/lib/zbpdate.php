<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

class ZbpDate
{

    private $time = 0;

    public function __construct($time)
    {
        $this->time = $time;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($format)
    {
        return date($format, $this->time);
    }

    public function __toString()
    {
        return (string) $this->time;
    }

}
