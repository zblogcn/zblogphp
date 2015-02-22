<?php
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('HeartComment')) {$zbp->ShowError(48);die();}

for($i = 1; $i <= 500; $i++) {
    score_update($i);
    ob_flush();
    flush();
}


function score_update($id) {

    global $table; 
    global $zbp;
    
	$sql = $zbp->db->sql->Select(
		$table['plugin_heartcomment_score'],
		'*',
		array(array('=', 'vote_postid', $id)),
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
			"vote_postid" => $id,
			"vote_nocomm_score" => 0,
			"vote_nocomm_count" => 0,
			"vote_comm_score" => 0,
			"vote_comm_count" => 0,
			"vote_sum_score" => 0,
			"vote_sum_count" => 0
		);
	}

	$sql = $zbp->db->sql->Select(
		$table['plugin_heartcomment_detail'],
		'SUM(vote_score) AS vote_nocomm_score, COUNT(vote_score) AS vote_nocomm_count',
		array(array('=', 'vote_postid', $id), array('=', 'vote_commid', 0)),
		null,
		null,
		null
	);
	
	$array = $zbp->db->Query($sql);
	if (count($array) > 0) {
	    $scores['vote_nocomm_count'] = $array[0]['vote_nocomm_count'];
	    $scores['vote_nocomm_score'] = $array[0]['vote_nocomm_score'];
	}
	
	$scores['vote_sum_score'] = (round($scores['vote_nocomm_score'] * HeartComment_nocomm_threshold + $scores['vote_comm_score'], 2));
	$scores['vote_sum_count'] = (round($scores['vote_nocomm_count'] * HeartComment_nocomm_threshold + $scores['vote_comm_count'], 2));

	if ($update_score) {
		$sql = $zbp->db->sql->Update(
			$table['plugin_heartcomment_score'], 
			$scores,
			array(array('=', 'vote_postid', $id))
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
	
    echo $sql;
    echo '<br/>' . "\n";

}

