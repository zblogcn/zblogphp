<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


/**
* DbFactory
*/
interface iDataBase
{
	public function Open($array);
	public function Close();
	public function Query();
	public function Update();
	public function Delete();
	public function CreateTable();
}


/**
* DbFactory
*/
class DbFactory #extends AnotherClass
{

	public $dbtype = null;

	function Create($type)
	{
		switch ($type) {
			case 'mysql':
				$db=New DbMySQL();
				break;
			
			case 'sqlite':
				$db=New DbSQLite();
				break;

			case 'sqlite3':
				$db=New DbSQLite3();
				break;
		}
		return $db;
	}

}

?>