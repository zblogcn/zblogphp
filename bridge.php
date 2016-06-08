<?php
require './zb_system/function/c_system_base.php';

$zbp->Load();

$fuck = new SQLMySQL($zbp->db);

echo $fuck
	->select("zbp_post")
	->count('log_id', 'countid')
	->count(array('*', 'fuck'))
	->max('log_id')
	->where(array('=', 'log_ID', "1"))
	->where(array('LIKE', 'log_Title', '%哈%'))
	->having(array('=', 'log_ID', '1'))
	->orderBy(array('fuck' => 'desc'), 'aaa')
	->orderBy('aaaa')
	->orderBy(array('a', 'b', 'c'))
	->groupBy("log_Id")
	->limit(array(5, 10))
	->sql;

//,$zbp->datainfo['Post']
echo '<br/>';

echo '<br/>';

echo $fuck->create($zbp->table['Post'])->data($zbp->datainfo['Post'])->sql;

echo '<br/>';

echo '<br/>';


echo $fuck->create($zbp->table['Post'])->index(array('indexname'=>array('ddd','eee','eeee')))->sql;