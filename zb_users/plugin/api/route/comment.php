<?php
function api_comment_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = return_comment($id);

	API::$IO->comment = $ret;
	API::$IO->end();
}

function return_comment($id) {
	global $zbp;

	$comment = $zbp->GetCommentByID($id);
	$ret = $comment->GetData();
	API::$IO->formatObjectName($ret);
	return $ret;
}
API::$Route->get('/comment/', 'api_comment_get_function');
