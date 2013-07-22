<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


function CheckRights($action){

	Logs('$action=' . $action);
	
	if ($GLOBALS['zbp']->user->Level > $GLOBALS['actions'][$action]) {
		return false;
	} else {
		return true;
	}	

}

function ViewList($page,$cate,$auth,$date,$tags){

	foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpname => &$fpsignal) {
		$fpreturn=$fpname($page,$cate,$auth,$date,$tags);
		if ($fpsignal==PLUGIN_EXITSIGNAL_RETURN) {return $fpreturn;}
	}
	
	global $zbp;

	$zbp->title=$zbp->option['ZC_BLOG_SUBTITLE'];
	$html=null;

	if(isset($zbp->templatetags['TEMPLATE_DEFAULT'])){$html=$zbp->templatetags['TEMPLATE_DEFAULT'];}

	foreach ($zbp->templatetags as $key => $value) {
		$html=str_replace('<#' . $key . '#>', $value, $html);
	}

	echo $html;
	return null;

}

function ViewArticle(){


}

function ViewPage(){


}

function Login(){
	global $zbp;


	if (isset($zbp->membersbyname[GetVars('username')])) {
		$m=$zbp->membersbyname[GetVars('username')];
		if($m->Password == md5(GetVars('password') . $m->Guid)){
			if(GetVars('savedate')==0){
				setcookie("username", GetVars('username'));
				setcookie("password", GetVars('password'));
			}else{
				setcookie("username", GetVars('username'), time()+3600*24*GetVars('savedate'));
				setcookie("password", GetVars('password'), time()+3600*24*GetVars('savedate'));
			}
			header('Location:admin/');
		}else{
			throw new Exception($GLOBALS['lang']['ZVA_ErrorMsg'][8]);
		}
	}else{
		throw new Exception($GLOBALS['lang']['ZVA_ErrorMsg'][8]);
		
	}

}


function Logout(){
	global $zbp;
	setcookie("username", '');
	setcookie("password", '');
}

?>