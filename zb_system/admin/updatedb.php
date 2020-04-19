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

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

function updatedb_162090to162300()
{
    global $zbp, $table, $datainfo;
    $t = &$table;
    $d = &$datainfo;
    $db = &$zbp->db;
    //ZBlogException::SuspendErrorHook();
    //补上1.5升级1.6.0时的2个字段
    $old = $db->sql->get()->select($t['Tag'])->column($d['Tag']['Type'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Type'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = $db->sql->get()->select($t['Category'])->column($d['Category']['Type'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Category']} ADD  {$d['Category']['Type'][0]} integer NOT NULL DEFAULT 0;");
    }

    $old = $db->sql->get()->select($t['Config'])->column($d['Config']['Key'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Config']} ADD {$d['Config']['Key'][0]} VARCHAR(255) NOT NULL DEFAULT '';");
    }
    $old = $db->sql->get()->select($t['Post'])->column($d['Post']['CreateTime'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = $db->sql->get()->select($t['Post'])->column($d['Post']['UpdateTime'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    if ($db->type == 'mysql') {
        @$db->Query("ALTER TABLE {$t['Post']} MODIFY  `{$d['Post']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
        @$db->Query("ALTER TABLE {$t['Tag']} MODIFY  `{$d['Tag']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
        @$db->Query("ALTER TABLE {$t['Category']} MODIFY  `{$d['Category']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
    }
    //ZBlogException::ResumeErrorHook();
    $zbp->option['ZC_LAST_VERSION'] = 162300;
    $zbp->SaveOption();
}

if ($zbp->version >= 162300 && (int) $zbp->option['ZC_LAST_VERSION'] < 162300) {
    updatedb_162090to162300();
}
die;
