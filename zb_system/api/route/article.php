<?php
/**
 * api.
 *
 * @author zsx<zsx@zsxsoft.com>
 * @php >= 5.2
 */
/**
 * Format single article object.
 *
 * @param object $article
 *
 * @return array
 */
function return_article($id)
{
    global $zbp;

    $article = $zbp->GetPostByID($id);
    $ret = $article->GetData();
    $ret['Url'] = $article->Url;
    API::$IO->formatObjectName($ret);

    return $ret;
}

/**
 * Get article.
 */
function api_article_get_function()
{
    $id = (int) API::$IO->id;
    if ($id === 0) {
        API::$IO->end(3);
    }
    //
    $ret = return_article($id);

    API::$IO->article = $ret;
}

API::$Route->get('/article/', 'api_article_get_function');

/**
 * A function will run after PostArticle().
 *
 * @param Post $article
 */
function api_article_post_callback(&$article)
{
    $ret = return_article($article->ID);
    API::$IO->article = $ret;
}
/**
 * Create & Update article.
 */
function api_article_post_function()
{
    global $zbp;
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'api_article_post_callback');
    PostArticle();
    $zbp->BuildModule();
    $zbp->SaveCache();
}

/**
 * Create article.
 */
function api_article_create_function()
{
    $_POST['ID'] = 0;
    api_article_post_function();
}

API::$Route->post('/article/create/', 'api_article_create_function');

/**
 * Update article.
 */
function api_article_update_function()
{
    $id = (int) API::$IO->id;
    if ($id === 0) {
        API::$IO->end(3);
    }
    $_POST['ID'] = $id;
    api_article_post_function();
}
API::$Route->post('/article/update/', 'api_article_update_function');

/**
 * Update article.
 */
function api_article_delete_function()
{
    $ret = DelArticle();
    if ($ret !== true) {
        API::$IO->end(0);
    }
}
API::$Route->post('/article/delete/', 'api_article_delete_function');
