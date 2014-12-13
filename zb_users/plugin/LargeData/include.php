<?php

#注册插件
RegisterPlugin("LargeData","ActivePlugin_LargeData");

function ActivePlugin_LargeData() {
	Add_Filter_Plugin('Filter_Plugin_Misc_Begin','LargeData_Misc_Begin');
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

	$all_artiles = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Post'], 'log_ID AS num', array(array('=', 'log_Type', '0')), array('log_ID' => 'DESC'), 1, null)), 'num');
	$all_pages = (int)$all_artiles - (int)$zbp->cache->normal_article_nums;
	$all_categorys = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Category'], 'cate_ID AS num', null, array('cate_ID' => 'DESC'), 1, null)), 'num');
	$all_comments = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Comment'], 'comm_ID AS num', null, array('comm_ID' => 'DESC'), 1, null)), 'num');
	$all_views = 0;
	$all_tags = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Tag'], 'tag_ID AS num', null, array('tag_ID' => 'DESC'), 1, null)), 'num');
	$all_members = GetValueInArrayByCurrent($zbp->db->Query($zbp->db->sql->Select($zbp->table['Member'], 'mem_ID AS num', null, array('mem_ID' => 'DESC'), 1, null)), 'num');

	$current_theme = '{$zbp->theme}';
	$current_style = '{$zbp->style}';
	$current_member = '{$zbp->user->Name}';
	$system_environment = '{$system_environment}';

	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_member']}" . '' . "</td><td class='td30'>{$current_member}</td><td class='td20'>{$zbp->lang['msg']['current_version']}</td><td class='td30'>{$current_version}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_artiles']}" . '(估算)' . "</td><td>{$all_artiles}</td><td>{$zbp->lang['msg']['all_categorys']}" . '(估算)' . "</td><td>{$all_categorys}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_pages']}" . '(估算)' . "</td><td>{$all_pages}</td><td>{$zbp->lang['msg']['all_tags']}" . '(估算)' . "</td><td>{$all_tags}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['all_comments']}" . '(估算)' . "</td><td>{$all_comments}</td><td>{$zbp->lang['msg']['all_views']}" . '(不计算)' . "</td><td>{$all_views}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['current_theme']}/{$zbp->lang['msg']['current_style']}</td><td>{$current_theme}/{$current_style}</td><td>{$zbp->lang['msg']['all_members']}" . '(估算)' . "</td><td>{$all_members}</td></tr>";
	$r .= "<tr><td class='td20'>{$zbp->lang['msg']['xmlrpc_address']}</td><td>{$xmlrpc_address}</td><td>{$zbp->lang['msg']['system_environment']}</td><td>{$system_environment}</td></tr>";
	$r .="<script type=\"text/javascript\">$('#statistic').next('small').remove();$('#statistic').after('<small> 更新时间：" . date ( "c" , $zbp->cache->reload_statistic_time ) . "</small>');</script>";
	
	$zbp->LoadConfigs();
	$zbp->LoadCache();
	$zbp->cache->reload_statistic = $r;
	$zbp->cache->reload_statistic_time = time();
	$zbp->cache->system_environment = $system_environment;
	//$zbp->SaveCache();
	CountNormalArticleNums();

	$zbp->AddBuildModule('statistics', array($all_artiles, $all_pages, $all_categorys, $all_tags, $all_views, $all_comments));
	$zbp->BuildModule();

	$r = str_replace('{#ZC_BLOG_HOST#}', $zbp->host, $r);
	$r = str_replace('{$zbp->user->Name}', $zbp->user->Name, $r);
	$r = str_replace('{$zbp->theme}', $zbp->theme, $r);
	$r = str_replace('{$zbp->style}', $zbp->style, $r);
	$r = str_replace('{$system_environment}', GetEnvironment(), $r);

	echo $r;
}


?>