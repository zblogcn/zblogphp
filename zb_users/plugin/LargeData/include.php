<?php

//注册插件
RegisterPlugin("LargeData", "ActivePlugin_LargeData");

$table['Post2Tag'] = '%pre%post2tag';

$datainfo['Post2Tag'] = array(
    'ID'    => array('pt_ID', 'integer', '', 0),
    'TagID' => array('pt_TagID', 'integer', '', 0),
    'LogID' => array('pt_LogID', 'integer', '', 0),
);

function ActivePlugin_LargeData()
{
    global $zbp;
    if ($zbp->option['ZC_LARGE_DATA'] == true && $zbp->db->type == 'mysql') {
        Add_Filter_Plugin('Filter_Plugin_Misc_Begin', 'LargeData_Misc_Begin');
        Add_Filter_Plugin('Filter_Plugin_Zbp_Load', 'LargeData_Zbp_Begin');
        Add_Filter_Plugin('Filter_Plugin_LargeData_Article', 'LargeData_Article');
        Add_Filter_Plugin('Filter_Plugin_LargeData_Page', 'LargeData_Page');
        Add_Filter_Plugin('Filter_Plugin_LargeData_Comment', 'LargeData_Comment');
        Add_Filter_Plugin('Filter_Plugin_LargeData_CountTagArray', 'LargeData_CountTagArray');
        Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'LargeData_PostArticle_Succeed');
        Add_Filter_Plugin('Filter_Plugin_Post_Del', 'LargeData_Post_Del');
        Add_Filter_Plugin('Filter_Plugin_Tag_Del', 'LargeData_Tag_Del');
    }
}

function LargeData_PostArticle_Succeed(&$article)
{
    LargeData_Delete_Post2Tag_ByLogID($article->ID);
    $array_tag = LargeData_LoadTagsByIDString($article->Tag);
    LargeData_Insert_Post2Tag($article->ID, $array_tag);
}

function LargeData_Post_Del(&$post)
{
    LargeData_Delete_Post2Tag_ByLogID($post->ID);
}

function LargeData_Tag_Del(&$tag)
{
    LargeData_Delete_Post2Tag_ByTagID($tag->ID);
}

function LargeData_CountTagArray(&$array, &$plus, &$log_id)
{
    if ($plus > 0) {
        foreach ($array as $tag) {
            LargeData_Insert_Post2Tag($log_id, array($tag->ID));
        }
    } elseif ($plus < 0) {
        foreach ($array as $tag) {
            LargeData_Delete_Post2Tag($log_id, array($tag->ID));
        }
    }
}

function LargeData_CreateTable()
{
    global $zbp;
    if ($zbp->db->ExistTable($GLOBALS['table']['Post2Tag']) == false) {
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['Post2Tag'], $GLOBALS['datainfo']['Post2Tag']);
        $zbp->db->QueryMulti($s);
        $zbp->db->Query("ALTER TABLE " . $GLOBALS['table']['Post2Tag'] . " ADD INDEX  " . $zbp->db->dbpre . "pt_LD_2ID(pt_TagID,pt_LogID) ;");
        $zbp->db->Query("ALTER TABLE " . $GLOBALS['table']['Post2Tag'] . " ADD INDEX  " . $zbp->db->dbpre . "pt_LD_LogID(pt_LogID) ;");
    }
}

function LargeData_ConvertTable_Post2Tag()
{
    global $zbp;
    $zbp->db->Query("DELETE FROM " . $GLOBALS['table']['Post2Tag']);
    $a = $zbp->db->Query("Select log_ID,log_Tag FROM " . $GLOBALS['table']['Post']);

    foreach ($a as $array) {
        $log_id = reset($array);
        $log_tag = end($array);
        $array_tag = LargeData_LoadTagsByIDString($log_tag);
        LargeData_Insert_Post2Tag($log_id, $array_tag);
    }
}

function LargeData_LoadTagsByIDString($s)
{
    $s = trim($s);
    if ($s == '') {
        return array();
    }

    $s = str_replace('}{', '|', $s);
    $s = str_replace('{', '', $s);
    $s = str_replace('}', '', $s);
    $a = explode('|', $s);

    return $a;
}

function LargeData_Delete_Post2Tag_ByTagID($id)
{
    global $zbp;
    $s = "DELETE FROM " . $GLOBALS['table']['Post2Tag'] . " WHERE (pt_TagID = " . $id . ");";
    $zbp->db->Query($s);
}

function LargeData_Delete_Post2Tag_ByLogID($id)
{
    global $zbp;
    $s = "DELETE FROM " . $GLOBALS['table']['Post2Tag'] . " WHERE (pt_LogID = " . $id . ");";
    $zbp->db->Query($s);
}

function LargeData_Delete_Post2Tag($log_id, $array_tag)
{
    global $zbp;
    if (count($array_tag) == 0) {
        return;
    }

    foreach ($array_tag as $tag_id) {
        $s = "DELETE FROM " . $GLOBALS['table']['Post2Tag'] . " WHERE (pt_TagID = " . $tag_id . ") AND (pt_LogID = " . $log_id . ");";
        $zbp->db->Query($s);
    }
}

function LargeData_Insert_Post2Tag($log_id, $array_tag)
{
    global $zbp;
    if (count($array_tag) == 0) {
        return;
    }

    foreach ($array_tag as $tag_id) {
        $s = "INSERT INTO " . $GLOBALS['table']['Post2Tag'] . " (pt_TagID,pt_logID) VALUES (" . $tag_id . "," . $log_id . ");";
        $zbp->db->Query($s);
    }
}

function LargeData_Article(&$select, &$where, &$order, &$limit, &$option)
{
    global $zbp;
    $tag_id = null;
    foreach ($where as $k => $v) {
        if ($v[0] == 'search') {
            $s = end($v);
            $where = array(array('like', 'log_Title', $s . '%'));
            continue;
        }
    }
    foreach ($where as $k => $v) {
        if ($v[0] == 'LIKE' && $v[1] = 'log_Tag') {
            $tag_id = end($v);
            $tag_id = str_replace('{', '', $tag_id);
            $tag_id = str_replace('}', '', $tag_id);
            $tag_id = str_replace('%', '', $tag_id);
            $tag_id = trim($tag_id);
            continue;
        }
    }

    $w = $where;
    $w[] = array('=', 'log_Type', '0');
    if ($tag_id == null) {
        $s = $zbp->db->sql->Select($zbp->table['Post'], $zbp->datainfo['Post']['ID'][0], $w, $order, $limit, null);
    } else {
        $w = array();
        $w[] = array('CUSTOM', $zbp->table['Post'] . '.log_ID' . '=' . $zbp->table['Post2Tag'] . '.pt_LogID');
        $w[] = array('CUSTOM', $zbp->table['Post'] . '.log_Type = 0');
        $w[] = array('CUSTOM', $zbp->table['Post'] . '.log_Status = 0');
        $w[] = array('CUSTOM', $zbp->table['Post2Tag'] . '.pt_TagID = ' . $tag_id);
        $s = $zbp->db->sql->Select($zbp->table['Post'] . ',' . $zbp->table['Post2Tag'], $zbp->table['Post2Tag'] . '.' . $zbp->datainfo['Post2Tag']['LogID'][0], $w, $order, $limit, null);
    }
    $array = $zbp->db->Query($s);
    $a = array();
    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            $a[] = current($v);
        }
        if (array_key_exists('pagebar', $option)) {
            if (count($w) == 1) {
                $option['pagebar']->Count = $zbp->cache->all_article_nums;
            }
            if ($option['pagebar']->Count === null) {
                if ($tag_id == null) {
                    $s = $zbp->db->sql->Select($zbp->table['Post'], 'COUNT(' . $zbp->datainfo['Post']['ID'][0] . ')', $w, null, null, null);
                    $c = $zbp->db->Query($s);
                } else {
                    $s = $zbp->db->sql->Select($zbp->table['Post'] . ',' . $zbp->table['Post2Tag'], 'COUNT(' . $zbp->datainfo['Post']['ID'][0] . ')', $w, null, null, null);
                    $c = $zbp->db->Query($s);
                }
                $option['pagebar']->Count = (int) current($c[0]);
            }
        }
        $where = array(array('IN', 'log_ID', implode(',', $a)));
        $limit = null;
        $order = array('log_PostTime' => 'DESC');
    } else {
        $where = array(array('IN', 'log_ID', '0'));
        $limit = null;
        $order = null;
        if (array_key_exists('pagebar', $option)) {
            $option['pagebar']->Count = 0;
        }
    }
}

function LargeData_Page(&$select, &$where, &$order, &$limit, &$option)
{
    global $zbp;
    foreach ($where as $k => $v) {
        if ($v[0] == 'search') {
            $s = end($v);
            $where = array(array('like', 'log_Title', $s . '%'));
            continue;
        }
    }
    $w = $where;
    $w[] = array('=', 'log_Type', '1');
    $s = $zbp->db->sql->Select($zbp->table['Post'], $zbp->datainfo['Post']['ID'][0], $w, $order, $limit, null);
    $array = $zbp->db->Query($s);
    $a = array();
    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            $a[] = $v[$zbp->datainfo['Post']['ID'][0]];
        }
        if (array_key_exists('pagebar', $option)) {
            if (count($w) == 1) {
                $option['pagebar']->Count = $zbp->cache->all_page_nums;
            }
            if ($option['pagebar']->Count === null) {
                $s = $zbp->db->sql->Select($zbp->table['Post'], 'COUNT(' . $zbp->datainfo['Post']['ID'][0] . ')', $w, null, null, null);
                $c = $zbp->db->Query($s);
                $option['pagebar']->Count = (int) current($c[0]);
            }
        }
        $where = array(array('IN', 'log_ID', implode(',', $a)));
        $limit = null;
        $order = array('log_PostTime' => 'DESC');
    } else {
        $where = array(array('IN', 'log_ID', '0'));
        $limit = null;
        $order = null;
        if (array_key_exists('pagebar', $option)) {
            $option['pagebar']->Count = 0;
        }
    }
}

function LargeData_Comment(&$select, &$where, &$order, &$limit, &$option)
{
    global $zbp;
    foreach ($where as $k => $v) {
        if ($v[0] == 'search') {
            //$s=end($v);
            //$where=array(array('like','log_Title',$s . '%'));
            $option['pagebar']->Count = 0;
            continue;
        }
    }
    $w = $where;
    $s = $zbp->db->sql->Select($zbp->table['Comment'], $zbp->datainfo['Comment']['ID'][0], $w, $order, $limit, null);
    $array = $zbp->db->Query($s);
    $a = array();
    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            $a[] = $v[$zbp->datainfo['Comment']['ID'][0]];
        }
        if (array_key_exists('pagebar', $option)) {
            if ($option['pagebar']->Count === null) {
                $s = $zbp->db->sql->Select($zbp->table['Comment'], 'COUNT(' . $zbp->datainfo['Comment']['ID'][0] . ')', $w, null, null, null);
                $c = $zbp->db->Query($s);
                $option['pagebar']->Count = (int) current($c[0]);
            }
        }
        $where = array(array('IN', 'comm_ID', implode(',', $a)));
        $limit = null;
    } else {
        $where = array(array('IN', 'comm_ID', '0'));
        $limit = null;
        $order = null;
        if (array_key_exists('pagebar', $option)) {
            $option['pagebar']->Count = 0;
        }
    }
}

function LargeData_Zbp_Begin()
{
    global $zbp;
    $zbp->modulesbyfilename['archives']->NoRefresh = true;
    $zbp->modulesbyfilename['authors']->NoRefresh = true;
}

function LargeData_Misc_Begin($type)
{
    global $zbp;
    if ($type == 'statistic') {
        if (!$zbp->CheckRights('root')) {
            echo $zbp->ShowError(6, __FILE__, __LINE__);
            die();
        }
        LargeData_Misc_Statistic();
        die();
    }
}

function LargeData_Misc_Statistic()
{
    global $zbp;

    $r = null;

    CountNormalArticleNums();
    CountTopArticle(null, null);
    CountCommentNums(null, null);
    $all_comments = $zbp->cache->all_comment_nums;

    $xmlrpc_address = $zbp->host . 'zb_system/xml-rpc/';
    $current_member = $zbp->user->Name;
    $current_version = ZC_VERSION_FULL;

    $all_artiles = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Post'], 'Count(log_ID) AS num', array(array('=', 'log_Type', '0')), null, 1, null)), 'num');
    $all_pages = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Post'], 'Count(log_ID) AS num', array(array('=', 'log_Type', '1')), null, 1, null)), 'num');
    $all_categorys = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Category'], 'Count(*) AS num', null, null, 1, null)), 'num');
    $all_views = '不计算';
    $all_tags = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Tag'], 'Count(*) AS num', null, null, 1, null)), 'num');
    $all_members = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Member'], 'Count(*) AS num', null, null, 1, null)), 'num');

    $current_theme = '{$zbp->theme}';
    $current_style = '{$zbp->style}';
    $current_member = '{$zbp->user->Name}';
    $system_environment = '{$system_environment}';

    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_member']}" . '' . "</td><td class='td30'>{$current_member}</td><td class='td20'>{$zbp->lang['msg']['current_version']}</td><td class='td30'>{$current_version}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_artiles']}" . '' . "</td><td>{$all_artiles}</td><td>{$zbp->lang['msg']['all_categorys']}" . '' . "</td><td>{$all_categorys}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_pages']}" . '' . "</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}" . '' . "</td><td>{$all_tags}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_comments']}" . '' . "</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}" . '' . "</td><td>{$all_views}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_theme']}/{$zbp->lang['msg']['current_style']}</td><td>{$current_theme}/{$current_style}</td><td>{$zbp->lang['msg']['all_members']}" . '' . "</td><td>{$all_members}</td></tr>";
    $r .= "<tr><td class='td20'>{$zbp->lang['msg']['xmlrpc_address']}</td><td>{$xmlrpc_address}</td><td>{$zbp->lang['msg']['system_environment']}</td><td>{$system_environment}</td></tr>";
    $r .= "<script type=\"text/javascript\">$('#statistic').next('small').remove();$('#statistic').after('<small> 更新时间：" . date("c", $zbp->cache->reload_statistic_time) . "</small>');</script>";

    $zbp->cache->reload_statistic = $r;
    $zbp->cache->reload_statistic_time = time();
    $zbp->cache->system_environment = $system_environment;
    $zbp->cache->all_article_nums = $all_artiles;
    $zbp->cache->all_page_nums = $all_pages;

    $zbp->AddBuildModule('statistics', array($all_artiles, $all_pages, $all_categorys, $all_tags, $all_views, $all_comments));
    $zbp->BuildModule();
    $zbp->SaveCache();

    $r = str_replace('{#ZC_BLOG_HOST#}', $zbp->host, $r);
    $r = str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
    $r = str_replace('{$zbp->theme}', $zbp->theme, $r);
    $r = str_replace('{$zbp->style}', $zbp->style, $r);
    $r = str_replace('{$system_environment}', GetEnvironment(), $r);

    echo $r;
}
