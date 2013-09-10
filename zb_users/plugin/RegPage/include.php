<?php


#注册插件
RegisterPlugin("RegPage","ActivePlugin_RegPage");


function ActivePlugin_RegPage() {

	Add_Filter_Plugin('Filter_Plugin_Index_Begin','RegPage_Main');
}

$RegPage_Table='%pre%RegPage';

$RegPage_DataInfo=array(
	'ID'=>array('reg_ID','integer','',0),
	'InviteCode'=>array('reg_InviteCode','string',50,''),
	'Level'=>array('reg_Level','integer','',5),
	'AuthorID'=>array('reg_AuthorID','integer','',0),
	'IsUsed'=>array('reg_IsUsed','boolean','',false),
	'Intro'=>array('reg_Intro','string','',''),
);

function InstallPlugin_RegPage(){
	global $zbp;

	if(!$zbp->Config('RegPage')->default_level){
		$zbp->Config('RegPage')->default_level=5;
		$zbp->Config('RegPage')->open_reg=0;
		$zbp->SaveConfig('RegPage');

		RegPage_CreateTable();
		RegPage_CreateCode(100);
	}
	
}

function RegPage_CreateCode($n){
	global $zbp;

	for ($i=0; $i < 100; $i++) { 
		$r = new Base($GLOBALS['RegPage_Table'],$GLOBALS['RegPage_DataInfo']);
		$r->InviteCode=GetGuid();
		$r->Level=$zbp->Config('RegPage')->default_level;

		$r->Save();
	}
	
}

function RegPage_DelUsedCode(){
	global $zbp;

	$sql = $zbp->db->sql->Delete($GLOBALS['RegPage_Table'],array(array('<>','reg_AuthorID',0)));
	$zbp->db->Delete($sql);
}


function RegPage_EmptyCode(){
	global $zbp;

	$sql = $zbp->db->sql->Delete($GLOBALS['RegPage_Table'],null);
	$zbp->db->Delete($sql);
}

function RegPage_CreateTable(){
	global $zbp;
	$s=$zbp->db->sql->CreateTable($GLOBALS['RegPage_Table'],$GLOBALS['RegPage_DataInfo']);
	$zbp->db->QueryMulit($s);
}

function RegPage_Main(){
	global $zbp;

	if(isset($_GET['reg'])){
		RegPage_Page();
		die();
	}
	
}


function RegPage_Page(){

	global $zbp;
	
	$zbp->header .='<script src="'.$zbp->host.'zb_users/plugin/RegPage/reg.js" type="text/javascript"></script>' . "\r\n";

	$article = new Post;
	$article->Title='会员注册';
	$article->IsLock=true;
	$article->Type=ZC_POST_TYPE_PAGE;

	$article->Content .='<dl style="font-size:1.1em;line-height:1.5em;">';
	$article->Content .='<dt>以下带星号为必填选项.</dt>';
	$article->Content .='<dt>&nbsp;</dt>';
	$article->Content .='<dd><p style="width:350px;text-align:right;">(*)名称：<input required="required" type="text" name="name" style="width:200px;font-size:1.2em;" </p></dd>';
	$article->Content .='<dd><p style="width:350px;text-align:right;">(*)密码：<input required="required" type="password" name="password" style="width:200px;font-size:1.2em;" /></p></dd>';
	$article->Content .='<dd><p style="width:350px;text-align:right;">(*)确认密码：<input required="required" type="password" name="repassword" style="width:200px;font-size:1.2em;" /></p></dd>';
	$article->Content .='<dd><p style="width:350px;text-align:right;">邮箱：<input type="text" name="email" style="width:200px;font-size:1.2em;" /></p></dd>';
	$article->Content .='<dd><p style="width:350px;text-align:right;">网站：<input type="text" name="homepage" style="width:200px;font-size:1.2em;" /></p></dd>';
	$article->Content .='<dd><p style="width:350px;text-align:right;">(*)邀请码：<input required="required" type="text" name="invitecode" style="width:200px;font-size:1.2em;" /></p></dd>';

	if($zbp->Config('RegPage')->open_reg){
		$article->Content .='<dd><p style="width:350px;text-align:right;">点击<a href="'.$zbp->host.'zb_users/plugin/RegPage/getinvitecode.php" target="_blank">这里</a>获取邀请码.</p></dd>';
	}
	
	$article->Content .='<dd><p style="width:350px;text-align:right;">'.$zbp->Config('RegPage')->readme_text.'</dd>';
	
	$article->Content .='<dd><p style="width:350px;text-align:right;"><input type="submit" style="width:100px;font-size:1.0em;padding:0.2em" value="提交" onclick="return RegPage()" /></p></dd>';

	$article->Content .='</dl>';

	$zbp->template->SetTags('title',$article->Title);
	$zbp->template->SetTags('article',$article);
	$zbp->template->SetTags('type',$article->type=0?'article':'page');
	$zbp->template->SetTemplate($article->Template);

	$zbp->template->Display();
}


?>