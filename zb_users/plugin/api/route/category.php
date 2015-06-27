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
var_dump($category);exit;
	$ret = array();
	$ret['id'] = $category->ID;
	$ret['order']  = $category->Order;
	$ret['url'] = $category->Url;
	$ret['name'] = $category->Name;
	$ret['alias'] = $category->Alias;
	$ret['article_count'] = $category->Count;
	$ret['parent_id'] = $category->ParentID;
	$ret['root_id'] = $category->RootID;
	$ret['template'] = $category->Template;
	$ret['log_template'] = $category->LogTemplate;
	$ret['intro'] = $category->Intro;
	$ret['categories'] = array();

	foreach($category->SubCategorys as $subCategory) {
		array_push($ret['categories'], return_category($subCategory));
	}
	
	return $ret;
}
API::$Route->get('/category/', 'api_category_get_function');
