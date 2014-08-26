<?php
define('HeartComment_nocomm_threshold', 0.8);
$HeartComment_RecordComment = false;
$HeartComment_LastVoteID = 0;
$HeartComment_LastVoteCommID = 0;
$HeartComment_LastVoteScore = 0;

RegisterPlugin("HeartComment","ActivePlugin_HeartComment");


function ActivePlugin_HeartComment() {

	Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags','HeartComment_Zbp_MakeTemplatetags');
	Add_Filter_Plugin('Filter_Plugin_Post_Call','HeartComment_Main');
	Add_Filter_Plugin('Filter_Plugin_PostComment_Core', 'HeartComment_PostComment_Core');
	Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed', 'HeartComment_PostComment_Succeed');

	
}

$table['plugin_heartcomment_detail']='%pre%plugin_heartcomment_detail';
$table['plugin_heartcomment_score']='%pre%plugin_heartcomment_score';

$datainfo['plugin_heartcomment_detail'] = array(
	'ID' => array('vote_ID', 'integer', '', 0),
	'postid' => array('vote_postid', 'integer', '', 0),
	'commid' => array('vote_commid', 'integer', '', 0),
	'userid' => array('vote_userid', 'integer', '', 0),
	'score' => array('vote_score', 'integer', '', 0),
	'ip' => array('vote_ip', 'string', 15, ''),
);

$datainfo['plugin_heartcomment_score']=array(
	'ID' => array('vote_ID', 'integer', '', 0),
	'postid' => array('vote_postid', 'integer', '', 0),
	'nocomm_score' => array('vote_nocomm_score', 'integer', '', 0),
	'nocomm_count' => array('vote_nocomm_count', 'integer', '', 0),
	'comm_score' => array('vote_comm_score', 'integer', '', 0),
	'comm_count' => array('vote_comm_count', 'integer', '', 0),
	'sum_score' => array('vote_sum_score', 'float', '', 0),
	'sum_count' => array('vote_sum_count', 'integer', '', 0)
);

function HeartComment_SaveComment($voteid, $postid, $commid, $userid, $score, $ip) {

	global $zbp;
	global $table;


	$score = (int)$score;
	if ($score < 1) $score = 1;
	if ($score > 10) $score = 10;

	$array = array(
		"vote_postid" => $postid,
		"vote_commid" => $commid,
		"vote_userid" => $userid,
		"vote_score" => $score,
		"vote_ip" => $ip
	);

	$sql = $zbp->db->sql->Select(
		$table['plugin_heartcomment_score'],
		'*',
		array(array('=', 'vote_postid', $postid)),
		null,
		null,
		null
	);
	$scores = $zbp->db->Query($sql);
	$update_score = false;

	if (count($scores) > 0) {
		$scores = $scores[0];
		$update_score = true;
	}
	else {
		$scores = array(
			"vote_postid" => $postid,
			"vote_nocomm_score" => 0,
			"vote_nocomm_count" => 0,
			"vote_comm_score" => 0,
			"vote_comm_count" => 0,
			"vote_sum_score" => 0,
			"vote_sum_count" => 0
		);
	}


	if ($voteid > 0) {
		$sql = $zbp->db->sql->Update(
			$table['plugin_heartcomment_detail'], 
			$array,
			array(array('=', 'vote_ID', $voteid))
		);
		$zbp->db->Update($sql);

		if ($commid > 0) {
			if ((int)$GLOBALS['HeartComment_LastVoteCommID'] == 0) {
				
				$scores['vote_nocomm_score'] -= (int)$GLOBALS['HeartComment_LastVoteScore'];
				$scores['vote_nocomm_count'] -= 1;

				$scores['vote_comm_score'] += $score;
				$scores['vote_comm_count'] += 1;

			}
			else {
				$scores['vote_comm_score'] += ($score - (int)$GLOBALS['HeartComment_LastVoteScore']);
			}

		}
		else {
			// Impossible
			$scores['vote_nocomm_score'] += $score;
			$scores['vote_nocomm_count'] += 1;
		}
	}
	else {
		$sql = $zbp->db->sql->Insert(
			$table['plugin_heartcomment_detail'], 
			$array
		);
		$zbp->db->Insert($sql);

		if ($commid > 0) {
			$scores['vote_comm_score'] += $score;
			$scores['vote_comm_count'] += 1;
		}
		else {
			$scores['vote_nocomm_score'] += $score;
			$scores['vote_nocomm_count'] += 1;
		}
	}

	$scores['vote_sum_score'] = (int)($scores['vote_nocomm_score'] * HeartComment_nocomm_threshold + $scores['vote_comm_score']);
	$scores['vote_sum_count'] = (int)($scores['vote_nocomm_count'] * HeartComment_nocomm_threshold + $scores['vote_comm_count']);

	if ($update_score) {
		$sql = $zbp->db->sql->Update(
			$table['plugin_heartcomment_score'], 
			$scores,
			array(array('=', 'vote_postid', $postid))
		);
		$zbp->db->Update($sql);
	}
	else {

		$sql = $zbp->db->sql->Insert(
			$table['plugin_heartcomment_score'], 
			$scores
		);
		$zbp->db->Insert($sql);
	}


}



function HeartComment_PostComment_Succeed(&$comment) {

	if ($GLOBALS['HeartComment_RecordComment'])
		HeartComment_SaveComment($GLOBALS['HeartComment_LastVoteID'], $comment->LogID, $comment->ID, $comment->AuthorID, $comment->HeartComment_Score, $comment->IP);

}

function HeartComment_PostComment_Core(&$comment) {

	global $zbp;
	global $user;

	$sql = $zbp->db->sql->Select(
			$zbp->table['plugin_heartcomment_detail'],
			'vote_ID, vote_commid, vote_score',
			array(array('=', 'vote_postid', $comment->LogID), array('=', 'vote_ip', $comment->IP)),
			null,null,null
		);

	$array = $zbp->db->Query($sql);

	$content = TransferHTML($comment->Content, '[nohtml]');

	
	if (count($array) > 0) {
		$GLOBALS['HeartComment_LastVoteID'] = $array[0]['vote_ID'];
		$GLOBALS['HeartComment_LastVoteCommID'] = $array[0]['vote_commid'];
		$GLOBALS['HeartComment_LastVoteScore'] = $array[0]['vote_score'];
		if ($content == '')
			$zbp->ShowError('没有理由的情况下不允许修改评分(￣口￣)！！！', __FILE__, __LINE__);
	}
	
	$score = (int)GetVars('score', 'POST');
	if ($score == 0) $score = (int)GetVars('score', 'GET');
	$comment->HeartComment_Score = $score;

	if ($content != '') {
		$GLOBALS['HeartComment_RecordComment'] = true;
		return;
	}

	HeartComment_SaveComment(0, $comment->LogID, $comment->ID, $comment->AuthorID, $comment->HeartComment_Score, $comment->IP);
	$zbp->ShowError('投票成功', __FILE__, __LINE__);

}

function InstallPlugin_HeartComment() {
	global $zbp;
	HeartComment_CreateTable();
}

function HeartComment_CreateTable() {
	global $zbp;
	if($zbp->db->ExistTable($GLOBALS['table']['plugin_heartcomment_detail'])==false){
		$s=$zbp->db->sql->CreateTable($GLOBALS['table']['plugin_heartcomment_detail'],$GLOBALS['datainfo']['plugin_heartcomment_detail']);
		$zbp->db->QueryMulit($s);
	}
	if($zbp->db->ExistTable($GLOBALS['table']['plugin_heartcomment_score'])==false){
		$s=$zbp->db->sql->CreateTable($GLOBALS['table']['plugin_heartcomment_score'],$GLOBALS['datainfo']['plugin_heartcomment_score']);
		$zbp->db->QueryMulit($s);
	}
}

function HeartComment_Zbp_MakeTemplatetags(&$template) {
	global $zbp;
	
	$s = '<div class="heart-comment" id="HeartComment">';
	$s .= "<ul class=\"unit-rating\">";
	$s .= "<li class='current-rating' style=\"width:0px;\"></li>";
	
	for($i = 1; $i <= 10; $i++) {
		$s .= "<li><a data-score=\"$i\" title=\"打 $i 分\" class=\"r$i-unit heartcomment heartcomment-vote ";
		if ($i == 10) $s .= 'heartcomment-selected';
		$s .= "\">$i</a></li>";
	}
	
	$s .= "</ul></div>";
	
	$zbp->header .= "<link rel=\"stylesheet\" href=\"{$zbp->host}zb_users/plugin/HeartComment/css/stars.css\" type=\"text/css\" />\r\n";	
	$zbp->header .= "<script type=\"text/javascript\" src=\"{$zbp->host}zb_users/plugin/HeartComment/js/vote.js\"></script>\r\n";	

	$template['HeartComment'] = $s;
}

function HeartComment_Main(&$post, $method, $args) {
	
	global $zbp;
	
	if($method != 'HeartComment_Func') return null;
	
	//$id=$template->GetTags('article')->ID;
	$id = $post->ID;
		
	$sql = $zbp->db->sql->Select(
		$zbp->table['plugin_heartcomment_score'],
		'*',
		array(array('=', 'vote_postid', $id)),
		null,
		null,
		null
	);
		
	$array = $zbp->db->Query($sql);
	$array = current($array);
	$alluser = GetValueInArray($array, 'vote_sum_count');
	$allvote = GetValueInArray($array, 'vote_sum_score');

	if ($alluser == 0){
		$alluser = 0;
		$allvote = 0;
	}else{
		$allvote = round($allvote / $alluser, 2);
	}
	
	if ($allvote > 10) $allvote = 10;
	

	$sql = $zbp->db->sql->Select(
			$zbp->table['plugin_heartcomment_detail'],
			'vote_ID, vote_commid, vote_score',
			array(array('=', 'vote_postid', $id), array('=', 'vote_ip', GetGuestIP())),
			null,null,null
		);

	$array = $zbp->db->Query($sql);
	$single = 10;
	if (count($array) > 0) {
		$single = $array[0]['vote_score'];
	}

	$post->HeartComment = array(
		'count' => $alluser,
		'score' => $allvote,
		'single' => $single
	);
		
	$GLOBALS['Filter_Plugin_Post_Call']['HeartComment_Main'] = PLUGIN_EXITSIGNAL_RETURN;
	
}
?>