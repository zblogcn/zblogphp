<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-08-10
 */

/**
 * 获取应用信息接口.
 *
 * @return array
 */
function api_app_get()
{
    global $zbp;

    if (($type = GetVars('type')) === 'theme') {
        ApiCheckAuth(true, 'ThemeMng');
    } elseif ($type === 'plugin') {
        ApiCheckAuth(true, 'PluginMng');
    } else {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }

    $id = GetVars('id');
    $app = null;

    if (IsValidAppId($id, $type)) {
        $app = $zbp->LoadApp($type, $id);
    }

    if (is_null($app) || is_null($app->name)) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['97'],
        );
    }

    $app = array_merge(array('is_actived' => $zbp->CheckPlugin($id)), (array) $app);

    return array(
        'data' => array(
            'app' => (array) $app,
        ),
    );
}

/**
 * 启用插件接口.
 *
 * @return array
 */
function api_app_enable_plugin()
{
    global $zbp;

    ApiCheckAuth(true, 'PluginEnb');

    $id = (string) GetVars('id', 'POST');

    if (!IsValidAppId($id, 'plugin')) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['61'],
        );
    }

    try {
        EnablePlugin($id);
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }

    return array(
        'data' => array(
            'enabled' => true,
            'id' => $id
        ),
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 停用插件接口.
 *
 * @return array
 */
function api_app_disable_plugin()
{
    global $zbp;

    ApiCheckAuth(true, 'PluginDis');

    $id = (string) GetVars('id', 'POST');

    if (!IsValidAppId($id, 'plugin')) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['61'],
        );
    }

    $result = DisablePlugin($id);

    if ($result instanceof App) {
        return array(
            'data' => array(
                'id' => $id,
                'dependency' => $result->name . '(' . $result->id . ')',
            ),
            'message' => $GLOBALS['lang']['msg']['operation_failed'],
        );
    }

    $zbp->BuildModule();
    $zbp->SaveCache();

    return array(
        'data' => array(
            'disabled' => $result,
            'id' => $id
        ),
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 更换主题接口.
 *
 * @return array
 */
function api_app_set_theme()
{
    global $zbp;

    ApiCheckAuth(true, 'ThemeSet');

    $id = (string) GetVars('id', 'POST');

    if (!IsValidAppId($id, 'theme')) {
        return array(
            'code' => 404,
            'message' => $GLOBALS['lang']['error']['61'],
        );
    }

    try {
        SetTheme($id, (string) GetVars('style', 'POST'));
        $zbp->BuildModule();
        $zbp->SaveCache();
    } catch (Exception $e) {
        return array(
            'code' => 500,
            'message' => $GLOBALS['lang']['msg']['operation_failed'] . ' ' . $e->getMessage(),
        );
    }

    return array(
        'data' => array(
            'enabled' => !empty($id),
            'id' => $id
        ),
        'message' => $GLOBALS['lang']['msg']['operation_succeed'],
    );
}

/**
 * 获取所有应用接口.
 *
 * @return array
 */
function api_app_get_apps()
{
    global $zbp;

    ApiCheckAuth(true, 'ThemeMng');
    ApiCheckAuth(true, 'PluginMng');

    $apps = array();

    foreach (array_merge($zbp->LoadThemes(), $zbp->LoadPlugins()) as $app) {
        $new = new StdClass;
        $vars = get_class_vars('App');
        foreach ($vars as $key => $value) {
            $new->$key = $app->$key;
        }
        $new->is_actived = $zbp->CheckPlugin($new->id);
        $app = (array) $new;
        $apps[] = $app;
    }

    return array(
        'data' => array(
            'list' => $apps,
        ),
    );
}

/**
 * 获取所有插件接口.
 *
 * @return array
 */
function api_app_get_plugins()
{
    global $zbp;
    
    ApiCheckAuth(true, 'PluginMng');

    $apps = array();

    foreach ($zbp->LoadPlugins() as $app) {
        $new = new StdClass;
        $vars = get_class_vars('App');
        foreach ($vars as $key => $value) {
            $new->$key = $app->$key;
        }
        $new->is_actived = $zbp->CheckPlugin($new->id);
        $app = (array) $new;
        $apps[] = $app;
    }

    return array(
        'data' => array(
            'list' => $apps,
        ),
    );
}

/**
 * 获取所有主题接口.
 *
 * @return array
 */
function api_app_get_themes()
{
    global $zbp;

    ApiCheckAuth(true, 'ThemeMng');

    $apps = array();

    foreach ($zbp->LoadThemes() as $app) {
        $new = new StdClass;
        $vars = get_class_vars('App');
        foreach ($vars as $key => $value) {
            $new->$key = $app->$key;
        }
        $new->is_actived = $zbp->CheckPlugin($new->id);
        $app = (array) $new;
        $apps[] = $app;
    }

    return array(
        'data' => array(
            'list' => $apps,
        ),
    );
}

/**
 * 验证 App id 是否合法.
 *
 * @param string $id
 * @param string $type
 *
 * @return bool
 */
function IsValidAppId($id = '', $type = '')
{
    global $zbp;

    $apps = array();
    $appIds = array();

    switch ($type) {
        case 'plugin':
            $apps += $zbp->LoadPlugins();
            break;
        case 'theme':
            $apps += $zbp->LoadThemes();
            break;
        default:
            $apps += $zbp->LoadPlugins();
            $apps += $zbp->LoadThemes();
            break;
    }

    foreach ($apps as $app) {
        $appIds[] = $app->id;
    }

    if (in_array($id, $appIds)) {
        return true;
    }

    return false;
}
