<?php

#注册插件
RegisterPlugin("LargeData","ActivePlugin_LargeData");

function ActivePlugin_LargeData() {
	global $zbp;
	if($zbp->option['ZC_LARGE_DATA'] == true && $zbp->db->type == 'mysql'){
		Add_Filter_Plugin('Filter_Plugin_Misc_Begin','LargeData_Misc_Begin');
		Add_Filter_Plugin('Filter_Plugin_Zbp_Load','LargeData_Zbp_Begin');
		Add_Filter_Plugin('Filter_Plugin_LargeData_Aritcle','LargeData_LargeData_Aritcle');
		Add_Filter_Plugin('Filter_Plugin_LargeData_Page','LargeData_LargeData_Page');
		Add_Filter_Plugin('Filter_Plugin_LargeData_Comment','LargeData_LargeData_Comment');		
	}
}

function LargeData_LargeData_Aritcle(&$select,&$where,&$order,&$limit,&$option){
	global $zbp;
	foreach($where as $k=>$v){
		if($v[0]=='search'){
			$s=end($v);
			$where=array(array('like','log_Title',$s . '%'));
			continue;
		}
	}
	$w=$where;
	$w[]=array('=','log_Type','0');
	$s=$zbp->db->sql->Select($zbp->table['Post'],$zbp->datainfo['Post']['ID'][0],$w,$order,$limit,null);
	$array = $zbp->db->Query($s);
	$a = array();
	if(count($array)>0){
		foreach($array as $k=>$v){
			$a[]=$v[$zbp->datainfo['Post']['ID'][0]];
		}
		if( array_key_exists('pagebar' ,  $option)){
			if(count($w)==1){
				$option['pagebar']->Count=$zbp->cache->all_article_nums;
			}
			if($option['pagebar']->Count===null){
				$s=$zbp->db->sql->Select($zbp->table['Post'],'COUNT('.$zbp->datainfo['Post']['ID'][0].')',$w,$order,null,null);
				$c = $zbp->db->Query($s);
				$option['pagebar']->Count=(int)current($c[0]);
			}
		}
		$where = array(array('IN','log_ID',implode(',',$a)));
		$limit = null;
		$order = array('log_PostTime' => 'DESC');
	}else{
		$where = array(array('IN','log_ID','0'));
		$limit = null;
		$order = null;
		if( array_key_exists('pagebar' ,  $option)){
			$option['pagebar']->Count=0;
		}
	}
}

function LargeData_LargeData_Page(&$select,&$where,&$order,&$limit,&$option){
	global $zbp;
	foreach($where as $k=>$v){
		if($v[0]=='search'){
			$s=end($v);
			$where=array(array('like','log_Title',$s . '%'));
			continue;
		}
	}
	$w=$where;
	$w[]=array('=','log_Type','1');
	$s=$zbp->db->sql->Select($zbp->table['Post'],$zbp->datainfo['Post']['ID'][0],$w,$order,$limit,null);
	$array = $zbp->db->Query($s);
	$a = array();
	if(count($array)>0){
		foreach($array as $k=>$v){
			$a[]=$v[$zbp->datainfo['Post']['ID'][0]];
		}
		if( array_key_exists('pagebar' ,  $option)){
			if(count($w)==1){
				$option['pagebar']->Count=$zbp->cache->all_page_nums;
			}
			if($option['pagebar']->Count===null){
				$s=$zbp->db->sql->Select($zbp->table['Post'],'COUNT('.$zbp->datainfo['Post']['ID'][0].')',$w,$order,null,null);
				$c = $zbp->db->Query($s);
				$option['pagebar']->Count=(int)current($c[0]);
			}
		}
		$where = array(array('IN','log_ID',implode(',',$a)));
		$limit = null;
		$order = array('log_PostTime' => 'DESC');
	}else{
		$where = array(array('IN','log_ID','0'));
		$limit = null;
		$order = null;
		if( array_key_exists('pagebar' ,  $option)){
			$option['pagebar']->Count=0;
		}
	}
}

function LargeData_LargeData_Comment(&$select,&$where,&$order,&$limit,&$option){
	global $zbp;
	foreach($where as $k=>$v){
		if($v[0]=='search'){
			//$s=end($v);
			//$where=array(array('like','log_Title',$s . '%'));
			$option['pagebar']->Count=0;
			continue;
		}
	}
	$w=$where;
	$s=$zbp->db->sql->Select($zbp->table['Comment'],$zbp->datainfo['Comment']['ID'][0],$w,$order,$limit,null);
	$array = $zbp->db->Query($s);
	$a = array();
	if(count($array)>0){
		foreach($array as $k=>$v){
			$a[]=$v[$zbp->datainfo['Comment']['ID'][0]];
		}
		if( array_key_exists('pagebar' ,  $option)){
			if($option['pagebar']->Count===null){
				$s=$zbp->db->sql->Select($zbp->table['Comment'],'COUNT('.$zbp->datainfo['Comment']['ID'][0].')',$w,$order,null,null);
				$c = $zbp->db->Query($s);
				$option['pagebar']->Count=(int)current($c[0]);
			}
		}
		$where = array(array('IN','comm_ID',implode(',',$a)));
		$limit = null;
	}else{
		$where = array(array('IN','comm_ID','0'));
		$limit = null;
		$order = null;
		if( array_key_exists('pagebar' ,  $option)){
			$option['pagebar']->Count=0;
		}
	}
}

function LargeData_Zbp_Begin(){
	global $zbp;
	$zbp->modulesbyfilename['archives']->NoRefresh = true;
	$zbp->modulesbyfilename['authors']->NoRefresh  = true;
}

function LargeData_Misc_Begin($type){
	global $zbp;
	if($type=='statistic'){
		if (!$zbp->CheckRights('root')) {
			echo $zbp->ShowError(6, __FILE__, __LINE__);
			die();
		}
		LargeData_Misc_Statistic();
		die();
	}
}

function LargeData_Misc_Statistic() {

	global $zbp;

	$r = null;

	$zbp->BuildTemplate();

	$xmlrpc_address = $zbp->host . 'zb_system/xml-rpc/';
	$current_member = $zbp->user->Name;
	$current_version = $zbp->option['ZC_BLOG_VERSION'];

	$all_artiles = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Post'], 'Count(log_ID) AS num', array(array('=', 'log_Type', '0')), null, 1, null)), 'num');
	$all_pages = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Post'], 'Count(log_ID) AS num', array(array('=', 'log_Type', '1')), null, 1, null)), 'num');
	$all_categorys = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Category'], 'Count(*) AS num', null, null, 1, null)), 'num');
	$all_comments = (int)GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Comment'], 'Count(*) AS num', null, null, 1, null)), 'num');
	$check_comments = (int)GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(*) AS num FROM ' . $GLOBALS['table']['Comment'] . ' WHERE comm_Ischecking=1'), 'num');
	$all_views = '不计算';
	$all_tags = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Tag'], 'Count(*) AS num', null, null, 1, null)), 'num');
	$all_members = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Member'], 'Count(*) AS num', null, null, 1, null)), 'num');

	$current_theme = '{$zbp->theme}';
	$current_style = '{$zbp->style}';
	$current_member = '{$zbp->user->Name}';
	$system_environment = '{$system_environment}';

	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_member']}" . '' . "</td><td class='td30'>{$current_member}</td><td class='td20'>{$zbp->lang['msg']['current_version']}</td><td class='td30'>{$current_version}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_artiles']}" . '' . "</td><td>{$all_artiles}</td><td>{$zbp->lang['msg']['all_categorys']}" . '' . "</td><td>{$all_categorys}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_pages']}" . '' . "</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}" . '' . "</td><td>{$all_tags}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_comments']}" . '' . "</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}" . '' . "</td><td>{$all_views}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_theme']}/{$zbp->lang['msg']['current_style']}</td><td>{$current_theme}/{$current_style}</td><td>{$zbp->lang['msg']['all_members']}" . '' . "</td><td>{$all_members}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['xmlrpc_address']}</td><td>{$xmlrpc_address}</td><td>{$zbp->lang['msg']['system_environment']}</td><td>{$system_environment}</td></tr>";
	$r .="<script type=\"text/javascript\">$('#statistic').next('small').remove();$('#statistic').after('<small> 更新时间：" . date ( "c" , $zbp->cache->reload_statistic_time ) . "</small>');</script>";
	
	$zbp->LoadConfigs();
	$zbp->LoadCache();
	$zbp->cache->reload_statistic = $r;
	$zbp->cache->reload_statistic_time = time();
	$zbp->cache->system_environment = $system_environment;
	$zbp->cache->all_article_nums = $all_artiles;
	$zbp->cache->all_page_nums = $all_pages;
	$zbp->cache->all_comment_nums = $all_comments;
	$zbp->cache->normal_comment_nums = $all_comments - $check_comments;
	CountNormalArticleNums();

	$zbp->AddBuildModule('statistics', array($all_artiles, $all_pages, $all_categorys, $all_tags, $all_views, $all_comments));
	$zbp->BuildModule();
	$zbp->SaveCache();

	$r = str_replace('{#ZC_BLOG_HOST#}', $zbp->host, $r);
	$r = str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
	$r = str_replace('{$zbp->theme}', $zbp->theme, $r);
	$r = str_replace('{$zbp->style}', $zbp->style, $r);
	$r = str_replace('{$system_environment}', GetEnvironment(), $r);

	echo $r;
}


?>