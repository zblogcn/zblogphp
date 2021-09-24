<?php

return array (
//  添加 页面页单页 动态路由
  'active_post_page_single' => 
  array (
    'posttype' => 1,
    'type' => 'active',
    'name' => 'post_page_single',
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
    'args_with' => 
    array (
    ),
    'to_permalink' => true,
  ),
//  添加 页面页单页 伪静路由
  'rewrite_post_page_single' => 
  array (
    'posttype' => 1,
    'type' => 'rewrite',
    'name' => 'post_page_single',
    'call' => 'ViewPost',
    'prefix' => '',
    'urlrule' => '',
    'args' => 
    array (
      0 => 'post@id',
      1 => 'post@alias',
    ),
    'args_with' => 
    array (
    ),
  ),
);
