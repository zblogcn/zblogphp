<?php

function zbp_admin2_SiteInfo()
{
    global $zbp;
    $data = zbp_admin2_statistic_data();
    foreach ($data as $item => $content) {
        $zbp->template_admin->SetTags($item, $content);
    }

    $rlt = $zbp->template_admin->Output('SiteInfo');

    return $rlt;
}

function zbp_admin2_ArticleMng()
{
    global $zbp;

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng{&status=%status%}{&istop=%istop%}{&category=%category%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = 0 == (int) GetVars('page', 'GET') ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;

    $p->UrlRule->Rules['{%category%}'] = GetVars('category');
    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);
    $p->UrlRule->Rules['{%status%}'] = GetVars('status');
    $p->UrlRule->Rules['{%istop%}'] = (bool) GetVars('istop');
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = [];
    $w[] = ['=', 'log_Type', 0];

    if (!$zbp->CheckRights('ArticleAll')) {
        $w[] = ['=', 'log_AuthorID', $zbp->user->ID];
    }
    if (GetVars('search')) {
        $w[] = ['search', 'log_Content', 'log_Intro', 'log_Title', $search];
    }
    if (GetVars('istop')) {
        $w[] = ['<>', 'log_Istop', '0'];
    }
    if (null !== GetVars('status') && '' !== GetVars('status')) {
        $w[] = ['=', 'log_Status', (int) GetVars('status')];
    }
    if (GetVars('category')) {
        $w[] = ['=', 'log_CateID', GetVars('category')];
    }

    $s = '';

    if ('id_desc' == $order_get) {
        $or = ['log_ID' => 'DESC'];
    } elseif ('id_asc' == $order_get) {
        $or = ['log_ID' => 'ASC'];
    } elseif ('cateid_desc' == $order_get) {
        $or = ['log_CateID' => 'DESC'];
    } elseif ('cateid_asc' == $order_get) {
        $or = ['log_CateID' => 'ASC'];
    } elseif ('authorid_desc' == $order_get) {
        $or = ['log_AuthorID' => 'DESC'];
    } elseif ('authorid_asc' == $order_get) {
        $or = ['log_AuthorID' => 'ASC'];
    } elseif ('posttime_desc' == $order_get) {
        $or = ['log_PostTime' => 'DESC'];
    } elseif ('posttime_asc' == $order_get) {
        $or = ['log_PostTime' => 'ASC'];
    } elseif ('updatetime_desc' == $order_get) {
        $or = ['log_UpdateTime' => 'DESC'];
    } elseif ('updatetime_asc' == $order_get) {
        $or = ['log_UpdateTime' => 'ASC'];
    } else {
        $or = [$zbp->manageorder => 'DESC'];
    }
    $l = [($p->PageNow - 1) * $p->PageCount, $p->PageCount];
    $op = ['pagebar' => $p];

    $type = null;

    // 1.7 新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_ArticleMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Article'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op, $type);
    }

    $articles = $zbp->GetPostList(
        $s,
        $w,
        $or,
        $l,
        $op,
    );

    // 上边查询会用总页数填充 page 字段
    $p->UrlRule->Rules['{%page%}'] = $p->PageNow;

    [$button_id_html] = MakeOrderButton('id', $p->UrlRule, $order_get);
    [$button_posttime_html] = MakeOrderButton('posttime', $p->UrlRule, $order_get);
    [$button_cateid_html] = MakeOrderButton('cateid', $p->UrlRule, $order_get);
    [$button_authorid_html] = MakeOrderButton('authorid', $p->UrlRule, $order_get);

    $zbp->template_admin->SetTags('button_id_html', $button_id_html);
    $zbp->template_admin->SetTags('button_posttime_html', $button_posttime_html);
    $zbp->template_admin->SetTags('button_cateid_html', $button_cateid_html);
    $zbp->template_admin->SetTags('button_authorid_html', $button_authorid_html);

    $zbp->template_admin->SetTags('articles', $articles);
    $zbp->template_admin->SetTags('p', $p);
    $zbp->template_admin->SetTags('post_type', ZC_POST_TYPE_ARTICLE);

    $rlt = $zbp->template_admin->Output('ArticleMng');

    return $rlt;
}

function zbp_admin2_PageMng()
{
    global $zbp;

    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=PageMng{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = 0 == (int) GetVars('page', 'GET') ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = [];
    $w[] = ['=', 'log_Type', 1];

    if (!$zbp->CheckRights('PageAll')) {
        $w[] = ['=', 'log_AuthorID', $zbp->user->ID];
    }

    $s = '';
    if ('id_desc' == $order_get) {
        $or = ['log_ID' => 'DESC'];
    } elseif ('id_asc' == $order_get) {
        $or = ['log_ID' => 'ASC'];
    } elseif ('cateid_desc' == $order_get) {
        $or = ['log_CateID' => 'DESC'];
    } elseif ('cateid_asc' == $order_get) {
        $or = ['log_CateID' => 'ASC'];
    } elseif ('authorid_desc' == $order_get) {
        $or = ['log_AuthorID' => 'DESC'];
    } elseif ('authorid_asc' == $order_get) {
        $or = ['log_AuthorID' => 'ASC'];
    } elseif ('posttime_desc' == $order_get) {
        $or = ['log_PostTime' => 'DESC'];
    } elseif ('posttime_asc' == $order_get) {
        $or = ['log_PostTime' => 'ASC'];
    } else {
        $or = ['log_PostTime' => 'DESC'];
    }

    $l = [($p->PageNow - 1) * $p->PageCount, $p->PageCount];
    $op = ['pagebar' => $p];

    // 1.7 新加入的接口
    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_PageMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Page'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $pages = $zbp->GetPostList(
        $s,
        $w,
        $or,
        $l,
        $op,
    );

    // 设置分页URL规则
    $p->UrlRule->Rules['{%page%}'] = $p->PageNow;

    // 生成排序按钮
    [$button_id_html] = MakeOrderButton('id', $p->UrlRule, $order_get);
    [$button_posttime_html] = MakeOrderButton('posttime', $p->UrlRule, $order_get);
    [$button_authorid_html] = MakeOrderButton('authorid', $p->UrlRule, $order_get);

    // 设置模板变量
    $zbp->template_admin->SetTags('button_id_html', $button_id_html);
    $zbp->template_admin->SetTags('button_posttime_html', $button_posttime_html);
    $zbp->template_admin->SetTags('button_authorid_html', $button_authorid_html);

    $zbp->template_admin->SetTags('pages', $pages);
    $zbp->template_admin->SetTags('p', $p);
    $zbp->template_admin->SetTags('post_type', ZC_POST_TYPE_PAGE);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('PageMng');

    return $rlt;
}

function zbp_admin2_CategoryMng()
{
    global $zbp;

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');
    $posttype = (int) GetVars('type');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=CategoryMng{&type=%type%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = 0 == (int) GetVars('page', 'GET') ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;

    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);
    $p->UrlRule->Rules['{%type%}'] = $posttype;

    $w = [];
    $w[] = ['=', 'cate_Type', $posttype];

    if ($search) {
        $w[] = ['search', 'cate_Name', 'cate_Alias', 'cate_Intro', $search];
    }

    $s = '';
    if ('id_desc' == $order_get) {
        $or = ['cate_ID' => 'DESC'];
    } elseif ('id_asc' == $order_get) {
        $or = ['cate_ID' => 'ASC'];
    } elseif ('order_desc' == $order_get) {
        $or = ['cate_Order' => 'DESC'];
    } elseif ('order_asc' == $order_get) {
        $or = ['cate_Order' => 'ASC'];
    } elseif ('name_desc' == $order_get) {
        $or = ['cate_Name' => 'DESC'];
    } elseif ('name_asc' == $order_get) {
        $or = ['cate_Name' => 'ASC'];
    } else {
        $or = ['cate_ID' => 'ASC'];
    }

    $l = [($p->PageNow - 1) * $p->PageCount, $p->PageCount];
    $op = ['pagebar' => $p];

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CategoryMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    if (!$zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY']) {
        $array = $zbp->GetCategoryList($s, $w, $or, $l, $op);
    } else {
        $array = $zbp->categoriesbyorder_type[$posttype];
    }

    // 生成排序按钮
    [$button_id_html] = MakeOrderButton('id', $p->UrlRule, $order_get);
    [$button_order_html] = MakeOrderButton('order', $p->UrlRule, $order_get);
    [$button_name_html] = MakeOrderButton('name', $p->UrlRule, $order_get);

    // 设置模板变量
    $zbp->template_admin->SetTags('button_id_html', $button_id_html);
    $zbp->template_admin->SetTags('button_order_html', $button_order_html);
    $zbp->template_admin->SetTags('button_name_html', $button_name_html);
    $zbp->template_admin->SetTags('categories', $array);
    $zbp->template_admin->SetTags('p', $p);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('CategoryMng');

    return $rlt;
}

function zbp_admin2_TagMng()
{
    global $zbp;

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');
    $posttype = (int) GetVars('type');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=TagMng{&type=%type%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = 0 == (int) GetVars('page', 'GET') ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;

    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);
    $p->UrlRule->Rules['{%type%}'] = $posttype;

    $w = [];
    $w[] = ['=', 'tag_Type', $posttype];

    if ($search) {
        $w[] = ['search', 'tag_Name', 'tag_Alias', 'tag_Intro', $search];
    }

    $s = '';
    if ('id_desc' == $order_get) {
        $or = ['tag_ID' => 'DESC'];
    } elseif ('id_asc' == $order_get) {
        $or = ['tag_ID' => 'ASC'];
    } elseif ('order_desc' == $order_get) {
        $or = ['tag_Order' => 'DESC'];
    } elseif ('order_asc' == $order_get) {
        $or = ['tag_Order' => 'ASC'];
    } elseif ('name_desc' == $order_get) {
        $or = ['tag_Name' => 'DESC'];
    } elseif ('name_asc' == $order_get) {
        $or = ['tag_Name' => 'ASC'];
    } else {
        $or = ['tag_ID' => 'ASC'];
    }

    $l = [($p->PageNow - 1) * $p->PageCount, $p->PageCount];
    $op = ['pagebar' => $p];

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_TagMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetTagList($s, $w, $or, $l, $op);

    // 生成排序按钮
    [$button_id_html] = MakeOrderButton('id', $p->UrlRule, $order_get);
    [$button_order_html] = MakeOrderButton('order', $p->UrlRule, $order_get);
    [$button_name_html] = MakeOrderButton('name', $p->UrlRule, $order_get);

    // 设置模板变量
    $zbp->template_admin->SetTags('button_id_html', $button_id_html);
    $zbp->template_admin->SetTags('button_order_html', $button_order_html);
    $zbp->template_admin->SetTags('button_name_html', $button_name_html);
    $zbp->template_admin->SetTags('tags', $array);
    $zbp->template_admin->SetTags('p', $p);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('TagMng');

    return $rlt;
}

function zbp_admin2_CommentMng()
{
    global $zbp;

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');
    $ischecking = (bool) GetVars('ischecking');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=CommentMng{&ischecking=%ischecking%}{&search=%search%}{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = 0 == (int) GetVars('page', 'GET') ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;

    $p->UrlRule->Rules['{%search%}'] = rawurlencode($search);
    $p->UrlRule->Rules['{%ischecking%}'] = (bool) GetVars('ischecking');
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = [];
    if (!$zbp->CheckRights('CommentAll')) {
        $w[] = ['=', 'comm_AuthorID', $zbp->user->ID];
    }
    if (GetVars('search')) {
        $w[] = ['search', 'comm_Content', 'comm_Name', GetVars('search')];
    }
    if (GetVars('id')) {
        $w[] = ['=', 'comm_ID', GetVars('id')];
    }

    $w[] = ['=', 'comm_Ischecking', (int) GetVars('ischecking')];

    $s = '';
    if ('id_desc' == $order_get) {
        $or = ['comm_ID' => 'DESC'];
    } elseif ('id_asc' == $order_get) {
        $or = ['comm_ID' => 'ASC'];
    } elseif ('posttime_desc' == $order_get) {
        $or = ['comm_PostTime' => 'DESC'];
    } elseif ('posttime_asc' == $order_get) {
        $or = ['comm_PostTime' => 'ASC'];
    } elseif ('logid_desc' == $order_get) {
        $or = ['comm_LogID' => 'DESC'];
    } elseif ('logid_asc' == $order_get) {
        $or = ['comm_LogID' => 'ASC'];
    } elseif ('authorid_desc' == $order_get) {
        $or = ['comm_AuthorID' => 'DESC'];
    } elseif ('authorid_asc' == $order_get) {
        $or = ['comm_AuthorID' => 'ASC'];
    } elseif ('parentid_desc' == $order_get) {
        $or = ['comm_ParentID' => 'DESC'];
    } elseif ('parentid_asc' == $order_get) {
        $or = ['comm_ParentID' => 'ASC'];
    } else {
        $or = ['comm_ID' => 'DESC'];
    }

    $l = [($p->PageNow - 1) * $p->PageCount, $p->PageCount];
    $op = ['pagebar' => $p];

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CommentMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Comment'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $comments = $zbp->GetCommentList(
        $s,
        $w,
        $or,
        $l,
        $op,
    );

    // 生成排序按钮
    [$button_id_html] = MakeOrderButton('id', $p->UrlRule, $order_get, 'desc');
    [$button_posttime_html] = MakeOrderButton('posttime', $p->UrlRule, $order_get);
    [$button_logid_html] = MakeOrderButton('logid', $p->UrlRule, $order_get);
    [$button_authorid_html] = MakeOrderButton('authorid', $p->UrlRule, $order_get);
    [$button_parentid_html] = MakeOrderButton('parentid', $p->UrlRule, $order_get);

    // 设置模板变量
    $zbp->template_admin->SetTags('button_authorid_html', $button_authorid_html);
    $zbp->template_admin->SetTags('button_id_html', $button_id_html);
    $zbp->template_admin->SetTags('button_logid_html', $button_logid_html);
    $zbp->template_admin->SetTags('button_parentid_html', $button_parentid_html);
    $zbp->template_admin->SetTags('button_posttime_html', $button_posttime_html);
    $zbp->template_admin->SetTags('comments', $comments);
    $zbp->template_admin->SetTags('ischecking', $ischecking);
    $zbp->template_admin->SetTags('p', $p);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('CommentMng');

    return $rlt;
}

function zbp_admin2_UploadMng()
{
    global $zbp;

    $w = [];
    if (!$zbp->CheckRights('UploadAll')) {
        $w[] = ['=', 'ul_AuthorID', $zbp->user->ID];
    }

    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=UploadMng{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = 0 == (int) GetVars('page', 'GET') ? 1 : (int) GetVars('page', 'GET');
    $p->PageBarCount = $zbp->pagebarcount;

    $s = '';
    if ('id_desc' == $order_get) {
        $or = ['ul_ID' => 'DESC'];
    } elseif ('id_asc' == $order_get) {
        $or = ['ul_ID' => 'ASC'];
    } elseif ('size_desc' == $order_get) {
        $or = ['ul_Size' => 'DESC'];
    } elseif ('size_asc' == $order_get) {
        $or = ['ul_Size' => 'ASC'];
    } elseif ('authorid_desc' == $order_get) {
        $or = ['ul_AuthorID' => 'DESC'];
    } elseif ('authorid_asc' == $order_get) {
        $or = ['ul_AuthorID' => 'ASC'];
    } elseif ('logid_desc' == $order_get) {
        $or = ['ul_LogID' => 'DESC'];
    } elseif ('logid_asc' == $order_get) {
        $or = ['ul_LogID' => 'ASC'];
    } elseif ('posttime_desc' == $order_get) {
        $or = ['ul_PostTime' => 'DESC'];
    } elseif ('posttime_asc' == $order_get) {
        $or = ['ul_PostTime' => 'ASC'];
    } else {
        $or = ['ul_PostTime' => 'DESC'];
    }

    $l = [($p->PageNow - 1) * $p->PageCount, $p->PageCount];
    $op = ['pagebar' => $p];

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_UploadMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $array = $zbp->GetUploadList($s, $w, $or, $l, $op);

    // 生成排序按钮
    [$button_id_html] = MakeOrderButton('id', $p->UrlRule, $order_get);
    [$button_size_html] = MakeOrderButton('size', $p->UrlRule, $order_get);
    [$button_authorid_html] = MakeOrderButton('authorid', $p->UrlRule, $order_get);
    [$button_logid_html] = MakeOrderButton('logid', $p->UrlRule, $order_get);
    [$button_posttime_html] = MakeOrderButton('posttime', $p->UrlRule, $order_get);

    // 设置模板变量
    $zbp->template_admin->SetTags('button_id_html', $button_id_html);
    $zbp->template_admin->SetTags('button_size_html', $button_size_html);
    $zbp->template_admin->SetTags('button_authorid_html', $button_authorid_html);
    $zbp->template_admin->SetTags('button_logid_html', $button_logid_html);
    $zbp->template_admin->SetTags('button_posttime_html', $button_posttime_html);
    $zbp->template_admin->SetTags('uploads', $array);
    $zbp->template_admin->SetTags('p', $p);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('UploadMng');

    return $rlt;
}

function zbp_admin2_MemberMng()
{
    global $zbp;

    $search = GetVars('search', '', '');
    $order_get = GetVars('order', 'GET');

    $p = new Pagebar('{%host%}zb_system/cmd.php?act=MemberMng{&order=%order%}{&page=%page%}', false);
    $p->PageCount = $zbp->managecount;
    $p->PageNow = 0 == (int) GetVars('page', 'GET') ? 1 : (int) GetVars('page', 'GET');
    if (GetVars('search') !== GetVars('search', 'GET')) {
        $p->PageNow = 1;
    }
    $p->PageBarCount = $zbp->pagebarcount;
    $p->UrlRule->Rules['{%order%}'] = $order_get;

    $w = [];
    if (!$zbp->CheckRights('MemberAll')) {
        $w[] = ['=', 'mem_ID', $zbp->user->ID];
    }
    if (GetVars('level')) {
        $w[] = ['=', 'mem_Level', GetVars('level')];
    }
    if (GetVars('search')) {
        $w[] = ['search', 'mem_Name', 'mem_Alias', 'mem_Email', GetVars('search')];
    }

    $s = '';
    if ('id_desc' == $order_get) {
        $or = ['mem_ID' => 'DESC'];
    } elseif ('id_asc' == $order_get) {
        $or = ['mem_ID' => 'ASC'];
    } elseif ('level_desc' == $order_get) {
        $or = ['mem_Level' => 'DESC'];
    } elseif ('level_asc' == $order_get) {
        $or = ['mem_Level' => 'ASC'];
    } elseif ('name_desc' == $order_get) {
        $or = ['mem_Name' => 'DESC'];
    } elseif ('name_asc' == $order_get) {
        $or = ['mem_Name' => 'ASC'];
    } elseif ('alias_desc' == $order_get) {
        $or = ['mem_Alias' => 'DESC'];
    } elseif ('alias_asc' == $order_get) {
        $or = ['mem_Alias' => 'ASC'];
    } else {
        $or = ['mem_ID' => 'ASC'];
    }

    $l = [($p->PageNow - 1) * $p->PageCount, $p->PageCount];
    $op = ['pagebar' => $p];

    foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_MemberMng_Core'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($s, $w, $or, $l, $op);
    }

    $members = $zbp->GetMemberList(
        '',
        $w,
        $or,
        $l,
        $op,
    );

    // 生成排序按钮
    [$button_id_html] = MakeOrderButton('id', $p->UrlRule, $order_get);
    [$button_level_html] = MakeOrderButton('level', $p->UrlRule, $order_get);
    [$button_name_html] = MakeOrderButton('name', $p->UrlRule, $order_get);
    [$button_alias_html] = MakeOrderButton('alias', $p->UrlRule, $order_get);

    // 设置模板变量
    $zbp->template_admin->SetTags('button_id_html', $button_id_html);
    $zbp->template_admin->SetTags('button_level_html', $button_level_html);
    $zbp->template_admin->SetTags('button_name_html', $button_name_html);
    $zbp->template_admin->SetTags('button_alias_html', $button_alias_html);
    $zbp->template_admin->SetTags('members', $members);
    $zbp->template_admin->SetTags('p', $p);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('MemberMng');

    return $rlt;
}

function zbp_admin2_ModuleMng()
{
    global $zbp;

    $sm = [];
    $um = [];
    $tm = [];
    $pm = [];

    foreach ($zbp->modules as $m) {
        if ('system' == $m->SourceType) {
            $sm[] = $m;
        } elseif ('user' == $m->SourceType) {
            $um[] = $m;
        } elseif ('theme' == $m->SourceType || 'themeinclude' == $m->SourceType) {
            // 判断模块归属当前主题
            if ('theme' == $m->Source || (substr($m->Source, (-1 - strlen($zbp->theme)))) == ('_' . $zbp->theme)) {
                $tm[] = $m;
            }
        } else {
            $pm[] = $m;
        }
    }

    $sideids = [1 => '', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9'];

    // 设置模板变量
    $zbp->template_admin->SetTags('sm', $sm);
    $zbp->template_admin->SetTags('um', $um);
    $zbp->template_admin->SetTags('tm', $tm);
    $zbp->template_admin->SetTags('pm', $pm);
    $zbp->template_admin->SetTags('sideids', $sideids);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('ModuleMng');

    return $rlt;
}

function zbp_admin2_ThemeMng()
{
    global $zbp;

    $allthemes = $zbp->LoadThemes();

    // 设置模板变量
    $zbp->template_admin->SetTags('allthemes', $allthemes);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('ThemeMng');

    return $rlt;
}

function zbp_admin2_PluginMng()
{
    global $zbp;

    $allplugins = $zbp->LoadPlugins();

    foreach ($allplugins as $key => $plugin) {
        if (in_array($plugin->id, ZBlogPHP::DISABLE_PLUGINS)) {
            unset($allplugins[$key]);
        }
    }

    $plugins = [];

    $app = new App();
    if (true == $app->LoadInfoByXml('theme', $zbp->theme)) {
        if ($app->HasPlugin()) {
            array_unshift($plugins, $app);
        }
    }

    $pl = $zbp->option['ZC_USING_PLUGIN_LIST'];
    $apl = explode('|', $pl);
    $apl = array_unique($apl);
    foreach ($apl as $name) {
        foreach ($allplugins as $plugin) {
            if ($name == $plugin->id) {
                $plugins[] = $plugin;
            }
        }
    }
    foreach ($allplugins as $plugin) {
        if (!$plugin->IsUsed()) {
            $plugins[] = $plugin;
        }
    }

    // 设置模板变量
    $zbp->template_admin->SetTags('plugins', $plugins);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('PluginMng');

    return $rlt;
}

function zbp_admin2_SettingMng()
{
    global $zbp;

    // 设置模板变量
    // $zbp->template_admin->SetTags('zbp', $zbp);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('SettingMng');

    return $rlt;
}

function zbp_admin2_CategoryEdt()
{
    global $zbp;

    $cateid = null;
    if (isset($_GET['id'])) {
        $cateid = (int) GetVars('id', 'GET');
    } else {
        $cateid = 0;
    }

    $type = (int) GetVars('type');

    $cate = $zbp->GetCategoryByID($cateid);
    if (0 == $cate->ID) {
        $cate->Type = (int) GetVars('type', 'GET');
    }

    $p = null;
    $p .= '<option value="0">' . $zbp->lang['msg']['none'] . '</option>';

    foreach ($zbp->categoriesbyorder_type[$cate->Type] as $k => $v) {
        if ($v->ID == $cate->ID) {
            continue;
        }
        if ($cate->ID > 0 && $v->RootID == $cate->ID) {
            continue;
        }
        if ($cate->RootID > 0) {
            if ($v->RootID == $cate->RootID && $v->Level >= $cate->Level) {
                continue;
            }
        }
        if ($v->Level < ($zbp->category_recursion_level - 1)) {
            $p .= '<option ' . ($v->ID == $cate->ParentID ? 'selected="selected"' : '') . ' value="' . $v->ID . '">' . $v->SymbolName . '</option>';
        }
    }

    // 设置模板变量
    $zbp->template_admin->SetTags('cate', $cate);
    $zbp->template_admin->SetTags('p', $p);
    $zbp->template_admin->SetTags('type', $type);

    // 渲染模板
    $rlt = $zbp->template_admin->Output('CategoryEdt');

    return $rlt;
}

function zbp_admin2_TagEdt()
{
    global $zbp;

    $tagid = null;
    if (isset($_GET['id'])) {
        $tagid = (int) GetVars('id', 'GET');
    } else {
        $tagid = 0;
    }

    $type = (int) GetVars('type');

    $tag = $zbp->GetTagByID($tagid);
    if (0 == $tag->ID) {
        $tag->Type = (int) GetVars('type', 'GET');
    }

    $zbp->template_admin->SetTags('tag', $tag);
    $zbp->template_admin->SetTags('type', $type);

    $rlt = $zbp->template_admin->Output('TagEdt');

    return $rlt;
}

function zbp_admin2_MemberEdt()
{
    global $zbp;

    $memberid = null;
    if (isset($_GET['id'])) {
        $memberid = (int) GetVars('id', 'GET');
    } else {
        $memberid = 0;
    }

    if (!$zbp->CheckRights('MemberAll')) {
        if ((int) $memberid != (int) $zbp->user->ID) {
            $zbp->ShowError(6, __FILE__, __LINE__);
        }
    }

    $member = $zbp->GetMemberByID($memberid);

    $zbp->template_admin->SetTags('member', $member);

    $rlt = $zbp->template_admin->Output('MemberEdt');

    return $rlt;
}

function zbp_admin2_ModuleEdt()
{
    global $zbp;

    $modid = null;
    $mod = null;

    if (isset($_GET['source'], $_GET['filename'])) {
        if (GetVars('source', 'GET') == 'themeinclude_' . $zbp->theme) {
            $mod = $zbp->GetModuleByFileName(GetVars('filename', 'GET'));
            if (0 == $mod->ID || 'themeinclude' != $mod->SourceType) {
                $zbp->ShowError(61);
            }
        } else {
            $zbp->ShowError(61);
        }
    } elseif (isset($_GET['filename'])) {
        $mod = $zbp->GetModuleByFileName(GetVars('filename', 'GET'));
        if (0 == $mod->ID || 'themeinclude' == $mod->SourceType) {
            $zbp->ShowError(69);
        }
    } else {
        if (isset($_GET['id'])) {
            $modid = (int) GetVars('id', 'GET');
        } else {
            $modid = 0;
        }
        $mod = $zbp->GetModuleByID($modid);
    }

    $zbp->template_admin->SetTags('mod', $mod);

    $rlt = $zbp->template_admin->Output('ModuleEdt');

    return $rlt;
}

function zbp_admin2_ArticleEdt(bool $ispage = false)
{
    global $zbp;

    $article = new Post();
    $article->AuthorID = $zbp->user->ID;

    if ($ispage) {
        $article->Type = ZC_POST_TYPE_PAGE;
    }

    if (!$zbp->CheckRights('ArticlePub')) {
        $article->Status = ZC_POST_STATUS_AUDITING;
    }

    if (isset($_GET['id']) && 0 != (int) $_GET['id']) {
        $article = $zbp->GetPostByID((int) GetVars('id', 'GET'));
    } else {
        // new Post
        $new_action = $ispage ? 'PageNew' : 'ArticleNew';
        if (!$zbp->CheckRights($new_action)) {
            $zbp->ShowError(6, __FILE__, __LINE__);

            exit();
        }
    }

    $requiredRight = $ispage ? 'PageAll' : 'ArticleAll';
    if (!$zbp->CheckRights($requiredRight) && $article->AuthorID != $zbp->user->ID) {
        $zbp->ShowError(6, __FILE__, __LINE__);

        exit();
    }

    if (!$ispage && $article->Intro) {
        if (false !== strpos($article->Content, '<!--more-->')) {
            $article->Intro = '';
            $article->Content = str_replace('<!--more-->', '<hr class="more" />', $article->Content);
        } elseif (false !== strpos($article->Intro, '<!--autointro-->')) {
            $article->Intro = '';
        }
    }

    $zbp->template_admin->SetTags('article', $article);
    $zbp->template_admin->SetTags('ispage', $ispage);

    $rlt = $zbp->template_admin->Output('ArticleEdt');

    return $rlt;
}

function zbp_admin2_RewriteMng()
{
    global $zbp, $action, $lang;

    $rlt = $zbp->template_admin->Output('RewriteMng');

    return $rlt;
}
