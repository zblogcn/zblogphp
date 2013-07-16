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
	public function CreateTable();
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



$TableSql_MySQL=array(
"CREATE TABLE %pre%Tag (tag_ID integer primary key auto_increment,tag_Name varchar(255) default '',tag_Intro text default '',tag_ParentID integer default 0,tag_Alias varchar(255) default '',tag_Order integer default 0,tag_Count integer default 0,tag_Template varchar(50) default '',tag_Url varchar(255) default '',tag_Meta text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Article (log_ID integer primary key auto_increment,log_CateID integer default 0,log_AuthorID integer default 0,log_Level integer default 0,log_Alias varchar(255) default '',log_Title varchar(255) default '',log_Intro text default '',log_Content text default '',log_IP varchar(20) default '',log_PostTime integer default 0,log_CommNums integer default 0,log_ViewNums integer default 0,log_TrackBackNums integer default 0,log_Tag varchar(255) default '',log_IsTop bit default 0,log_Yea integer default 0,log_Nay integer default 0,log_Ratting integer default 0,log_Template varchar(50) default '',log_Url varchar(255) default '',log_Type integer default 0,log_Meta text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Category (cate_ID integer primary key auto_increment,cate_Name varchar(50) default '',cate_Order integer default 0,cate_Intro text default '',cate_Count integer default 0,cate_Alias varchar(255) default '',cate_ParentID integer default 0,cate_Template varchar(50) default '',cate_LogTemplate varchar(50) default '',cate_Url varchar(255) default '',cate_Meta text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Comment (comm_ID integer primary key auto_increment,log_ID integer default 0,comm_AuthorID integer default 0,comm_Author varchar(20) default '',comm_Content text default '',comm_Email varchar(50) default '',comm_HomePage varchar(255) default '',comm_PostTime integer default 0,comm_IP varchar(20) default '',comm_Agent text default '',comm_Reply text default '',comm_LastReplyIP varchar(20) default '',comm_LastReplyTime integer default 0,comm_Yea integer default 0,comm_Nay integer default 0,comm_Ratting integer default 0,comm_ParentID integer default 0,comm_IsCheck bit default 0,comm_Meta text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%UpLoad (ul_ID integer primary key auto_increment,ul_AuthorID integer default 0,ul_FileSize integer default 0,ul_FileName varchar(255) default '',ul_PostTime integer default 0,ul_Quote varchar(255) default '',ul_DownNum integer default 0,ul_FileIntro varchar(255) default '',ul_DirByTime bit default 0,ul_Meta text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Counter (coun_ID integer primary key auto_increment,coun_IP varchar(20) default '',coun_Agent text default '',coun_Refer varchar(255) default '',coun_PostTime integer default 0,coun_Content text default '',coun_UserID integer default 0,coun_PostData text default '',coun_Alias text default '',coun_AllRequestHeader text default '',coun_LogName text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Keyword (key_ID integer primary key auto_increment,key_Name varchar(255) default '',key_Intro text default '',key_Alias varchar(255) default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Member (mem_ID integer primary key auto_increment,mem_Guid  varchar(36) default '',mem_Level integer default 0,mem_Name varchar(20) default '',mem_Password varchar(32) default '',mem_Sex integer default 0,mem_Email varchar(50) default '',mem_HomePage varchar(255) default '',mem_PostTime integer default 0,mem_Status integer default 0,mem_Articles integer default 0,mem_Pages integer default 0,mem_Comments integer default 0,mem_Intro text default '',mem_IP varchar(20) default '',mem_Count integer default 0,mem_Template varchar(50) default '',mem_Url varchar(255) default '',mem_Alias varchar(255) default '',mem_Meta text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Config (conf_Name varchar(255) not null default '',conf_Value text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE TABLE %pre%Module (mod_ID integer primary key auto_increment,mod_Name varchar(50) default '',mod_FileName varchar(50) default '',mod_Order integer default 0,mod_Content text default '',mod_IsHidden bit default 0,mod_SidebarID integer default 0,mod_HtmlID varchar(50) default '',mod_Ftype varchar(5) default '',mod_MaxLi integer default 0,mod_Source varchar(50) default '',mod_ViewType varchar(50) default '',mod_IsHideTitle bit default 0,mod_Meta text default '') ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci"
,
"CREATE INDEX %pre%log_PostTime on %pre%Article (log_PostTime)"
,
"CREATE INDEX %pre%comm_PostTime on %pre%Comment (comm_PostTime)"
,
"CREATE INDEX %pre%mem_Name on %pre%Member (mem_Name)"
);


$TableSql_SQLite3=array(
"CREATE TABLE %pre%Tag (tag_ID integer primary key autoincrement,tag_Name varchar(255) default '',tag_Intro text default '',tag_ParentID integer default 0,tag_Alias varchar(255) default '',tag_Order integer default 0,tag_Count integer default 0,tag_Template varchar(50) default '',tag_Url varchar(255) default '',tag_Meta text default '')"
,
"CREATE TABLE %pre%Article (log_ID integer primary key autoincrement,log_CateID integer default 0,log_AuthorID integer default 0,log_Level integer default 0,log_Alias varchar(255) default '',log_Title varchar(255) default '',log_Intro text default '',log_Content text default '',log_IP varchar(20) default '',log_PostTime integer default 0,log_CommNums integer default 0,log_ViewNums integer default 0,log_TrackBackNums integer default 0,log_Tag varchar(255) default '',log_IsTop bit default 0,log_Yea integer default 0,log_Nay integer default 0,log_Ratting integer default 0,log_Template varchar(50) default '',log_Url varchar(255) default '',log_Type integer default 0,log_Meta text default '')"
,
"CREATE TABLE %pre%Category (cate_ID integer primary key autoincrement,cate_Name varchar(50) default '',cate_Order integer default 0,cate_Intro text default '',cate_Count integer default 0,cate_Alias varchar(255) default '',cate_ParentID integer default 0,cate_Template varchar(50) default '',cate_LogTemplate varchar(50) default '',cate_Url varchar(255) default '',cate_Meta text default '')"
,
"CREATE TABLE %pre%Comment (comm_ID integer primary key autoincrement,log_ID integer default 0,comm_AuthorID integer default 0,comm_Author varchar(20) default '',comm_Content text default '',comm_Email varchar(50) default '',comm_HomePage varchar(255) default '',comm_PostTime integer default 0,comm_IP varchar(20) default '',comm_Agent text default '',comm_Reply text default '',comm_LastReplyIP varchar(20) default '',comm_LastReplyTime integer default 0,comm_Yea integer default 0,comm_Nay integer default 0,comm_Ratting integer default 0,comm_ParentID integer default 0,comm_IsCheck bit default 0,comm_Meta text default '')"
,
"CREATE TABLE %pre%UpLoad (ul_ID integer primary key autoincrement,ul_AuthorID integer default 0,ul_FileSize integer default 0,ul_FileName varchar(255) default '',ul_PostTime integer default 0,ul_Quote varchar(255) default '',ul_DownNum integer default 0,ul_FileIntro varchar(255) default '',ul_DirByTime bit default 0,ul_Meta text default '')"
,
"CREATE TABLE %pre%Counter (coun_ID integer primary key autoincrement,coun_IP varchar(20) default '',coun_Agent text default '',coun_Refer varchar(255) default '',coun_PostTime integer default 0,coun_Content text default '',coun_UserID integer default 0,coun_PostData text default '',coun_Alias text default '',coun_AllRequestHeader text default '',coun_LogName text default '')"
,
"CREATE TABLE %pre%Keyword (key_ID integer primary key autoincrement,key_Name varchar(255) default '',key_Intro text default '',key_Alias varchar(255) default '')"
,
"CREATE TABLE %pre%Member (mem_ID integer primary key autoincrement,mem_Guid  varchar(36) default '',mem_Level integer default 0,mem_Name varchar(20) default '',mem_Password varchar(32) default '',mem_Sex integer default 0,mem_Email varchar(50) default '',mem_HomePage varchar(255) default '',mem_PostTime integer default 0,mem_Status integer default 0,mem_Articles integer default 0,mem_Pages integer default 0,mem_Comments integer default 0,mem_Intro text default '',mem_IP varchar(20) default '',mem_Count integer default 0,mem_Template varchar(50) default '',mem_Url varchar(255) default '',mem_Alias varchar(255) default '',mem_Meta text default '')"
,
"CREATE TABLE %pre%Config (conf_Name varchar(255) not null default '',conf_Value text default '')"
,
"CREATE TABLE %pre%Module (mod_ID integer primary key autoincrement,mod_Name varchar(50) default '',mod_FileName varchar(50) default '',mod_Order integer default 0,mod_Content text default '',mod_IsHidden bit default 0,mod_SidebarID integer default 0,mod_HtmlID varchar(50) default '',mod_Ftype varchar(5) default '',mod_MaxLi integer default 0,mod_Source varchar(50) default '',mod_ViewType varchar(50) default '',mod_IsHideTitle bit default 0,mod_Meta text default '')"
,
"CREATE INDEX %pre%log_PostTime on %pre%Article (log_PostTime)"
,
"CREATE INDEX %pre%comm_PostTime on %pre%Comment (comm_PostTime)"
,
"CREATE INDEX %pre%mem_Name on %pre%Member (mem_Name)"
,
"CREATE UNIQUE INDEX %pre%tag_ID on %pre%Tag (tag_ID)"
,
"CREATE UNIQUE INDEX %pre%log_ID on %pre%Article (log_ID)"
,
"CREATE UNIQUE INDEX %pre%cate_ID on %pre%Category (cate_ID)"
,
"CREATE UNIQUE INDEX %pre%comm_ID on %pre%Comment (comm_ID)"
,
"CREATE UNIQUE INDEX %pre%ul_ID on %pre%UpLoad (ul_ID)"
,
"CREATE UNIQUE INDEX %pre%key_ID on %pre%Keyword (key_ID)"
,
"CREATE UNIQUE INDEX %pre%mem_ID on %pre%Member (mem_ID)"
,
"CREATE UNIQUE INDEX %pre%mod_ID on %pre%Module (mod_ID)"
);


$TableSql_SQLite=array(
"CREATE TABLE %pre%Tag (tag_ID integer primary key,tag_Name varchar(255) default '',tag_Intro text default '',tag_ParentID integer default 0,tag_Alias varchar(255) default '',tag_Order integer default 0,tag_Count integer default 0,tag_Template varchar(50) default '',tag_Url varchar(255) default '',tag_Meta text default '')"
,
"CREATE TABLE %pre%Article (log_ID integer primary key,log_CateID integer default 0,log_AuthorID integer default 0,log_Level integer default 0,log_Alias varchar(255) default '',log_Title varchar(255) default '',log_Intro text default '',log_Content text default '',log_IP varchar(20) default '',log_PostTime integer default 0,log_CommNums integer default 0,log_ViewNums integer default 0,log_TrackBackNums integer default 0,log_Tag varchar(255) default '',log_IsTop bit default 0,log_Yea integer default 0,log_Nay integer default 0,log_Ratting integer default 0,log_Template varchar(50) default '',log_Url varchar(255) default '',log_Type integer default 0,log_Meta text default '')"
,
"CREATE TABLE %pre%Category (cate_ID integer primary key,cate_Name varchar(50) default '',cate_Order integer default 0,cate_Intro text default '',cate_Count integer default 0,cate_Alias varchar(255) default '',cate_ParentID integer default 0,cate_Template varchar(50) default '',cate_LogTemplate varchar(50) default '',cate_Url varchar(255) default '',cate_Meta text default '')"
,
"CREATE TABLE %pre%Comment (comm_ID integer primary key,log_ID integer default 0,comm_AuthorID integer default 0,comm_Author varchar(20) default '',comm_Content text default '',comm_Email varchar(50) default '',comm_HomePage varchar(255) default '',comm_PostTime integer default 0,comm_IP varchar(20) default '',comm_Agent text default '',comm_Reply text default '',comm_LastReplyIP varchar(20) default '',comm_LastReplyTime integer default 0,comm_Yea integer default 0,comm_Nay integer default 0,comm_Ratting integer default 0,comm_ParentID integer default 0,comm_IsCheck bit default 0,comm_Meta text default '')"
,
"CREATE TABLE %pre%UpLoad (ul_ID integer primary key,ul_AuthorID integer default 0,ul_FileSize integer default 0,ul_FileName varchar(255) default '',ul_PostTime integer default 0,ul_Quote varchar(255) default '',ul_DownNum integer default 0,ul_FileIntro varchar(255) default '',ul_DirByTime bit default 0,ul_Meta text default '')"
,
"CREATE TABLE %pre%Counter (coun_ID integer primary key,coun_IP varchar(20) default '',coun_Agent text default '',coun_Refer varchar(255) default '',coun_PostTime integer default 0,coun_Content text default '',coun_UserID integer default 0,coun_PostData text default '',coun_Alias text default '',coun_AllRequestHeader text default '',coun_LogName text default '')"
,
"CREATE TABLE %pre%Keyword (key_ID integer primary key,key_Name varchar(255) default '',key_Intro text default '',key_Alias varchar(255) default '')"
,
"CREATE TABLE %pre%Member (mem_ID integer primary key,mem_Level integer default 0,mem_Guid  varchar(36) default '',mem_Name varchar(20) default '',mem_Password varchar(32) default '',mem_Sex integer default 0,mem_Email varchar(50) default '',mem_HomePage varchar(255) default '',mem_PostTime integer default 0,mem_Status integer default 0,mem_Articles integer default 0,mem_Pages integer default 0,mem_Comments integer default 0,mem_Intro text default '',mem_IP varchar(20) default '',mem_Count integer default 0,mem_Template varchar(50) default '',mem_Url varchar(255) default '',mem_Alias varchar(255) default '',mem_Meta text default '')"
,
"CREATE TABLE %pre%Config (conf_Name varchar(255) not null default '',conf_Value text default '')"
,
"CREATE TABLE %pre%Module (mod_ID integer primary key,mod_Name varchar(50) default '',mod_FileName varchar(50) default '',mod_Order integer default 0,mod_Content text default '',mod_IsHidden bit default 0,mod_SidebarID integer default 0,mod_HtmlID varchar(50) default '',mod_Ftype varchar(5) default '',mod_MaxLi integer default 0,mod_Source varchar(50) default '',mod_ViewType varchar(50) default '',mod_IsHideTitle bit default 0,mod_Meta text default '')"
,
"CREATE INDEX %pre%log_PostTime on %pre%Article (log_PostTime)"
,
"CREATE INDEX %pre%comm_PostTime on %pre%Comment (comm_PostTime)"
,
"CREATE INDEX %pre%mem_Name on %pre%Member (mem_Name)"
,
"CREATE UNIQUE INDEX %pre%tag_ID on %pre%Tag (tag_ID)"
,
"CREATE UNIQUE INDEX %pre%log_ID on %pre%Article (log_ID)"
,
"CREATE UNIQUE INDEX %pre%cate_ID on %pre%Category (cate_ID)"
,
"CREATE UNIQUE INDEX %pre%comm_ID on %pre%Comment (comm_ID)"
,
"CREATE UNIQUE INDEX %pre%ul_ID on %pre%UpLoad (ul_ID)"
,
"CREATE UNIQUE INDEX %pre%key_ID on %pre%Keyword (key_ID)"
,
"CREATE UNIQUE INDEX %pre%mem_ID on %pre%Member (mem_ID)"
,
"CREATE UNIQUE INDEX %pre%mod_ID on %pre%Module (mod_ID)"
);


?>