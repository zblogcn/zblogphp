<?php
function api_tag_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = return_tag($id);

	API::$IO->tag = $ret;
	API::$IO->end();
}

function return_tag($id) {
	global $zbp;

	$tag = $zbp->GetTagByID($id);
	$ret = $tag->GetData();
	$ret['Url'] = $tag->Url;

	API::$IO->formatObjectName($ret);
	return $ret;
}
API::$Route->get('/tag/', 'api_tag_get_function');
