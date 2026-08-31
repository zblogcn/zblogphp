<?php

RegisterPlugin('backend_legacy', 'ActivePlugin_backend_legacy');

function ActivePlugin_backend_legacy()
{
    // 临时使用接口返回 c_admin_js_add 所需数据
    Add_Filter_Plugin('Filter_Plugin_Cmd_Ajax', 'backend_legacy_get_c_admin_js_add_data');
}

/**
 * 返回 c_admin_js_add 所需要的变量数据.
 *
 * @param mixed $src
 */
function backend_legacy_get_c_admin_js_add_data($src)
{
    global $zbp;

    if ('admin2' !== $src) {
        return null;
    }

    // 构造 options（与旧版 c_admin_js_add.php 保持一致）
    $options = [
        'bloghost' => $zbp->host,
        'blogversion' => $zbp->version,
        'ajaxurl' => $zbp->ajaxurl,
        'cookiepath' => $zbp->cookiespath,
        'comment' => [
            'useDefaultEvents' => false,
            // 输出为 {} 而非 []
            'inputs' => new stdClass(),
        ],
    ];

    // 构造语言项（尽量取系统语言，缺失时提供英文兜底）
    $lang_msg = [
        'notify' => isset($zbp->lang['msg']['notify']) ? $zbp->lang['msg']['notify'] : 'Notification',
        'refresh_cache' => isset($zbp->lang['msg']['refresh_cache']) ? $zbp->lang['msg']['refresh_cache'] : 'Please refresh cache',
        'operation_failed' => isset($zbp->lang['msg']['operation_failed']) ? $zbp->lang['msg']['operation_failed'] : 'Operation failed',
        'batch_operation_in_progress' => isset($zbp->lang['msg']['batch_operation_in_progress']) ? $zbp->lang['msg']['batch_operation_in_progress'] : 'Batch operation in progress...',
    ];

    $lang_error = [
        '94' => isset($zbp->lang['error']['94']) ? $zbp->lang['error']['94'] : 'Login status expired, please refresh and retry. (%s)',
    ];

    $lang = [
        'msg' => $lang_msg,
        'error' => $lang_error,
    ];

    // 收集插件注入的 JS 片段（与 Filter_Plugin_Admin_Js_Add 保持兼容）
    $extraScripts = [];
    if (isset($GLOBALS['hooks']['Filter_Plugin_Admin_Js_Add']) && is_array($GLOBALS['hooks']['Filter_Plugin_Admin_Js_Add'])) {
        ob_start();
        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Js_Add'] as $fpname => &$fpsignal) {
            // 插件函数预计会 echo 出 JS 代码
            $fpname();
        }
        $pluginJs = ob_get_clean();
        if (is_string($pluginJs) && '' !== $pluginJs) {
            // 作为一段脚本片段传给前端执行
            $extraScripts[] = $pluginJs;
        }
    }

    JsonReturn([
        'options' => $options,
        'lang' => $lang,
        'extraScripts' => $extraScripts,
    ]);
}
