<?php

return array (
    'id' => 0,
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
    'single_urlrule' => $GLOBALS['zbp']->option['ZC_ARTICLE_REGEX'],
    'list_urlrule' => $GLOBALS['zbp']->option['ZC_INDEX_REGEX'],
    'list_category_urlrule' => $GLOBALS['zbp']->option['ZC_CATEGORY_REGEX'],
    'list_author_urlrule' => $GLOBALS['zbp']->option['ZC_AUTHOR_REGEX'],
    'list_date_urlrule' => $GLOBALS['zbp']->option['ZC_DATE_REGEX'],
    'list_tag_urlrule' => $GLOBALS['zbp']->option['ZC_TAGS_REGEX'],
    'search_urlrule' => $GLOBALS['zbp']->option['ZC_SEARCH_REGEX'],
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


