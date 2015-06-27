<?php
function api_category_get_function() {
	global $zbp;
	$id = (int)API::$IO->id;
	//if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = array();
	if ($id === 0) {
		foreach ($zbp->categorysbyorder as $category) {
			if ($category->ParentID == 0)
				array_push($ret, return_category($category));
		}
	} else {
		array_push($ret, return_category($zbp->categories[$id]));
	}

	API::$IO->categories = $ret;
	API::$IO->end();
}

function return_category($category) {
	$ret = $category->GetData();
	$ret['Url'] = $category->Url;
	$ret['categories'] = array();
	API::$IO->formatObjectName($ret);
	foreach($category->SubCategorys as $subCategory) {
		array_push($ret['categories'], return_category($subCategory));
	}
	
	return $ret;
}
API::$Route->get('/category/', 'api_category_get_function');
