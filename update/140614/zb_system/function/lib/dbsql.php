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
	public function CreateTable($table,$datainfo);
	public function DelTable($table);
	public function ExistTable($table);
}



/**
* DbSql
*/
class DbSql #extends AnotherClass
{
	public $type=null;
	protected $db=null;
	
	function __construct($db=null)
	{
		$this->db=$db;
		$this->type=get_class($db);
	}
	
	public function ReplacePre(&$s){
		$s=str_replace('%pre%', $this->db->dbpre, $s);
		return $s;
	}

	public function DelTable($table){
		$this->ReplacePre($table);

		$s='';
		$s="DROP TABLE $table";
		return $s;
	}

	public function ExistTable($table,$dbname=''){
		$this->ReplacePre($table);

		$s='';
		if($this->type=='DbSQLite'||$this->type=='DbSQLite3'){
			$s="SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table'";
		}
		if($this->type=='Dbpdo_MySQL'||$this->type=='DbMySQL'||$this->type=='DbMySQLi'){
			$s="SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$dbname' AND TABLE_NAME='$table'";
		}

		return $s;
	}

	public function CreateTable($table,$datainfo){

		$s='';

		if($this->type=='DbSQLite'){
			$s.='CREATE TABLE '.$table.' (';

			$i=0;
			foreach ($datainfo as $key => $value) {
				if($value[1]=='integer'||$value[1]=='timeinteger'){
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
						if(strpos($value[2],'char')!==false){
							$s.=$value[0] . ' char('.str_replace('char','',$value[2]).') NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif(is_int($value[2])){
							$s.=$value[0] . ' varchar('.$value[2].') NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}else{
							$s.=$value[0] . ' text NOT NULL DEFAULT \'\',';
						}
					}else{
						$s.=$value[0] . ' text NOT NULL DEFAULT \'\',';
					}
				}
				if($value[1]=='double'||$value[1]=='float'){
					$s.=$value[0] . ' $value[1] NOT NULL DEFAULT \'0\'' . ',';
				}
				if($value[1]=='date'||$value[1]=='time'||$value[1]=='datetime'){
					$s.=$value[0] . ' $value[1] NOT NULL,';
				}
				if($value[1]=='timestamp'){
					$s.=$value[0] . ' $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP,';
				}
				$i +=1;
			}
			$s=substr($s,0,strlen($s)-1);

			$s.=');';
			reset($datainfo);
			$s.='CREATE UNIQUE INDEX %pre%'.GetValueInArrayByCurrent($datainfo,0).' on '.$table.' ('.GetValueInArrayByCurrent($datainfo,0).');';

		}

		if($this->type=='DbSQLite3'){
			$s.='CREATE TABLE '.$table.' (';

			$i=0;
			foreach ($datainfo as $key => $value) {
				if($value[1]=='integer'||$value[1]=='timeinteger'){
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
						if(strpos($value[2],'char')!==false){
							$s.=$value[0] . ' char('.str_replace('char','',$value[2]).') NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif(is_int($value[2])){
							$s.=$value[0] . ' varchar('.$value[2].') NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}else{
							$s.=$value[0] . ' text NOT NULL DEFAULT \'\',';
						}
					}else{
						$s.=$value[0] . ' text NOT NULL DEFAULT \'\',';
					}
				}
				if($value[1]=='double'||$value[1]=='float'){
					$s.=$value[0] . ' $value[1] NOT NULL DEFAULT \'0\'' . ',';
				}
				if($value[1]=='date'||$value[1]=='time'||$value[1]=='datetime'){
					$s.=$value[0] . ' $value[1] NOT NULL,';
				}
				if($value[1]=='timestamp'){
					$s.=$value[0] . ' $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP,';
				}
				$i +=1;
			}
			$s=substr($s,0,strlen($s)-1);

			$s.=');';
			reset($datainfo);
			$s.='CREATE UNIQUE INDEX %pre%'.GetValueInArrayByCurrent($datainfo,0).' on '.$table.' ('.GetValueInArrayByCurrent($datainfo,0).');';
		}

		if($this->type=='Dbpdo_MySQL'||$this->type=='DbMySQL'||$this->type=='DbMySQLi'){
			$s.='CREATE TABLE IF NOT EXISTS '.$table.' (';

			$i=0;
			foreach ($datainfo as $key => $value) {
				if($value[1]=='integer'||$value[1]=='timeinteger'){
					if($i==0){
						$s.=$value[0] .' int(11) NOT NULL AUTO_INCREMENT' . ',';
					}else{
						if($value[2]==''){
							$s.=$value[0] .' int(11) NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='tinyint'){
							$s.=$value[0] .' tinyint(4) NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='smallint'){
							$s.=$value[0] .' smallint(6) NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='mediumint'){
							$s.=$value[0] .' mediumint(9) NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='int'){
							$s.=$value[0] .' int(11) NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='bigint'){
							$s.=$value[0] .' bigint(20) NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}
					}
				}
				if($value[1]=='boolean'){
					$s.=$value[0] . ' tinyint(1) NOT NULL DEFAULT \''.(int)$value[3].'\'' . ',';
				}
				if($value[1]=='string'){
					if($value[2]!=''){
						if(strpos($value[2],'char')!==false){
							$s.=$value[0] . ' char('.str_replace('char','',$value[2]).') NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif(is_int($value[2])){
							$s.=$value[0] . ' varchar('.$value[2].') NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='tinytext'){
							$s.=$value[0] . ' tinytext NOT NULL ' . ',';
						}elseif($value[2]=='text'){
							$s.=$value[0] . ' text NOT NULL ' . ',';
						}elseif($value[2]=='mediumtext'){
							$s.=$value[0] . ' mediumtext NOT NULL ' . ',';
						}elseif($value[2]=='longtext'){
							$s.=$value[0] . ' longtext NOT NULL ' . ',';
						}
					}else{
						$s.=$value[0] . ' longtext NOT NULL ' . ',';
					}
				}
				if($value[1]=='double'||$value[1]=='float'){
					$s.=$value[0] . ' $value[1] NOT NULL DEFAULT \'0\'' . ',';
				}
				if($value[1]=='date'||$value[1]=='time'||$value[1]=='datetime'){
					$s.=$value[0] . ' $value[1] NOT NULL,';
				}
				if($value[1]=='timestamp'){
					$s.=$value[0] . ' $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,';
				}
				$i +=1;
			}
			reset($datainfo);
			$s.='PRIMARY KEY ('.GetValueInArrayByCurrent($datainfo,0).'),';
			$s=substr($s,0,strlen($s)-1);
			$s.=') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
		}

		$this->ReplacePre($s);
		return $s;
	}


	public function ParseWhere($where,$changewhere=null){

		$sqlw=null;
		if(empty($where))return null;

		if(!is_null($changewhere)){
			$sqlw .= " $changewhere ";
		}else{
			$sqlw .= ' WHERE ';
		}
		
		if(!is_array($where))return $sqlw . $where;
		
		$comma = '';
		foreach($where as $k => $w) {
			$eq=strtoupper($w[0]);
			if($eq=='='|$eq=='<'|$eq=='>'|$eq=='LIKE'|$eq=='<>'|$eq=='<='|$eq=='>='|$eq=='NOT LIKE'){
				$x = (string)$w[1];
				$y = (string)$w[2];
				$y = $this->db->EscapeString($y);
				$sqlw .= $comma . " $x $eq '$y' ";
			}
			if($eq=='EXISTS'|$eq=='NOT EXISTS'){
				if(!isset($w[2])){
					$sqlw .= $comma .  ' ' . $eq . ' (' . $w[1] . ') ';
				}else{
					$sqlw .= $comma .  '('. $w[1] .' ' . $eq . ' (' . $w[2] . ')) ';
				}
			}
			if($eq=='BETWEEN'){
				$b1 = (string)$w[1];
				$b2 = (string)$w[2];
				$b3 = (string)$w[3];
				$sqlw .= $comma . " $b1 BETWEEN '$b2' AND '$b3' ";
			}
			if($eq=='SEARCH'){
				$j=count($w);
				$sql_search='';
				$c='';
				for ($i=1; $i <= $j-1-1; $i++) {
					$x=(string)$w[$i];
					$y=(string)$w[$j-1];
					$y=$this->db->EscapeString($y);
					$sql_search .= $c . " ($x LIKE '%$y%') ";
					$c='OR';
				}
				$sqlw .= $comma .  '(' . $sql_search . ') ';
			}
			if($eq=='ARRAY'){
				$c='';
				$sql_array='';
				if(!is_array($w[1]))continue;
				if(count($w[1])==0)continue;
				foreach ($w[1] as $x=>$y) {
					$y[1]=$this->db->EscapeString($y[1]);
					$sql_array .= $c . " $y[0]='$y[1]' ";
					$c='OR';
				}
				$sqlw .= $comma .  '(' . $sql_array . ') ';
			}
			if($eq=='ARRAY_NOT'){
				$c='';
				$sql_array='';
				if(!is_array($w[1]))continue;
				if(count($w[1])==0)continue;
				foreach ($w[1] as $x=>$y) {
					$y[1]=$this->db->EscapeString($y[1]);
					$sql_array .= $c . " $y[0]<>'$y[1]' ";
					$c='OR';
				}
				$sqlw .= $comma .  '(' . $sql_array . ') ';
			}
			if($eq=='ARRAY_LIKE'){
				$c='';
				$sql_array='';
				if(!is_array($w[1]))continue;
				if(count($w[1])==0)continue;
				foreach ($w[1] as $x=>$y) {
					$y[1]=$this->db->EscapeString($y[1]);
					$sql_array .= $c . " ($y[0] LIKE '$y[1]') ";
					$c='OR';
				}
				$sqlw .= $comma .  '(' . $sql_array . ') ';
			}
			if($eq=='IN'|$eq=='NOT IN'){
				$c='';
				$sql_array='';
				if(!is_array($w[2])){
					$sql_array=$w[2];
				}else{
					if(count($w[2])==0)continue;
					foreach ($w[2] as $x=>$y) {
						$y=$this->db->EscapeString($y);
						$sql_array .= $c . " '$y' ";
						$c=',';
					}
				}
				$sqlw .= $comma .  '('. $w[1] .' '. $eq .' (' . $sql_array . ')) ';
			}
			if($eq=='CUSTOM'){
				$sqlw .= $comma . ' ' . $w[1] . ' ';
			}
			$comma = 'AND';
		}

		return $sqlw;
	}

	public function Select($table,$select,$where,$order,$limit,$option=null){
		$this->ReplacePre($table);
	
		$sqls='';
		$sqlw='';
		$sqlo='';
		$sqll='';

		if(!empty($select)){
			if(is_array($select)){
				$selectstr=implode($select,',');
				if(trim($selectstr)=='')$selectstr='*';
				$sqls="SELECT $selectstr FROM $table ";
			}else{
				if(trim($sqls)=='')$sqls='*';
				$sqls="SELECT $select FROM $table ";
			}
		}else{
				$sqls="SELECT * FROM $table ";
		}

		if(isset($option['changewhere'])){
			$sqlw=$this->ParseWhere($where,$option['changewhere']);
		}else{
			$sqlw=$this->ParseWhere($where);
		}

		if(!empty($order)){
			$sqlo .= ' ORDER BY ';
			$comma = '';
			if(!is_array($order)){
				$sqlo .= $order;
			}else{
				foreach($order as $k=>$v) {
					$sqlo .= $comma ."$k $v";
					$comma = ',';
				}
			}
		}

		if(!empty($limit)){
			if(!is_array($limit)){
				$sqll .= " LIMIT $limit";
			}elseif(!isset($limit[1])){
				$sqll .= " LIMIT $limit[0]";
			}else{
				if($limit[1]>0){
					//$sqll .= " LIMIT $limit[0], $limit[1]";
					$sqll .= " LIMIT $limit[1] OFFSET $limit[0]";
				}
			}
		}

		if(!empty($option)){
			if(isset($option['pagebar'])){
				if($option['pagebar']->Count===null){
					$s2 = $this->Count($table,array(array('COUNT','*','num')),$where);
					$option['pagebar']->Count = GetValueInArrayByCurrent($this->db->Query($s2),'num');
				}
				$option['pagebar']->Count=(int)$option['pagebar']->Count;
				$option['pagebar']->make();
			}
		}
		return $sqls . $sqlw . $sqlo . $sqll;
	}

	public function Count($table,$count,$where,$option=null){
		$this->ReplacePre($table);

		$sqlc="SELECT ";

		if(!empty($count)) {
			foreach ($count as $key => $value) {
				$sqlc.=" $value[0]($value[1]) AS $value[2],";
			}
		}
		$sqlc=substr($sqlc, 0,strlen($sqlc)-1);

 		$sqlc.=" FROM $table ";

		if(isset($option['changewhere'])){
			$sqlw=$this->ParseWhere($where,$option['changewhere']);
		}else{
			$sqlw=$this->ParseWhere($where);
		}

		return $sqlc . $sqlw;
	}

	public function Update($table,$keyvalue,$where,$option=null){
		$this->ReplacePre($table);
	
		$sql="UPDATE $table SET ";

		$comma = '';
		foreach ($keyvalue as $k => $v) {
			if(is_null($v))continue;
			$v=$this->db->EscapeString($v);
			$sql.= $comma . "$k = '$v'";
			$comma = ' , ';
		}

		if(isset($option['changewhere'])){
			$sql.=$this->ParseWhere($where,$option['changewhere']);
		}else{
			$sql.=$this->ParseWhere($where);
		}
		return $sql;
	}

	public function Insert($table,$keyvalue){
		$this->ReplacePre($table);

		$sql="INSERT INTO $table ";

		$sql.='(';
		$comma = '';
		foreach($keyvalue as $k => $v) {
			if(is_null($v))continue;
			$sql.= $comma . "$k";
			$comma = ',';
		}
		$sql.=')VALUES(';

		$comma = '';
		foreach($keyvalue as $k => $v) {
			if(is_null($v))continue;
			$v=$this->db->EscapeString($v);
			$sql.= $comma . "'$v'";
			$comma = ',';
		}
		$sql.=')';
		return  $sql;
	}

	public function Delete($table,$where,$option=null){
		$this->ReplacePre($table);

		$sql="DELETE FROM $table ";
		if(isset($option['changewhere'])){
			$sql.=$this->ParseWhere($where,$option['changewhere']);
		}else{
			$sql.=$this->ParseWhere($where);
		}
		return $sql;
	}

	public function Filter($sql){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] + 1;
		
		foreach ($GLOBALS['Filter_Plugin_DbSql_Filter'] as $fpname => &$fpsignal) {
			$fpreturn=$fpname($sql);
			if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {$fpsignal=PLUGIN_EXITSIGNAL_NONE;return $fpreturn;}
		}
		//Logs($sql);
		return $sql;
	}
}
