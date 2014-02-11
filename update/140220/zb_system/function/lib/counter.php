<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


class Counter extends Base{


	function __construct()
	{
		global $zbp;
		parent::__construct($zbp->table['Counter'],$zbp->datainfo['Counter']);
	}


}
