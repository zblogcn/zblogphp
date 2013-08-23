<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

if (!$zbp->CheckPlugin('HeartVote')) {$zbp->ShowError(48);die();}


$vote=$_POST["vote"];
$id=$_POST["id"];
$ip=GetGuestIP();


$sql=$zbp->db->sql->Select($zbp->table['HeartVote'],'*',array(array('=','vote_LogID',$id),array('=','vote_IP',$ip)),null,null,null);
$array=$zbp->db->Query($sql);
if(count($array)==0){

$vh=new HeartVote;
$vh->LogID=$id;
$vh->Score=$vote;
$vh->IP=$ip;

$vh->Save();

$sql=$zbp->db->sql->Count($zbp->table['HeartVote'],array(array('SUM','vote_Score','allvote'),array('COUNT','*','alluser')),array(array('=','vote_LogID',$id)));
$array=$zbp->db->Query($sql);
$alluser=GetValueInArray(current($array),'alluser');
$allvote=GetValueInArray(current($array),'allvote');
$allvote=substr($allvote/$alluser,0,3);

echo "{$allvote}|{$alluser}";


}else{
echo '你已经投过一次了，还想投么(￣口￣)！！！';
}

?>