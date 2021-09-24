<?php

return array (
//  添加 文章页单页 动态路由
  'active_post_article_single' => 
  array (
    'posttype' => 0,
    'type' => 'active',
    'name' => 'post_article_single',
    'call' => 'ViewPost',
    'get' => 
    array (
      0 => 'id',
      1 => 'alias',
    ),
    'not_get' => 
    array (
      0 => 'cate',
      1 => 'auth',
      2 => 'tags',
      3 => 'date',
    ),
    'urlrule' => '',
    'to_permalink' => true,
  ),
//  添加 文章cate页列表(带参数) 动态路由
  'active_post_article_list_category' => 
  array (
    'posttype' => 0,
    'type' => 'active',
    'name' => 'post_article_list_category',
    'call' => 'ViewList',
    'urlrule' => '',
    'get' => 
    array (
      0 => 'page',
    ),
    'must_get' => 
    array (
      0 => 'cate',
    ),
    'not_get' => 
    array (
      0 => 'id',
      1 => 'alias',
    ),
  ),
//  添加 文章auth页列表(带参数) 动态路由
  'active_post_article_list_author' => 
  array (
    'posttype' => 0,
    'type' => 'active',
    'name' => 'post_article_list_author',
    'call' => 'ViewList',
    'urlrule' => '',
    'get' => 
    array (
      0 => 'page',
    ),
    'must_get' => 
    array (
      0 => 'auth',
    ),
    'not_get' => 
    array (
      0 => 'id',
      1 => 'alias',
    ),
  ),
//  添加 文章date页列表(带参数) 动态路由
  'active_post_article_list_date' => 
  array (
    'posttype' => 0,
    'type' => 'active',
    'name' => 'post_article_list_date',
    'call' => 'ViewList',
    'urlrule' => '',
    'get' => 
    array (
      0 => 'page',
    ),
    'must_get' => 
    array (
      0 => 'date',
    ),
    'not_get' => 
    array (
      0 => 'id',
      1 => 'alias',
    ),
  ),
//  添加 文章tags页列表(带参数) 动态路由
  'active_post_article_list_tag' => 
  array (
    'posttype' => 0,
    'type' => 'active',
    'name' => 'post_article_list_tag',
    'call' => 'ViewList',
    'urlrule' => '',
    'get' => 
    array (
      0 => 'page',
    ),
    'must_get' => 
    array (
      0 => 'tags',
    ),
    'not_get' => 
    array (
      0 => 'id',
      1 => 'alias',
    ),
  ),
//  添加 文章页单页 伪静路由
  'rewrite_post_article_single' => 
  array (
    'posttype' => 0,
    'type' => 'rewrite',
    'name' => 'post_article_single',
    'call' => 'ViewPost',
    'urlrule' => '',
    'args' => 
    array (
      0 => 'post@id',
      1 => 'post@alias',
    ),
  ),
//  添加 文章index列表 伪静路由
  'rewrite_post_article_list' => 
  array (
    'posttype' => 0,
    'type' => 'rewrite',
    'name' => 'post_article_list',
    'call' => 'ViewList',
    'urlrule' => '',
    'args' => 
    array (
      0 => 'page',
    ),
    'args_with' => 
    array (
    ),
    'abbr_url' => true,
  ),
//  添加 文章category列表 伪静路由
  'rewrite_post_article_list_category' => 
  array (
    'posttype' => 0,
    'type' => 'rewrite',
    'name' => 'post_article_list_category',
    'call' => 'ViewList',
    'urlrule' => '',
    'args' => 
    array (
      0 => 'cate@id',
      1 => 'cate@alias',
      2 => 'page',
    ),
  ),
//  添加 文章author列表 伪静路由
  'rewrite_post_article_list_author' => 
  array (
    'posttype' => 0,
    'type' => 'rewrite',
    'name' => 'post_article_list_author',
    'call' => 'ViewList',
    'urlrule' => '',
    'args' => 
    array (
      0 => 'auth@id',
      1 => 'auth@alias',
      2 => 'page',
    ),
  ),
//  添加 文章date列表 伪静路由
  'rewrite_post_article_list_date' => 
  array (
    'posttype' => 0,
    'type' => 'rewrite',
    'name' => 'post_article_list_date',
    'call' => 'ViewList',
    'urlrule' => '',
    'args' => 
    array (
      0 => 'date',
      1 => 'page',
    ),
  ),
//  添加 文章tag列表 伪静路由
  'rewrite_post_article_list_tag' => 
  array (
    'posttype' => 0,
    'type' => 'rewrite',
    'name' => 'post_article_list_tag',
    'call' => 'ViewList',
    'urlrule' => '',
    'args' => 
    array (
      0 => 'tags@id',
      1 => 'tags@alias',
      2 => 'page',
    ),
  ),
//  这是一个例子： 文章搜索的伪静路由的实现
/*
  'rewrite_post_article_search' => 
  array (
    'posttype' => 0,
    'type' => 'rewrite',
    'name' => 'post_article_search',
    'call' => 'ViewSearch',
    'prefix' => 'search',
    'urlrule' => '{%host%}{%q%}_{%page%}.html',
    'args' => 
    array (
      'q' => '[^\\/_]+',
      0 => 'page',
    ),
    'args_with' => 
    array (
    ),
    'request_method' => 
    array (
      0 => 'GET',
      1 => 'POST',
    ),
    'only_match_page' => false,
  ),
*/
//  添加 默认路由 （文章页列表的首页路由，只在没有开启伪静下启用）
  'default_post_article_list' => 
  array (
    'posttype' => 0,
    'type' => 'default',
    'name' => 'post_article_list',
    'call' => 'ViewList',
    'urlrule' => '',
    'get' => 
    array (
      0 => 'page',
    ),
    'not_get' => 
    array (
      0 => 'cate',
      1 => 'tags',
      2 => 'auth',
      3 => 'date',
      4 => 'id',
      5 => 'alias',
    ),
    'abbr_url' => true,
    'only_active' => true,
  ),
);
