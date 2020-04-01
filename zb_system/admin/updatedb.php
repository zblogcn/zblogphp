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

function updatedb_162090to169999()
{
    global $zbp, $table;
    ZBlogException::SuspendErrorHook();
    if ($zbp->db->type == 'mysql') {
        //$zbp->db->Query("ALTER TABLE " . $table['Tag'] . " ADD  `tag_Type` INT(11) NOT NULL DEFAULT '0';");
        //$zbp->db->Query("ALTER TABLE " . $table['Category'] . " ADD  `cate_Type` INT(11) NOT NULL DEFAULT '0';");
    } elseif ($zbp->db->type == 'sqlite') {
        //$zbp->db->Query("ALTER TABLE " . $table['Tag'] . " ADD  tag_Type integer NOT NULL DEFAULT 0;");
        //$zbp->db->Query("ALTER TABLE " . $table['Category'] . " ADD  cate_Type integer NOT NULL DEFAULT 0;");
    }
    if ($zbp->db->type == 'mysql') {
        $zbp->db->Query("ALTER TABLE " . $table['Post'] . " MODIFY  `log_Type` INT(11) NOT NULL DEFAULT '0';");
        $zbp->db->Query("ALTER TABLE " . $table['Tag'] . " MODIFY  `tag_Type` INT(11) NOT NULL DEFAULT '0';");
        $zbp->db->Query("ALTER TABLE " . $table['Category'] . " MODIFY  `cate_Type` INT(11) NOT NULL DEFAULT '0';");
    }
    ZBlogException::ResumeErrorHook();
    $zbp->option['ZC_LAST_VERSION'] = 169999;
    $zbp->SaveOption();
}

if ($zbp->version >= 169999 && (int) $zbp->option['ZC_LAST_VERSION'] < 169999) {
    updatedb_162090to169999();
}
die;
