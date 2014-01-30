<?php
function export_post_article($http,$intmin,$intmax)
{	
	global $zbp;
	global $duoshuo;
	$where = array();
	if($intmax>0) $where[] = array('between','log_ID',$intmin,$intmax);
	$return = $zbp->GetPostList('*',$where,null,null,null);
	$data = export_article($return);
	$http->open("POST","http://" . $duoshuo->cfg->api_hostname . '/'. $duoshuo->url['threads']['import']);
	$http->send('short_name=' . urlencode($duoshuo->cfg->short_name) . '&secret=' . urlencode($duoshuo->cfg->secret) . '&' . $data);
	
}

function export_article($return)
{
	$ary = array();
	foreach($return as $a)
	{
		$w = array(
			'thread_key' => $a->ID,
			'title' => $a->Title,
			'excerpt' => $a->Intro,
			'author_key' => $a->AuthorID,
			'views' => $a->ViewNums,
			'url' => $a->Url,
			'content' => '',
			'comment_status' => ($a->IsLock?'open':'close'),
			'created_at' => date("Y-m-d H:i:s",$a->PostTime),
		);
		$k = '';
		foreach($w as $b => $c)
		{
			$ary[] = 'threads['.$w['thread_key'].']['.$b.']=' .urlencode($c); 
		}
	}
	return implode('&',$ary);
}
