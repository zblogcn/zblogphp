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

    ApiCheckAuth(false, 'view');

    if (! $zbp->CheckRights('ModuleMng')) {
        $remove_props = array('Metas');
    } else {
        $remove_props = array();
    }

    $module = null;
    $modId = (int) GetVars('id');
    $modFileName = GetVars('filename');

    if ($modId != 0) {
        $module = $zbp->GetModuleByID($modId);
    } elseif ($modFileName != '') {
        $module = $zbp->GetModuleByFileName($modFileName);
    }

    if ($module && $module->ID != 0) {
        $array = ApiGetObjectArray(
            $module,
            array('SourceType', 'NoRefresh'),
            $remove_props
        );

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

    try {
        $module = PostModule();
        $zbp->BuildModule();
        $zbp->SaveCache();

        $array = ApiGetObjectArray($module, array('SourceType', 'NoRefresh'));

        return array(
            'message' => $GLOBALS['lang']['msg']['operation_succeed'],
            'data' => array('module' => $array),
        );
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }
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

    ApiVerifyCSRF(true);

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

    ApiCheckAuth(false, 'view');

    if (! $zbp->CheckRights('ModuleMng')) {
        $remove_props = array('Metas');
    } else {
        $remove_props = array();
    }

    $modType = GetVars('type');
    $systemMods = array();
    $userMods = array();
    $themeMods = array();
    $pluginMods = array();

    foreach ($zbp->modules as $module) {
        if ($module->SourceType == 'system') {
            $systemMods[] = $module;
        } elseif ($module->SourceType == 'user') {
            $userMods[] = $module;
        } elseif ($module->SourceType == 'theme') {
            $themeMods[] = $module;
        } else {
            $pluginMods[] = $module;
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
        case 'plugin':
            $modules = $pluginMods;
            break;
        default:
            $modules = $zbp->modules;
    }

    return array(
        'data' => array(
            'list' => ApiGetObjectArrayList($modules, array(), $remove_props),
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

    //传入的参数是$_Post['sidebar'] .. $_Post['sidebar9'],值为"模块filename名|模块2filename.."

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
        $data = ApiGetObjectArrayList($zbp->template->$sidebarName, array(), $remove_props);
    } else {
        // 列出所有侧栏列表
        for ($i = 1; $i <= 9; $i++) {
            $data['sidebar' . ($i == 1 ? '' : $i)] = array();
            $sidebarName = ($i == 1) ? 'sidebar' : 'sidebar' . $i;

            $data['sidebar' . ($i == 1 ? '' : $i)] = ApiGetObjectArrayList($zbp->template->$sidebarName, array(), $remove_props);
        }
    }

    return compact('data');
}
