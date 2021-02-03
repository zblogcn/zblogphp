<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-03-26
 */
require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->Load();

Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError', 'JsonError4ShowErrorHook', PLUGIN_EXITSIGNAL_RETURN);

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

    //172300
    $old = @$db->sql->get()->select($t['Config'])->column($d['Config']['Key'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Config']} ADD {$d['Config']['Key'][0]} VARCHAR(250) NOT NULL DEFAULT '';");
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

    //172315
    $old = @$db->sql->get()->select($t['Tag'])->column($d['Tag']['Group'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Group'][0]} VARCHAR(250) NOT NULL DEFAULT '';");
    }
    $old = @$db->sql->get()->select($t['Category'])->column($d['Category']['Group'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$table['Category']} ADD  {$d['Category']['Group'][0]} VARCHAR(250) NOT NULL DEFAULT '';");
    }

    //172330
    $old = @$db->sql->get()->select($t['Member'])->column($d['Member']['CreateTime'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$t['Member']} ADD {$d['Member']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = @$db->sql->get()->select($t['Member'])->column($d['Member']['UpdateTime'][0])->limit(1)->query;
    if (count($old) == 1 && $old[0] === false) {
        @$db->Query("ALTER TABLE {$t['Member']} ADD {$d['Member']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    //删除一个长期存在而又无用的索引
    @$db->sql->get()->drop($t['Post'])->index('%pre%log_VTSC')->query;

    //ZBlogException::ResumeErrorHook();
    $zbp->option['ZC_LAST_VERSION'] = ZC_LAST_VERSION;
    $zbp->SaveOption();
}

if ($zbp->version >= ZC_LAST_VERSION && (int) $zbp->option['ZC_LAST_VERSION'] < ZC_LAST_VERSION) {
    updatedb();
    JsonReturn($zbp->langs->msg->operation_succeed);
}
die;
