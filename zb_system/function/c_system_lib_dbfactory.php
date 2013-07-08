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

	private $db = null;
	public $dbtype = null;
	public $dbpre = null;

	function __construct($type)
	{
		$dbtype=$type;
	}

	function open($array)
	{
		switch ($dbtype) {
			case 'mysql':
				# code...
				break;

			case 'sqlite':


if ($db = sqlite_open($array[0], 0666, $sqliteerror)) {
	$dbpre=$array[1];
	return true;
} else {
	return false;
}

				break;

			case 'sqlite3':


if ($db = new SQLite3($array[0]) ){
	$dbpre=$array[1];
	return ture;
} else{
	return false;
}

				break;	
		}
	}

}

?>