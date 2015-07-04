<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/route/category
 * @php >= 5.2
 */
/**
 * Format single category object
 * @param object $category
 * @return array
 */
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

/**
 * Get category
 */
function api_category_get_function() {
	global $zbp;
	$id = (int)API::$IO->id;
	$name = API::$IO->name;
	if ($id === 0 && $name == "") API::$IO->end(3);

	if ($id != 0) {
		API::$IO->category = return_category($zbp->categories[$id]);
	} else if ($name != "") {
		API::$IO->category = return_category($zbp->GetCategoryByName($name));
	}


}
API::$Route->get('/category/', 'api_category_get_function');

/**
 * Get categories
 */
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

/**
 * Create category
 */
function api_category_create_function() {

}
API::$Route->post('/category/create/', 'api_category_create_function');

/**
 * Update category
 */
function api_category_update_function() {

}
API::$Route->post('/category/update/', 'api_category_update_function');

/**
 * Update category
 */
function api_category_delete_function() {

}
API::$Route->post('/category/delete/', 'api_category_delete_function');
