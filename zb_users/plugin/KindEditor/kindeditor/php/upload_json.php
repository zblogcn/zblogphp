<?php
/**
 * KindEditor for Z-BlogPHP.
 *
 * @author 未寒
 * @copyright (C) RainbowSoft Studio
 */
require '../../../../../zb_system/function/c_system_base.php';

$zbp->Load();

if (!$zbp->CheckRights('UploadPst')) {
    die();
}

//格式化允许上传的文件扩展名
$ext_arr = explode("|", $zbp->option['ZC_UPLOAD_FILETYPE']);
//最大文件大小
$max_size = 1024 * 1024 * (int) $zbp->option['ZC_UPLOAD_FILESIZE'];

//PHP上传失败
if (!empty($_FILES['imgFile']['error'])) {
    switch ($_FILES['imgFile']['error']) {
    case '1':
        $error = '超过php.ini允许的大小。';
        break;
    case '2':
        $error = '超过表单允许的大小。';
        break;
    case '3':
        $error = '图片只有部分被上传。';
        break;
    case '4':
        $error = '请选择图片。';
        break;
    case '5':
        $error = '上传文件大小为0。';
        break;
    case '6':
        $error = '找不到临时目录。';
        break;
    case '7':
        $error = '写文件到硬盘出错。';
        break;
    case '8':
        $error = 'File upload stopped by extension。';
        break;
    case '999':
    default:
        $error = '未知错误。';
    }
    alert($error);
}

//有上传文件时
if (empty($_FILES) === false) {
    //原文件名
    $file_name = $_FILES['imgFile']['name'];
    //服务器上临时文件名
    $tmp_name = $_FILES['imgFile']['tmp_name'];
    //文件大小
    $file_size = $_FILES['imgFile']['size'];
    //检查文件名
    if (!$file_name) {
        alert("请选择文件。");
    }
    //检查目录
    // if (@is_dir($save_path) === false) {
    // 	alert("上传目录不存在。");
    // }
    //检查目录写权限
    // if (@is_writable($save_path) === false) {
    // 	alert("上传目录没有写权限。");
    // }
    //检查是否已上传
    if (@is_uploaded_file($tmp_name) === false) {
        alert("上传失败。");
    }
    //检查文件大小
    if ($file_size > $max_size) {
        alert("上传文件大小超过限制。");
    }
    //检查目录名
    // $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
    // if (empty($ext_arr[$dir_name])) {
    // 	alert("目录名不正确。");
    // }
    //获得文件扩展名
    $temp_arr = explode(".", $file_name);
    $file_ext = array_pop($temp_arr);
    $file_ext = trim($file_ext);
    $file_ext = strtolower($file_ext);
    //检查扩展名
    if (in_array($file_ext, $ext_arr) === false) {
        alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr) . "格式。");
    }

    $upload = new Upload();
    $upload->Name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
    $upload->SourceName = $file_name;
    $upload->MimeType = $_FILES['imgFile']['type'];
    $upload->Size = $file_size;
    $upload->AuthorID = $zbp->user->ID;

    $upload->SaveFile($tmp_name);
    $upload->Save();

    $file_url = $upload->Url;

    header('Content-type: text/html; charset=UTF-8');

    echo json_encode(array('error' => 0, 'url' => $file_url));
    exit;
}

function alert($msg)
{
    header('Content-type: text/html; charset=UTF-8');
    echo json_encode(array('error' => 1, 'message' => $msg));
    exit;
}
