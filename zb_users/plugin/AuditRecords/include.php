<?php


//注册插件
RegisterPlugin("AuditRecords", "ActivePlugin_AuditRecords");

$actions['audit'] = 2;

//参数有3个
//1 $article：被审核的文章object (传入引用)
//2 $opeate指令 -1(申诉) 0(未通过) 1(通过)
//3 $logs文本记录
$Filter_Plugin_AuditRecords_Submit = array();

function ActivePlugin_AuditRecords()
{
    Add_Filter_Plugin('Filter_Plugin_Edit_Response', 'AuditRecords_Edit_Response');
    Add_Filter_Plugin('Filter_Plugin_PostArticle_Core', 'AuditRecords_Main');
    Add_Filter_Plugin('Filter_Plugin_Edit_Response3', 'AuditRecords_Edit_Response3');
    Add_Filter_Plugin('Filter_Plugin_Admin_LeftMenu', 'AuditRecords_AddMenu');
}

function AuditRecords_AddMenu(&$m)
{
    global $zbp;
    array_unshift($m, '');
    $n = GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Post'] . ' WHERE log_Type=0 AND log_Status=2'), 'num');
    $m[0] = $m[1];
    $m[1] = MakeLeftMenu("audit", "(" . $n . ")审核管理", $zbp->host . "zb_users/plugin/AuditRecords/main.php", "nav_AuditRecords", "aAuditRecords", "");
}

$table['AuditRecords'] = '%pre%auditrecords';

$datainfo['AuditRecords'] = array(
    'ID'       => array('ar_ID', 'integer', '', 0),
    'LogID'    => array('ar_LogID', 'integer', '', 0),
    'AuthorID' => array('ar_AuthorID', 'integer', '', 0),
    'Logs'     => array('ar_Logs', 'string', '', ''),
    'Opeate'   => array('ar_Opeate', 'integer', '', 0),
    'PostTime' => array('ar_PostTime', 'integer', '', 0),
);

class AuditRecords extends Base
{
    public function __construct()
    {
        global $zbp;
        parent::__construct($zbp->table['AuditRecords'], $zbp->datainfo['AuditRecords']);
    }
}

function InstallPlugin_AuditRecords()
{
    global $zbp;
    AuditRecords_CreateTable();
}

function AuditRecords_CreateTable()
{
    global $zbp;
    if ($zbp->db->ExistTable($GLOBALS['table']['AuditRecords']) == false) {
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['AuditRecords'], $GLOBALS['datainfo']['AuditRecords']);
        $zbp->db->QueryMulti($s);
    }
}

function AuditRecords_Edit_Response()
{
    global $zbp;
    global $article;
    if ($article->ID == 0) {
        return;
    }

    echo '<p>';
    if (!$zbp->CheckRights('audit')) {
        echo '<label><input name="AuditRecords_op" value="-1" type="radio" /><b>发布者意见(作者更新了请选择该项并留下相关意见)</b></label>';
    } else {
        if ($zbp->user->ID == $article->AuthorID) {
            echo '<label><input name="AuditRecords_op" value="-1" type="radio" /><b>发布者意见</b>&nbsp;&nbsp;</label>';
        }
        echo '<label><input name="AuditRecords_op" value="0" type="radio" onclick="$(\'#cmbPostStatus\').get(0).selectedIndex=2; " /><b>审核者不通过并留下意见</b>&nbsp;&nbsp;</label>';
        echo '<label><input name="AuditRecords_op" value="1" type="radio" onclick="$(\'#cmbPostStatus\').get(0).selectedIndex=0; "  /><b>审核者通过并留下意见</b>&nbsp;&nbsp;</label>';
    }
    echo '<textarea name="AuditRecords_logs" style="width:100%;height:60px;"></textarea></p>';
}

function AuditRecords_Main($article)
{
    global $zbp;
    if (isset($_POST['AuditRecords_op'])) {
        $ar = new AuditRecords();
        $ar->LogID = $article->ID;
        $ar->AuthorID = $zbp->user->ID;
        $ar->Logs = trim(GetVars('AuditRecords_logs', 'POST'));
        $ar->Opeate = GetVars('AuditRecords_op', 'POST');

        if (!$ar->Logs) {
            if ($ar->Opeate == -1) {
                $ar->Logs = "我有更新了，麻烦请审核一下。";
            }
            if ($ar->Opeate == 0) {
                $ar->Logs = "不想写审核意见，还是不通过。";
            }
            if ($ar->Opeate == 1) {
                $ar->Logs = "通过了通过了，真的没有意见。";
            }
        }

        $ar->PostTime = time();

        $ar->Save();

        foreach ($GLOBALS['Filter_Plugin_AuditRecords_Submit'] as $fpname => &$fpsignal) {
            $fpname($article, $ar->Opeate, $ar->Logs);
        }
    }
}

function AuditRecords_Edit_Response3()
{
    global $zbp;
    global $article;
    if ($article->ID == 0) {
        return;
    }

    $sql = $zbp->db->sql->Select(
        $GLOBALS['table']['AuditRecords'],
        array('*'),
        array(array('=', 'ar_LogID', $article->ID)),
        array('ar_ID' => 'DESC'),
        null,
        null
    );
    echo '<dl style="padding-left:10px;">';
    echo '<dt><b>审核记录</b></dt>';

    $array = $zbp->GetList('AuditRecords', $sql);
    foreach ($array as $key => $ar) {
        echo '<dd style="text-align:left;padding-bottom:5px;"">';

        if ($ar->Opeate == 1) {
            echo '<img src="' . $zbp->host . 'zb_users/plugin/AuditRecords/tick.png" alt="通过" width="16" />';
        } elseif ($ar->Opeate == 0) {
            echo '<img src="' . $zbp->host . 'zb_users/plugin/AuditRecords/cancel.png" alt="未通过" width="16" />';
        } elseif ($ar->Opeate == -1) {
            echo '<img src="' . $zbp->host . 'zb_users/plugin/AuditRecords/error.png" alt="申诉" width="16" />';
        }

        echo '<b>' . $zbp->GetMemberByID($ar->AuthorID)->Name . '</b>于' . date('Y-m-d', $ar->PostTime) . '发布意见:';
        echo '<br/>' . htmlspecialchars(str_replace(PHP_EOL, '<br/>', $ar->Logs));
        echo '</dd>';
    }

    echo '</dl>';
}
