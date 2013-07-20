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
	public function Query($query);
	public function Insert($query);
	public function Update($query);
	public function Delete($query);
	public function CreateTable($path);
}


/**
* DbFactory
*/
class DbFactory #extends AnotherClass
{

	public $dbtype = null;

	public static function Create($type)
	{
		$newtype='Db'.$type;
		$db=New $newtype();
		return $db;
	}

}



$table=array(

'Module'=> '%pre%Module',
'Member'=> '%pre%Member',

);


$datainfo=array(
	
'Module'=> array(
	'ID'=>array('mod_ID','integer','',0),
	'Name'=>array('mod_Name','string',50,''),
	'FileName'=>array('mod_FileName','string',50,''),
	'Order'=>array('mod_Order','integer','',0),
	'Content'=>array('mod_Content','string','',''),
	'IsHidden'=>array('mod_IsHidden','boolean','',0),
	'SidebarID'=>array('mod_SidebarID','integer','',0),
	'HtmlID'=>array('mod_HtmlID','string',50,''),
	'Ftype'=>array('mod_Ftype','string',5,''),
	'MaxLi'=>array('mod_MaxLi','integer','',0),
	'Source'=>array('mod_Source','string',50,''),
	'ViewType'=>array('mod_ViewType','string',50,''),
	'IsHideTitle'=>array('mod_IsHideTitle','boolean','',0),
	'Meta'=>array('mod_Meta','string','',''),
	),
'Member'=> array(
	'ID'=>array('mem_ID','integer','',0),
	'Guid'=>array('mem_Guid','string',36,''),
	'Name'=>array('mem_Name','string',20,''),
	'Level'=>array('mem_Level','integer','',5),
	'Password'=>array('mem_Password','string',32,''),
	'Email'=>array('mem_Email','string',50,''),
	'HomePage'=>array('mem_HomePage','string',250,''),
	'Count'=>array('mem_Count','integer','',0),
	'Alias'=>array('mem_Alias','string',250,''),
	'Intro'=>array('mem_Intro','string','',''),
	'PostTime'=>array('mem_PostTime','integer','',0),	
	'Template'=>array('mem_Template','string',50,''),
	'Meta'=>array('mem_Meta','string','',''),
	),


);
?>