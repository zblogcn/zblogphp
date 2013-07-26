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
				setcookie("username", GetVars('username'),0,$zbp->cookiespath);
				setcookie("password", GetVars('password'),0,$zbp->cookiespath);
			}else{
				setcookie("username", GetVars('username'), time()+3600*24*GetVars('savedate'),$zbp->cookiespath);
				setcookie("password", GetVars('password'), time()+3600*24*GetVars('savedate'),$zbp->cookiespath);
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
	setcookie("username", "",time() - 3600,$zbp->cookiespath);
	setcookie("password", "",time() - 3600,$zbp->cookiespath);
}

function Reload(){
	global $zbp;
	
	$qs=GetVars('QUERY_STRING','SERVER');
	$r=null;



	if(strpos($qs,'statistic')){

		$xmlrpc_address=$zbp->host . 'zb_system/xml-rpc/';
		$current_member=$zbp->user->Name;
		$current_version=$zbp->option['ZC_BLOG_VERSION'];
		$all_artiles=GetValueInArray(current($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Log'] . ' WHERE log_Type=0')),'num');
		$all_pages=GetValueInArray(current($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Log'] . ' WHERE log_Type=0')),'num');	
		$all_categorys=GetValueInArray(current($zbp->db->Query('SELECT COUNT(cate_ID) AS num FROM ' . $GLOBALS['table']['Category'])),'num');
		$all_comments=GetValueInArray(current($zbp->db->Query('SELECT COUNT(comm_ID) AS num FROM ' . $GLOBALS['table']['Comment'])),'num');
		$all_views=GetValueInArray(current($zbp->db->Query('SELECT SUM(log_ViewNums) AS num FROM ' . $GLOBALS['table']['Log'])),'num');
		$all_tags=GetValueInArray(current($zbp->db->Query('SELECT COUNT(tag_ID) as num FROM ' . $GLOBALS['table']['Tag'])),'num');
		$all_members=GetValueInArray(current($zbp->db->Query('SELECT COUNT(mem_ID) AS num FROM ' . $GLOBALS['table']['Member'])),'num');
		$current_theme=$zbp->option['ZC_BLOG_THEME'];
		$current_style=$zbp->option['ZC_BLOG_CSS'];

		$r .= "<tr><td width='20%'>{$zbp->lang['msg']['current_member']}</td><td width='30%'>{$current_member}</td><td width='20%'>{$zbp->lang['msg']['current_version']}</td><td width='30%'>{$current_version}</td></tr>";
		$r .= "<tr><td>{$zbp->lang['msg']['all_artiles']}</td><td>{$all_artiles}</td><td>{$zbp->lang['msg']['all_categorys']}</td><td>{$all_categorys}</td></tr>";
		$r .= "<tr><td>{$zbp->lang['msg']['all_pages']}</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}</td><td>{$all_tags}</td></tr>";
		$r .= "<tr><td>{$zbp->lang['msg']['all_comments']}</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}</td><td>{$all_views}</td></tr>";
		$r .= "<tr><td>{$zbp->lang['msg']['current_theme']}/{$zbp->lang['msg']['current_style']}</td><td>{$current_theme}/{$current_style}</td><td>{$zbp->lang['msg']['all_members']}</td><td>{$all_members}</td></tr>";
		$r .= "<tr><td>{$zbp->lang['msg']['xmlrpc_address']}</td><td>{$xmlrpc_address}</td><td></td><td></td></tr>";		

		$zbp->SetCache('reload_statistic',$r);
		$zbp->SaveCache();


	}
	if(strpos($qs,'updateinfo')){
		#$r = file_get_contents($zbp->option['ZC_UPDATE_INFO_URL']);
		$r = file_get_contents('http://www.baidu.com/robots.txt');
		$r = '<tr><td>' . $r . '</td></tr>';

		$zbp->SetCache('reload_updateinfo',$r);
		$zbp->SaveCache();
	}
	echo $r;

}

?>