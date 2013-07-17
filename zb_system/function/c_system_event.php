<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-06-14
 */


function CheckRights($action){
	ZBlogException::Log('$action=' . $action);
	if ($GLOBALS['zbp']->user->Level > $GLOBALS['actions'][$action]) {
		return false;
	} else {
		return true;
	}
}

function ViewList($page,$cate,$auth,$date,$tags){

	foreach ($GLOBALS['Filter_Plugin_ViewList_Begin'] as $fpk => &$fpv) {
		$fpr=$fpk($page,$cate,$auth,$date,$tags);
		if ($fpv==PLUGIN_EXITSIGNAL_RETURN) {return $fpr;}
	}

	$zbp=$GLOBALS['zbp'];

	echo $page;

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
			throw new Exception("用户密码错误！");
		}
	}else{
		throw new Exception("用户不存在！");
		
	}

}

?>