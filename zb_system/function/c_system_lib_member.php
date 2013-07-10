<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


/**
* 
*/
class BaseMember
{

	public $ID=null;
	public $Guid=null;
	public $Name=null;
	public $Level=null;
	public $Password=null;
	public $Email=null;
	public $HomePage=null;
	public $Count=null;
	public $Alias=null;
	public $TemplateName=null;
	public $FullUrl=null;
	public $Intro=null;
	public $MetaString=null;

/**
* 
*/
class Member extends BaseMember
{
	
	function __construct(argument)
	{
		# code...
	}
}

?>