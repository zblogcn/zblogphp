<?php
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

function api_category_get_function() {
	global $zbp;
	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	API::$IO->category = return_category($zbp->categories[$id]);
	API::$IO->end();
}
API::$Route->get('/category/', 'api_category_get_function');

function api_categories_get_function() {
	global $zbp;
	$ret = array();
	foreach ($zbp->categorysbyorder as $category) {
		if ($category->ParentID == 0)
			array_push($ret, return_category($category));
	}
	API::$IO->categories = $ret;
	API::$IO->end();

}
API::$Route->get('/categories/', 'api_categories_get_function');
