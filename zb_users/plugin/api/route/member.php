<?php
function api_member_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = return_member($id);

	API::$IO->member = $ret;
	API::$IO->end();
}

function return_member($id) {
	global $zbp;

	$member = $zbp->GetMemberByID($id);
	$ret = $member->GetData();
	$ret['Url'] = $member->Url;
	unset($ret['Password']);
	unset($ret['Guid']);
	API::$IO->formatObjectName($ret);
	return $ret;
	
}
API::$Route->get('/member/', 'api_member_get_function');
