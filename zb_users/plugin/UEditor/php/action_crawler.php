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

if (function_exists('is_intranet_ip') == false) {
    function is_intranet_ip($check_ip) {
        if (filter_var($check_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            if (filter_var($check_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE|FILTER_FLAG_NO_PRIV_RANGE) === false) {
                return true;
            } else {
                $ip = explode('.', $check_ip);
                if (
                    ($ip[0] == 0) ||
                    ($ip[0] >= 240) ||
                    ($ip[0] == 127) ||
                    ($ip[0] == 169 && $ip[1] == 254)
                ) {
                    return true;
                }
                if (
                    ($ip[0] == 0) ||
                    ($ip[0] >= 240) ||
                    ($ip[0] == 127) ||
                    ($ip[0] == 169 && $ip[1] == 254)
                ) {
                    return true;
                }
                if (
                        ($ip[0] == 100 && $ip[1] >= 64 && $ip[1] <= 127 ) ||
                        ($ip[0] == 192 && $ip[1] == 0 && $ip[2] == 0 ) ||
                        ($ip[0] == 192 && $ip[1] == 0 && $ip[2] == 2 ) ||
                        ($ip[0] == 198 && $ip[1] >= 18 && $ip[1] <= 19 ) ||
                        ($ip[0] == 198 && $ip[1] == 51 && $ip[2] == 100 ) ||
                        ($ip[0] == 203 && $ip[1] == 0 && $ip[2] == 113 )
                ) {
                    return true;
                }
                return false;
            }
        } elseif(filter_var($check_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            if (filter_var($check_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === false) {
                return true;
            }else{
                $ip = explode(':', $check_ip);
                if (($ip[0] == 0 && $ip[1] == 0 && $ip[2] == 0 && $ip[3] == 0
                                && $ip[4] == 0 && $ip[5] == 0 && $ip[6] == 0 && ($ip[7] == 0 || $ip[7] == 1))
                            || ($ip[0] == 0x5f)
                            || ($ip[0] >= 0xfe80 && $ip[0] <= 0xfebf)
                            || ($ip[0] == 0x2001 && ($ip[1] == 0x0db8 || ($ip[1] >= 0x0010 && $ip[1] <= 0x001f)))
                            || ($ip[0] == 0x3ff3)
                    ) {
                    return true;
                }
                if ($ip[0] >= 0xfc00 && $ip[0] <= 0xfdff) {
                    return true;
                }
                if (($ip[0] == 0 && $ip[1] == 0 && $ip[2] == 0 && $ip[3] == 0 && $ip[4] == 0 && $ip[5] == 0xffff) ||
                        ($ip[0] == 0x0100 && $ip[1] == 0 && $ip[2] == 0 && $ip[3] == 0) ||
                        ($ip[0] == 0x2001 && $ip[1] <= 0x01ff) ||
                        ($ip[0] == 0x2001 && $ip[1] == 0x0002 && $ip[2] == 0) ||
                        ($ip[0] >= 0xfc00 && $ip[0] <= 0xfdff)
                   ) {
                    return true;
                }
                return false;
            }
        }
        return null;
    }
}

foreach ($source as $imgUrl) {
    $host = parse_url($imgUrl, PHP_URL_HOST);
    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
        if (is_intranet_ip($host) === true) {
            return json_encode(array(
                'state' => 'callback参数不合法',
            ));
        }
    } elseif(filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
        if (is_intranet_ip($host) === true) {
            return json_encode(array(
                'state' => 'callback参数不合法',
            ));
        }
    } else {
        $host = gethostbyname($host);
        if (is_intranet_ip($host) !== false) {
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
