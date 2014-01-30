<?php
function export_post_member($http,$intmin,$intmax)
{	
	global $zbp;
	global $duoshuo;
	$where = array();
	$where[] = array('custom','%pre%member.mem_ID not in (SELECT ds_memid FROM %pre%plugin_duoshuo_members)');
	$return = $zbp->GetMemberList('*',$where,null,null,null);
	$data = export_member($return);
	$http->open('POST',"http://" . $duoshuo->cfg->api_hostname . '/' . $duoshuo->url['users']['import']);
	$http->send('short_name=' . urlencode($duoshuo->cfg->short_name) . '&secret=' . urlencode($duoshuo->cfg->secret) . '&' . $data);
	
	$json = json_decode($http->responseText);
	if(isset($json->response))
	{
		foreach($json->response as $a => $v)
		{
			$sql = $zbp->db->sql->Insert($duoshuo->db['members'],array('ds_memid'=>$a,'ds_key'=>$v));
			$zbp->db->Insert($sql);
		}
	}
	
}

function export_member($return)
{
	$role = array('','administrator','editor','author','user');
	$ary = array();
	foreach($return as $a)
	{
		
		$w = array(
			'user_key' => $a->ID,
			'name' => $a->Name,
			'role' => $role[$a->Level],
			'avatar_url' => $a->Avatar,
			'url' => $a->HomePage,
			'email' => $a->Email,
			'created_at' => date("Y-m-d H:i:s",$a->PostTime),
		);
		
		$k = '';
		foreach($w as $b => $c)
		{
			$ary[] = 'users['.$w['user_key'].']['.$b.']=' .urlencode($c); 
		}
	}
	return implode('&',$ary);
}
