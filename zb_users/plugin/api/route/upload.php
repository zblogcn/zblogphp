<?php
function api_upload_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = return_upload($id);

	API::$IO->upload = $ret;
	API::$IO->end();
}

function return_upload($id) {
	global $zbp;

	$upload = $zbp->GetUploadByID($id);
	$ret = $upload->GetData();
	API::$IO->formatObjectName($ret);
	return $ret;
}
API::$Route->get('/upload/', 'api_upload_get_function');
