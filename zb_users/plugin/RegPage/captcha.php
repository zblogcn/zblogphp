<?php 

/**
验证验证码正确或错误的方法
验证码图片上的文字被存放到了SESSION 变量里面，验证的时候，我们需要将SESSION 里面的值和用户输入的值进行比较即可。

$_SESSION[6_letters_code] – 存放着验证码的文字值
$_POST[6_letters_code] – 这是用户输入的验证码的内容


<?php session_start(); 
 
if(isset($_REQUEST['Submit'])){ 
    
// 服务器端验证的代码
    if(empty($_SESSION['6_letters_code'] ) || strcasecmp($_SESSION['6_letters_code'], $_POST['6_letters_code']) != 0)
    { 
        $msg="验证失败！";
    }else{
        
//验证码验证正确，这里放验证成功后的代码
    }
} 
?>
*/
	session_start();
	//设置: 你可以在这里修改验证码图片的参数
	$image_width = 80;
	$image_height = 30;
	$characters_on_image = 6;
	$font = 'monofont.ttf'; 

	$possible_letters = '234567890abcdefghjklmnopqrstuvwxyz';
	$random_dots = 80;
	$random_lines = 3;
	$captcha_text_color="0x14864";
	$captcha_noice_color = "0x4264"; 
	 
	$code = ''; 
	 
	$i = 0;
	while ($i < $characters_on_image) { 
		$code .= substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
		$i++;
	}
	 
	$font_size = $image_height * 0.75; 
	$image = @imagecreate($image_width, $image_height);
	 
	/* 设置背景、文本和干扰的噪点 */
	$background_color = imagecolorallocate($image, 255, 255, 255);
	 
	$arr_text_color = hexrgb($captcha_text_color); 
	$text_color = imagecolorallocate($image, $arr_text_color['red'], 
	$arr_text_color['green'], $arr_text_color['blue']);
	 
	$arr_noice_color = hexrgb($captcha_noice_color); 
	$image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], 
	$arr_noice_color['green'], $arr_noice_color['blue']);
	 
	/* 在背景上随机的生成干扰噪点 */
	for( $i=0; $i<$random_dots; $i++ ) {
		imagefilledellipse($image, mt_rand(0,$image_width),
		mt_rand(0,$image_height), 2, 3, $image_noise_color);
	}
	 
	/* 在背景图片上，随机生成线条 */
	for( $i=0; $i<$random_lines; $i++ ) {
		imageline($image, mt_rand(0,$image_width), mt_rand(0,$image_height),
		mt_rand(0,$image_width), mt_rand(0,$image_height), $image_noise_color);
	}
	 
	/* 生成一个文本框，然后在里面写生6个字符 */
	$textbox = imagettfbbox($font_size, 0, $font, $code); 
	$x = ($image_width - $textbox[4])/2;
	$y = ($image_height - $textbox[5])/2;
	imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code);
	 
	/* 将验证码图片在HTML页面上显示出来 */
	header('Content-Type: image/jpeg');
	// 设定图片输出的类型
	imagejpeg($image);
	//显示图片
	imagedestroy($image);
	//销毁图片实例
	$_SESSION['6_letters_code'] = $code;
	
	function hexrgb ($hexstr) {
		$int = hexdec($hexstr);
	 
		return array( "red" => 0xFF & ($int >> 0x10),
					"green" => 0xFF & ($int >> 0x8),
					"blue" => 0xFF & $int
		);
	}
?>