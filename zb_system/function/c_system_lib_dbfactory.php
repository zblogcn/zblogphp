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
class DbFactory #extends AnotherClass
{

	public $dbtype = null;
	public $dbpre = null;
	private $db = null;	

	function __construct($type)
	{
		$this->dbtype=$type;
	}

	function Open($array)
	{
		switch ($this->dbtype) {
			case 'mysql':
				# code...
				break;

			case 'sqlite':


if ($this->db = sqlite_open($array[0], 0666, $sqliteerror)) {
	$this->dbpre=$array[1];
	return true;
} else {
	return false;
}

				break;

			case 'sqlite3':


if ($this->db = new SQLite3($array[0]) ){
	$this->dbpre=$array[1];
	return true;
} else{
	return false;
}

				break;	
		}
	}
	function Close(){
		switch ($this->dbtype) {
			case 'mysql':
				break;
			case 'sqlite':

				break;
			case 'sqlite3':
				$this->db::close();
				break;	
		}
	}

	function CreateTable(){

	}

}

?>