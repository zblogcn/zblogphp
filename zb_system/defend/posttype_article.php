<?php

return array (
    'name' => 'article',
    'classname' => 'Post',
    //  自己的模板 列表的模板 对应分类的模板 对应Tag的模板 对应作者的模板 日期列表的模板 搜索页的模板
    'template' => 'single',
    'list_template' => 'index',
    'category_template' => 'index',
    'tag_template' => 'index',
    'author_template' => 'index',
    'date_template' => 'index',
    'search_template' => 'search',
    //  自身规则 列表规则 分类列表规则 作者列表规则 日期列表规则 Tag列表规则 搜索列表规则
    'single_urlrule' => '',
    'list_urlrule' => '',
    'list_category_urlrule' => '',
    'list_author_urlrule' => '',
    'list_date_urlrule' => '',
    'list_tag_urlrule' => '',
    'search_urlrule' => '',
    //  权限名称分别是 新建 编辑 删除 提交 公开发布 管理 全部管理 查看 搜索
    'actions' => 
    array (
      'new' => 'ArticleNew',
      'edit' => 'ArticleEdt',
      'del' => 'ArticleDel',
      'post' => 'ArticlePst',
      'publish' => 'ArticlePub',
      'manage' => 'ArticleMng',
      'all' => 'ArticleAll',
      'view' => 'view',
      'search' => 'search',
    ),
    'routes' => 
    array (
    ),
  );


