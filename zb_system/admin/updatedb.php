<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-03-26
 */
require '../function/c_system_base.php';
require ZBP_PATH . 'zb_system/function/c_system_updatedb.php';

$zbp->Load();

Add_Filter_Plugin('Filter_Plugin_Debug_Handler_Common', 'JsonError4ShowErrorHook', PLUGIN_EXITSIGNAL_RETURN);

if (!$zbp->CheckRights('root')) {
    $zbp->ShowError(6);
    die();
}

// updatedb=1 允许修复“版本已推进但历史 DDL 实际失败”的库；普通调用仍按版本判断。
if ($zbp->version >= ZC_LAST_VERSION
    && ((int) $zbp->option['ZC_LAST_VERSION'] < ZC_LAST_VERSION || isset($_GET['updatedb']))
) {
    try {
        updatedb();
    } catch (Throwable $e) {
        // updatedb() 仅在全部 DDL 成功后更新版本；这里只负责把底层错误
        // 转换成管理员可读的响应，不继续执行或保存配置。
        $zbp->ShowError('数据库升级失败：' . $e->getMessage(), __FILE__, __LINE__, null, 500);
        die();
    }

    if (isset($_GET['updatedb'])) {
        echo $zbp->langs->msg->operation_succeed;
    } else {
        JsonReturn($zbp->langs->msg->operation_succeed);
    }
}
die;
