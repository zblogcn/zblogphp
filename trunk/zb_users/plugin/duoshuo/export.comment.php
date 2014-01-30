<?php
function export_post_comment($http,$intmin,$intmax)
{	
	global $zbp;
	global $duoshuo;
	$where = array();
	if($intmax>0) $where[] = array('between','comm_ID',$intmin,$intmax);
	$where[] = array('custom','%pre%comment.comm_id not in (SELECT ds_cmtid FROM %pre%plugin_duoshuo_comment)');
	$return = $zbp->GetCommentList('*',$where,null,null,null);
	$data = export_comment($return);
	$http->open('POST',"http://" . $duoshuo->cfg->api_hostname . '/' . $duoshuo->url['posts']['import']);
	$http->send('short_name=' . urlencode($duoshuo->cfg->short_name) . '&secret=' . urlencode($duoshuo->cfg->secret) . '&' . $data);
	
	$json = json_decode($http->responseText);
	if(isset($json->response))
	{
		foreach($json->response as $a => $v)
		{
			$sql = $zbp->db->sql->Insert($duoshuo->db['comment'],array('ds_cmtid'=>$a,'ds_key'=>$v));
			$zbp->db->Insert($sql);
		}
	}
	
}

function export_comment($return)
{
	$ary = array();
	foreach($return as $a)
	{
		$w = array(
			'post_key' => $a->ID,
			'thread_key' => $a->LogID,
			'message' => $a->Content,
			'parent_key' => $a->ParentID,
			'author_key' => $a->AuthorID,
			'author_name' => $a->Name,
			'author_email' => $a->Email,
			'ip' => $a->IP,
			'agent' => $a->Agent,
			'status' => ($a->IsChecking?'pending':'approved'),
			'created_at' => date("Y-m-d H:i:s",$a->PostTime),
		);
		$k = '';
		foreach($w as $b => $c)
		{
			$ary[] = 'posts['.$w['post_key'].']['.$b.']=' .urlencode($c); 
		}
	}
	return implode('&',$ary);
}
