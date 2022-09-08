<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/*
 * 抓取远程图片
 * User: Jinqn
 * Date: 14-04-14
 * Time: 下午19:18.
 */
if (function_exists('set_time_limit')) {
    set_time_limit(0);
}
include "Uploader.class.php";

/* 上传配置 */
$config = array(
    "pathFormat" => $CONFIG['catcherPathFormat'],
    "maxSize"    => $CONFIG['catcherMaxSize'],
    "allowFiles" => $CONFIG['catcherAllowFiles'],
    "oriName"    => "remote.png",
);
$fieldName = $CONFIG['catcherFieldName'];

/* 抓取远程图片 */
$list = array();
if (isset($_POST[$fieldName])) {
    $source = $_POST[$fieldName];
} else {
    $source = $_GET[$fieldName];
}

foreach ($source as $imgUrl) {
    $host = parse_url($imgUrl, PHP_URL_HOST);
    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false) {
            return json_encode(array(
                'state' => 'callback参数不合法',
            ));
        }
    } elseif(filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === false) {
            return json_encode(array(
                'state' => 'callback参数不合法',
            ));
        }
    }
}

foreach ($source as $imgUrl) {
    $item = new Uploader($imgUrl, $config, "remote");
    $info = $item->getFileInfo();
    array_push($list, array(
        "state"    => $info["state"],
        "url"      => $info["url"],
        "size"     => $info["size"],
        "title"    => htmlspecialchars($info["title"]),
        "original" => htmlspecialchars($info["original"]),
        "source"   => htmlspecialchars($imgUrl),
    ));
}

/* 返回抓取数据 */
return json_encode(array(
    'state' => count($list) ? 'SUCCESS' : 'ERROR',
    'list'  => $list,
));
