<?php


//注册插件
RegisterPlugin("HeartVote", "ActivePlugin_HeartVote");

function ActivePlugin_HeartVote()
{
    Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags', 'HeartVote_Pre');
    Add_Filter_Plugin('Filter_Plugin_Post_Call', 'HeartVote_Main');
}

$table['HeartVote'] = '%pre%heartvote';

$datainfo['HeartVote'] = array(
    'ID'    => array('vote_ID', 'integer', '', 0),
    'LogID' => array('vote_LogID', 'integer', '', 0),
    'Score' => array('vote_Score', 'integer', '', 0),
    'IP'    => array('vote_IP', 'string', 15, ''),
);

class HeartVote extends Base
{
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['HeartVote'], $zbp->datainfo['HeartVote']);
    }
}

function InstallPlugin_HeartVote()
{
    global $zbp;
    HeartVote_CreateTable();
}

function HeartVote_CreateTable()
{
    global $zbp;
    if ($zbp->db->ExistTable($GLOBALS['table']['HeartVote']) == false) {
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['HeartVote'], $GLOBALS['datainfo']['HeartVote']);
        $zbp->db->QueryMulit($s);
    }
}

function HeartVote_Pre(&$template)
{
    global $zbp;
    $zbp->header .= "<script type=\"text/javascript\" src=\"{$zbp->host}zb_users/plugin/HeartVote/js/vote.js\"></script>\r\n";
    $zbp->header .= "<link rel=\"stylesheet\" href=\"{$zbp->host}zb_users/plugin/HeartVote/css/stars.css\" type=\"text/css\" />\r\n";
}

function HeartVote_Main(&$post, $method, $args)
{
    global $zbp;

    if ($method != 'HeartVote') {
        return;
    }

    //$id=$template->GetTags('article')->ID;
    $id = $post->ID;

    $s = "<!--hvbegin--><div class=\"heart-vote\" id=\"HeartVote_{$id}\">";
    $s .= "<ul class=\"unit-rating\">";
    $s .= "<li class='current-rating' style=\"width:0px;\"></li>";
    $s .= "<li><a href=\"javascript:heartVote('1','{$id}')\" title=\"打1分\" class=\"r1-unit\">1</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('2','{$id}')\" title=\"打2分\" class=\"r2-unit\">2</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('3','{$id}')\" title=\"打3分\" class=\"r3-unit\">3</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('4','{$id}')\" title=\"打4分\" class=\"r4-unit\">4</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('5','{$id}')\" title=\"打5分\" class=\"r5-unit\">5</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('6','{$id}')\" title=\"打6分\" class=\"r6-unit\">6</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('7','{$id}')\" title=\"打7分\" class=\"r7-unit\">7</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('8','{$id}')\" title=\"打8分\" class=\"r8-unit\">8</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('9','{$id}')\" title=\"打9分\" class=\"r9-unit\">9</a></li>";
    $s .= "<li><a href=\"javascript:heartVote('10','{$id}')\" title=\"打10分\" class=\"r10-unit\">10</a></li>";
    $s .= "</ul><b>0</b><i>分/0个投票</i></div>";

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
    $s .= "<script type=\"text/javascript\">showVote('{$allvote}','{$alluser}')</script><!--hvend-->";

    //$s.="<script src=\"{$zbp->host}zb_users/plugin/HeartVote/getvote.php?id={$id}\" type=\"text/javascript\"></script>";

    //$template->SetTags('HeartVote',$s);

    echo $s;

    $GLOBALS['Filter_Plugin_Post_Call']['HeartVote_Main'] = PLUGIN_EXITSIGNAL_RETURN;
}
