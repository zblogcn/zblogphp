<?php
/**
 * FileSystem for Z-BlogPHP
 * @author 未寒
 * @copyright (C) 
 */
require '../../../zb_system/function/c_system_base.php';
global $zbp;
$zbp->Load();

//print_r($_POST);

$cmd_arg = $_POST['cmd_arg'];
$cmd_data = $_POST['cmd_data'];
$current_path = urldecode($_POST['current_path']);
$selected_file_list = $_POST['selected_file_list'];

switch ($cmd_arg) {
    case 1:
        create_dir();
        break;
	case 2:
        create_file();
        break;
	case 888:
		phpinfo();
		break;
}


function create_file(){
	global $cmd_data, $current_path, $selected_file_list;
	if (strlen($cmd_data)){
		$cmd_data = $current_path.$cmd_data;
		if (!file_exists($cmd_data)){
			if ($fh = @fopen($cmd_data, "w")){
				@fclose($fh);
			}
			chmod($cmd_data,0666);
			$error_msg = 0;
		} else $error_msg = '文件已存在。';
	}
	back_url($error_msg);
}

function create_dir(){
	global $cmd_data, $current_path, $selected_file_list;
	if (strlen($cmd_data)){
		$cmd_data = format_path($current_path.$cmd_data);
		if (!file_exists($cmd_data)){
			mkdir($cmd_data,0777);
			chmod($cmd_data,0777);
			$error_msg = 0;
		} else $error_msg = '文件夹已存在。';
	}
	back_url($error_msg);
}






function back_url($error_msg){
	global $current_path, $blogpath;
	$url = 'main.php?';
	if ($blogpath != $current_path) $url = $url . '&path=' . urlencode(str_replace($blogpath, "", $current_path));
	if($error_msg) $url = $url . '&error=' . $error_msg;
	Redirect($url);
}

function format_path($str){
    global $islinux;
    $str = trim($str);
    $str = str_replace("..","",str_replace("\\","/",str_replace("\$","",$str)));
    $done = false;
    while (!$done) {
        $str2 = str_replace("//","/",$str);
        if (strlen($str) == strlen($str2)) $done = true;
        else $str = $str2;
    }
    $tam = strlen($str);
    if ($tam){
        $last_char = $tam - 1;
        if ($str[$last_char] != "/") $str .= "/";
        if (!$islinux) $str = ucfirst($str);
    }
    return $str;
}
?>