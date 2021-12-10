<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

class ZbpLangs implements IteratorAggregate
{

    private $item = null;

    private $array = array();

    public function __construct(&$array, $name = '')
    {
        if (is_array($array)) {
            $this->array = &$array;
            $this->item = $name;
        } else {
            $this->item = $array;
        }
    }

    public function __toString()
    {
        return (string) $this->item;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->array)) {
            if (is_array($this->array[$name])) {
                return new ZbpLangs($this->array[$name], $name);
            } else {
                return $this->array[$name];
                //return new ZbpLangs($this->array[$name]);
            }
        } else {
            return new ZbpLangs($name);
        }
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->array);
    }

    #[\ReturnTypeWillChange]
    public function getIterator() {
        $newarray = array();
        foreach ($this->array as $key => $value) {
            if (!is_array($value)) {
                $newarray[$key] = $value;
            }
        }
        return new ArrayIterator($newarray);
    }

}
