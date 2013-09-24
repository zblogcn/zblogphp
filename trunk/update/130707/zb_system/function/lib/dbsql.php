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
			$i +=1;
		}
		reset($datainfo);
		$s.='PRIMARY KEY ('.GetValueInArrayByCurrent($datainfo,0).'),';
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
			$i +=1;
		}
		$s=substr($s,0,strlen($s)-1);

		$s.=');';
		reset($datainfo);
		$s.='CREATE UNIQUE INDEX %pre%'.GetValueInArrayByCurrent($datainfo,0).' on '.$tablename.' ('.GetValueInArrayByCurrent($datainfo,0).');';

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
			$i +=1;
		}
		$s=substr($s,0,strlen($s)-1);

		$s.=');';
		reset($datainfo);
		$s.='CREATE UNIQUE INDEX %pre%'.GetValueInArray($datainfo,0).' on '.$tablename.' ('.GetValueInArray($datainfo,0).');';
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
						$sql_search .= $c . " ($x LIKE '%$y%') ";
						$c='OR';
					}
					$sqlw .= $comma .  '(' . $sql_search . ')';
				}
				if($eq=='array'){
					$c='';
					$sql_array='';
					if(!is_array($w[1]))continue;
					if(count($w[1])==0)continue;
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
					$option['pagebar']->Count = GetValueInArrayByCurrent($zbp->db->Query($s2),'num');
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

?>