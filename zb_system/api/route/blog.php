<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
function api_route_index_function()
{
    global $zbp;
    API::$IO->bloghost = $zbp->host;
    if ($zbp->CheckRights('root')) {
        API::$IO->blogpath = $zbp->path;
        API::$IO->environment = array(
            'php'    => PHP_SYSTEM,
            'x64'    => IS_X64,
            'server' => PHP_SERVER,
            'engine' => PHP_ENGINE,
            'str'    => GetEnvironment(),
        );
        API::$IO->option = $zbp->option;
    }
}

function api_route_login_function()
{
    global $zbp;
    $originalString = trim(GetVars('HTTP_AUTHORIZATION', 'SERVER'));
    $originalArray = explode(' ', $originalString);
    if (strtoupper($originalArray[0]) !== "BASIC" || count($originalArray) !== 2) {
        return false;
    }
    $authString = base64_decode($originalArray[1]);
    $authArray = explode(':', $authString);
    if (count($authArray) !== 2) {
        return false;
    }

    $key = $authArray[0];
    $secret = $authArray[1];
    API::$User->login($key, $secret);
}

function api_route_global_function()
{
    global $zbp;
    API::$IO->version = $zbp->version;
}

API::$Route->route("/*/", 'api_route_login_function');
API::$Route->route("/*/", 'api_route_global_function');
API::$Route->route("//", 'api_route_index_function');
