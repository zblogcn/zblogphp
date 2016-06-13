<?php
/**
 * FileSystem for Z-BlogPHP
 * @author 未寒
 * @copyright (C) 
 */
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
global $zbp;
$zbp->Load();
if (!$zbp->CheckRights('root')) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('FileSystem')) {$zbp->ShowError(48);die();}
//print_r($_POST);die();

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
		del_file(iconv('UTF-8', 'GB2312', $current_path.$selected_file_list));
		break;
	case 4://下载文件
		download();
		break;
	case 5://重命名
		renamefile();
		break;
	case 6://编辑文件
		edit_file();
		break;
	case 10://上传文件
		upload_form();
		break;
	case 888://phpinfo
		phpinfo();
		break;
}

function edit_file() {
	global $cmd_data, $current_path, $selected_file_list;
	$file = iconv('UTF-8', 'GB2312', $current_path.$selected_file_list);
    if(file_exists($oldfile)){
		$error_msg = 0;
		if (!rename($oldfile, $newfile)) $error_msg = '重命名失败，请重试。';
		back_url($error_msg);
    }
}
function renamefile() {
	global $cmd_data, $current_path, $selected_file_list;
	$oldfile = iconv('UTF-8', 'GB2312', $current_path.$selected_file_list);
	$newfile = iconv('UTF-8', 'GB2312', $current_path.$cmd_data);
    if(file_exists($oldfile)){
		$error_msg = 0;
		if (!rename($oldfile, $newfile)) $error_msg = '重命名失败，请重试。';
		back_url($error_msg);
    }
}

function download() {
	global $cmd_data, $current_path;
	$file = iconv('UTF-8', 'GB2312', $current_path.$cmd_data);
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

function del_file($arg) {
	delfile($arg);
	$error_msg = 0;
	if (file_exists($arg)) $error_msg = '文件删除失败。';
	back_url($error_msg);
}

function delfile($arg) {
	if (file_exists($arg)) {
        @chmod($arg, 0755);
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


function create_file() {
	global $cmd_data, $current_path;
	$cmd_data = iconv('UTF-8', 'GB2312', $current_path.$cmd_data);
	if (strlen($cmd_data)){
		if (!file_exists($cmd_data)){
			if ($fh = @fopen($cmd_data, "w")){
				@fclose($fh);
			}
			chmod($cmd_data, 0666);
			$error_msg = 0;
		} else $error_msg = '文件已存在。';
	}
	back_url($error_msg);
}

function create_dir() {
	global $cmd_data, $current_path;
	$cmd_data = iconv('UTF-8', 'GB2312', $current_path.$cmd_data);
	if (strlen($cmd_data)){
		if (!file_exists($cmd_data)){
			mkdir($cmd_data, 0755);
			$error_msg = 0;
		} else $error_msg = '文件夹已存在。';
	}
	back_url($error_msg);
}


function upload_form() {
    global $_FILES,$current_dir,$dir_dest,$fechar,$quota_mb,$path_info;
    $num_uploads = 5;
    html_header();
    echo "<body marginwidth=\"0\" marginheight=\"0\">";
    if (count($_FILES) == 0){
        echo "
        <table height=\"100%\" border=0 cellspacing=0 cellpadding=2 align=center>
        <form name=\"upload_form\" action=\"".$path_info["basename"]."\" method=\"post\" ENCTYPE=\"multipart/form-data\">
        <input type=hidden name=dir_dest value=\"$current_dir\">
        <input type=hidden name=action value=10>
        <tr><th colspan=2>".et('Upload')."</th></tr>
        <tr><td align=right><b>".et('Destination').":<td><b><nobr>$current_dir</nobr>";
        for ($x = 0;$x < $num_uploads;$x++){
            echo "<tr><td width=1 align=right><b>".et('File').":<td><nobr><input type=\"file\" name=\"file$x\"></nobr>";
            $test_js .= "(document.upload_form.file$x.value.length>0)||";
        }
        echo "
        <input type=button value=\"".et('Send')."\" onclick=\"test_upload_form()\"></nobr>
        <tr><td> <td><input type=checkbox name=fechar value=\"1\"> <a href=\"JavaScript:troca();\">".et('AutoClose')."</a>
        <tr><td colspan=2> </td></tr>
        </form>
        </table>
        <script language=\"Javascript\" type=\"text/javascript\">
        <!--
            function troca(){
                if(document.upload_form.fechar.checked){document.upload_form.fechar.checked=false;}else{document.upload_form.fechar.checked=true;}
            }
            foi = false;
            function test_upload_form(){
                if(".substr($test_js, 0, strlen($test_js) - 2)."){
                    if (foi) alert('".et('SendingForm')."...');
                    else {
                        foi = true;
                        document.upload_form.submit();
                    }
                } else alert('".et('NoFileSel').".');
            }
            window.moveTo((window.screen.width-400)/2,((window.screen.height-200)/2)-20);
        //-->
        </script>";
    } else {
        $out = "<tr><th colspan=2>".et('UploadEnd')."</th></tr>
                <tr><th colspan=2><nobr>".et('Destination').": $dir_dest</nobr>";
        for ($x = 0;$x < $num_uploads;$x++){
            $temp_file = $_FILES["file".$x]["tmp_name"];
            $filename = $_FILES["file".$x]["name"];
            if (strlen($filename)) $resul = save_upload($temp_file, $filename, $dir_dest);
            else $resul = 7;
            switch($resul){
                case 1:
                $out .= "<tr><td><b>".str_zero($x + 1, 3).".<font color=green><b> ".et('FileSent').":</font><td>".$filename."</td></tr>\n";
                break;
                case 2:
                $out .= "<tr><td colspan=2><font color=red><b>".et('IOError')."</font></td></tr>\n";
                $x = $upload_num;
                break;
                case 3:
                $out .= "<tr><td colspan=2><font color=red><b>".et('SpaceLimReached')." ($quota_mb Mb)</font></td></tr>\n";
                $x = $upload_num;
                break;
                case 4:
                $out .= "<tr><td><b>".str_zero($x + 1, 3).".<font color=red><b> ".et('InvExt').":</font><td>".$filename."</td></tr>\n";
                break;
                case 5:
                $out .= "<tr><td><b>".str_zero($x + 1, 3).".<font color=red><b> ".et('FileNoOverw')."</font><td>".$filename."</td></tr>\n";
                break;
                case 6:
                $out .= "<tr><td><b>".str_zero($x + 1, 3).".<font color=green><b> ".et('FileOverw').":</font><td>".$filename."</td></tr>\n";
                break;
                case 7:
                $out .= "<tr><td colspan=2><b>".str_zero($x + 1, 3).".<font color=red><b> ".et('FileIgnored')."</font></td></tr>\n";
            }
        }
        if ($fechar) {
            echo "
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                window.close();
            //-->
            </script>
            ";
        } else {
            echo "
            <table height=\"100%\" border=0 cellspacing=0 cellpadding=2 align=center>
            $out
            <tr><td colspan=2> </td></tr>
            </table>
            <script language=\"Javascript\" type=\"text/javascript\">
            <!--
                window.focus();
            //-->
            </script>
            ";
        }
    }
    echo "</body>\n</html>";
}



function back_url($error_msg) {
	global $current_path, $blogpath;
	$url = 'main.php?';
	if ($blogpath != $current_path) $url = $url . '&path=' . urlencode(str_replace($blogpath, "", $current_path));
	if($error_msg) $url = $url . '&error=' . $error_msg;
	Redirect($url);
}

function format_path($str) {
    global $islinux;
    $str = trim($str);
    $str = str_replace("..", "", str_replace("\\", "/", str_replace("\$", "", $str)));
    $done = false;
    while (!$done) {
        $str2 = str_replace("//", "/", $str);
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
