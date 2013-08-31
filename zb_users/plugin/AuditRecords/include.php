<?php


#注册插件
RegisterPlugin("AuditRecords","ActivePlugin_AuditRecords");


function ActivePlugin_AuditRecords() {

	Add_Filter_Plugin('Filter_Plugin_Edit_Response','AuditRecords_Edit_Response');
	Add_Filter_Plugin('Filter_Plugin_PostArticle_Core','AuditRecords_Main');
	Add_Filter_Plugin('Filter_Plugin_Edit_Response3','AuditRecords_Edit_Response3');	
}

$table['AuditRecords']='%pre%AuditRecords';

$datainfo['AuditRecords']=array(
	'ID'=>array('ar_ID','integer','',0),
	'LogID'=>array('ar_LogID','integer','',0),
	'AuthorID'=>array('ar_AuthorID','integer','',0),
	'Logs'=>array('ar_Logs','string','',''),
	'Opeate'=>array('ar_Opeate','integer','',0),
	'PostTime'=>array('ar_PostTime','integer','',0),
);

class AuditRecords extends Base{

	function __construct()
	{
        global $zbp;
		$this->table=&$zbp->table['AuditRecords'];	
		$this->datainfo=&$zbp->datainfo['AuditRecords'];

		foreach ($this->datainfo as $key => $value) {
			$this->Data[$key]=$value[3];
		}

	}
}

function InstallPlugin_AuditRecords(){
	global $zbp;
	AuditRecords_CreateTable();
}

function AuditRecords_CreateTable(){
	global $zbp;
	$s=$zbp->db->sql->CreateTable($GLOBALS['table']['AuditRecords'],$GLOBALS['datainfo']['AuditRecords']);
	$zbp->db->QueryMulit($s);
}


function AuditRecords_Edit_Response(){
	global $zbp;
	global $article;
	if($article->ID==0)return ;

	if($zbp->CheckRights('ArticleAll')){
		echo '<p><b>审核者意见：</b><textarea name="AuditRecords_logs" style="width:100%;height:50px;"></textarea></p>';
	}else{
		if($zbp->user->ID==$article->AuthorID)
			echo '<p><b>发布者意见：</b><textarea name="AuditRecords_logs" style="width:100%;height:40px;"></textarea></p>';
	}
}

function AuditRecords_Main($article){
	global $zbp;
	
	$ar=new AuditRecords;
	$ar->LogID=$article->ID;
	$ar->AuthorID=$zbp->user->ID;
	$ar->Logs=Trim(GetVars('AuditRecords_logs','POST'));
	$ar->Opeate=$article->Status==ZC_POST_STATUS_AUDITING?0:1;
	$ar->PostTime=time();

	if($ar->Logs)$ar->Save();

}

function AuditRecords_Edit_Response3(){
	global $zbp;
	global $article;
	if($article->ID==0)return ;

	$sql=$zbp->db->sql->Select(
		$GLOBALS['table']['AuditRecords'],
		array('*'),
		array(array('=','ar_LogID',$article->ID)),
		array('ar_ID'=>'DESC'),
		null,
		null
	);
	echo '<dl style="padding-left:10px;">';
	echo '<dt><b>审核记录</b></dt>';

	$array=$zbp->GetList('AuditRecords',$sql);
	foreach ($array as $key => $ar) {
		echo '<dd style="text-align:left;padding-bottom:5px;"">';
		if($ar->Opeate){
			echo '<img src="'.$zbp->host.'zb_users/plugin/AuditRecords/thumb_up.png" alt="通过" width="16" />';
		}else{
			echo '<img src="'.$zbp->host.'zb_users/plugin/AuditRecords/thumb_down.png" alt="未通过" width="16" />';
		}
		echo '<b>'.$zbp->GetMemberByID($ar->AuthorID)->Name .'</b>于'.date('Y-m-d',$ar->PostTime).'发布意见:';
		echo '<br/>'.htmlspecialchars($ar->Logs);
		echo '</dd>';
	}

	echo '</dl>';
}

?>