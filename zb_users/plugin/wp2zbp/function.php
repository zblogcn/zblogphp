<?php

function convert_article_table($_prefix)
{
    global $zbp;
    $list = array(
        "`ID`"                                  => "log_ID",
        "`post_title`"                          => "log_Title",
        "unix_timestamp(`post_date`)"           => "log_PostTime",
        "`post_excerpt`"                        => "log_Intro",
        "`post_content`"                        => "log_Content",
        "`post_name`"                           => "log_Alias",
        "`post_author`"                         => "log_AuthorID",
        '0'                                     => "log_CateID",
        'IF(`post_type` =  "post", 0, 1 )'      => "log_Type",
        "0"                                     => "log_ViewNums",
        "`comment_count`"                       => "log_CommNums",
        '0'                                     => "log_IsTop",
        'IF(`post_status` =  "publish", 0, 1 )' => "log_Status",
        '""'                                    => "log_Meta",
    );

    $ary1 = array();
    $ary2 = array();
    foreach ($list as $name => $value) {
        $ary1[] = $value;
        $ary2[] = $name . ' AS ' . $value;
    }

    $sql = build_sql('Post', $_prefix . 'posts', $ary1, $ary2, ' WHERE (((`post_type`="page") OR (`post_type`="post")) AND (`post_status`<>"auto-draft"))');

    $zbp->db->QueryMulit($sql);

    $array = $zbp->GetArticleList(null, null, null, null, null, false);
    foreach ($array as $a) {
        if ($a->Intro == '') {
            if (strpos($a->Content, '<!--more-->') !== false) {
                $a->Intro = GetValueInArray(explode('<!--more-->', $a->Content), 0);
            } else {
                if ($a->Intro == '') {
                    $a->Intro = SubStrUTF8($a->Content, $zbp->option['ZC_ARTICLE_EXCERPT_MAX']);
                    if (strpos($a->Intro, '<') !== false) {
                        $a->Intro = CloseTags($a->Intro);
                    }
                }
            }
        }
        $a->Save();
    }
}

function convert_comment_table($_prefix)
{
    global $zbp;
    $list = array(
        'comm_ID'         => '`comment_ID`',
        'comm_LogID'      => '`comment_post_ID`',
        'comm_IsChecking' => 'IF(`comment_approved` =  "1", 0, 1 )',
        'comm_RootID'     => 0,
        'comm_ParentID'   => '`comment_parent`',
        'comm_AuthorID'   => '`user_id`',
        'comm_Name'       => '`comment_author`',
        'comm_Content'    => '`comment_content`',
        'comm_Email'      => '`comment_author_email`',
        'comm_HomePage'   => '`comment_author_url`',
        'comm_PostTime'   => 'unix_timestamp(`comment_date`)',
        'comm_IP'         => '`comment_author_IP`',
        'comm_Agent'      => '`comment_agent`',
        'comm_Meta'       => '""',
    );

    $ary1 = array();
    $ary2 = array();
    foreach ($list as $name => $value) {
        $ary1[] = $name;
        $ary2[] = $value . ' AS ' . $name;
    }
    $sql = build_sql('Comment', $_prefix . 'comments', $ary1, $ary2);

    return $zbp->db->QueryMulit($sql);
}

function convert_attachment_table($_prefix)
{
    global $zbp;
    $list = array(
        'ul_ID'         => '`ID`',
        'ul_AuthorID'   => '`post_author`',
        'ul_Size'       => '0',
        'ul_Name'       => '`meta_value`',
        'ul_SourceName' => '`post_title`',
        'ul_MimeType'   => '`post_mime_type`',
        'ul_PostTime'   => 'unix_timestamp(`post_date`)',
        'ul_DownNums'   => 0,
        'ul_LogID'      => '`post_parent`',
        'ul_Intro'      => '""',
        'ul_Meta'       => '""',
    );

    $ary1 = array();
    $ary2 = array();
    foreach ($list as $name => $value) {
        $ary1[] = $name;
        $ary2[] = $value . ' AS ' . $name;
    }
    $sql = build_sql('Upload',
    $_prefix . 'posts' . '`,`' . $_prefix . 'postmeta',
    $ary1,
    $ary2,
    ' WHERE (`' . $_prefix . 'posts`.`ID`=`' . $_prefix . 'postmeta`.`post_id` AND `' . $_prefix . 'posts`.`post_type`="attachment" AND `' . $_prefix . 'postmeta`.`meta_key`="_wp_attached_file")'
    );
    //die($sql);
    return $zbp->db->QueryMulit($sql);
}

function convert_category_table($_prefix)
{
    global $zbp;
    $list = array(
        'cate_ID'          => '`term_id`',
        'cate_Name'        => '`name`',
        'cate_Order'       => '0',
        'cate_Count'       => '0',
        'cate_Alias'       => '`slug`',
        'cate_Intro'       => '""',
        'cate_RootID'      => '0',
        'cate_ParentID'    => '0',
        'cate_Template'    => '""',
        'cate_LogTemplate' => '""',
        'cate_Meta'        => '""',
    );

    $ary1 = array();
    $ary2 = array();
    foreach ($list as $name => $value) {
        $ary1[] = $name;
        $ary2[] = $value . ' AS ' . $name;
    }
    $sql = build_sql('Category', $_prefix . 'terms', $ary1, $ary2, ' WHERE `term_id` IN ( SELECT `term_id` FROM `' . $_prefix . 'term_taxonomy` WHERE `taxonomy`="category")');
    //die($sql);
    return $zbp->db->QueryMulit($sql);
}

function convert_tag_table($_prefix)
{
    global $zbp;
    $list = array(
        'tag_ID'       => '`term_id`',
        'tag_Name'     => '`name`',
        'tag_Order'    => '0',
        'tag_Count'    => '0',
        'tag_Alias'    => '`slug`',
        'tag_Intro'    => '""',
        'tag_Template' => '""',
        'tag_Meta'     => '""',
    );

    $ary1 = array();
    $ary2 = array();
    foreach ($list as $name => $value) {
        $ary1[] = $name;
        $ary2[] = $value . ' AS ' . $name;
    }
    $sql = build_sql('Tag', $_prefix . 'terms', $ary1, $ary2, ' WHERE `term_id` IN ( SELECT `term_id` FROM `' . $_prefix . 'term_taxonomy` WHERE `taxonomy`="post_tag")');
    //die($sql);
    return $zbp->db->QueryMulit($sql);
}

function convert_user_table($_prefix)
{
    global $zbp;

    $sql = $zbp->db->sql->Select(
        $_prefix . 'users' . ',' . $_prefix . 'usermeta',
        array('*'),
        array(
            array('CUSTOM', $_prefix . 'users.ID=' . $_prefix . 'usermeta.user_id'),
            array('CUSTOM', $_prefix . 'usermeta.meta_key="wp_user_level"'),
            ),
        '',
        '',
        ''
    );

    $array = $zbp->db->Query($sql);

    $zbp->db->Query('TRUNCATE `' . $zbp->table['Member'] . '`;');

    $isadmin = false;

    foreach ($array as $key => $value) {
        $amem = array();
        $guid = GetGuid();

        $amem['mem_ID'] = $value['ID'];
        $amem['mem_Guid'] = $guid;
        $amem['mem_Name'] = $value['user_login'];
        $amem['mem_Alias'] = $value['user_nicename'];
        $amem['mem_Email'] = $value['user_email'];
        $amem['mem_HomePage'] = $value['user_url'];
        $amem['mem_Password'] = Member::GetPassWordByGuid(GetGuid(), $guid);
        $amem['mem_PostTime'] = strtotime($value['user_registered']);
        $amem['mem_Level'] = 5;
        if ($value['meta_value'] == 10) {
            $amem['mem_Level'] = 1;
        }
        if ($value['meta_value'] == 7) {
            $amem['mem_Level'] = 2;
        }
        if ($value['meta_value'] == 2) {
            $amem['mem_Level'] = 3;
        }
        if ($value['meta_value'] == 1) {
            $amem['mem_Level'] = 4;
        }
        if ($value['meta_value'] == 0) {
            $amem['mem_Level'] = 5;
        }

        if ($isadmin == false && $amem['mem_Level'] == 1) {
            $amem['mem_Name'] = $zbp->user->Name;
            $amem['mem_Guid'] = $zbp->user->Guid;
            $amem['mem_Password'] = $zbp->user->Password;
            $isadmin = true;
        }

        $zbp->db->Query($zbp->db->sql->Insert($zbp->table['Member'], $amem));
    }
}

function upgrade_comment_id()
{
    global $zbp;
    $comm_list = $zbp->GetCommentList();
    foreach ($comm_list as $o) {
        if ($o->ParentID == 0) {
            continue;
        }
        $rootid = find_comment_rootid($o->ParentID);
        $o->RootID = $rootid;
        $o->Save();
        echo '<p>已转换评论ID：' . $o->ID . '</p>';
    }
}

function find_comment_rootid($id)
{
    $comment = new Comment();
    $comment->LoadInfoByID($id);
    if ($comment->ParentID == 0) {
        return $id;
    } else {
        return find_comment_rootid($comment->ParentID);
    }
}

function upgrade_category_and_tag_count($_prefix)
{
    global $zbp;

    $sql = $zbp->db->sql->Select(
        $_prefix . 'term_relationships' . ',' . $_prefix . 'term_taxonomy',
        array('*'),
        array(
            array('CUSTOM', $_prefix . 'term_relationships.term_taxonomy_id=' . $_prefix . 'term_taxonomy.term_taxonomy_id'),
            array('CUSTOM', $_prefix . 'term_taxonomy.taxonomy="category"'),
            ),
        '',
        '',
        ''
    );

    $array = $zbp->db->Query($sql);
    foreach ($array as $key => $value) {
        $zbp->db->Query($zbp->db->sql->Update(
                $zbp->table['Post'],
                array('log_CateID' => $value['term_id']),
                array(array('=', 'log_ID', $value['object_id']))
            )
        );
    }

    $sql = $zbp->db->sql->Select(
        $_prefix . 'term_relationships' . ',' . $_prefix . 'term_taxonomy',
        array('*'),
        array(
            array('CUSTOM', $_prefix . 'term_relationships.term_taxonomy_id=' . $_prefix . 'term_taxonomy.term_taxonomy_id'),
            array('CUSTOM', $_prefix . 'term_taxonomy.taxonomy="post_tag"'),
            ),
        '',
        '',
        ''
    );

    $array = $zbp->db->Query($sql);
    $array2 = array();
    foreach ($array as $key => $value) {
        if (isset($array2[$value['object_id']])) {
            $array2[$value['object_id']] .= '{' . $value['term_id'] . '}';
        } else {
            $array2[$value['object_id']] = '{' . $value['term_id'] . '}';
        }
    }

    foreach ($array2 as $key => $value) {
        $zbp->db->Query($zbp->db->sql->Update(
                $zbp->table['Post'],
                array('log_Tag' => $value),
                array(array('=', 'log_ID', $key))
            )
        );
    }

    $cate_list = $zbp->GetCategoryList();
    foreach ($cate_list as $o) {
        $sql = 'SELECT COUNT(log_ID) AS `c` FROM `' . $zbp->db->dbpre . 'post` WHERE `log_CateID` = ' . $o->ID;
        $result = $zbp->db->Query($sql);
        if (count($result) > 0) {
            $o->Count = $result[0]['c'];
        }

        $o->Save();
        echo '<p>分类ID=' . $o->ID . ' 计数=' . $o->Count . '</p>';
    }
}

function upgrade_user_rebuild()
{
    global $zbp;
    $user_list = $zbp->GetMemberList();
    foreach ($user_list as $o) {
        $sql = 'SELECT COUNT(log_ID) AS `c` FROM `' . $zbp->db->dbpre . 'post` WHERE `log_Type` = 1 AND `log_AuthorID` = ' . $o->ID;
        $result = $zbp->db->Query($sql);
        if (count($result) > 0) {
            $o->Articles = $result[0]['c'];
        }

        $sql = 'SELECT COUNT(log_ID) AS `c` FROM `' . $zbp->db->dbpre . 'post` WHERE `log_Type` = 0 AND `log_AuthorID` = ' . $o->ID;
        $result = $zbp->db->Query($sql);
        if (count($result) > 0) {
            $o->Pages = $result[0]['c'];
        }

        $o->Save();
        echo '<p>用户ID=' . $o->ID . ' 文章=' . $o->Articles . ' 页面=' . $o->Pages . '</p>';
    }
}

function upgrade_tag_rebuild()
{
    global $zbp;
    $tag_list = $zbp->GetTagList();
    foreach ($tag_list as $o) {
        $intro_array = explode(',', $o->Intro);
        $o->Count = count($intro_array) - 2;
        $sql = 'UPDATE `' . $zbp->db->dbpre . 'post` SET log_Tag = concat(log_Tag, "{' . $o->ID . '}") WHERE log_ID in(0' . $o->Intro . '0)';
        $zbp->db->Update($sql);
        $o->Intro = '';
        $o->Save();
        echo '<p>Tag ID=' . $o->ID . ' Count=' . $o->Count . '</p>';
    }
}

function build_sql($zbp_field, $em_table, $array4zbp, $array4em, $where = '')
{
    global $zbp;
    $table = str_replace('%pre%', $zbp->db->dbpre, $GLOBALS['table'][$zbp_field]);
    $sql = 'TRUNCATE `' . $table . '`; ';
    $sql .= 'INSERT INTO ' . $table;
    $sql .= ' (' . implode(',', $array4zbp) . ') ';
    $sql .= 'SELECT ' . implode(',', $array4em) . ' FROM `' . $em_table . '` ' . $where . ';';

    return $sql;
}

function finish_convert()
{
    global $zbp;
    echo '<p>恭喜您，数据转移成功！</p>';
    echo '<p>转移完成后，请停用并删除此插件，否则可能会导致未知的安全问题。</p>';
    echo '<p>转换后的管理员账号和密码就是当前系统的管理员账号和密码。</p>';
    echo '<p>除了管理员以外，用户密码已经被重置为随机了，管理员可以再重置的。</p>';
    echo '<p>现在，让我们畅游Z-Blog PHP吧！</p>';
    echo '<p>&nbsp;</p>';
    echo '<p>一些链接：'; //<a class="href-ajax" href="convert.php?func=drop_emlog&prefix='. htmlspecialchars(GetVars('prefix', 'GET')). '">删除emlog数据表</a>';
    echo '&nbsp;&nbsp;<a href="../../../zb_system/cmd.php?act=PluginDis&name=em2zbp&token=' . $zbp->GetToken() . '">停用本插件</a>';
    echo '&nbsp;&nbsp;<a href="../AppCentre/main.php">去应用中心下载最新应用</a>';
    echo '&nbsp;&nbsp;<a href="../../../zb_system/cmd.php?act=ArticleEdt">写一篇新的文章</a></p>';
}

function drop_emlog()
{
    global $zbp;
    $emlist = array(
        'emlog_attachment',
        'emlog_blog',
        'emlog_comment',
        'emlog_link',
        'emlog_navi',
        'emlog_options',
        'emlog_reply',
        'emlog_sort',
        'emlog_tag',
        'emlog_twitter',
        'emlog_user',
    );
    $sql = '';
    $prefix = GetVars('prefix', 'GET');
    for ($i = 0; $i < count($emlist); $i++) {
        $zbp->db->Query('DROP TABLE IF EXISTS`' . str_replace('emlog_', $prefix, $emlist[$i]) . '`;');
    }

    echo 'OK';
}
