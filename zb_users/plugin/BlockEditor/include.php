<?php

/**
 * BlockEditor：为 admin-next 提供富文本编辑器.
 *
 * 编辑器本体是 dist/editor.js，通过 plugin.xml 的 <admin><editor> 声明注册，
 * 宿主读取声明后把资源注入写文章页。这里只做插件登记与设置项。
 */

RegisterPlugin('BlockEditor', 'ActivePlugin_BlockEditor');

function ActivePlugin_BlockEditor()
{
    // 编辑器本身不注册钩子：资源注入由 admin-next 的清单采集完成。
    // 这里保留钩子入口，方便以后追加（例如自定义上传接口）。
}

function InstallPlugin_BlockEditor()
{
    global $zbp;

    if (!$zbp->HasConfig('BlockEditor')) {
        $zbp->Config('BlockEditor')->EnableSourceMode = true;
        $zbp->Config('BlockEditor')->EnableLocalDraft = true;
        $zbp->SaveConfig('BlockEditor');
    }
}

function UninstallPlugin_BlockEditor()
{
    global $zbp;

    $zbp->DelConfig('BlockEditor');
}
