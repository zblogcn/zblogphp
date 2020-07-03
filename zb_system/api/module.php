<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-07-04
 */

/**
 * 获取模块信息接口.
 */
function api_module_get()
{
    global $zbp;

    ApiCheckAuth(true, 'ModuleMng');

    $module = null;
    $modId = (int) GetVars('id');
    $modFileName = GetVars('filename');

    if ($modId > 0) {
        $module = $zbp->GetModuleByID($modId);
    } elseif ($modFileName !== null) {
        $module = $zbp->GetModuleByFileName($modFileName);
    }

    $array = ApiGetObjectArray($module);

    if ($module && $module->ID !== null) {
        ApiResponse($array);
    }

    ApiResponse(null, null, 404, $GLOBALS['lang']['error']['97']);
}

/**
 * 新增/修改模块接口.
 */
function api_module_post()
{
    global $zbp;

    ApiCheckAuth(true, 'ModulePst');

    if (PostModule()) {
        $zbp->BuildModule();
        $zbp->SaveCache();
        ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
    }

    ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
}

/**
 * 删除模块接口.
 */
function api_module_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'ModuleDel');

    if (DelModule()) {
        $zbp->BuildModule();
        $zbp->SaveCache();
        ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
    }

    ApiResponse(null, null, 500, $GLOBALS['lang']['msg']['operation_failed']);
}

/**
 * 列出模块接口.
 */
function api_module_list()
{
    global $zbp;

    ApiCheckAuth(true, 'ModuleMng');

    $modType = GetVars('type');
    $systemMods = array();
    $userMods = array();
    $themeMods = array();
    $fileMods = array();

    foreach ($zbp->modules as $module) {
        if ($module->SourceType == 'system') {
            $systemMods[] = $module;
        } elseif ($module->SourceType == 'user') {
            $userMods[] = $module;
        } elseif ($module->SourceType == 'theme') {
            if ($module->Source == 'theme' || (substr($module->Source, (-1 - strlen($zbp->theme)))) == ('_' . $zbp->theme)) {
                $themeMods[] = $module;
            }
        } else {
            $fileMods[] = $module;
        }
    }

    switch ($modType) {
        case 'system':
            $modules = $systemMods;
            break;
        case 'user':
            $modules = $userMods;
            break;
        case 'theme':
            $modules = $themeMods;
            break;
        case 'file':
            $modules = $fileMods;
            break;
        default:
            $modules = $zbp->modules;
    }

    $listArr = ApiGetObjectArrayList(
        $modules
    );

    ApiResponse($listArr);
}

/**
 * 设置侧栏模接口.
 */
function api_module_set_sidebar()
{
    global $zbp;

    ApiCheckAuth(true, 'SidebarSet');

    SetSidebar();
    $zbp->BuildModule();
    $zbp->SaveCache();
    ApiResponse(null, null, 200, $GLOBALS['lang']['msg']['operation_succeed']);
}

/**
 * 列出侧栏接口.
 */
function api_module_list_sidebar()
{
    global $zbp;

    ApiCheckAuth(true, 'SidebarSet');

    $sidebarId = (int) GetVars('id');
    $data = array();

    if ($sidebarId > 0 && $sidebarId < 10) {
        // 列出指定 id 的侧栏
        $sidebarName = ($sidebarId == 1) ? 'sidebar' : 'sidebar' . $sidebarId;
        foreach ($zbp->template->$sidebarName as $module) {
            $data[] = ApiGetObjectArray($module);
        }
    } else {
        // 列出所有侧栏列表
        for ($i = 1; $i <= 9; $i++) {
            $data['sidebar' . $i] = array();
            $sidebarName = ($i == 1) ? 'sidebar' : 'sidebar' . $i;
            foreach ($zbp->template->$sidebarName as $module) {
                $data['sidebar' . $i][] = ApiGetObjectArray($module);
            }
        }
    }

    ApiResponse($data);
}
