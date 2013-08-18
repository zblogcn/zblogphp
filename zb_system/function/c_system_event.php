<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */





function VerifyLogin(){
	global $zbp;

	if (isset($zbp->membersbyname[GetVars('username','POST')])) {
		if($zbp->Verify_MD5(GetVars('username','POST'),GetVars('password','POST'))){
			$un=GetVars('username','POST');
			$ps=md5($zbp->user->Password . $zbp->path);
			if(GetVars('savedate')==0){
				setcookie("username", $un,0,$zbp->cookiespath);
				setcookie("password", $ps,0,$zbp->cookiespath);
			}else{
				setcookie("username", $un, time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
				setcookie("password", $ps, time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
			}
			return true;
		}else{
			$zbp->ShowError(8);
		}
	}else{
		$zbp->ShowError(8);
	}
}


function Logout(){
	global $zbp;

	setcookie('username', '',time() - 3600,$zbp->cookiespath);
	setcookie('password', '',time() - 3600,$zbp->cookiespath);

}






################################################################################################################
function ViewList($page,$cate,$auth,$date,$tags){
	global $zbp;
	foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($page,$cate,$auth,$date,$tags);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
	}

	$type='index';
	$template=$zbp->option['ZC_INDEX_DEFAULT_TEMPLATE'];
	$page=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
	$articles_top=array();
	$articles=array();

	if($type=='index' && $page==1){
		$articles_top=$zbp->GetArticleList(
			array('*'),
			array(array('=','log_Istop',1),array('=','log_Status',0)),
			array('log_PostTime'=>'DESC'),
			null,
			null
		);
	}


	$pagebar=new Pagebar($zbp->option['ZC_INDEX_REGEX']);
	$pagebar->PageCount=$zbp->displaycount;
	$pagebar->PageNow=$page;
	$pagebar->PageBarCount=$zbp->pagebarcount;
	$pagebar->UrlRule->Rules['{%page%}']=$page;

	$articles=$zbp->GetArticleList(
		array('*'),
		array(array('=','log_Istop',0),array('=','log_Status',0)),
		array('log_PostTime'=>'DESC'),
		array(($pagebar->PageNow-1) * $pagebar->PageCount,$pagebar->PageCount),
		array('pagebar'=>$pagebar)
	);

	if($type=='index'&&$page==1){$zbp->title=$zbp->subname;}

	$zbp->template->SetTags('title',$zbp->title);
	$zbp->template->SetTags('articles',array_merge($articles_top,$articles));
	$zbp->template->SetTags('pagebar',$pagebar);
	$zbp->template->SetTags('type',$type);
	$zbp->template->SetTags('page',$page);
	$zbp->template->SetTags('header',$zbp->header);
	$zbp->template->SetTags('footer',$zbp->footer);

	$zbp->template->display($template);

}





function ViewPost($id,$alias){
	global $zbp;
	foreach ($GLOBALS['Filter_Plugin_ViewPost'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($id,$alias);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
	}

	$w=array();

	if($id!==null){
		$w[]=array('=','log_ID',$id);
	}elseif($alias!==null){
		$w[]=array('array',array(array('log_Alias',$alias),array('log_Title',$alias)));
	}else{
		$zbp->ShowError(2);
		die();
	}

	$w[]=array('=','log_Status',0);
	$articles=$zbp->GetPostList(
		array('*'),
		$w,
		null,
		array(1),
		null
	);
	if(count($articles)==0){
		$zbp->ShowError(2);
		die();
	}

	$article = $articles[0];
	if($zbp->option['ZC_COMMENT_TURNOFF']){
		$article->IsLock = true;
	}

	if($article->Type==0){
		$zbp->LoadTagsByIDString($article->Tag);
	}

	$article->ViewNums +=1;
	$sql = $zbp->db->sql->Update("Post",array('log_ViewNums'=>$article->ViewNums),array(array('=','log_ID',$article->ID)));
	$zbp->db->Update($sql);


	$pagebar=new Pagebar('javascript:GetComments(\''.$article->ID.'\',\'{%page%}\')',false);
	$pagebar->PageCount=$zbp->commentdisplaycount;
	$pagebar->PageNow=1;
	$pagebar->PageBarCount=$zbp->pagebarcount;

	$comments=array();

	$comments=$zbp->GetCommentList(
		array('*'),
		array(array('=','comm_LogID',$article->ID),array('=','comm_RootID',0),array('=','comm_IsChecking',0)),
		array('comm_ID'=>($zbp->option['ZC_COMMENT_REVERSE_ORDER']?'DESC':'ASC')),
		array(($pagebar->PageNow-1) * $pagebar->PageCount,$pagebar->PageCount),
		array('pagebar'=>$pagebar)
	);
	$rootid=array();
	foreach ($comments as &$comment) {
		$rootid[]=array('comm_RootID',$comment->ID);
	}
	$comments2=$zbp->GetCommentList(
		array('*'),
		array(array('array',$rootid),array('=','comm_IsChecking',0)),
		array('comm_ID'=>($zbp->option['ZC_COMMENT_REVERSE_ORDER']?'DESC':'ASC')),
		null,
		null
	);

	foreach ($comments as &$comment){
		$comment->Content=TransferHTML($comment->Content,'[enter]') . '<label id="AjaxComment'.$comment->ID.'"></label>';
	}
	foreach ($comments2 as &$comment){
		$comment->Content=TransferHTML($comment->Content,'[enter]') . '<label id="AjaxComment'.$comment->ID.'"></label>';
	}

	$zbp->template->SetTags('title',$article->Title);
	$zbp->template->SetTags('article',$article);
	$zbp->template->SetTags('type',$article->type=0?'article':'page');
	$zbp->template->SetTags('page',1);
	if($pagebar->PageAll==0||$pagebar->PageAll==1)$pagebar=null;
	$zbp->template->SetTags('pagebar',$pagebar);
	$zbp->template->SetTags('comments',$comments);
	$zbp->template->SetTags('header',$zbp->header);
	$zbp->template->SetTags('footer',$zbp->footer);

	$zbp->template->display($article->Template);
}





function ViewComments($postid,$page){
	global $zbp;

	$post = New Post;
	$post->LoadInfoByID($postid);
	$page=$page==0?1:$page;
	$template='comments';

	$pagebar=new Pagebar('javascript:GetComments(\''.$post->ID.'\',\'{%page%}\')');
	$pagebar->PageCount=$zbp->commentdisplaycount;
	$pagebar->PageNow=$page;
	$pagebar->PageBarCount=$zbp->pagebarcount;

	$comments=array();

	$comments=$zbp->GetCommentList(
		array('*'),
		array(array('=','comm_LogID',$post->ID),array('=','comm_RootID',0),array('=','comm_IsChecking',0)),
		array('comm_ID'=>($zbp->option['ZC_COMMENT_REVERSE_ORDER']?'DESC':'ASC')),
		array(($pagebar->PageNow-1) * $pagebar->PageCount,$pagebar->PageCount),
		array('pagebar'=>$pagebar)
	);
	$rootid=array();
	foreach ($comments as $comment) {
		$rootid[]=array('comm_RootID',$comment->ID);
	}
	$comments2=$zbp->GetCommentList(
		array('*'),
		array(array('array',$rootid),array('=','comm_IsChecking',0)),
		array('comm_ID'=>($zbp->option['ZC_COMMENT_REVERSE_ORDER']?'DESC':'ASC')),
		null,
		null
	);

	foreach ($comments as &$comment){
		$comment->Content=TransferHTML($comment->Content,'[enter]') . '<label id="AjaxComment'.$comment->ID.'"></label>';
	}
	foreach ($comments2 as &$comment){
		$comment->Content=TransferHTML($comment->Content,'[enter]') . '<label id="AjaxComment'.$comment->ID.'"></label>';
	}

	$zbp->template->SetTags('title',$zbp->title);
	$zbp->template->SetTags('article',$post);
	$zbp->template->SetTags('type','comment');
	$zbp->template->SetTags('page',$page);
	if($pagebar->PageAll==1)$pagebar=null;
	$zbp->template->SetTags('pagebar',$pagebar);
	$zbp->template->SetTags('comments',$comments);

	$zbp->template->display($template);

}





function ViewComment($id){
	global $zbp;

	$template='comment';
	$comment=$zbp->GetCommentByID($id);
	$post=new Post;
	$post->LoadInfoByID($comment->LogID);

	$comment->Content=TransferHTML($comment->Content,'[enter]') . '<label id="AjaxComment'.$comment->ID.'"></label>';

	$zbp->template->SetTags('title',$zbp->title);
	$zbp->template->SetTags('comment',$comment);
	$zbp->template->SetTags('article',$post);
	$zbp->template->SetTags('type','comment');
	$zbp->template->SetTags('page',1);

	$zbp->template->display($template);

}







################################################################################################################
function PostArticle(){
	global $zbp;
	if(!isset($_POST['ID']))return ;

	if(isset($_POST['Tag'])){
		$_POST['Tag']=CheckUnsetTagAndConvertIDString($_POST['Tag']);
	}
	if(isset($_POST['Content'])){
		$_POST['Content']=str_replace('<hr class="more" />', '<!--more-->', $_POST['Content']);
		$_POST['Content']=str_replace('<hr class="more"/>', '<!--more-->', $_POST['Content']);
		if(strpos($_POST['Content'], '<!--more-->')!==false){
			$_POST['Intro']=GetValueInArray(explode('<!--more-->',$_POST['Content']),0);
		}else{
			if(isset($_POST['Intro'])&&$_POST['Intro']==''){
				$_POST['Intro']=substr($_POST['Content'], 0,250);
				if(strpos($_POST['Intro'],'<')!==false){
					$_POST['Intro']=CloseTags($_POST['Intro']);
				}
			}
		}
	}

	if(!isset($_POST['AuthorID'])){
		$_POST['AuthorID']=$zbp->user->ID;
	}else{
		if(($_POST['AuthorID']!=$zbp->user->ID )&&(!$zbp->CheckRights('ArticleAll'))){
			$_POST['AuthorID']=$zbp->user->ID;
		}
	}

	if(isset($_POST['PostTime'])){
		$_POST['PostTime']=strtotime($_POST['PostTime']);
	}

	$article = new Post();
	$pre_author=null;
	$pre_tag=null;
	$pre_category=null;
	if(GetVars('ID','POST') == 0){
		if(!$zbp->CheckRights('ArticlePub')){$article->Status=ZC_POST_STATUS_AUDITING;}
	}else{
		$article->LoadInfoByID(GetVars('ID','POST'));
		if(($article->AuthorID!=$zbp->user->ID )&&(!$zbp->CheckRights('ArticleAll'))){$zbp->ShowError(11);}
		if((!$zbp->CheckRights('ArticlePub'))&&($article->Status==ZC_POST_STATUS_AUDITING)){unset($_POST['Status']);}
		$pre_author=$article->AuthorID;
		$pre_tag=$article->Tag;
		$pre_category=$article->CateID;
	}
	$article->Type = ZC_POST_TYPE_ARTICLE;

	if(isset($_POST['Title'])   ) $article->Title    = GetVars('Title','POST');
	if(isset($_POST['Content']) ) $article->Content  = GetVars('Content','POST');
	if(isset($_POST['Alias'])   ) $article->Alias    = GetVars('Alias','POST');
	if(isset($_POST['Tag'])     ) $article->Tag      = GetVars('Tag','POST');
	if(isset($_POST['Intro'])   ) $article->Intro    = GetVars('Intro','POST');
	if(isset($_POST['CateID'])  ) $article->CateID   = GetVars('CateID','POST');
	if(isset($_POST['Template'])) $article->Template = GetVars('Template','POST');
	if(isset($_POST['Status'])  ) $article->Status   = GetVars('Status','POST');
	if(isset($_POST['AuthorID'])) $article->AuthorID = GetVars('AuthorID','POST');
	if(isset($_POST['PostTime'])) $article->PostTime = GetVars('PostTime','POST');
	if(isset($_POST['IsTop'])   ) $article->IsTop    = GetVars('IsTop','POST');
	if(isset($_POST['IsLock'])  ) $article->IsLock   = GetVars('IsLock','POST');


	FilterArticle($article);
	FilterMeta($article);

	$article->Save();

	CountCategoryArrayString($pre_tag . $article->Tag);
	CountMemberArray(array($pre_author,$article->AuthorID));
	CountCategoryArray(array($pre_category,$article->CateID));

	return true;
}


function DelArticle(){
	global $zbp;

	$id=(int)GetVars('id','GET');

	$article = new Post();
	$article->LoadInfoByID($id);
	if($article->ID>0){

		if(!$zbp->CheckRights('ArticleAll')&&$article->AuthorID!=$zbp->user->ID)$zbp->ShowError(22);

		$pre_author=$article->AuthorID;
		$pre_tag=$article->Tag;
		$pre_category=$article->CateID;

		$article->Del();

		CountCategoryArrayString($pre_tag);
		CountMemberArray(array($pre_author));
		CountCategoryArray(array($pre_category));

	}else{
		
	}
	return true;
}



function CheckUnsetTagAndConvertIDString($tagnamestring){
	global $zbp;
	$s='';
	$tagnamestring=str_replace(';', ',', $tagnamestring);
	$tagnamestring=str_replace('，', ',', $tagnamestring);
	$tagnamestring=str_replace('、', ',', $tagnamestring);
	$tagnamestring=trim($tagnamestring);
	if($tagnamestring=='')return '';
	if($tagnamestring==',')return '';		
	$a=explode(',', $tagnamestring);
	$b=array_unique($a);
	$b=array_slice($b, 0, 20);
	$c=array();

	$t=$zbp->LoadTagsByNameString(GetVars('Tag','POST'));
	foreach ($t as $key => $value) {
		$c[]=$key;
	}
	$d=array_diff($b,$c);
	if($zbp->CheckRights('TagNew')){
		foreach ($d as $key) {
			$tag = new Tag;
			$tag->Name = $key;
			$tag->Save();
			$zbp->tags[$tag->ID]=$tag;
			$zbp->tagsbyname[$tag->Name]=&$zbp->tags[$tag->ID];
		}
	}

	foreach ($a as $key) {
		if(!isset($zbp->tagsbyname[$key]))continue;
		$s .= '{' . $zbp->tagsbyname[$key]->ID . '}';
	}
	return $s;
}




################################################################################################################
function PostPage(){
	global $zbp;
	if(!isset($_POST['ID']))return ;

	if(isset($_POST['PostTime'])){
		$_POST['PostTime']=strtotime($_POST['PostTime']);
	}	

	if(!isset($_POST['AuthorID'])){
		$_POST['AuthorID']=$zbp->user->ID;
	}else{
		if(($_POST['AuthorID']!=$zbp->user->ID )&&(!$zbp->CheckRights('PageAll'))){
			$_POST['AuthorID']=$zbp->user->ID;
		}
	}

	$article = new Post();
	$pre_author=null;
	if(GetVars('ID','POST') == 0){
	}else{
		$article->LoadInfoByID(GetVars('ID','POST'));
		if(($article->AuthorID!=$zbp->user->ID )&&(!$zbp->CheckRights('PageAll'))){$zbp->ShowError(11);}
		$pre_author=$article->AuthorID;
	}
	$article->Type = ZC_POST_TYPE_PAGE;

	if(isset($_POST['Title'])   ) $article->Title    = GetVars('Title','POST');
	if(isset($_POST['Content']) ) $article->Content  = GetVars('Content','POST');
	if(isset($_POST['Alias'])   ) $article->Alias    = GetVars('Alias','POST');
	if(isset($_POST['Template'])) $article->Template = GetVars('Template','POST');
	if(isset($_POST['Status'])  ) $article->Status   = GetVars('Status','POST');
	if(isset($_POST['AuthorID'])) $article->AuthorID = GetVars('AuthorID','POST');
	if(isset($_POST['PostTime'])) $article->PostTime = GetVars('PostTime','POST');
	if(isset($_POST['IsLock'])  ) $article->IsLock   = GetVars('IsLock','POST');

	FilterArticle($article);
	FilterMeta($article);

	$article->Save();

	CountMemberArray(array($pre_author,$article->AuthorID));

	return true;
}

function DelPage(){
	global $zbp;

	$id=(int)GetVars('id','GET');

	$article = new Post();
	$article->LoadInfoByID($id);
	if($article->ID>0){

		if(!$zbp->CheckRights('PageAll')&&$article->AuthorID!=$zbp->user->ID)$zbp->ShowError(22);

		$pre_author=$article->AuthorID;

		$article->Del();

		CountMemberArray(array($pre_author));

	}else{
		
	}
	return true;
}










################################################################################################################
function PostComment(){
	global $zbp;

	$_POST['LogID'] = $_GET['postid'];

	$replyid=(integer)GetVars('replyid','POST');

	if($replyid==0){
		$_POST['RootID'] = 0;
		$_POST['ParentID'] = 0;
	}else{
		$_POST['ParentID'] = $replyid;
		$c = new Comment();
		$c->LoadInfoByID($replyid);
		if($c->Level==3){
			$zbp->ShowError(52);
		}
		if($c->RootID==0){
			$_POST['RootID'] = $c->ID;
		}else{
			$_POST['RootID'] = $c->RootID;
		}
	}

	$_POST['AuthorID'] = $zbp->user->ID;
	$_POST['Name'] = $_POST['name'];
	$_POST['Email'] = $_POST['email'];	
	$_POST['HomePage'] = $_POST['homepage'];
	$_POST['Content'] = $_POST['content'];	
	$_POST['PostTime'] = Time();
	$_POST['IP'] = GetGuestIP();	
	$_POST['Agent'] = GetGuestAgent();

	$cmt = new Comment();

	$cmt->LogID        = GetVars('LogID','POST');
	$cmt->RootID       = GetVars('RootID','POST');
	$cmt->ParentID     = GetVars('ParentID','POST');
	$cmt->AuthorID     = GetVars('AuthorID','POST');
	$cmt->Name         = GetVars('Name','POST');
	$cmt->Email        = GetVars('Email','POST');
	$cmt->HomePage     = GetVars('HomePage','POST');
	$cmt->Content      = GetVars('Content','POST');	
	$cmt->PostTime     = GetVars('PostTime','POST');
	$cmt->IP           = GetVars('IP','POST');
	$cmt->Agent        = GetVars('Agent','POST');	


	FilterComment($cmt);

	$cmt->Save();

	CountPostArray(array($cmt->LogID));

	$zbp->comments[$cmt->ID]=$cmt;
	
	if(GetVars('isajax','POST')){
		ViewComment($cmt->ID);
	}

	return true;
}


function DelComment(){

}

function DelComment_Children($id){

}


function CheckComment(){
	global $zbp;

	$id=(int)GetVars('id','GET');
	$ischecking=(bool)GetVars('ischecking','GET');

	$cmt = $zbp->GetCommentByID($id);
	$cmt->IsChecking=$ischecking;

	$cmt->Save();
}





################################################################################################################
function PostCategory(){
	global $zbp;
	if(!isset($_POST['ID']))return ;

	$parentid=(int)GetVars('ParentID','POST');
	if($parentid>0){
		if($zbp->categorys[$parentid]->Level>2){
			$_POST['ParentID']='0';
		}
	}

	$cate = new Category();
	if(GetVars('ID','POST') == 0){
	}else{
		$cate->LoadInfoByID(GetVars('ID','POST'));
	}

	if(isset($_POST['Name'])       ) $cate->Name        = GetVars('Name','POST');
	if(isset($_POST['Order'])      ) $cate->Order       = GetVars('Order','POST');
	if(isset($_POST['Alias'])      ) $cate->Alias       = GetVars('Alias','POST');
	if(isset($_POST['ParentID'])   ) $cate->ParentID    = GetVars('ParentID','POST');
	if(isset($_POST['Template'])   ) $cate->Template    = GetVars('Template','POST');
	if(isset($_POST['LogTemplate'])) $cate->LogTemplate = GetVars('LogTemplate','POST');

	FilterMeta($cate);

	CountCategory($cate);

	$cate->Save();
	return true;
}



function DelCategory(){
	global $zbp;

	$id=(int)GetVars('id','GET');
	$cate=$zbp->GetCategoryByID($id);
	if($cate->ID>0){
		$cate->Del();
	}
	return true;
}









################################################################################################################
function PostTag(){
	global $zbp;
	if(!isset($_POST['ID']))return ;

	$tag = new Tag();
	if(GetVars('ID','POST') == 0){
	}else{
		$tag->LoadInfoByID(GetVars('ID','POST'));
	}
	if(isset($_POST['Name'])    ) $tag->Name     = GetVars('Name','POST');
	if(isset($_POST['Alias'])   ) $tag->Alias    = GetVars('Alias','POST');
	if(isset($_POST['Template'])) $tag->Template = GetVars('Template','POST');

	FilterMeta($tag);

	CountTag($tag);

	$tag->Save();

	return true;
}


function DelTag(){
	global $zbp;

	$tagid=(int)GetVars('id','GET');
	$tag=$zbp->GetTagByID($tagid);
	if($tag->ID>0){
		$tag->Del();
	}
	return true;
}







################################################################################################################
function PostMember(){
	global $zbp;
	if(!isset($_POST['ID']))return ;

	if(!$zbp->CheckRights('MemberAll')){
		unset($_POST['Level']);
	}
	if(isset($_POST['Password'])){
		if($_POST['Password']==''){
			unset($_POST['Password']);
		}else{
			$_POST['Password']=Member::GetPassWordByGuid($_POST['Password'],$_POST['Guid']);
		}
	}

	$mem = new Member();
	if(GetVars('ID','POST') == 0){
		if(isset($_POST['Password'])==false||$_POST['Password']==''){
			$zbp->ShowError(73);
		}
	}else{
		$mem->LoadInfoByID(GetVars('ID','POST'));
	}
	if(isset($_POST['Name'])    ) $mem->Name     = GetVars('Name','POST');
	if(isset($_POST['Alias'])   ) $mem->Alias    = GetVars('Alias','POST');
	if(isset($_POST['Guid'])    ) $mem->Guid     = GetVars('Guid','POST');
	if(isset($_POST['Email'])   ) $mem->Email    = GetVars('Email','POST');
	if(isset($_POST['HomePage'])) $mem->HomePage = GetVars('HomePage','POST');
	if(isset($_POST['Template'])) $mem->Template = GetVars('Template','POST');
	if(isset($_POST['Level'])   ) $mem->Level    = GetVars('Level','POST');
	if(isset($_POST['Intro'])   ) $mem->Intro    = GetVars('Intro','POST');	
	if(isset($_POST['Password'])   ) $mem->Password    = GetVars('Password','POST');

	FilterMeta($mem);
	FilterMember($mem);

	CountMember($mem);

	$mem->Save();
	return true;
}

function DelMember(){
	global $zbp;

	$id=(int)GetVars('id','GET');
	$m=$zbp->GetMemberByID($id);
	if($m->ID>0 && $m->ID<>$zbp->user->ID){
		$m->Del();
		DelMember_AllData($id);
	}else{
		return false;
	}
	return true;
}


function DelMember_AllData($id){

}





################################################################################################################
function PostModule(){
	global $zbp;
	if(!isset($_POST['ID']))return ;
	if(!GetVars('FileName','POST')){
		$_POST['FileName']='mod' . rand(1000,2000);
	}else{
		$_POST['FileName']=strtolower($_POST['FileName']);
	}
	if(!GetVars('HtmlID','POST')){
		$_POST['HtmlID']=$_POST['FileName'];
	}
	if(isset($_POST['MaxLi'])){
		$_POST['MaxLi']=(integer)$_POST['MaxLi'];
	}
	if(!isset($_POST['Type'])){
		$_POST['Type']='div';
	}	
	if(isset($_POST['Content'])){
		if($_POST['Type']!='div'){
			$_POST['Content']=str_replace(array("\r","\n"), array('',''), $_POST['Content']);
		}
	}
	if(isset($_POST['Source'])){
		if($_POST['Source']=='theme'){
			$c=GetVars('Content','POST');
			$f=$zbp->usersdir . 'theme/' . $zbp->theme . '/include/' . GetVars('FileName','POST') . '.php';
			@file_put_contents($f, $c);
			return true;
		}
	}
	$mod = new Module();
	if(GetVars('ID','POST') == 0){

	}else{
		$mod->LoadInfoByID(GetVars('ID','POST'));
	}
	if(isset($_POST['Name'])          ) $mod->Name          = GetVars('Name','POST');
	if(isset($_POST['Type'])          ) $mod->Type          = GetVars('Type','POST');	
	if(isset($_POST['FileName'])      ) $mod->FileName      = GetVars('FileName','POST');
	if(isset($_POST['HtmlID'])        ) $mod->HtmlID        = GetVars('HtmlID','POST');
	if(isset($_POST['MaxLi'])         ) $mod->MaxLi         = GetVars('MaxLi','POST');
	if(isset($_POST['Content'])       ) $mod->Content       = GetVars('Content','POST');
	if(isset($_POST['Source'])        ) $mod->Source        = GetVars('Source','POST');
	if(isset($_POST['IsHiddenTitle']) ) $mod->IsHiddenTitle = GetVars('IsHiddenTitle','POST');

	FilterModule($mod);

	$mod->Save();
	return true;
}

function DelModule(){
	global $zbp;

	$id=(int)GetVars('id','GET');
	$m=$zbp->GetModuleByID($id);
	if($m->Source<>'system'){
		$m->Del();
	}else{
		return false;
	}
	return true;
}








################################################################################################################
function PostUpload(){
	global $zbp;

	foreach ($_FILES as $key => $value) {
		if($_FILES[$key]['error']==0){
			if (is_uploaded_file($_FILES[$key]['tmp_name'])) {
				$tmp_name = $_FILES[$key]['tmp_name'];
				$name = $_FILES[$key]['name'];

				$upload = new Upload;
				$upload->Name = $_FILES[$key]['name'];
				$upload->SourceName = $_FILES[$key]['name'];
				$upload->MimeType = $_FILES[$key]['type'];
				$upload->Size = $_FILES[$key]['size'];
				$upload->AuthorID = $zbp->user->ID;

				$upload->SaveFile($_FILES[$key]['tmp_name']);
				$upload->Save();
			}
		}

	}

	CountMemberArray(array($upload->AuthorID));

}

function DelUpload(){
	global $zbp;

	$id=(int)GetVars('id','GET');
	$u=$zbp->GetUploadByID($id);
	if($zbp->CheckRights('UploadAll')||(!$zbp->CheckRights('UploadAll')&&$u->AuthorID==$zbp->user->ID)){
		$u->Del();
		CountMemberArray(array($u->AuthorID));
		@unlink($u->FullFile);
	}else{
		return false;
	}
	return true;
}







################################################################################################################
function EnablePlugin($name){
	global $zbp;
	$zbp->option['ZC_USING_PLUGIN_LIST']=AddNameInString($zbp->option['ZC_USING_PLUGIN_LIST'],$name);
	$zbp->SaveOption();
	return $name;
}

function DisablePlugin($name){
	global $zbp;
	$zbp->option['ZC_USING_PLUGIN_LIST']=DelNameInString($zbp->option['ZC_USING_PLUGIN_LIST'],$name);
	$zbp->SaveOption();
}

function SetTheme($theme,$style){
	global $zbp;

	$zbp->option['ZC_BLOG_THEME']=$theme;
	$zbp->option['ZC_BLOG_CSS']=$style;

	$zbp->BuildTemplate();

	$zbp->SaveOption();
}

function SetSidebar(){
	global $zbp;

	$zbp->option['ZC_SIDEBAR_ORDER'] =trim(GetVars('sidebar','POST'),'|');
	$zbp->option['ZC_SIDEBAR2_ORDER']=trim(GetVars('sidebar2','POST'),'|');
	$zbp->option['ZC_SIDEBAR3_ORDER']=trim(GetVars('sidebar3','POST'),'|');
	$zbp->option['ZC_SIDEBAR4_ORDER']=trim(GetVars('sidebar4','POST'),'|');
	$zbp->option['ZC_SIDEBAR5_ORDER']=trim(GetVars('sidebar5','POST'),'|');	
	$zbp->SaveOption();
}


function SaveSetting(){
	global $zbp;

	foreach ($_POST as $key => $value) {
		if(substr($key,0,2)!=='ZC')continue;
		if($key=='ZC_PERMANENT_DOMAIN_ENABLE'
		 ||$key=='ZC_DEBUG_MODE'
		 ||$key=='ZC_COMMENT_TURNOFF'
		 ||$key=='ZC_COMMENT_REVERSE_ORDER_EXPORT'
		){
			$zbp->option[$key]=(boolean)$value;
			continue;
		}
		if($key=='ZC_RSS2_COUNT'
		 ||$key=='ZC_UPLOAD_FILESIZE'
		 ||$key=='ZC_DISPLAY_COUNT'
		 ||$key=='ZC_SEARCH_COUNT'
		 ||$key=='ZC_PAGEBAR_COUNT'
		 ||$key=='ZC_COMMENTS_DISPLAY_COUNT'
		 ||$key=='ZC_MANAGE_COUNT'
		){
			$zbp->option[$key]=(integer)$value;
			continue;
		}		
		$zbp->option[$key]=trim(str_replace(array("\r","\n"),array("",""),$value));
	}
	$zbp->SaveOption();
}









################################################################################################################
function FilterMeta(&$object){

	//$type=strtolower(get_class($object));

	foreach ($_POST as $key => $value) {
		if(substr($key,0,5)=='meta_'){
			$name=substr($key,5-strlen($key));
			$object->Metas->$name=$value;
		}
	}

	foreach ($object->Metas->Data as $key => $value) {
		if($value=="")unset($object->Metas->Data[$key]);
	}

}


function FilterComment(&$comment){
	global $zbp;

	if(!CheckRegExp($comment->Name,'[username]')){
		$zbp->ShowError(15);
	}
	if($comment->Email && (!CheckRegExp($comment->Email,'[email]'))){
		$zbp->ShowError(29);
	}
	if($comment->HomePage && (!CheckRegExp($comment->HomePage,'[homepage]'))){
		$zbp->ShowError(30);
	}

	$comment->Name=substr($comment->Name, 0,20);
	$comment->Email=substr($comment->Email, 0,50);
	$comment->HomePage=substr($comment->HomePage, 0,250);

	$comment->Content=TransferHTML($comment->Content,'[nohtml]');

	$comment->Content=substr($comment->Content, 0,1000);
	$comment->Content=trim($comment->Content);
	if(strlen($comment->Content)==0){
		$zbp->ShowError(46);
	}
}


function FilterArticle(&$article){
	global $zbp;

	if($article->Type == ZC_POST_TYPE_ARTICLE){
		if(!$zbp->CheckRights('ArticleAll')){
			$article->Content=TransferHTML($article->Content,'[noscript]');
			$article->Intro=TransferHTML($article->Intro,'[noscript]');
		}
	}elseif($article->Type == ZC_POST_TYPE_PAGE){
		if(!$zbp->CheckRights('PageAll')){
			$article->Content=TransferHTML($article->Content,'[noscript]');
			$article->Intro=TransferHTML($article->Intro,'[noscript]');
		}
	}
}


function FilterMember(&$member){
	global $zbp;
	$member->Intro=TransferHTML($member->Intro,'[noscript]');
}


function FilterModule(&$module){
	global $zbp;
	$module->FileName=TransferHTML($module->FileName,'[filename]');
	$module->HtmlID=TransferHTML($module->HtmlID,'[normalname]');	
}



################################################################################################################
#统计函数
function CountPost(&$article){
	global $zbp;

	$id=$article->ID;

	$s=$zbp->db->sql->Count('Comment',array('comm_ID'=>'num'),array(array('=','comm_LogID',$id),array('=','comm_IsChecking',0)));
	$num=GetValueInArray(current($zbp->db->Query($s)),'num');

	$article->CommNums=$num;
}

function CountPostArray($array){
	global $zbp;
	$array=array_unique($array);
	foreach ($array as $value) {
		if($value==0)continue;
		$article=new Post;
		$article->LoadInfoByID($value);
		CountPost($article);
		$article->Save();
	}
}

function CountCategory(&$category){
	global $zbp;

	$id=$category->ID;

	$s=$zbp->db->sql->Count('Post',array('log_ID'=>'num'),array(array('=','log_CateID',$id)));
	$num=GetValueInArray(current($zbp->db->Query($s)),'num');

	$category->Count=$num;
}

function CountCategoryArray($array){
	global $zbp;
	$array=array_unique($array);
	foreach ($array as $value) {
		if($value==0)continue;
		CountMember($zbp->categorys[$value]);
		$zbp->categorys[$value]->Save();
	}
}

function CountTag(&$tag){
	global $zbp;

	$id=$tag->ID;

	$s=$zbp->db->sql->Count('Post',array('log_ID'=>'num'),array(array('LIKE','log_Tag','%{'.$id.'}%')));
	$num=GetValueInArray(current($zbp->db->Query($s)),'num');

	$tag->Count=$num;
}

function CountCategoryArrayString($string){
	global $zbp;
	$array=$zbp->LoadTagsByIDString($string);
	foreach ($array as &$tag) {
		CountTag($tag);
		$tag->Save();
	}	
}

function CountMember(&$member){
	global $zbp;

	$id=$member->ID;

	$s=$zbp->db->sql->Count('Post',array('log_ID'=>'num'),array(array('=','log_AuthorID',$id),array('=','log_Type',0)));
	$member_Articles=GetValueInArray(current($zbp->db->Query($s)),'num');

	$s=$zbp->db->sql->Count('Post',array('log_ID'=>'num'),array(array('=','log_AuthorID',$id),array('=','log_Type',1)));
	$member_Pages=GetValueInArray(current($zbp->db->Query($s)),'num');

	$s=$zbp->db->sql->Count('Comment',array('comm_ID'=>'num'),array(array('=','comm_AuthorID',$id)));
	$member_Comments=GetValueInArray(current($zbp->db->Query($s)),'num');

	$s=$zbp->db->sql->Count('Upload',array('ul_ID'=>'num'),array(array('=','ul_AuthorID',$id)));
	$member_Uploads=GetValueInArray(current($zbp->db->Query($s)),'num');

	$member->Articles=$member_Articles;
	$member->Pages=$member_Pages;
	$member->Comments=$member_Comments;
	$member->Uploads=$member_Uploads;
}

function CountMemberArray($array){
	global $zbp;
	$array=array_unique($array);
	foreach ($array as $value) {
		if($value==0)continue;
		CountMember($zbp->members[$value]);
		$zbp->members[$value]->Save();
	}	
}

?>