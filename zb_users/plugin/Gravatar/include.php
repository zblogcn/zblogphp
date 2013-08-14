<?php


#注册插件
RegisterPlugin("Gravatar","ActivePlugin_Gravatar");


function ActivePlugin_Gravatar() {

Add_Filter_Plugin('Filter_Plugin_Mebmer_Avatar','Gravatar_Url');

}

function InstallPlugin_Gravatar(){
	global $zbp;
	$zbp->Config('Gravatar')->default_url='http://cn.gravatar.com/avatar/{%emailmd5%}?s=40&d={%source%}';
	$zbp->Config('Gravatar')->source='{%host%}zb_users/avatar/0.png';	
	$zbp->SaveConfig('Gravatar');	
}

function UninstallPlugin_Gravatar(){
	global $zbp;

}


function Gravatar_Url(&$member){
	global $zbp;
	$default_url=$zbp->Config('Gravatar')->default_url;
	$source=$zbp->Config('Gravatar')->source;
	$source=str_replace('{%host%}', $zbp->host, $source);
	
	if($member->Email!==''){
		$GLOBALS['Filter_Plugin_Mebmer_Avatar']['Gravatar_Url']=PLUGIN_EXITSIGNAL_RETURN;
		$s=$default_url;
		$s=str_replace('{%source%}', urlencode($source), $s);
		$s=str_replace('{%emailmd5%}',md5($member->Email), $s);		
		return $s;
	}else{
		return $source;
	}

	return 'sss';

}



?>