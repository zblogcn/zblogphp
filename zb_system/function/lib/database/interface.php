<?php

if (!defined('ZBP_PATH')) {
    exit('Access denied');
}

/**
 * Interface Database__Interface.
 *
 * @property string|null $dbpre 数据库名前缀(Database Prefix)
 * @property mixed $db 数据库连接
 * @property string|null $dbname 数据库名
 * @property string|null $type 数据库类型
 * @property string|null $dbengine 数据库引擎
 * @property DbSql|null $sql DbSql实例
 * @property string $version 数据库版本
 */
interface Database__Interface
{

    public function Open($array);

    public function Close();

    public function Query($query);

    public function Insert($query);

    public function Update($query);

    public function Delete($query);

    public function QueryMulti($s);

    public function EscapeString($s);

    public function CreateTable($table, $dataInfo);

    public function DelTable($table);

    public function ExistTable($table);

    public function Transaction($command);//command = 'begin','commit','rollback'

    public function ExistColumn($table, $field);

}
