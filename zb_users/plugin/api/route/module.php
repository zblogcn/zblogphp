<?php
function api_module_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = return_module($id);

	API::$IO->module = $ret;
	API::$IO->end();
}

function return_module($id) {
	global $zbp;

	$module = $zbp->GetModuleByID($id);
	$ret = $module->GetData();
	API::$IO->formatObjectName($ret);
	return $ret;
	
}
API::$Route->get('/module/', 'api_module_get_function');
