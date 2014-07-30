<?php

function convert_article_table($_prefix)
{	
	global $zbp;
	$list = array(
		"`gid`" => "log_ID",
		"`title`" => "log_Title",
		"`date`" => "log_PostTime",
		"`excerpt`" => "log_Intro",
		"`content`" => "log_Content",
		"`alias`" => "log_Alias",
		"`author`" => "log_AuthorID", 
		'IF(`sortid` = -1, 1, `sortid`)' => "log_CateID",
		'IF(`type` =  "blog", 1, 0 )' => "log_Type",
		"`views`" => "log_ViewNums",
		"`comnum`" => "log_CommNums",
		'IF(`top` =  "y", 1, 0 )' => "log_IsTop",
		'IF(`hide` =  "y", 2, 0 )' => "log_Status",
		'""' => "log_Meta"
	);
	
	$ary1 = array(); $ary2 = array();
	foreach($list as $name => $value)
	{
		$ary1[] = $value;
		$ary2[] = $name . ' AS ' . $value;
	}	

	$sql = build_sql('Post', $_prefix . 'blog', $ary1, $ary2);
	return $zbp->db->QueryMulit($sql);

}

function convert_comment_table($_prefix)
{
	global $zbp;
	$list = array(
		'comm_ID' => '`cid`',
		'comm_LogID' => '`gid`',
		'comm_IsChecking' => 'IF(`hide` =  "y", 1, 0 )',
		'comm_RootID' => 0,
		'comm_ParentID' => '`pid`',
		'comm_AuthorID' => 0,
		'comm_Name' => '`poster`',
		'comm_Content' => '`comment`',
		'comm_Email' => '`mail`',
		'comm_HomePage' => '`url`',
		'comm_PostTime' => '`date`',
		'comm_IP' => '`ip`',
		'comm_Agent' => '"Convert from em2zb"',
		'comm_Meta' => '""'
	);
	
	$ary1 = array(); $ary2 = array();
	foreach($list as $name => $value)
	{
		$ary1[] = $name;
		$ary2[] = $value . ' AS ' . $name;
	}
	$sql = build_sql('Comment', $_prefix . 'comment', $ary1, $ary2);

	return $zbp->db->QueryMulit($sql);


}

function convert_attachment_table($_prefix)
{
	global $zbp;
	$list = array(
		'ul_ID' => '`aid`',
		'ul_AuthorID' => 1,
		'ul_Size' => '`filesize`',
		'ul_Name' => '`filename`',
		'ul_SourceName' => '`filename`',
		'ul_MimeType' => '`mimetype`',
		'ul_PostTime' => '`addtime`',
		'ul_DownNums' => 0,
		'ul_LogID' => '`blogid`',
		'ul_Intro' => '""',
		'ul_Meta' => '""'
	);

	$ary1 = array(); $ary2 = array();
	foreach($list as $name => $value)
	{
		$ary1[] = $name;
		$ary2[] = $value . ' AS ' . $name;
	}
	$sql = build_sql('Upload', $_prefix . 'attachment', $ary1, $ary2);

	return $zbp->db->QueryMulit($sql);
}

function convert_category_table($_prefix)
{
	global $zbp;
	$list = array(
		'cate_ID' => '`sid`',
		'cate_Name' => '`sortname`',
		'cate_Order' => '`taxis`',
		'cate_Count' => '0', 
		'cate_Alias' => '`alias`',
		'cate_Intro' => '`description`',
		'cate_RootID' => '`pid`',
		'cate_ParentID' => '`pid`',
		'cate_Template' => '""',
		'cate_LogTemplate' => '""',
		'cate_Meta' => '""',
	);

	$ary1 = array(); $ary2 = array();
	foreach($list as $name => $value)
	{
		$ary1[] = $name;
		$ary2[] = $value . ' AS ' . $name;
	}
	$sql = build_sql('Category', $_prefix . 'sort', $ary1, $ary2);

	return $zbp->db->QueryMulit($sql);
}

function convert_tag_table($_prefix)
{
	global $zbp;
	$list = array(
		'tag_ID' => '`tid`',
		'tag_Name' => '`tagname`',
		'tag_Order' => '0',
		'tag_Count' => '0',
		'tag_Alias' => '""', 
		'tag_Intro' => '`gid`',
		'tag_Template' => '""',
		'tag_Meta' => '""'
	);

	$ary1 = array(); $ary2 = array();
	foreach($list as $name => $value)
	{
		$ary1[] = $name;
		$ary2[] = $value . ' AS ' . $name;
	}
	$sql = build_sql('Tag', $_prefix . 'tag', $ary1, $ary2);

	return $zbp->db->QueryMulit($sql);
}

function convert_user_table($_prefix)
{
	global $zbp;
	
	$list = array(
		'mem_ID' => '`uid`',
		'mem_Guid' => '"' . $zbp->user->Guid . '"', //Todo: Guid
		'mem_Level' => 'IF(`role` =  "admin", 1, 4 )', 
		'mem_Status' => '0',
		'mem_Name' => '`username`',
		'mem_Password' => '"' . $zbp->user->Password .'"', //Todo: Password
		'mem_Email' => '`email`',
		'mem_HomePage' => '""',
		'mem_IP' => '""',
		'mem_PostTime' => time(),
		'mem_Alias' => '`nickname`',
		'mem_Intro' => '`description`',
		'mem_Articles' => 0, 
		'mem_Pages' => 0, 
		'mem_Comments' => 0,
		'mem_Uploads' => 0, 
		'mem_Template' => '""',
		'mem_Meta' => '""'
	);

	$ary1 = array(); $ary2 = array();
	foreach($list as $name => $value)
	{
		$ary1[] = $name;
		$ary2[] = $value . ' AS ' . $name;
	}
	$sql = build_sql('Member', $_prefix . 'user', $ary1, $ary2);

	return $zbp->db->QueryMulit($sql);
}

function upgrade_comment_id()
{
	global $zbp;
	$comm_list = $zbp->GetCommentList();
	ob_start();
	flush();
	foreach($comm_list as $o)
	{
		if ($o->ParentID == 0) continue;
		$rootid = find_comment_rootid($o->ParentID);
		$o->RootID = $rootid;
		$o->Save();
		echo '<p>已转换评论ID：' . $o->ID . '</p>';
		ob_flush();
		flush();
	}
}

function find_comment_rootid($id)
{
	$comment = new Comment;
	$comment->LoadInfoByID($id);
	if ($comment->ParentID == 0)
		return $id;
	else
		return find_comment_rootid($comment->ParentID);
}

function upgrade_category_count()
{
	global $zbp;
	$cate_list = $zbp->GetCategoryList();
	ob_start();
	flush();
	foreach($cate_list as $o)
	{
		$sql = 'SELECT COUNT(log_ID) AS `c` FROM `' . $zbp->db->dbpre . 'post` WHERE `log_CateID` = ' . $o->ID;
		$result = $zbp->db->Query($sql);
		if (count($result) > 0)
		{
			$o->Count = $result[0]['c'];
		}
		
		$o->Save();
		echo '<p>分类ID=' . $o->ID . ' 计数=' . $o->Count . '</p>';
		ob_flush();
		flush();
	}
}

function upgrade_user_rebuild()
{
	global $zbp;
	$user_list = $zbp->GetMemberList();
	ob_start();
	flush();
	foreach($user_list as $o)
	{
		$sql = 'SELECT COUNT(log_ID) AS `c` FROM `' . $zbp->db->dbpre . 'post` WHERE `log_Type` = 1 AND `log_AuthorID` = ' . $o->ID;
		$result = $zbp->db->Query($sql);
		if (count($result) > 0) 	$o->Articles = $result[0]['c'];

		$sql = 'SELECT COUNT(log_ID) AS `c` FROM `' . $zbp->db->dbpre . 'post` WHERE `log_Type` = 0 AND `log_AuthorID` = ' . $o->ID;
		$result = $zbp->db->Query($sql);
		if (count($result) > 0) 	$o->Pages = $result[0]['c'];
		
		if ($o->ID > 1)
		{
			$o->Guid = substr(md5('emlog' . GetGuid() . time() ), 0, 22);
			$o->Password = $o->GetPassWordByGuid('emlogolduser', $o->Guid);
		}
		
		$o->Save();
		echo '<p>用户ID=' . $o->ID . ' 文章=' . $o->Articles . ' 页面=' . $o->Pages . ' 密码=emlogolduser</p>';
		ob_flush();
		flush();
	}
}

function upgrade_tag_rebuild()
{
	global $zbp;
	$tag_list = $zbp->GetTagList();
	ob_start();
	flush();
	foreach($tag_list as $o)
	{
		$intro_array = explode(',', $o->Intro);
		$o->Count = count($intro_array) - 2;
		$sql = 'UPDATE `' .  $zbp->db->dbpre . 'post` SET log_Tag = concat(log_Tag, "{' . $o->ID . '}") WHERE log_ID in(0' . $o->Intro . '0)';
		$zbp->db->Update($sql);
		$o->Intro = '';
		$o->Save();
		echo '<p>Tag ID=' . $o->ID . ' Count=' . $o->Count . '</p>';
		ob_flush();
		flush();
	}

}




function build_sql($zbp_field, $em_table, $array4zbp, $array4em)
{
	global $zbp;
	$table = str_replace('%pre%', $zbp->db->dbpre, $GLOBALS['table'][$zbp_field]);
	$sql  = 'TRUNCATE `' . $table . '`; ';
	$sql .= 'INSERT INTO ' . $table;
	$sql .= ' (' . implode(',', $array4zbp) . ') ';
	$sql .= 'SELECT ' . implode(',', $array4em) . ' FROM `' . $em_table . '`;';
	return $sql;
}

function finish_convert()
{
	global $zbp;
	echo '<p>恭喜您，数据转移成功！</p>';
	echo '<p>转移完成后，请停用并删除此插件，否则可能会导致未知的安全问题。</p>';
	echo '<p>除了管理员以外，用户密码已经被重置为emlogolduser。</p>';
	echo '<p>现在，让我们畅游Z-Blog PHP吧！</p>';
	echo '<p>&nbsp;</p>';
	echo '<p>一些链接：<a class="href-ajax" href="convert.php?func=drop_emlog&prefix='. htmlspecialchars(GetVars('prefix', 'GET')). '">删除emlog数据表</a>';
	echo '&nbsp;&nbsp;<a href="../../../zb_system/cmd.php?act=PluginDis&name=em2zbp&token=' . $zbp->GetToken() . '">停用本插件</a>';
	echo '&nbsp;&nbsp;<a href="../AppCentre/main.php">去应用中心下载最新应用</a>';
	echo '&nbsp;&nbsp;<a href="../../../zb_system/cmd.php?act=ArticleEdt">写一篇新的文章</a></p>';
}

function drop_emlog()
{
	global $zbp;
	$emlist = array(
		'emlog_attachment',
		'emlog_blog',
		'emlog_comment',
		'emlog_link',
		'emlog_navi',
		'emlog_options',
		'emlog_reply',
		'emlog_sort',
		'emlog_tag',
		'emlog_twitter',
		'emlog_user'
	);
	$sql = '';
	$prefix = GetVars('prefix', 'GET');
	for($i = 0; $i < count($emlist); $i++)
	{
		$zbp->db->Query('DROP TABLE IF EXISTS`'. str_replace('emlog_', $prefix, $emlist[$i]) . '`;');
	}
	
	echo 'OK';

}