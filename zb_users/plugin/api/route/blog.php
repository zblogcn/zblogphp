<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/route/global
 * @php >= 5.2
 */
function api_route_index_function() {
	global $zbp;
	API::$IO->version = $zbp->version;
	API::$IO->bloghost = $zbp->host;
	if ($zbp->CheckRights('root')) {
		API::$IO->blogpath = $zbp->path;
		API::$IO->environment = array(
			'php' => PHP_SYSTEM,
			'x64' => IS_X64,
			'server' => PHP_SERVER,
			'engine' => PHP_ENGINE,
			'str' => GetEnvironment()
		);
		API::$IO->option = $zbp->option;
	}


}
API::$Route->route("//", 'api_route_index_function');

function api_route_global_function() {
	global $zbp;
	API::$IO->version = $zbp->version;
}
API::$Route->route("/*/", 'api_route_global_function');