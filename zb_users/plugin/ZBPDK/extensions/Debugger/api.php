<?php
/**
 * AJAX请求处理.
 *
 * @author 心扬 <chrishyze@gmail.com>
 */
require_once '../../../../../zb_system/function/c_system_base.php';
$zbp->Load();
if (!headers_sent()) {
    header('Content-Type: application/json;charset=utf-8');
}
if (!$zbp->CheckPlugin('ZBPDK')) {
    echo json_msg(false, '插件未启用!');
    die();
}
if (!$zbp->CheckRights('root')) {
    echo json_msg(false, '没有访问权限!');
    die();
}

//处理请求
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] == 'detail') {
        $res = get_interface_detail($_GET['interface'], $_GET['func']);
        if (strpos($res, 'Exception') !== false && strpos($res, 'Exception') == 0) {
            $res = '<p>' . $res . '</p><p>获取接口详情失败! </p><p><strong>interface</strong>: ' . $_GET['interface'] . '</p><p><strong>func</strong>:' . $_GET['func'] . '</p>';
        }
        echo json_msg(true, $res);
    } else {
        echo json_msg(false, '参数错误!');
    }
} else {
    echo json_msg(false, '非法请求!');
}

/**
 * 获取接口详情
 * SourceCode: PluginInterface @zsx.
 *
 * @param string $interface_name 接口名称
 * @param string $func_name      函数名称
 *
 * @return string
 */
function get_interface_detail($interface_name = '', $func_name = '')
{
    $str = '';

    try {
        $func = new ReflectionFunction($func_name);
    } catch (ReflectionException $e) {
        return 'Exception: ' . $e->getMessage();
    }
    $start = $func->getStartLine() - 1;
    $end = $func->getEndLine() - 1;
    $filename = $func->getFileName();
    $str .= '<p><strong>Interface</strong>: ' . $interface_name . '</p>';
    $str .= '<p><strong>FilePath</strong>: ' . $filename . '</p>';
    $str .= '<p><strong>StartLine</strong>: ' . $start . '</p>';
    $str .= '<p><strong>EndLine</strong>: ' . $end . '</p>';
    $str .= '<pre class="debug-pre">' . htmlspecialchars(implode('', array_slice(file($filename), $start, $end - $start + 1))) . '</pre>';

    return $str;
}

/**
 * JSON返回
 * 避免中文转码 Unicode
 * 兼容 PHP5.2.0～PHP7.
 *
 * @param bool   $status  状态
 * @param string $message 消息
 *
 * @return string
 */
function json_msg($status, $message)
{
    if (version_compare(PHP_VERSION, '5.4.0', '<')) {
        $str = json_encode(array($status, $message));

        return preg_replace_callback('#\\\u([0-9a-f]{4})#i', 'get_matched_iconv', $str);
    } else {
        return json_encode(array($status, $message), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

/**
 * 字符编码转换
 * 单独定义函数以兼容PHP5.2.
 *
 * @param array $matchs
 *
 * @return string
 */
function get_matched_iconv($matchs)
{
    return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
}
