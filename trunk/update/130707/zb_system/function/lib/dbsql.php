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
	public function QueryMulit($s);
	public function EscapeString($s);
}



/**
* DbSql
*/
class DbSql #extends AnotherClass
{
	public $type=null;

	public function CreateTable($tablename,$datainfo){

	$s='';

	if($this->type=='Dbpdo_MySQL'||$this->type=='DbMySQL'){
		$s.='CREATE TABLE IF NOT EXISTS '.$tablename.' (';

		$i=0;
		foreach ($datainfo as $key => $value) {
			if($value[1]=='integer'){
				if($i==0){
					$s.=$value[0] .' int(11) NOT NULL AUTO_INCREMENT' . ',';
				}else{
					$s.=$value[0] .' int(11) NOT NULL DEFAULT \''.$value[3].'\'' . ',';
				}
			}
			if($value[1]=='boolean'){
				$s.=$value[0] . ' tinyint(1) NOT NULL DEFAULT \''.(int)$value[3].'\'' . ',';
			}
			if($value[1]=='string'){
				if($value[2]!=''){
					$s.=$value[0] . ' varchar('.$value[2].') NOT NULL DEFAULT \'\'' . ',';
				}else{
					$s.=$value[0] . ' text NOT NULL ' . ',';	
				}
			}
			if($value[1]=='double'||$value[1]=='float'){
				$s.=$value[0] . ' $value[1] NOT NULL DEFAULT \'0\'' . ',';
			}
			$i +=1;
		}
		reset($datainfo);
		$s.='PRIMARY KEY ('.GetValueInArray(current($datainfo),0).'),';
		$s=substr($s,0,strlen($s)-1);
		$s.=') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
	}

	if($this->type=='DbSQLite'){
		$s.='CREATE TABLE '.$tablename.' (';

		$i=0;
		foreach ($datainfo as $key => $value) {
			if($value[1]=='integer'){
				if($i==0){
					$s.=$value[0] .' integer primary key' . ',';
				}else{
					$s.=$value[0] .' integer NOT NULL DEFAULT \''.$value[3].'\'' . ',';
				}
			}
			if($value[1]=='boolean'){
				$s.=$value[0] . ' bit NOT NULL DEFAULT \''.(int)$value[3].'\'' . ',';
			}
			if($value[1]=='string'){
				if($value[2]!=''){
					$s.=$value[0] . ' varchar('.$value[2].') NOT NULL DEFAULT \'\'' . ',';
				}else{
					$s.=$value[0] . ' text NOT NULL DEFAULT \'\',';	
				}
			}
			if($value[1]=='double'||$value[1]=='float'){
				$s.=$value[0] . ' $value[1] NOT NULL DEFAULT \'0\'' . ',';
			}
			$i +=1;
		}
		$s=substr($s,0,strlen($s)-1);

		$s.=');';
		reset($datainfo);
		$s.='CREATE UNIQUE INDEX %pre%'.GetValueInArray(current($datainfo),0).' on '.$tablename.' ('.GetValueInArray(current($datainfo),0).');';

	}

	if($this->type=='DbSQLite3'){
		$s.='CREATE TABLE '.$tablename.' (';

		$i=0;
		foreach ($datainfo as $key => $value) {
			if($value[1]=='integer'){
				if($i==0){
					$s.=$value[0] .' integer primary key autoincrement' . ',';
				}else{
					$s.=$value[0] .' integer NOT NULL DEFAULT \''.$value[3].'\'' . ',';
				}
			}
			if($value[1]=='boolean'){
				$s.=$value[0] . ' bit NOT NULL DEFAULT \''.(int)$value[3].'\'' . ',';
			}
			if($value[1]=='string'){
				if($value[2]!=''){
					$s.=$value[0] . ' varchar('.$value[2].') NOT NULL DEFAULT \'\'' . ',';
				}else{
					$s.=$value[0] . ' text NOT NULL DEFAULT \'\',';	
				}
			}
			if($value[1]=='double'||$value[1]=='float'){
				$s.=$value[0] . ' $value[1] NOT NULL DEFAULT \'0\'' . ',';
			}
			$i +=1;
		}
		$s=substr($s,0,strlen($s)-1);

		$s.=');';
		reset($datainfo);
		$s.='CREATE UNIQUE INDEX %pre%'.GetValueInArray(current($datainfo),0).' on '.$tablename.' ('.GetValueInArray(current($datainfo),0).');';
	}

	return $s;
}


	public function ParseWhere($where){
		global $zbp;

		$sqlw=null;
		if(!empty($where)) {
			$sqlw .= ' WHERE ';
			$comma = '';
			foreach($where as $k => $w) {
				$eq=$w[0];
				if($eq=='='|$eq=='<'|$eq=='>'|$eq=='LIKE'|$eq=='<>'|$eq=='!='){
					$x = (string)$w[1];
					$y = (string)$w[2];
					$y = $zbp->db->EscapeString($y);
					$sqlw .= $comma . " $x $eq '$y' ";
				}
				if($eq=='BETWEEN'){
					$b1 = (string)$w[1];
					$b2 = (string)$w[2];
					$b3 = (string)$w[3];
					$sqlw .= $comma . " $b1 BETWEEN '$b2' AND '$b3' ";
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
					$sqlw .= $comma .  '(' . $sql_search . ')';
				}
				if($eq=='array'){
					$c='';
					$sql_array='';
					foreach ($w[1] as $x=>$y) {
						$y[1]=$zbp->db->EscapeString($y[1]);
						$sql_array .= $c . " $y[0]='$y[1]' ";
						$c='OR';
					}
					$sqlw .= $comma .  '(' . $sql_array . ')';
				}
				if($eq=='custom'){
					$sqlw .= $comma .  '(' . $w[1] . ')';
				}
				$comma = 'AND';
			}
		}
		return $sqlw;
	}

	public function Select($table,$select,$where,$order,$limit,$option)
	{
		global $zbp;

		$sqls='';
		$sqlw='';
		$sqlo='';
		$sqll='';

		if(!empty($select)) {
			if(is_array($select)){
				$selectstr=implode($select,',');
				$sqls="SELECT $selectstr FROM $table ";			
			}else{
				$sqls="SELECT $select FROM $table ";	
			}
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
				if($limit[1]>0){
					$sqll .= " LIMIT $limit[0], $limit[1]";
				}
			}
		}

		if(!empty($option)){
			if(isset($option['pagebar'])){
				if($option['pagebar']->Count===null){
					$s2 = $this->Count($table,array(array('COUNT','*','num')),$where);
					$option['pagebar']->Count = GetValueInArray(current($zbp->db->Query($s2)),'num');
				}
				$option['pagebar']->Count=(int)$option['pagebar']->Count;
				$option['pagebar']->make();
			}
		}
		return $sqls . $sqlw . $sqlo . $sqll;
	}

	public function Count($table,$count,$where)
	{
		global $zbp;

		$sqlc="SELECT ";

		if(!empty($count)) {
			foreach ($count as $key => $value) {
				$sqlc.=" $value[0]($value[1]) AS $value[2],";
			}
		}
		$sqlc=substr($sqlc, 0,strlen($sqlc)-1);

 		$sqlc.=" FROM $table ";

		$sqlw=$this->ParseWhere($where);

		return $sqlc . $sqlw;
	}
	
	public function Update($table,$keyvalue,$where)
	{
		global $zbp;

		$sql="UPDATE $table SET ";

		$comma = '';
		foreach ($keyvalue as $k => $v) {
			$v=$zbp->db->EscapeString($v);
			$sql.= $comma . "$k = '$v'";
			$comma = ' , ';
		}

		$sql.=$this->ParseWhere($where);
		return $sql;
	}

	public function Insert($table,$keyvalue)
	{
		global $zbp;

		$sql="INSERT INTO $table ";

		$sql.='(';
		$comma = '';
		foreach($keyvalue as $k => $v) {
			$sql.= $comma . "$k";
			$comma = ',';
		}
		$sql.=')VALUES(';

		$comma = '';
		foreach($keyvalue as $k => $v) {
			$v=$zbp->db->EscapeString($v);
			$sql.= $comma . "'$v'";
			$comma = ',';
		}
		$sql.=')';
		return  $sql;
	}

	public function Delete($table,$where)
	{
		global $zbp;

		$sql="DELETE FROM $table ";
		$sql.=$this->ParseWhere($where);
		return $sql;
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
'Config'=>array(
	'Name'=>array('conf_Name','string',250,''),
	'Value'=>array('conf_Value','string','',''),
),
'Post'=> array(
	'ID'=>array('log_ID','integer','',0),
	'CateID'=>array('log_CateID','integer','',0),
	'AuthorID'=>array('log_AuthorID','integer','',0),
	'Tag'=>array('log_Tag','string',250,''),
	'Status'=>array('log_Status','integer','',0),
	'Type'=>array('log_Type','integer','',0),
	'Alias'=>array('log_Alias','string',250,''),
	'IsTop'=>array('log_IsTop','boolean','',false),
	'IsLock'=>array('log_IsLock','boolean','',false),
	'Title'=>array('log_Title','string',250,''),
	'Intro'=>array('log_Intro','string','',''),
	'Content'=>array('log_Content','string','',''),
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
	'IsChecking'=>array('comm_IsChecking','boolean','',false),
	'RootID'=>array('comm_RootID','integer','',0),
	'ParentID'=>array('comm_ParentID','integer','',0),
	'AuthorID'=>array('comm_AuthorID','integer','',0),
	'Name'=>array('comm_Name','string',20,''),
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
	'Name'=>array('mod_Name','string',100,''),
	'FileName'=>array('mod_FileName','string',50,''),
	'Content'=>array('mod_Content','string','',''),
	'HtmlID'=>array('mod_HtmlID','string',50,''),
	'Type'=>array('mod_Type','string',5,'div'),
	'MaxLi'=>array('mod_MaxLi','integer','',0),
	'Source'=>array('mod_Source','string',50,'user'),
	'IsHideTitle'=>array('mod_IsHideTitle','boolean','',false),
	'Meta'=>array('mod_Meta','string','',''),
),
'Member'=> array(
	'ID'=>array('mem_ID','integer','',0),
	'Guid'=>array('mem_Guid','string',36,''),
	'Level'=>array('mem_Level','integer','',6),	
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
	'Order'=>array('tag_Order','integer','',0),
	'Count'=>array('tag_Count','integer','',0),
	'Alias'=>array('tag_Alias','string',250,''),	
	'Intro'=>array('tag_Intro','string','',''),
	'Template'=>array('tag_Template','string',50,''),
	'Meta'=>array('tag_Meta','string','',''),
),
'Upload'=> array(
	'ID'=>array('ul_ID','integer','',0),
	'AuthorID'=>array('ul_AuthorID','integer','',0),
	'Size'=>array('ul_Size','integer','',0),
	'Name'=>array('ul_Name','string',250,''),
	'SourceName'=>array('ul_SourceName','string',250,''),
	'MimeType'=>array('ul_MimeType','string',50,''),
	'PostTime'=>array('ul_PostTime','integer','',0),
	'DownNums'=>array('ul_DownNums','integer','',0),
	'LogID'=>array('ul_LogID','integer','',0),	
	'Intro'=>array('ul_Intro','string','',''),
	'Meta'=>array('ul_Meta','string','',''),
),
);

?>