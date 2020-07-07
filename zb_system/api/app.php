<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-07-02
 */

/**
 * 获取应用信息接口.
 */
function api_app_get()
{
    global $zbp;

    if (($type = GetVars('type')) === 'theme') {
        ApiCheckAuth(true, 'ThemeMng');
    } elseif ($type === 'plugin') {
        ApiCheckAuth(true, 'PluginMng');
    } else {
        ApiResponse(null, null, 404, $GLOBALS['lang']['error']['97']);
    }
    $id = GetVars('id');

    $app = $zbp->LoadApp($type, $id);
    $app = array_merge(array('is_actived' => $zbp->CheckPlugin($id)), (array) $app);

    ApiResponse(
        array(
            'app' => (array) $app
        )
    );
}

/**
 * 启用插件接口.
 */
function api_app_enable_plugin()
{
    global $zbp;

    ApiCheckAuth(true, 'PluginEnb');

    $id = EnablePlugin(GetVars('id', 'POST'));
    $zbp->BuildModule();
    $zbp->SaveCache();

    ApiResponse(
        array(
            'enabled' => !empty($id),
            'id' => $id
        ),
        null,
        200,
        $GLOBALS['lang']['msg']['operation_succeed']
    );
}

/**
 * 停用插件接口.
 */
function api_app_disable_plugin()
{
    global $zbp;

    ApiCheckAuth(true, 'PluginDis');

    $result = DisablePlugin($id = GetVars('id', 'POST'));
    $zbp->BuildModule();
    $zbp->SaveCache();

    ApiResponse(
        array(
            'disabled' => $result,
            'id' => $id
        ),
        null,
        200,
        $GLOBALS['lang']['msg']['operation_succeed']
    );
}

/**
 * 更换主题接口.
 */
function api_app_set_theme()
{
    global $zbp;

    ApiCheckAuth(true, 'ThemeSet');

    $id = SetTheme(GetVars('id', 'POST'), GetVars('style', 'POST'));
    $zbp->BuildModule();
    $zbp->SaveCache();

    ApiResponse(
        array(
            'enabled' => !empty($id),
            'id' => $id
        ),
        null,
        200,
        $GLOBALS['lang']['msg']['operation_succeed']
    );
}

/**
 * 获取所有应用接口.
 */
function api_app_get_apps()
{
    global $zbp;

    ApiCheckAuth(true, 'ThemeMng');
    ApiCheckAuth(true, 'PluginMng');

    $apps = array();

    foreach (array_merge($zbp->LoadThemes(), $zbp->LoadPlugins()) as $app) {
        $app = (array) $app;
        $app = array_merge(array('is_actived' => $zbp->CheckPlugin($app['id'])), (array) $app);
        $apps[] = $app;
    }

    ApiResponse(
        array(
            'list' => $apps
        )
    );
}

/**
 * 获取所有插件接口.
 */
function api_app_get_plugins()
{
    global $zbp;
    
    ApiCheckAuth(true, 'PluginMng');

    $apps = array();

    foreach ($zbp->LoadPlugins() as $app) {
        $app = (array) $app;
        $app = array_merge(array('is_actived' => $zbp->CheckPlugin($app['id'])), (array) $app);
        $apps[] = $app;
    }

    ApiResponse(
        array(
            'list' => $apps
        )
    );
}

/**
 * 获取所有主题接口.
 */
function api_app_get_themes()
{
    global $zbp;

    ApiCheckAuth(true, 'ThemeMng');

    $apps = array();

    foreach ($zbp->LoadThemes() as $app) {
        $app = (array) $app;
        $app = array_merge(array('is_actived' => $zbp->CheckPlugin($app['id'])), (array) $app);
        $apps[] = $app;
    }

    ApiResponse(
        array(
            'list' => $apps
        )
    );
}
