<?php
function api_article_get_function() {

	$id = (int)API::$IO->id;
	if ($id === 0) API::$IO->end(API_ERROR::MISSING_PARAMATER);
	//
	$ret = return_article($id);

	API::$IO->article = $ret;
	API::$IO->end();
}

function return_article($id) {
	global $zbp;

	$article = $zbp->GetPostByID($id);
	$ret = $article->GetData();
	$ret['Url'] = $article->Url;
	API::$IO->formatObjectName($ret);

	return $ret;
}
API::$Route->get('/article/', 'api_article_get_function');
