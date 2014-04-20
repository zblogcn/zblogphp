<?php
return function($name, $ip, $email, $url, $content, &$sv, $config_sv){
	$sv += (preg_match('/[\x{4e00}-\x{9fa5}]+/u', $content) == 0) ? $config_sv : 0;
};