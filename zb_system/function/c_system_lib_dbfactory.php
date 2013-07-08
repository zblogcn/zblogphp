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
				break;

			case 'sqlite3':
				break;	
		}
	}

}

?>