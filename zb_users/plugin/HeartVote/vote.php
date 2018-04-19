<?php

require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

if (!$zbp->CheckPlugin('HeartVote')) {
    $zbp->ShowError(48);
    die();
}

$mode = null;
if (!isset($_POST['vote'])) {
    $mode = 'script';
    header('Content-type: application/x-javascript; Charset=utf-8');
}
$vote = GetVars("vote");
$id = GetVars("id");
$ip = GetGuestIP();

$sql = $zbp->db->sql->Select($zbp->table['HeartVote'], '*', array(array('=', 'vote_LogID', $id), array('=', 'vote_IP', $ip)), null, null, null);
$array = $zbp->db->Query($sql);

if (count($array) == 0) {
    $vh = new HeartVote();
    $vh->LogID = $id;
    $vh->Score = $vote;
    $vh->IP = $ip;

    $vh->Save();

    $sql = $zbp->db->sql->Count($zbp->table['HeartVote'], array(array('SUM', 'vote_Score', 'allvote'), array('COUNT', '*', 'alluser')), array(array('=', 'vote_LogID', $id)));
    $array = $zbp->db->Query($sql);
    $array = current($array);
    $alluser = GetValueInArray($array, 'alluser');
    $allvote = GetValueInArray($array, 'allvote');
    if ($alluser == 0) {
        $allvote = 0;
    } else {
        $allvote = substr($allvote / $alluser, 0, 3);
    }

    if ($mode == 'script') {
        echo "showVote({$allvote},{$alluser});";
    } else {
        echo "{$allvote}|{$alluser}";
    }
} else {
    if ($mode == 'script') {
        echo "alert('你已经投过一次了，还想投么(￣口￣)！！！');showVote(0,0);";
    } else {
        echo '你已经投过一次了，还想投么(￣口￣)！！！';
    }
}
