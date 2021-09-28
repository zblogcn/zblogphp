<?php

return array (
    'id' => 1,
    'name' => 'page',
    'classname' => 'Post',
    'template' => 'single',
    'list_template' => 'index',
    'category_template' => 'index',
    'tag_template' => 'index',
    'author_template' => 'index',
    'date_template' => 'index',
    'search_template' => 'search',
    'single_urlrule' => $GLOBALS['zbp']->option['ZC_PAGE_REGEX'],
    'list_urlrule' => '',
    'list_category_urlrule' => '',
    'list_author_urlrule' => '',
    'list_date_urlrule' => '',
    'list_tag_urlrule' => '',
    'search_urlrule' => '',
    'actions' => 
    array (
      'new' => 'PageNew',
      'edit' => 'PageEdt',
      'del' => 'PageDel',
      'post' => 'PagePst',
      'publish' => 'PagePub',
      'manage' => 'PageMng',
      'all' => 'PageAll',
      'view' => 'view',
      'search' => 'search',
    ),
    'routes' => 
    array (
    ),
  );
