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
    //ZBlogException::SuspendErrorHook();
    $old = @$zbp->db->Query($zbp->db->sql->Select($t['Config'], '*', array(array('=', $d['Config']['Key'][0], ''))));
    if (count($old) == 1 && $old[0] === false) {
        @$zbp->db->Query("ALTER TABLE {$table['Config']} ADD {$d['Config']['Key'][0]} VARCHAR(255) NOT NULL DEFAULT '';");
    }
    $old = @$zbp->db->Query('' . $zbp->db->sql->Select($t['Post'], '*', array(array('=', $d['Post']['CreateTime'][0], ''))) . '');
    if (count($old) == 1 && $old[0] === false) {
        @$zbp->db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = @$zbp->db->Query('' . $zbp->db->sql->Select($t['Post'], '*', array(array('=', $d['Post']['UpdateTime'][0], ''))) . '');
    if (count($old) == 1 && $old[0] === false) {
        @$zbp->db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    if ($zbp->db->type == 'mysql') {
        $zbp->db->Query("ALTER TABLE " . $t['Post'] . " MODIFY  `log_Type` INT(11) NOT NULL DEFAULT '0';");
        $zbp->db->Query("ALTER TABLE " . $t['Tag'] . " MODIFY  `tag_Type` INT(11) NOT NULL DEFAULT '0';");
        $zbp->db->Query("ALTER TABLE " . $t['Category'] . " MODIFY  `cate_Type` INT(11) NOT NULL DEFAULT '0';");
    }
    //ZBlogException::ResumeErrorHook();
    $zbp->option['ZC_LAST_VERSION'] = 162300;
    $zbp->SaveOption();
}

if ($zbp->version >= 162300 && (int) $zbp->option['ZC_LAST_VERSION'] < 162300) {
    updatedb_162090to162300();
}
die;
