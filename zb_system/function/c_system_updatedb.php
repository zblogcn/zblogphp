<?php

declare(strict_types=1);

/**
 * Z-BlogPHP 旧库迁移.
 *
 * 迁移步骤按历史版本顺序保留；只有全部语句执行成功后才推进
 * ZC_LAST_VERSION。SQLite 临时库测试直接调用本文件，不经过站点入口。
 */
if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * 判断字段是否存在.
 *
 * @param mixed $table
 * @param mixed $field
 */
function updatedb_checkexist($table, $field)
{
    global $zbp;

    return $zbp->db->ExistColumn($table, $field);
}

/**
 * 执行迁移 SQL，并检查数据库驱动返回值和新增错误记录.
 *
 * 各驱动的 Query() 返回值并不一致：有的直接返回 false，有的返回
 * [false]，PostgreSQL 原生驱动失败时可能返回空数组。因此同时检查
 * 返回值与驱动新增的 error 记录，不能只判断返回值是否为真。
 */
function updatedb_query(string $sql, string $description)
{
    global $zbp;

    $db = $zbp->db;
    $errorCount = count((array) ($db->error ?? []));

    try {
        $result = @$db->Query($sql);
    } catch (Throwable $e) {
        throw new RuntimeException($description . '失败：' . $e->getMessage(), 0, $e);
    }

    $errors = array_slice((array) ($db->error ?? []), $errorCount);
    $failed = false === $result
        || (is_array($result) && in_array(false, $result, true))
        || [] !== $errors;

    if ($failed) {
        $detail = [] === $errors
            ? '数据库未返回执行成功的响应'
            : (json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: print_r($errors, true));

        throw new RuntimeException($description . '失败：' . $detail);
    }

    return $result;
}

/**
 * 执行全部历史迁移，但不保存配置.
 *
 * 拆出配置保存便于一次性临时库测试；生产入口仍由 updatedb() 在全部
 * DDL 成功后保存版本。
 */
function updatedb_migrate(): void
{
    global $zbp, $table, $datainfo;

    $t = &$table;
    $d = &$datainfo;
    $db = &$zbp->db;

    // 162090
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['Type'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Type'][0]} integer NOT NULL DEFAULT 0;",
            "为 Tag 表添加 {$d['Tag']['Type'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['Type'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$table['Category']} ADD  {$d['Category']['Type'][0]} integer NOT NULL DEFAULT 0;",
            "为 Category 表添加 {$d['Category']['Type'][0]} 字段",
        );
    }

    // 172300
    $old = updatedb_checkexist($t['Config'], $d['Config']['Key'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$table['Config']} ADD {$d['Config']['Key'][0]} VARCHAR(250) NOT NULL DEFAULT '';",
            "为 Config 表添加 {$d['Config']['Key'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Post'], $d['Post']['CreateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Post']} ADD {$d['Post']['CreateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Post 表添加 {$d['Post']['CreateTime'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Post'], $d['Post']['UpdateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Post']} ADD {$d['Post']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Post 表添加 {$d['Post']['UpdateTime'][0]} 字段",
        );
    }

    if ($zbp->option['ZC_LAST_VERSION'] < 162315 && 'mysql' == $db->type) {
        updatedb_query(
            "ALTER TABLE {$t['Post']} MODIFY  `{$d['Post']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';",
            "修改 Post 表 {$d['Post']['Type'][0]} 字段类型",
        );
        updatedb_query(
            "ALTER TABLE {$t['Tag']} MODIFY  `{$d['Tag']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';",
            "修改 Tag 表 {$d['Tag']['Type'][0]} 字段类型",
        );
        updatedb_query(
            "ALTER TABLE {$t['Category']} MODIFY  `{$d['Category']['Type'][0]}` INT(11) NOT NULL DEFAULT '0';",
            "修改 Category 表 {$d['Category']['Type'][0]} 字段类型",
        );
    }

    // 172315
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['Group'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$table['Tag']} ADD  {$d['Tag']['Group'][0]} VARCHAR(250) NOT NULL DEFAULT '';",
            "为 Tag 表添加 {$d['Tag']['Group'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['Group'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$table['Category']} ADD  {$d['Category']['Group'][0]} VARCHAR(250) NOT NULL DEFAULT '';",
            "为 Category 表添加 {$d['Category']['Group'][0]} 字段",
        );
    }

    // 172330
    $old = updatedb_checkexist($t['Member'], $d['Member']['CreateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Member']} ADD {$d['Member']['CreateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Member 表添加 {$d['Member']['CreateTime'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Member'], $d['Member']['UpdateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Member']} ADD {$d['Member']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Member 表添加 {$d['Member']['UpdateTime'][0]} 字段",
        );
    }

    // 172800
    updatedb_query(
        "UPDATE {$t['Post']} SET {$d['Post']['UpdateTime'][0]} = {$d['Post']['PostTime'][0]} WHERE {$d['Post']['UpdateTime'][0]} = 0;",
        '回填 Post 表 UpdateTime 字段',
    );

    // 173000
    $old = updatedb_checkexist($t['Category'], $d['Category']['CreateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Category']} ADD {$d['Category']['CreateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Category 表添加 {$d['Category']['CreateTime'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['UpdateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Category']} ADD {$d['Category']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Category 表添加 {$d['Category']['UpdateTime'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Category'], $d['Category']['PostTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Category']} ADD {$d['Category']['PostTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Category 表添加 {$d['Category']['PostTime'][0]} 字段",
        );
    }

    $old = updatedb_checkexist($t['Tag'], $d['Tag']['CreateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Tag']} ADD {$d['Tag']['CreateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Tag 表添加 {$d['Tag']['CreateTime'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['UpdateTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Tag']} ADD {$d['Tag']['UpdateTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Tag 表添加 {$d['Tag']['UpdateTime'][0]} 字段",
        );
    }
    $old = updatedb_checkexist($t['Tag'], $d['Tag']['PostTime'][0]);
    if (false === $old) {
        updatedb_query(
            "ALTER TABLE {$t['Tag']} ADD {$d['Tag']['PostTime'][0]} integer NOT NULL DEFAULT 0;",
            "为 Tag 表添加 {$d['Tag']['PostTime'][0]} 字段",
        );
    }
}

/**
 * 生产迁移入口：全部 DDL 成功后才保存新版本.
 */
function updatedb(): void
{
    global $zbp;

    updatedb_migrate();

    $zbp->option['ZC_LAST_VERSION'] = ZC_LAST_VERSION;
    $zbp->SaveOption();
}
