<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/route/tag
 * @php >= 5.2
 */
/**
 * Format single tag object
 * @param object $tag
 * @return array          
 */
function return_tag($id) {
	global $zbp;

	$tag = $zbp->GetTagByID($id);
	$ret = $tag->GetData();
	$ret['Url'] = $tag->Url;

	API::$IO->formatObjectName($ret);
	return $ret;
}

/**
 * Get tag
 */
function api_tag_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(3);
	//
	$ret = return_tag($id);

	API::$IO->tag = $ret;
	API::$IO->end();
}
API::$Route->get('/tag/', 'api_tag_get_function');

/**
 * Get tags
 */
function api_tags_get_function() {
	
}
API::$Route->get('/tags/', 'api_tags_get_function');

/**
 * Create tag
 */
function api_tag_create_function() {

}
API::$Route->post('/tag/create/', 'api_tag_create_function');

/**
 * Update tag
 */
function api_tag_update_function() {

}
API::$Route->post('/tag/update/', 'api_tag_update_function');

/**
 * Update tag
 */
function api_tag_delete_function() {

}
API::$Route->post('/tag/delete/', 'api_tag_delete_function');

