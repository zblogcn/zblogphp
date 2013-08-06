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
	public function EscapeString($s);	
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
		$db=new $newtype();
		$db->sql=new DbSql;
		return $db;
	}

}


/**
* DbSql
*/
class DbSql #extends AnotherClass
{
	public function ParseWhere($where){
		global $zbp;

		$sqlw=null;
		if(!empty($where)) {
			$sqlw .= ' WHERE ';
			$comma = '';
			foreach($where as $k => $w) {
				$eq=$w[0];
				if($eq=='='|$eq=='<'|$eq=='>'|$eq=='LIKE'){
					$x = (string)$w[1];
					$y = (string)$w[2];
					$y = $zbp->db->EscapeString($y);
					$sqlw .= $comma . " $x $eq '$y' ";
				}
				if($eq=='search'){
					$j=count($w);
					$sql_search='';
					$c='';
					for ($i=1; $i <= $j-1-1; $i++) { 
						$x=(string)$w[$i];
						$y=(string)$w[$j-1];
						$y=$zbp->db->EscapeString($y);
						$y=$w[$j-1];
						$sql_search .= $c . " ($x LIKE '%$y%') ";
						$c='OR';
					}
					logs($sql_search);
					$sqlw .= $comma .  '(' . $sql_search . ')';
				}
				if($eq=='array'){
					$c='';
					$sql_array='';
					foreach ($w[1] as $x=>$y) {
						$sql_array .= $c . " $y[0]=$y[1] ";
						$c='OR';
					}
					$sqlw .= $comma .  '(' . $sql_array . ')';
				}
				$comma = 'AND';
			}
		}
		return $sqlw;
	}

	public function Select($type,$select,$where,$order,$limit,$option)
	{
		global $zbp;

		$sqls='';
		$sqlw='';
		$sqlo='';
		$sqll='';

		if(!empty($select)) {
			$sqls="SELECT $select[0] FROM {$zbp->table[$type]} ";
		}

		$sqlw=$this->ParseWhere($where);

		if(!empty($order)) {
			$sqlo .= ' ORDER BY ';
			$comma = '';
			foreach($order as $k=>$v) {
				$sqlo .= $comma ."$k $v";
				$comma = ',';
			}
		}

		if(!empty($limit)){
			if(!isset($limit[1])){
				$sqll .= " LIMIT $limit[0]";
			}else{
				$sqll .= " LIMIT $limit[0], $limit[1]";
			}
		}

		if(!empty($option)){
			if(isset($option['pagebar'])){
				$s2 = $this->Count($type,array($zbp->datainfo[$type]['ID'][0]=>'num'),$where);
				$option['pagebar']->Count = GetValueInArray(current($zbp->db->Query($s2)),'num');
				$option['pagebar']->make();
			}
		}

		return $sqls . $sqlw . $sqlo . $sqll;
	}

	public function Count($type,$count,$where)
	{
		global $zbp;
		$sqlc=null;

		if(!empty($count)) {
			foreach ($count as $key => $value) {
				$sqlc="SELECT COUNT($key) AS $value FROM {$zbp->table[$type]} ";
			}
		}

		$sqlw=$this->ParseWhere($where);

		return $sqlc . $sqlw;
	}
	
	public function Update()
	{

	}

	public function Insert()
	{

	}

	public function Delete()
	{

	}

}



$table=array(

'Post'=> '%pre%post',
'Category'=> '%pre%category',
'Comment'=> '%pre%comment',
'Tag'=> '%pre%tag',
'Upload'=> '%pre%upload',
'Counter'=> '%pre%counter',
'Module'=> '%pre%module',
'Member'=> '%pre%member',
'Config'=>'%pre%config',

);


$datainfo=array(
'Post'=> array(
	'ID'=>array('log_ID','integer','',0),
	'CateID'=>array('log_CateID','integer','',0),
	'AuthorID'=>array('log_AuthorID','integer','',0),
	'Tag'=>array('log_Tag','string',250,''),
	'Status'=>array('log_Status','integer','',0),
	'Type'=>array('log_Type','integer','',0),
	'Alias'=>array('log_Alias','string',250,''),
	'IsTop'=>array('log_IsTop','boolean','',0),
	'IsLock'=>array('log_IsLock','boolean','',0),
	'Title'=>array('log_Title','string',250,''),
	'Intro'=>array('log_Intro','string','',''),
	'Content'=>array('log_Content','string','',''),
	'IP'=>array('log_IP','string',15,''),
	'PostTime'=>array('log_PostTime','integer','',0),
	'CommNums'=>array('log_CommNums','integer','',0),
	'ViewNums'=>array('log_ViewNums','integer','',0),
	'Template'=>array('log_Template','string',50,''),
	'Meta'=>array('log_Meta','string','',''),
),
'Category'=>array(
	'ID'=>array('cate_ID','integer','',0),
	'Name'=>array('cate_Name','string',50,''),
	'Order'=>array('cate_Order','integer','',0),
	'Count'=>array('cate_Count','integer','',0),
	'Alias'=>array('cate_Alias','string',50,''),
	'Intro'=>array('cate_Intro','string','',''),
	'RootID'=>array('cate_RootID','integer','',0),
	'ParentID'=>array('cate_ParentID','integer','',0),
	'Template'=>array('cate_Template','string',50,''),
	'LogTemplate'=>array('cate_LogTemplate','string',50,''),
	'Meta'=>array('cate_Meta','string','',''),
),
'Comment'=> array(
	'ID'=>array('comm_ID','integer','',0),
	'LogID'=>array('comm_LogID','integer','',0),
	'IsCheck'=>array('comm_IsCheck','boolean','',0),
	'RootID'=>array('comm_RootID','integer','',0),
	'ParentID'=>array('comm_ParentID','integer','',0),
	'AuthorID'=>array('comm_AuthorID','integer','',0),
	'Author'=>array('comm_Author','string',20,''),
	'Content'=>array('comm_Content','string','',''),
	'Email'=>array('comm_Email','string',50,''),
	'HomePage'=>array('comm_HomePage','string',250,''),
	'PostTime'=>array('comm_PostTime','integer','',0),
	'IP'=>array('comm_IP','string',15,''),
	'Agent'=>array('comm_Agent','string','',''),
	'Meta'=>array('comm_Meta','string','',''),
),
'Counter'=> array(
	'ID'=>array('coun_ID','integer','',0),
	'MemID'=>array('coun_MemID','integer','',0),
	'IP'=>array('coun_IP','string',15,''),
	'Agent'=>array('coun_Agent','string','',''),
	'Refer'=>array('coun_Refer','string',250,''),
	'Title'=>array('coun_Title','string',250,''),
	'PostTime'=>array('coun_PostTime','integer','',0),
	'Description'=>array('coun_Description','string','',''),
	'PostData'=>array('coun_PostData','string','',''),
	'AllRequestHeader'=>array('coun_AllRequestHeader','string','',''),
),
'Module'=> array(
	'ID'=>array('mod_ID','integer','',0),
	'Name'=>array('mod_Name','string',50,''),
	'FileName'=>array('mod_FileName','string',50,''),
	'Content'=>array('mod_Content','string','',''),
	'SidebarID'=>array('mod_SidebarID','integer','',0),
	'HtmlID'=>array('mod_HtmlID','string',50,''),
	'Type'=>array('mod_Type','string',5,''),
	'MaxLi'=>array('mod_MaxLi','integer','',0),
	'Source'=>array('mod_Source','string',50,''),
	'IsHideTitle'=>array('mod_IsHideTitle','boolean','',0),
	'Meta'=>array('mod_Meta','string','',''),
),
'Member'=> array(
	'ID'=>array('mem_ID','integer','',0),
	'Guid'=>array('mem_Guid','string',36,''),
	'Level'=>array('mem_Level','integer','',5),	
	'Status'=>array('mem_Status','integer','',0),
	'Name'=>array('mem_Name','string',20,''),
	'Password'=>array('mem_Password','string',32,''),
	'Email'=>array('mem_Email','string',50,''),
	'HomePage'=>array('mem_HomePage','string',250,''),
	'IP'=>array('mem_IP','string',15,''),
	'PostTime'=>array('mem_PostTime','integer','',0),
	'Alias'=>array('mem_Alias','string',250,''),
	'Intro'=>array('mem_Intro','string','',''),
	'Articles'=>array('mem_Articles','integer','',0),
	'Pages'=>array('mem_Pages','integer','',0),
	'Comments'=>array('mem_Comments','integer','',0),
	'Uploads'=>array('mem_Uploads','integer','',0),
	'Template'=>array('mem_Template','string',50,''),
	'Meta'=>array('mem_Meta','string','',''),
),
'Tag'=> array(
	'ID'=>array('tag_ID','integer','',0),
	'Name'=>array('tag_Name','string',250,''),
	'Alias'=>array('tag_Alias','string',250,''),
	'Order'=>array('tag_Order','integer','',0),
	'Count'=>array('tag_Count','integer','',0),
	'Intro'=>array('tag_Intro','string','',''),
	'Template'=>array('tag_Template','string',50,''),
	'Meta'=>array('tag_Meta','string','',''),
),
'Upload'=> array(
	'ID'=>array('ul_ID','integer','',0),
	'AuthorID'=>array('ul_AuthorID','integer','',0),
	'FileSize'=>array('ul_FileSize','integer','',0),
	'FileName'=>array('ul_FileName','string',250,''),
	'PostTime'=>array('ul_PostTime','integer','',0),
	'DownNum'=>array('ul_DownNum','integer','',0),
	'Intro'=>array('ul_Intro','string','',''),
	'Meta'=>array('ul_Meta','string','',''),
),

);
?>