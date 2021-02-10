<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * form creat.
 *
 * @author 未寒 <im@imzhou.com>
 *
 */

/**用法
zbpform::radio('aaaa',array('选项1', '选项2'));
zbpform::zbradio('aaaa',1);
zbpform::select('aaaa',array('a'=>'选项1', 'b'=>'选项2'),'a');
zbpform::checkbox('aaaa',array(array('选项1',0),array('选项2',1)));
zbpform::text('aaaa','文本框');
zbpform::hidden('aaaa','文本框');
zbpform::textarea('aaaa','多行文本');
zbpform::password('aaaa','文本框');
 */
class ZbpForm
{

    static public $setreturn = false;

    public static function radio($name, $array = array('否', '是'), $checkedkey = 0)
    {
        $s = '';
        foreach ((array) $array as $k => $v) {
            $checked = $k == $checkedkey ? ' checked="checked"' : '';
            $s .= "<input type=\"radio\" name=\"$name\" id=\"$name-$k\" class=\"$name\" value=\"$k\"$checked /><label for=\"$name-$k\">$v</label>\r\n";
        }
        if (self::$setreturn) {
            return $s;
        }
        echo $s;
    }

    public static function zbradio($name, $ischecked = 0)
    {
        $s = '';
        $s .= "<input name=\"$name\" id=\"$name\" class=\"$name checkbox\" type=\"text\" value=\"$ischecked\">\r\n";
        if (self::$setreturn) {
            return $s;
        }
        echo $s;
    }

    public static function select($name, $array, $checkedkey = 0, $change = '')
    {
        if (empty($array)) {
            return;
        }

        $onchange = !empty($change) ? ' onchange="' . $change . '"' : '';
        $s = "<select name=\"$name\" id=\"$name\" class=\"$name\"$onchange> \r\n";
        $s .= self::options($array, $checkedkey);
        $s .= "</select> \r\n";
        if (self::$setreturn) {
            return $s;
        }
        echo $s;
    }

    public static function options($array, $checkedkey = 0)
    {
        $s = '';
        foreach ((array) $array as $k => $v) {
            $checked = $k == $checkedkey ? ' selected="selected"' : '';
            $s .= "<option value=\"$k\"$checked>$v</option> \r\n";
        }
        if (self::$setreturn) {
            return $s;
        }
        return $s;
    }

    public static function checkbox($name, $array)
    {
        $s = '';
        foreach ((array) $array as $k => $v) {
            $checked = $v[1] ? ' checked="checked"' : '';

            $s .= "<input type=\"checkbox\" name=\"" . $name . "[]\" id=\"$name-$k\" class=\"$name\" value=\"$k\"$checked /><label for=\"$name-$k\">$v[0]</label>\r\n";
        }
        if (self::$setreturn) {
            return $s;
        }
        echo $s;
    }

    public static function text($name, $value, $width = '150px')
    {
        $style = $width ? ' style="width: ' . $width . ';"' : '';
        $s = "<input type=\"text\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$value\"$style/>\r\n";
        if (self::$setreturn) {
            return $s;
        }
        echo $s;
    }

    public static function hidden($name, $value)
    {
        $s = "<input type=\"hidden\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$value\" />\r\n";
        echo $s;
    }

    public static function textarea($name, $value, $width = '250px', $height = '100px')
    {
        $style = $width ? ' style="width: ' . $width . '; height: ' . $height . '"' : '';
        $s = "<textarea name=\"$name\" id=\"$name\" class=\"$name\"$style>$value</textarea>\r\n";
        if (self::$setreturn) {
            return $s;
        }
        echo $s;
    }

    public static function password($name, $value, $width = '150px')
    {
        $style = $width ? ' style="width: ' . $width . ';"' : '';
        $s = "<input type=\"password\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$value\"$style/>\r\n";
        if (self::$setreturn) {
            return $s;
        }
        echo $s;
    }

}
