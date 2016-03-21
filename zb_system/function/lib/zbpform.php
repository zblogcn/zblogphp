<?php

/**
 * form creat
 * @package Z-BlogPHP
 * @author 未寒 <im@imzhou.com>
 * @subpackage 表单生成操作
 * @copyright (C) RainbowSoft Studio
 */

/**用法
zbpform::radio('aaaa',array('选项1', '选项2'));
zbpform::zbradio('aaaa',array('选项1', '选项2'),1);
zbpform::select('aaaa',array('选项1', '选项2'));
zbpform::checkbox('aaaa',array('选项1', '选项2'),1);
zbpform::text('aaaa','文本框');
zbpform::hidden('aaaa','文本框');
zbpform::textarea('aaaa','多行文本');
zbpform::password('aaaa','文本框');
 */
class ZbpForm {

	public static function radio($name, $array = array('否', '是'), $checkedkey = 0) {
		$s = '';
		foreach ((array) $array as $k => $v) {
			$checked = $k == $checkedkey ? ' checked="checked"' : '';
			$s .= "<input type=\"radio\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$k\"$checked />$v\r\n";
		}
		echo $s;
	}

	public static function zbradio($name, $ischecked = 0, $array = array('否', '是')) {
		$s = '';
		//$checkedtext = $array[$ischecked];
		$s .= "<input name=\"$name\" id=\"$name\" class=\"$name checkbox\" style=\"display:none;\" type=\"text\" value=\"$ischecked\">\r\n";
		echo $s;
	}

	public static function select($name, $array, $checkedkey = 0, $change = '') {
		if (empty($array)) {
			return;
		}

		$onchange = !empty($change) ? ' onchange="' . $change . '"' : '';
		$s = "<select name=\"$name\" id=\"$name\" class=\"$name\"$onchange> \r\n";
		$s .= self::options($array, $checkedkey);
		$s .= "</select> \r\n";
		echo $s;
	}

	public static function options($array, $checkedkey = 0) {
		$s = '';
		foreach ((array) $array as $k => $v) {
			$checked = $k == $checkedkey ? ' selected="selected"' : '';
			$s .= "<option value=\"$k\"$checked>$v</option> \r\n";
		}
		return $s;
	}

	public static function checkbox($name, $array, $ischecked = 0) {
		$s = '';
		$checked = $ischecked ? ' checked="checked"' : '';
		$checkedtext = $array[$ischecked];
		$s .= "<input type=\"checkbox\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$ischecked\"$checked />$checkedtext \r\n";
		echo $s;
	}

	public static function text($name, $value, $width = 150) {
		$s = "<input type=\"text\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$value\" style=\"width: {$width}px\" />\r\n";
		echo $s;
	}

	public static function hidden($name, $value) {
		$s = "<input type=\"hidden\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$value\" />\r\n";
		echo $s;
	}

	public static function textarea($name, $value, $width = 250, $height = 100) {
		$s = "<textarea name=\"$name\" id=\"$name\" class=\"$name\" style=\"width: {$width}px; height: {$height}px;\">$value</textarea>\r\n";
		echo $s;
	}

	public static function password($name, $value, $width = 150) {
		$s = "<input type=\"password\" name=\"$name\" id=\"$name\" class=\"$name\" value=\"$value\" style=\"width: {$width}px\" />\r\n";
		echo $s;
	}

}