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
 *
 * @return array
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
        return array(
            'data' => array('module' => $array),
        );
    }

    return array(
        'code' => 404,
        'message' => $GLOBALS['lang']['error']['97'],
    );
}

/**
 * 新增/修改模块接口.
 *
 * @return array
 */
function api_module_post()
{
    global $zbp;

    ApiCheckAuth(true, 'ModulePst');

    if (PostModule()) {
        $zbp->BuildModule();
        $zbp->SaveCache();

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
        );
    }

    return array(
        'code' => 500,
        'message' => $GLOBALS['lang']['msg']['operation_failed'],
    );
}

/**
 * 删除模块接口.
 *
 * @return array
 */
function api_module_delete()
{
    global $zbp;

    ApiCheckAuth(true, 'ModuleDel');

    ApiVerifyCSRF();

    if ($zbp->GetModuleByID((int) GetVars('id', 'GET'))->ID == 0) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }
    if (DelModule()) {
        $zbp->BuildModule();
        $zbp->SaveCache();
        
        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
        );
    }

    return array(
        'code' => 500,
        'message' => $GLOBALS['lang']['msg']['operation_failed'],
    );
}

/**
 * 列出模块接口.
 *
 * @return array
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

    return array(
        'data' => array(
            'list' => ApiGetObjectArrayList($modules),
        ),
    );
}

/**
 * 设置侧栏模接口.
 *
 * @return array
 */
function api_module_set_sidebar()
{
    global $zbp;

    ApiCheckAuth(true, 'SidebarSet');

    SetSidebar();
    $zbp->BuildModule();
    $zbp->SaveCache();

    return array(
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 列出侧栏接口.
 *
 * @return array
 */
function api_module_list_sidebar()
{
    global $zbp;

    ApiCheckAuth(false, 'view');

    $sidebarId = (int) GetVars('id');
    $data = array();

    if (! $zbp->CheckRights('ModuleMng')) {
        $remove_props = array('MaxLi', 'Source', 'Metas');
    } else {
        $remove_props = array();
    }

    if ($sidebarId > 0 && $sidebarId < 10) {
        // 列出指定 id 的侧栏
        $sidebarName = ($sidebarId == 1) ? 'sidebar' : 'sidebar' . $sidebarId;
        foreach ($zbp->template->$sidebarName as $module) {
            $data[] = ApiGetObjectArray($module, array(), $remove_props);
        }
    } else {
        // 列出所有侧栏列表
        for ($i = 1; $i <= 9; $i++) {
            $data['sidebar' . $i] = array();
            $sidebarName = ($i == 1) ? 'sidebar' : 'sidebar' . $i;
            foreach ($zbp->template->$sidebarName as $module) {
                $data['sidebar' . $i][] = ApiGetObjectArray($module, array(), $remove_props);
            }
        }
    }

    return compact('data');
}
