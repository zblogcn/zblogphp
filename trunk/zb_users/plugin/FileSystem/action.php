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
    case 1://新建文件
        create_dir();
        break;
	case 2://新建文件夹
        create_file();
        break;
	case 3://删除文件
		del_file(iconv('UTF-8','GB2312',$current_path.$selected_file_list));
		break;
	case 4://下载文件
		download();
		break;
	case 888://phpinfo
		phpinfo();
		break;
}

function download(){
	global $cmd_data, $current_path;
	$file = iconv('UTF-8','GB2312',$current_path.$cmd_data);
    if(file_exists($file)){
		$size = filesize($file);
		header("Content-Type: application/save");
		header("Content-Length: $size");
		header("Content-Disposition: attachment; filename=\"$cmd_data\"");
		header("Content-Transfer-Encoding: binary");
		if ($fh = fopen("$file", "rb")){
			fpassthru($fh);
			fclose($fh);
		}
    }
}

function del_file($arg){
	delfile($arg);
	$error_msg = 0;
	if (file_exists($arg)) $error_msg = '文件删除失败。';
	back_url($error_msg);
}

function delfile($arg){
	if (file_exists($arg)) {
        @chmod($arg,0777);
        if (is_dir($arg)) {
            $handle = opendir($arg);
            while(false !== ($aux = readdir($handle))) {
                if ($aux != "." && $aux != "..") delfile($arg."/".$aux);
            }
            @closedir($handle);
            rmdir($arg);
        } else unlink($arg);
    }
}


function create_file(){
	global $cmd_data, $current_path;
	$cmd_data = iconv('UTF-8','GB2312',$current_path.$cmd_data);
	if (strlen($cmd_data)){
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
	global $cmd_data, $current_path;
	$cmd_data = iconv('UTF-8','GB2312',$current_path.$cmd_data);
	if (strlen($cmd_data)){
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