<?php
require '../../../../zb_system/function/c_system_base.php';

$zbp->Load();
$s=file_get_contents($blogpath . 'zb_system/defend/createtable/mysql.sql'). "\r\n\r\n";

foreach($table as $tablename=>$tablevalue){

	$s.=$zbp->db->sql->CreateTable($tablevalue,$datainfo[$tablename]) . ";\r\n\r\n";
	
}


foreach($table as $tablename=>$tablevalue){

	$pl = $zbp->GetListCustom($tablevalue,$datainfo[$tablename],'SELECT * FROM '.$tablevalue);
	foreach($pl as $p){

		$keys=array();
		foreach ($datainfo[$tablename] as $key => $value) {
			if(!is_array($value) || count($value)!=4)continue;
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');
		$data=$p->GetData();

		foreach ($datainfo[$tablename] as $key => $value) {
			if(!is_array($value)|| count($value)!=4)continue;
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$data[$key];
			}elseif($value[1] == 'string'){
				if($key=='Meta'){
					$keyvalue[$value[0]]=$data[$key];
				}else{
					$keyvalue[$value[0]]=str_replace($bloghost,'{#ZC_BLOG_HOST#}',$data[$key]);
				}
			}else{
				$keyvalue[$value[0]]=$data[$key];
			}
		}
		$s .= $zbp->db->sql->Export($tablevalue,$keyvalue);

	}

}





header('Content-Disposition:attachment;filename=export.sql.gz');

echo gzencode ( $s ,  9 );

