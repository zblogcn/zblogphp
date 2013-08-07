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
	
	$pagebar=new Pagebar();
	$pagebar->PageCount=5;
	$pagebar->PageNow=(int)GetVars('page','GET')==0?1:(int)GetVars('page','GET');
	$pagebar->PageBarCount=10;
	$pagebar->UrlRule='{%host%}?page={%page%}';

	$articles=$zbp->GetArticleList(
		array('*'),
		array(array('=','log_Istop',0)),
		array('log_PostTime'=>'DESC'),
		array(($pagebar->PageNow-1) * $pagebar->PageCount,$pagebar->PageCount),
		array('pagebar'=>$pagebar)
	);

	$zbp->template->SetTags('title',$pagebar->PageNow);
	$zbp->template->SetTags('articles',$articles);
	$zbp->template->SetTags('pagebar',$pagebar);

	$zbp->template->display($zbp->option['ZC_INDEX_DEFAULT_TEMPLATE']);

}

function ViewPost($id,$alias){
	global $zbp;


	$article = new Post;
	$article->LoadInfoByID($id);

	$zbp->template->SetTags('title',$article->Title);
	$zbp->template->SetTags('article',$article);

	$zbp->template->display($zbp->option['ZC_ARTICLE_DEFAULT_TEMPLATE']);
}








function Login(){
	global $zbp;

	if (isset($zbp->membersbyname[GetVars('username','POST')])) {
		$m=$zbp->membersbyname[GetVars('username','POST')];
		if($m->Password == md5(GetVars('password','POST') . $m->Guid)){
			if(GetVars('savedate')==0){
				setcookie("username", GetVars('username','POST'),0,$zbp->cookiespath);
				setcookie("password", GetVars('password','POST'),0,$zbp->cookiespath);
			}else{
				setcookie("username", GetVars('username','POST'), time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
				setcookie("password", GetVars('password','POST'), time()+3600*24*GetVars('savedate','POST'),$zbp->cookiespath);
			}
			header('Location:admin/');
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

	$article = new Post();

	if(GetVars('ID','POST') == 0){
	}else{
		$article->LoadInfoByID(GetVars('ID','POST'));
	}

	if(isset($_POST['Type'])    ) $article->Type     = GetVars('Type','POST');
	if(isset($_POST['Title'])   ) $article->Title    = GetVars('Title','POST');
	if(isset($_POST['Content']) ) $article->Content  = GetVars('Content','POST');
	if(isset($_POST['Alias'])   ) $article->Alias    = GetVars('Alias','POST');
	if(isset($_POST['Tag'])     ) $article->Tag      = GetVars('Tag','POST');
	if(isset($_POST['Intro'])   ) $article->Intro    = GetVars('Intro','POST');
	if(isset($_POST['CateID'])  ) $article->CateID   = GetVars('CateID','POST');
	if(isset($_POST['Template'])) $article->Template = GetVars('Template','POST');
	if(isset($_POST['Status'])  ) $article->Status   = GetVars('Status','POST');
	if(isset($_POST['AuthorID'])) $article->AuthorID = GetVars('AuthorID','POST');
	if(isset($_POST['PostTime'])) $article->PostTime = strtotime(GetVars('PostTime','POST'));
	if(isset($_POST['IsTop'])   ) $article->IsTop    = GetVars('IsTop','POST');
	if(isset($_POST['IsLock'])  ) $article->IsLock   = GetVars('IsLock','POST');

	$article->Save();
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

}


function DelTag(){
	global $zbp;

	$tagid=(int)GetVars('id','GET');
	$tag=$zbp->GetTagByID($tagid);
	if($tag->ID>0){
		$tag->Del();
	}

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
	$zbp->SaveOption();

	$zbp->LoadTemplates();
	$zbp->BuildTemplate();
}


?>