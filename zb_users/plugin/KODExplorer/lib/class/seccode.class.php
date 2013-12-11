<?php

/**
 * 验证码类,可以随机产生
 * 
 * @author eachcan<eachcan@gmail.com> 
 * @package classes
 * @uses $v = new vcode;
 * $config['type'] = "default"; default为大小写加数字混合,upper为大写,lower为小写,alpha为英文大小写,num为数字
 * $config['length'] = 4;
 * $config['interfere']= 10;
 * $config['width'] = 50;
 * $config['height'] = 20;
 * $config['session'] = "seccode";
 * $v->init($config);
 * $v->create();
 */

class seccode {
	private $config;
	private $im;
	private $str;

	private $font_type = array(
		FONT_DIR.'TrajanPro-Bold.otf',
		FONT_DIR.'StencilStd.otf',
		FONT_DIR.'VINERITC.TTF'
	);

	function __construct(){
		$this -> config['width']	= 100;
		$this -> config['height']	= 30;
		$this -> config['session']	= "seccode";
		$this -> config['type']		= "default";
		$this -> config['length']	= 4;
		$this -> config['interfere']= 10;

		$this -> str['default']	= "ABCDEFGHJKMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz23456789";
		$this -> str['upper']	= "ABCDEFGHJKMNPQRSTUVWXYZ";
		$this -> str['lower']	= "abcdefghjkmnpqrstuvwxyz";
		$this -> str['alpha']	= "ABCDEFGHJKMNPQRSTUVWXYZabcdefghijklmnpqrstuvwxyz";
		$this -> str['num']		= "23456789";
	}
	// 配置
	public function init($config){
		if (!empty($config) && is_array($config)) {
			foreach($config as $key => $value) {
				$this -> config[$key] = $value;
			} 
		} 
	} 
	// 输出验证码
	public function create(){
		if (!function_exists("imagecreate")) {
			return false;
		}
		$this -> create_do();
	} 
	// 创建
	private function create_do(){
		$this -> im = imagecreate($this -> config['width'], $this -> config['height']); 
		imagecolorallocate($this -> im, 255, 255, 255); // 设置背景为白色
		$bordercolor = imagecolorallocate($this -> im, 37, 37, 37);// 为此背景加个边框
		imagerectangle($this -> im, 0, 0, $this -> config['width']-1, $this -> config['height']-1, $bordercolor); 
		$this -> create_str();// 生成验证码
		$vcode = $_SESSION[$this -> config['session']];
		$rand_num = rand(0, 2); 
		// 输入文字
		$size = min(floor($this -> config['width'] / $this -> config['length']), $this -> config['height']);
		for($i = 0;$i < $this -> config['length'];$i++) {
			$fontcolor = imagecolorallocate($this -> im, mt_rand(40, 190), mt_rand(40, 190), mt_rand(40, 190));

			imagettftext($this -> im, $size, mt_rand(-30, 30), $i * $size + 1 , mt_rand(-2, 2) + $size , $fontcolor, FONT_DIR . $this -> font_type[$rand_num], $vcode[$i]);
		} 
		// 加入干扰线
		$interfere = $this -> config['interfere'];
		$interfere = $interfere > 30?"30":$interfere;
		if (!empty($interfere) && $interfere > 1) {
			for($i = 1;$i < $interfere;$i++) {
				$linecolor = imagecolorallocate($this -> im, rand(0, 255), rand(0, 255), rand(0, 255));

				$x = rand(1, $this -> config['width']);
				$y = rand(1, $this -> config['height']);
				$x2 = rand($x-10, $x + 10);
				$y2 = rand($y-10, $y + 10);

				imageline($this -> im, $x, $y, $x2, $y2, $linecolor);
			} 
		} 

		header("Pragma:no-cache\r\n");
		header("Cache-Control:no-cache\r\n");
		header("Expires:0\r\n");
		header("content-type:image/jpeg\r\n");
		imagejpeg($this -> im);
		imagedestroy($this -> im);
	} 
	// 得到验证码
	private function create_str(){
		if ($this -> config['type'] == "int") {
			for($i = 1;$i <= $this -> config['length'];$i++) {
				$vcode .= rand(0, 9);
			}
			$_SESSION[$this -> config['session']] = $vcode;
			return true;
		}
		$len = strlen($this -> str[$this -> config['type']]);
		if (!$len) {
			$this -> config['type'] = "default";
			$this -> create_str();
		}
		for($i = 1;$i <= $this -> config['length'];$i++) {
			$offset = rand(0, $len-1);
			$vcode .= substr($this -> str[$this -> config['type']], $offset, 1);
		}
		$_SESSION[$this -> config['session']] = $vcode;
		return true;
	} 
} 
