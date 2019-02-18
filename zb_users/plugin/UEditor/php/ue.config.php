<?php

require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'UploadPst';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

$upload_dir = 'zb_users/upload/' . date('Y/m') . '/';
//$upload_path = $bloghost . $upload_dir;
$upload_path = ''; // = $upload_dir = '';

$upload_dir = $bloghost . $upload_dir;
$upload_allow = explode('|', '.' . str_replace('|', '|.', $zbp->option['ZC_UPLOAD_FILETYPE']));
$max_size = $zbp->option['ZC_UPLOAD_FILESIZE'] * 1024 * 1024;

$CONFIG = array(
    /* 上传图片配置项 */
    "imageActionName"     => "uploadimage", /* 执行上传图片的action名称 */
    "imageFieldName"      => "upfile", /* 提交的图片表单名称 */
    "imageMaxSize"        => $max_size, /* 上传大小限制，单位B */
    "imageAllowFiles"     => $upload_allow, /* 上传图片格式显示 */
    "imageCompressEnable" => false, /* 是否压缩图片,默认是true */
    "imageCompressBorder" => 2940, /* 图片压缩最长边限制 */
    "imageInsertAlign"    => "none", /* 插入的图片浮动方式 */
    "imageUrlPrefix"      => "", /* 图片访问路径前缀 */
    "imagePathFormat"     => $upload_dir . "{yyyy}{mm}{dd}{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
    /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
    /* {time} 会替换成时间戳 */
    /* {yyyy} 会替换成四位年份 */
    /* {yy} 会替换成两位年份 */
    /* {mm} 会替换成两位月份 */
    /* {dd} 会替换成两位日期 */
    /* {hh} 会替换成两位小时 */
    /* {ii} 会替换成两位分钟 */
    /* {ss} 会替换成两位秒 */
    /* 非法字符 \  => * ? " < > | */
    /* 具请体看线上文档 => fex.baidu.com/ueditor/#use-format_upload_filename */

    /* 涂鸦图片上传配置项 */
    "scrawlActionName"  => "uploadscrawl", /* 执行上传涂鸦的action名称 */
    "scrawlFieldName"   => "upfile", /* 提交的图片表单名称 */
    "scrawlPathFormat"  => $upload_dir . "{yyyy}{mm}{dd}{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    "scrawlMaxSize"     => $max_size, /* 上传大小限制，单位B */
    "scrawlUrlPrefix"   => "", /* 图片访问路径前缀 */
    "scrawlAllowFiles"  => $upload_allow, /* 上传图片格式显示 */
    "scrawlInsertAlign" => "none",

    /* 抓取远程图片配置 */
    "catcherLocalDomain" => array("127.0.0.1", "localhost", "img.baidu.com", parse_url($zbp->host, PHP_URL_HOST)),
    "catcherActionName"  => "catchimage", /* 执行抓取远程图片的action名称 */
    "catcherFieldName"   => "source", /* 提交的图片列表表单名称 */
    "catcherPathFormat"  => $upload_dir . "{yyyy}{mm}{dd}{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    "catcherUrlPrefix"   => "", /* 图片访问路径前缀 */
    "catcherMaxSize"     => $max_size, /* 上传大小限制，单位B */
    "catcherAllowFiles"  => $upload_allow, /* 抓取图片格式显示 */

    /* 上传视频配置 */
    "videoActionName" => "uploadvideo", /* 执行上传视频的action名称 */
    "videoFieldName"  => "upfile", /* 提交的视频表单名称 */
    "videoPathFormat" => $upload_dir . "{yyyy}{mm}{dd}{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    "videoUrlPrefix"  => "", /* 视频访问路径前缀 */
    "videoMaxSize"    => $max_size, /* 上传大小限制，单位B，默认100MB */
    "videoAllowFiles" => $upload_allow, /* 上传视频格式显示 */

    /* 上传文件配置 */
    "fileActionName" => "uploadfile", /* controller里,执行上传视频的action名称 */
    "fileFieldName"  => "upfile", /* 提交的文件表单名称 */
    "filePathFormat" => $upload_dir . "{yyyy}{mm}{dd}{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
    "fileUrlPrefix"  => "", /* 文件访问路径前缀 */
    "fileMaxSize"    => $max_size, /* 上传大小限制，单位B，默认50MB */
    "fileAllowFiles" => $upload_allow, /* 上传文件格式显示 */

);
