<?php

/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 1.0 2020-03-26
 */
require '../function/c_system_base.php';

$zbp->Load();

Add_Filter_Plugin('Filter_Plugin_Debug_Handler_Common', 'JsonError4ShowErrorHook', PLUGIN_EXITSIGNAL_RETURN);

if (!$zbp->CheckRights('root')) {
    $zbp->ShowError(6);
    die();
}

function updatedb_checkexist($table, $field)
{
    global $zbp;

    return $zbp->db->ExistColumn($table, $field);
}

function updatedb()
{
    global $zbp, $table, $datainfo;
    $t = &$table;
    $d = &$datainfo;
    $db = &$zbp->db;

    //162090
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['Type'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Type'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['Type'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$table['Category']} ADD  {$d['Category']['Type'][0]} integer NOT NULL DEFAULT 0;");
    }

    //172300
    $old = updatedb_checkexist($t['Config'], $d['Config']['Key'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$table['Config']} ADD {$d['Config']['Key'][0]} VARCHAR(250) NOT NULL DEFAULT '';");
    }
    $old = updatedb_checkexist($t['Post'], $d['Post']['CreateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = updatedb_checkexist($t['Post'], $d['Post']['UpdateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Post']} ADD {$d['Post']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    if ($zbp->option['ZC_LAST_VERSION'] < 162315 && $db->type == 'mysql') {
        @$db->Query("ALTER TABLE {$t['Post']} MODIFY  `{$d['Post']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
        @$db->Query("ALTER TABLE {$t['Tag']} MODIFY  `{$d['Tag']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
        @$db->Query("ALTER TABLE {$t['Category']} MODIFY  `{$d['Category']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';");
    }

    //172315
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['Group'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Group'][0]} VARCHAR(250) NOT NULL DEFAULT '';");
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['Group'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$table['Category']} ADD  {$d['Category']['Group'][0]} VARCHAR(250) NOT NULL DEFAULT '';");
    }

    //172330
    $old = updatedb_checkexist($t['Member'], $d['Member']['CreateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Member']} ADD {$d['Member']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = updatedb_checkexist($t['Member'], $d['Member']['UpdateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Member']} ADD {$d['Member']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    //172800
    @$db->Query("UPDATE {$t['Post']} SET {$d['Post']['UpdateTime'][0]} = {$d['Post']['PostTime'][0]} WHERE {$d['Post']['UpdateTime'][0]} = 0;");


    //173000
    $old = updatedb_checkexist($t['Category'], $d['Category']['CreateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Category']} ADD {$d['Category']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['UpdateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Category']} ADD {$d['Category']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['PostTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Category']} ADD {$d['Category']['PostTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    $old = updatedb_checkexist($t['Tag'], $d['Tag']['CreateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Tag']} ADD {$d['Tag']['CreateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['UpdateTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Tag']} ADD {$d['Tag']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;");
    }
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['PostTime'][0]);
    if ($old === false) {
        @$db->Query("ALTER TABLE {$t['Tag']} ADD {$d['Tag']['PostTime'][0]} integer NOT NULL DEFAULT 0;");
    }

    $zbp->option['ZC_LAST_VERSION'] = ZC_LAST_VERSION;
    $zbp->SaveOption();
}

if ($zbp->version >= ZC_LAST_VERSION && (int) $zbp->option['ZC_LAST_VERSION'] < ZC_LAST_VERSION) {
    updatedb();
    if (isset($_GET['updatedb'])) {
        echo $zbp->langs->msg->operation_succeed;
    } else {
        JsonReturn($zbp->langs->msg->operation_succeed);
    }
}
die;
