<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */

/**
 * Verify user.
 */
function api_app_get_function()
{
    global $zbp;
    if (!$zbp->CheckRights('root')) {
        API::$IO->end(6);
    }
    exit;
}
API::$Route->route('/app/*', 'api_app_get_function');

/**
 * Enable app
 * now plugin only.
 */
function api_app_enable_function()
{
    $id = API::$IO->id;
    if ($id == "") {
        API::$IO->end(3);
    }
    API::$IO->app = EnablePlugin($id);
}
API::$Route->post('/app/enable/', 'api_app_enable_function');

/**
 * Disable app.
 */
function api_app_disable_function()
{
    $id = API::$IO->id;
    if ($id == "") {
        API::$IO->end(3);
    }
    DisablePlugin($id);
    API::$IO->app = $id;
}
API::$Route->post('/app/disable/', 'api_app_disable_function');

/**
 * Modify app
 * For theme.
 */
function api_app_modify_function()
{
}
API::$Route->post('/app/modify/', 'api_app_modify_function');

/**
 * Delete app.
 */
function api_app_delete_function()
{
}
API::$Route->post('/app/delete/', 'api_app_delete_function');
