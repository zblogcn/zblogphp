<?php
#注册插件
RegisterPlugin("forcehttps", "ActivePlugin_forcehttps");

function ActivePlugin_forcehttps() {
    if (!forcehttps_isHttps()) {
        $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        Redirect301($redirect_url);
    }
}

function InstallPlugin_forcehttps() {
}

function UninstallPlugin_forcehttps() {

}

function forcehttps_isHttps() {
    if (defined('HTTP_SCHEME')) {
        return HTTP_SCHEME == "https://";
    } else {
        if (array_key_exists('REQUEST_SCHEME', $array)) {
            if (strtolower($array['REQUEST_SCHEME']) == 'https') {
                return 'https://';
            }
        } elseif (array_key_exists('HTTPS', $array)) {
            if (strtolower($array['HTTPS']) == 'on') {
                return 'https://';
            }
        }

        return 'http://';
    }
}
