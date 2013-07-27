<?php


// 简单的模板引擎原理

$html_file = 'test.html';	//HTML模板

$php_file = 'test.php';		//编译后的PHP模板代码

// 如果要做html模板修改后再编译一下，可以使用 filemtime 判断 HTML模板修改时间 是否大于 PHP模板修改时间
// 如： filemtime($html_file) > filemtime($php_file)
if(!is_file($php_file)) {
	$tpl_str = file_get_contents($html_file);	//读

	//解析
	//=========================================================================
	//严格要求的变量和数组 $abc[a]['b']["c"][$d] 合法    $abc[$a[b]] 不合法
	$reg_arr = '[a-zA-Z_]\w*(?:\[\w+\]|\[\'\w+\'\]|\[\"\w+\"\]|\[\$[a-zA-Z_]\w*\])*';

	$tpl_str = preg_replace('#\{\$([^\}]+)\}#', '<?php echo $\\1; ?>', $tpl_str);	//解析变量

	//其他好像 if for 之类的 代码会有点多。可以看 DZ 或者 XN 再或者 我的模板引擎代码了，原理一样使用正则。效率不用但心，只会正则一次。之后都使用缓存。
	

	file_put_contents($php_file, $tpl_str, LOCK_EX);	//写
}

include $php_file;

