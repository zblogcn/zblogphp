<?php
include dirname(__FILE__) . '/api.php';
RegisterPlugin("api", "ActivePlugin_api");
// 首先，我们需要在系统里加载api
// 然后剩下的再说
// $apiRealRouteUrl = "";

function api_index_begin() {

    global $apiRealRouteUrl;
    global $zbp;
    if ($apiRealRouteUrl == "") return false;
    $requestMethod = strtoupper(GetVars('REQUEST_METHOD', 'SERVER'));
    //API::$Route::$debug = true;
    API::$Route->scanRoute($requestMethod, $apiRealRouteUrl);
    API::$IO->end(-1);

}

function api_zbp_load_pre() {
    // Check URL first
    global $bloghost;
    global $apiRealRouteUrl;
    global $zbp;

    $requestUri = str_replace('index.php?', '', GetVars('HTTP_HOST', 'SERVER') . GetVars('REQUEST_URI', 'SERVER') . '/');
    $removedHttpHost = preg_replace('/^http.+\/\//', '', $bloghost) . 'api/';
    if (false === strpos($requestUri, $removedHttpHost)) {
        $apiRealRouteUrl = "";

        return false;
    }
    $apiRealRouteUrl = str_replace($removedHttpHost, '', $requestUri);

    // Now we know that this is API Request, so remove Cookie.
    // The verification shoule be in API Route.
    $_COOKIE['username'] = "";
    $_COOKIE['password'] = "";

    API::init();

}
