<?php
/**
 * 数据库操作接口
 *
 * @package Z-BlogPHP
 * @subpackage Interface/DataBase 类库
 */
interface iDataBase {

	public function Open($array);

	public function Close();

	public function Query($query);

	public function Insert($query);

	public function Update($query);

	public function Delete($query);

	public function QueryMulti($s);

	public function EscapeString($s);

	public function CreateTable($table,$datainfo);

	public function DelTable($table);

	public function ExistTable($table);

}



/**
* SQL语句生成类
 * @package Z-BlogPHP
 * @subpackage ClassLib/DataBase
*/
class DbSql
{
	/**
	* @var null 数据库连接实例
	*/
	private $db=null;
	/**
	* @var null|string 数据库类型名称
	*/
	private $dbclass=null;
	/**
	* @param object $db
	*/
	function __construct(&$db=null)
	{
		$this->db=&$db;
		$this->dbclass=get_class($this->db);
	}
	/**
	 * 替换数据表前缀
	 * @param string $
	 * @return string
	 */
	public function ReplacePre(&$s){
		$s=str_replace('%pre%', $this->db->dbpre, $s);
		return $s;
	}
	
	/**
	 * 删除表,返回SQL语句
	 * @param string $table
	 * @return string
	 */
	public function DelTable($table){
		$this->ReplacePre($table);

		$s='';
		$s="DROP TABLE $table;";
		if($this->dbclass=='Dbpdo_PgSQL'||$this->dbclass=='DbPgSQL'){
			$s.="DROP SEQUENCE $table" . "_seq;";
		}
		return $s;
	}

	/**
	 * 检查表是否存在，返回SQL语句
	* @param string $table
	* @param string $dbname
	* @return string
	*/
	public function ExistTable($table,$dbname=''){
		$this->ReplacePre($table);

		$s='';
		if($this->dbclass=='DbSQLite'||$this->dbclass=='DbSQLite3'){
			$s="SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table'";
		}
		if($this->dbclass=='Dbpdo_MySQL'||$this->dbclass=='DbMySQL'||$this->dbclass=='DbMySQLi'){
			$s="SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='$dbname' AND TABLE_NAME='$table'";
		}
		if($this->dbclass=='Dbpdo_PgSQL'||$this->dbclass=='DbPgSQL'){
			$s="SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public' AND  table_name ='$table'";
		}
		return $s;
	}

	/**
	 * 创建表，返回构造完整的SQL语句
	 * @param string $table
	 * @param array $datainfo
	 * @return string
	*/
	public function CreateTable($table,$datainfo,$engine=null){
	
		reset($datainfo);
		$idname=GetValueInArrayByCurrent($datainfo,0);

		$s='';

		if($this->dbclass=='DbSQLite' || $this->dbclass=='DbSQLite3' || $this->dbclass=='Dbpdo_SQLite'){
			$s.='CREATE TABLE '.$table.' (';

			$i=0;
			foreach ($datainfo as $key => $value) {
				if($value[1]=='integer'){
					if($i==0){
						$s.=$value[0] .' integer primary key' . ($this->dbclass=='DbSQLite'?'':' autoincrement') . ',';
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
					$s.=$value[0] . " $value[1] NOT NULL DEFAULT 0" . ',';
				}
				if($value[1]=='date'||$value[1]=='datetime'){
					$s.=$value[0] . " $value[1] NOT NULL,";
				}
				if($value[1]=='timestamp'){
					$s.=$value[0] . " $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP,";
				}
				$i +=1;
			}
			$s=substr($s,0,strlen($s)-1);

			$s.=');';
			reset($datainfo);
			$s.='CREATE UNIQUE INDEX ' . $table . '_' . $idname.' on '.$table.' ('.$idname.');';

		}

		if($this->dbclass=='Dbpdo_MySQL'||$this->dbclass=='DbMySQL'||$this->dbclass=='DbMySQLi'){
			$s.='CREATE TABLE IF NOT EXISTS '.$table.' (';

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
					$s.=$value[0] . " $value[1] NOT NULL DEFAULT 0" . ',';
				}
				if($value[1]=='date'||$value[1]=='time'||$value[1]=='datetime'){
					$s.=$value[0] . " $value[1] NOT NULL,";
				}
				if($value[1]=='timestamp'){
					$s.=$value[0] . " $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,";
				}
				$i +=1;
			}
			$s.='PRIMARY KEY ('.$idname.'),';
			$s=substr($s,0,strlen($s)-1);
			$myengtype=$this->db->dbengine;
			if($engine!=null)$myengtype=$engine;
			if(!$myengtype)$myengtype='MyISAM';
			$s.=') ENGINE=' . $myengtype . ' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
		}

		if($this->dbclass=='Dbpdo_PgSQL'||$this->dbclass=='DbPgSQL'){
			$s.='CREATE SEQUENCE ' . $table . '_seq;';
			$s.='CREATE TABLE '.$table.' (';

			$i=0;
			foreach ($datainfo as $key => $value) {
				if($value[1]=='integer'){
					if($i==0){
						$s.=$value[0] .' INT NOT NULL DEFAULT nextval(\'' . $table. '_seq\')' . ',';
					}else{
						if($value[2]==''){
							$s.=$value[0] .' integer NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='tinyint'){
							$s.=$value[0] .' integer NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='smallint'){
							$s.=$value[0] .' smallint NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='mediumint'){
							$s.=$value[0] .' integer NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='int'){
							$s.=$value[0] .' integer NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}elseif($value[2]=='bigint'){
							$s.=$value[0] .' bigint NOT NULL DEFAULT \''.$value[3].'\'' . ',';
						}
					}
				}
				if($value[1]=='boolean'){
					$s.=$value[0] . ' char(1) NOT NULL DEFAULT \''.(int)$value[3].'\'' . ',';
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
				if($value[1]=='double'){
					$s.=$value[0] . " double precision NOT NULL DEFAULT 0" . ',';
				}
				if($value[1]=='float'){
					$s.=$value[0] . " real NOT NULL DEFAULT 0" . ',';
				}				
				if($value[1]=='date'||$value[1]=='time'){
					$s.=$value[0] . " $value[1] NOT NULL,";
				}
				if($value[1]=='datetime'){
					$s.=$value[0] . " time NOT NULL,";
				}				
				if($value[1]=='timestamp'){
					$s.=$value[0] . " $value[1] NOT NULL DEFAULT CURRENT_TIMESTAMP,";
				}
				$i +=1;
			}
			$s.='PRIMARY KEY ('.$idname.'),';
			$s=substr($s,0,strlen($s)-1);
			
			$s.=')';
			$s.='CREATE INDEX ' . $table . '_ix_id on ' . $table .'('.$idname.');';
		}
		
		$this->ReplacePre($s);
		return $s;
	}


	/**
	 * 构造条件查询语句
	 * @param array $where
	 * @param null $changewhere 是否更改'WHERE'，放空表示不更改，如设为'like'等将替换'WHERE'
	 * @return null|string 返回构造的语句
	 */
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
			if($eq=='='|$eq=='<'|$eq=='>'|$eq=='LIKE'|$eq=='<>'|$eq=='<='|$eq=='>='|$eq=='NOT LIKE'|$eq=='ILIKE'|$eq=='NOT ILIKE'){
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
				$sqlw .= $comma .  ' '. $w[1] .' '. $eq .' (' . $sql_array . ') ';
			}
			if($eq=='META_NAME'){
				if(count($w)!=3)continue;
				$sql_array='';
				$sql_meta='s:' . strlen($w[2]) . ':"'.$w[2].'";';	
				$sql_meta=$this->db->EscapeString($sql_meta);
				$sql_array .= "$w[1] LIKE '%$sql_meta%'";
				$sqlw .= $comma .  '(' . $sql_array . ') ';
			}
			if($eq=='META_NAMEVALUE'){
				if(count($w)==4){
					$sql_array='';
					$sql_meta='s:' . strlen($w[2]) . ':"'.$w[2].'";' . 's:' . strlen($w[3]) . ':"'.$w[3].'"';	
					$sql_meta=$this->db->EscapeString($sql_meta);
					$sql_array .= "$w[1] LIKE '%$sql_meta%'";
					$sqlw .= $comma .  '(' . $sql_array . ') ';
				}elseif(count($w)==5){
					$sql_array='';
					$sql_meta='s:' . strlen($w[2]) . ':"'.$w[2].'";' . $w[3];	
					$sql_meta=$this->db->EscapeString($sql_meta);
					$sql_array .= "$w[1] LIKE '%$sql_meta%'";
					$sqlw .= $comma .  '(' . $sql_array . ') ';
				}
			}
			if($eq=='CUSTOM'){
				$sqlw .= $comma . ' ' . $w[1] . ' ';
			}
			$comma = 'AND';
		}

		return $sqlw;
	}

	/**
	 * 构造查询语句
	 * @param string $table
	 * @param string $select
	 * @param string $where
	 * @param string $order
	 * @param string $limit
	 * @param array|null $option
	 * @return string 返回构造的语句
	 */
	public function Select($table,$select=null,$where=null,$order=null,$limit=null,$option=null){
		$this->ReplacePre($table);

		$sqlp='SELECT ';
		$sqls='';
		$sqlw='';
		$sqlg='';
		$sqlh='';
		$sqlo='';
		$sqll='';
		
		if(is_array($option)==false)$option=array();
		$option=array_change_key_case($option);
		
		if(isset($option['sql_no_cache'])){
			$sqlp.= 'SQL_NO_CACHE ';
		}
		if(isset($option['sql_cache'])){
			$sqlp.= 'SQL_CACHE ';
		}
		if(isset($option['sql_buffer_result'])){
			$sqlp.= 'SQL_BUFFER_RESULT ';
		}

		if(isset($option['select2count'])){
			$sqls = $sqlp;
			if(is_array($select)) {
				foreach ($select as $key => $value) {
					if(count($value)==3)
						$sqls .= "$value[0]($value[1]) AS $value[2],";
					if(count($value)==2)
						$sqls .= "$value[0]($value[1]),";
				}
				$sqls=substr($sqls, 0,strlen($sqls)-1);
			}else{
				$sqls .= $select;
			}
		}else{
			if(!empty($select)){
				if(is_array($select)){
					$selectstr=implode($select,',');
					if(trim($selectstr)=='')$selectstr='*';
					$sqls="{$sqlp} {$selectstr} ";
				}else{
					if(trim($sqls)=='')$sqls='*';
					$sqls="{$sqlp} {$select} ";
				}
			}else{
				$sqls="{$sqlp} *";
			}
		}
		$sqls .= " FROM $table ";

		if(isset($option['useindex'])){
			if(is_array($option['useindex'])){
				$sqls.='USE INDEX ('.implode($option['useindex'],',').') ';
			}else{
				$sqls.='USE INDEX ('.$option['useindex'].') ';
			}
		}
		if(isset($option['forceindex'])){
			if(is_array($option['forceindex'])){
				$sqls.='FORCE INDEX ('.implode($option['forceindex'],',').') ';
			}else{
				$sqls.='FORCE INDEX ('.$option['forceindex'].') ';
			}
		}
		if(isset($option['ignoreindex'])){
			if(is_array($option['ignoreindex'])){
				$sqls.='IGNORE INDEX ('.implode($option['ignoreindex'],',').') ';
			}else{
				$sqls.='IGNORE INDEX ('.$option['ignoreindex'].') ';
			}
		}

		if(!empty($where)){
			if(isset($option['changewhere'])){
				$sqlw=$this->ParseWhere($where,$option['changewhere']);
			}else{
				$sqlw=$this->ParseWhere($where);
			}
		}
		
		if(isset($option['groupby'])){
			$sqlg=' GROUP BY ';
			$comma = '';
			if(!is_array($option['groupby'])){
				$sqlg .= $option['groupby'];
			}else{
				foreach($option['groupby'] as $k=>$v) {
					$sqlg .= $comma ."$v";
					$comma = ',';
				}
			}
		}

		if(isset($option['having'])){
			$sqlh=' HAVING ';
			$comma = '';
			if(!is_array($option['having'])){
				$sqlh .= $option['having'];
			}else{
				$sqlh .= $this->ParseWhere($option['having'],'');
			}
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
		return $sqls . $sqlw . $sqlg . $sqlh . $sqlo . $sqll;
	}

	/**
	 * 构造计数语句
	 * @param string $table
	 * @param string $count
	 * @param string $where
	 * @param null $option
	 * @return string 返回构造的语句
	 */
	public function Count($table,$count,$where=null,$option=null){
		$this->ReplacePre($table);

		if(is_array($option)==false)$option=array();
		$option['select2count']=true;

		return $this->Select($table,$count,$where,null,null,$option);
	}

	/**
	 * 构造数据更新语句
	* @param string $table
	* @param string $keyvalue
	* @param string $where
	* @param array|null $option
	* @return string 返回构造的语句
	*/
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

	/**
	 * 构造数据插入语句
	* @param string $table
	* @param string $keyvalue
	* @return string 返回构造的语句
	*/
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

	/**
	 * 构造数据删除语句
	 * @param string $table
	 * @param string $where
	 * @param array|null $option
	 * @return string 返回构造的语句
	 */
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

	/**
	 * 返回经过过滤的SQL语句
	 * @param $sql
	 * @return mixed
	 */
	public function Filter($sql){
		$_SERVER['_query_count'] = $_SERVER['_query_count'] + 1;
		
		foreach ($GLOBALS['Filter_Plugin_DbSql_Filter'] as $fpname => &$fpsignal) {
			$fpname($sql);
		}
		//Logs($sql);
		return $sql;
	}

	/**
	 * 导出sql生成语句，用于备份数据用。
	 * @param $type 数据连接类型
	 * @return mixed
	 */
	private $_explort_db = null;
	public function Export($table,$keyvalue,$type='mysql'){

		if($type=='mysql' && $this->_explort_db === null)$this->_explort_db= new DbMySQL;
		if($type=='mysqli' && $this->_explort_db === null)$this->_explort_db= new DbMySQLi;
		if($type=='pdo_mysql' && $this->_explort_db === null)$this->_explort_db= new Dbpdo_MySQL;
		if($type=='sqlite' && $this->_explort_db === null)$this->_explort_db= new DbSQLite;
		if($type=='sqlite3' && $this->_explort_db === null)$this->_explort_db= new DbSQLite3;
		if($type=='pdo_sqlite' && $this->_explort_db === null)$this->_explort_db= new Dbpdo_SQLite;
		if($this->_explort_db === null)$this->_explort_db= new DbMySQL;
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
			$v=$this->_explort_db->EscapeString($v);
			$sql.= $comma . "'$v'";
			$comma = ',';
		}
		$sql.=')';
		
		return  $sql . ";\r\n";
	}
}
