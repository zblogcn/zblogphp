<?php
/**
 * api
 * @author zsx<zsx@zsxsoft.com>
 * @package api/route/article
 * @php >= 5.2
 */
/**
 * Format single article object
 * @param object $article
 * @return array          
 */
function return_article($id) {
	global $zbp;

	$article = $zbp->GetPostByID($id);
	$ret = $article->GetData();
	$ret['Url'] = $article->Url;
	API::$IO->formatObjectName($ret);

	return $ret;
}

/**
 * Get article
 */
function api_article_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = return_article($id);

	API::$IO->article = $ret;
	API::$IO->end();
}


API::$Route->get('/article/', 'api_article_get_function');


/**
 * Create article
 */
function api_article_create_function() {

}
API::$Route->post('/article/create/', 'api_article_create_function');

/**
 * Update article
 */
function api_article_update_function() {

}
API::$Route->post('/article/update/', 'api_article_update_function');

/**
 * Update article
 */
function api_article_delete_function() {

}
API::$Route->post('/article/delete/', 'api_article_delete_function');
