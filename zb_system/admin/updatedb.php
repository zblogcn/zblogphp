<?php

/**
 * Z-Blog with PHP.
 *
 * @author
 * @copyright (C) ZBlogger社区
 *
 * @version 1.0 2020-03-26
 */
require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->Load();

if (!$zbp->CheckRights('root')) {
    $zbp->ShowError(6);
    die();
}

function updatedb()
{
    global $zbp, $table, $datainfo;
    $t = &$table;
    $d = &$datainfo;
    $db = &$zbp->db;
    //ZBlogException::SuspendErrorHook();
    //162090
    $old = @$db->sql->get()->select($t['Tag'])->column($d['Tag']['Type'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Type'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = @$db->sql->get()->select($t['Category'])->column($d['Category']['Type'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Category']} ADD  {$d['Category']['Type'][0]} integer NOT NULL DEFAULT 0;");
    }

    //162300
    $old = @$db->sql->get()->select($t['Config'])->column($d['Config']['Key'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Config']} ADD {$d['Config']['Key'][0]} VARCHAR(255) NOT NULL DEFAULT '';");
    }
    $old = @$db->sql->get()->select($t['Post'])->column($d['Post']['CreateTime'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = @$db->sql->get()->select($t['Post'])->column($d['Post']['UpdateTime'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    if ($zbp->option['ZC_LAST_VERSION'] < 162315 && $db->type == 'mysql') {
        @$db->Query("ALTER TABLE {$t['Post']} MODIFY  `{$d['Post']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
        @$db->Query("ALTER TABLE {$t['Tag']} MODIFY  `{$d['Tag']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
        @$db->Query("ALTER TABLE {$t['Category']} MODIFY  `{$d['Category']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
    }

    //162315
    $old = @$db->sql->get()->select($t['Tag'])->column($d['Tag']['Group'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Group'][0]} VARCHAR(255) NOT NULL DEFAULT '';");
    }
    $old = @$db->sql->get()->select($t['Category'])->column($d['Category']['Group'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Category']} ADD  {$d['Category']['Group'][0]} VARCHAR(255) NOT NULL DEFAULT '';");
    }

    //删除一个长期存在而又无用的索引
    @$db->sql->get()->drop($t['Post'])->index('%pre%log_VTSC')->query;

    //ZBlogException::ResumeErrorHook();
    $zbp->option['ZC_LAST_VERSION'] = 162315;
    $zbp->SaveOption();
}

if ($zbp->version >= 162315 && (int) $zbp->option['ZC_LAST_VERSION'] < 162315) {
    updatedb();
}
die;
