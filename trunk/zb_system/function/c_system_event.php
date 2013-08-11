<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


function ViewList($page,$cate,$auth,$date,$tags){
	global $zbp;
	foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($page,$cate,$auth,$date,$tags);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
	}

	$type='index';
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
	$pagebar->PageCount=5;
	$pagebar->PageNow=$page;
	$pagebar->PageBarCount=10;
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

	$zbp->template->display($zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']);

}

function ViewPost($id,$alias){
	global $zbp;

	$articles=$zbp->GetPostList(
		array('*'),
		array(array('=','log_ID',$id),array('=','log_Status',0)),
		null,
		array(1),
		null
	);
	if(count($articles)==0){
		Http404();
		throw new Exception($zbp->lang['error'][9], 1);
	}

	$article =$articles[0];
	if($article->Type==0){
		$zbp->LoadTagsByIDString($article->Tag);
	}

	$zbp->template->SetTags('title',$article->Title);
	$zbp->template->SetTags('article',$article);
	$zbp->template->SetTags('type',$article->type=0?'article':'page');
	$zbp->template->SetTags('page',1);

	$zbp->template->display($zbp->option['ZC_ARTICLE_DEFAULT_TEMPLATE']);
}








function VerifyLogin(){
	global $zbp;

	if (isset($zbp->membersbyname[GetVars('username','POST')])) {
		if($zbp->Verify_MD5(GetVars('username','POST'),GetVars('password','POST'))){
			if(GetVars('savedate')==0){
				setcookie("username", GetVars('username','POST'),0,$zbp->cookiespath);
				setcookie("password", GetVars('password','POST'),0,$zbp->cookiespath);
			}else{
				setcookie("username", GetVars('username','POST'), time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
				setcookie("password", GetVars('password','POST'), time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
			}
			return true;
		}else{
			throw new Exception($GLOBALS['lang']['error'][8]);
		}
	}else{
		throw new Exception($GLOBALS['lang']['error'][8]);
	}
}


function Logout(){
	global $zbp;

	setcookie('username', '',time() - 3600,$zbp->cookiespath);
	setcookie('password', '',time() - 3600,$zbp->cookiespath);

}



function PostArticle(){
	global $zbp;
	if(!isset($_POST['ID']))return ;

	if(isset($_POST['Tag'])){
		$_POST['Tag']=$zbp->CheckUnsetTagAndConvertIDString($_POST['Tag']);
	}
	if(isset($_POST['PostTime'])){
		$_POST['PostTime']=strtotime($_POST['PostTime']);
	}	

	$article = new Post();
	if(GetVars('ID','POST') == 0){
	}else{
		$article->LoadInfoByID(GetVars('ID','POST'));
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

	$article->Save();
	return true;
}


function DelArticle(){
	global $zbp;

	$id=(int)GetVars('id','GET');
	$article = new Post();
	$article->LoadInfoByID($id);
	if($article->ID>0){
		$article->Del();
	}
	return true;
}


function PostPage(){
	global $zbp;
	if(!isset($_POST['ID']))return ;

	if(isset($_POST['PostTime'])){
		$_POST['PostTime']=strtotime($_POST['PostTime']);
	}	

	$article = new Post();
	if(GetVars('ID','POST') == 0){
	}else{
		$article->LoadInfoByID(GetVars('ID','POST'));
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

	$article->Save();
	return true;
}

function DelPage(){
	return DelArticle();
}


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
			$_POST['Password']=md5(md5($_POST['Password']) . $_POST['Guid']);
		}
	}

	$mem = new Member();
	if(GetVars('ID','POST') == 0){
		if(isset($_POST['Password'])==false||$_POST['Password']==''){
			throw new Exception($zbp->lang['error'][73]);
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
	$mem->Save();
	return true;
}

function DelMember(){
	global $zbp;

	$id=(int)GetVars('id','GET');
	$m=$zbp->GetMemberByID($id);
	if($m->ID>0 && $m->ID<>$zbp->user->ID){
		$m->Del();
	}else{
		return false;
	}
	return true;
}


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

	$mod->Save();
	return true;
}

function DelModule(){
	global $zbp;
}



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

}

function DelUpload(){

}

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

	$zbp->theme=$theme;
	$zbp->style=$style;

	$zbp->LoadTemplates();
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
?>