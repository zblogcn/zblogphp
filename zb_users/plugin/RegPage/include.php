<?php

DefinePluginFilter('Filter_Plugin_RegPage_RegSucceed');

//注册插件
RegisterPlugin("RegPage", "ActivePlugin_RegPage");

function ActivePlugin_RegPage()
{
    global $zbp;
    Add_Filter_Plugin('Filter_Plugin_Index_Begin', 'RegPage_Main');
    Add_Filter_Plugin('Filter_Plugin_Login_Header', 'RegPage_LoginAddon');
    if (trim($zbp->Config('RegPage')->rewrite_url) == '') {
        $zbp->Config('RegPage')->rewrite_url = '/reg.html';
    }
}

$RegPage_Table = '%pre%regpage';

$RegPage_DataInfo = array(
    'ID'         => array('reg_ID', 'integer', '', 0),
    'InviteCode' => array('reg_InviteCode', 'string', 50, ''),
    'Level'      => array('reg_Level', 'integer', '', 5),
    'AuthorID'   => array('reg_AuthorID', 'integer', '', 0),
    'IsUsed'     => array('reg_IsUsed', 'boolean', '', false),
    'Intro'      => array('reg_Intro', 'string', '', ''),
    'IP'         => array('reg_IP', 'string', 50, ''),
    'Time'       => array('reg_Time', 'integer', '', 0),
);

function InstallPlugin_RegPage()
{
    global $zbp;

    if (!$zbp->Config('RegPage')->default_level) {
        $zbp->Config('RegPage')->default_level = 5;
        $zbp->Config('RegPage')->open_reg = 0;
        $zbp->Config('RegPage')->loginpage_addon = 0;
        $zbp->Config('RegPage')->loginpage_text = '欢迎您注册本站账号,请点击链接完成注册.';
        $zbp->Config('RegPage')->title_text = '会员注册';
        $zbp->Config('RegPage')->only_one_ip = 0;
        $zbp->Config('RegPage')->disable_website = 0;
        $zbp->Config('RegPage')->disable_validcode = 0;
        $zbp->Config('RegPage')->rewrite_url = '/reg.html';
        $zbp->SaveConfig('RegPage');

        RegPage_CreateTable();
        RegPage_CreateCode(500);
    }
}

function UninstallPlugin_RegPage()
{
    global $zbp;
    $zbp->DelConfig('RegPage');
    if ($zbp->db->ExistTable($zbp->db->sql->ReplacePre($GLOBALS['RegPage_Table'])) == true) {
        $zbp->db->DelTable($zbp->db->sql->ReplacePre($GLOBALS['RegPage_Table']));
    }
}

function RegPage_LoginAddon()
{
    global $zbp;
    if ($zbp->Config('RegPage')->loginpage_addon == false) {
        return;
    }
    echo '<script type="text/javascript">
$(document).ready(function(){
	$("dl:last").after("<dl><dd></dd><dd style=\"text-align:right;\"><a href=\"../?reg\">' . $zbp->Config('RegPage')->loginpage_text . '</a></dd></dl>");
});
	</script>';
}

function RegPage_CreateCode($n)
{
    global $zbp;

    for ($i = 0; $i < $n; $i++) {
        $r = new Base($GLOBALS['RegPage_Table'], $GLOBALS['RegPage_DataInfo']);
        $r->InviteCode = GetGuid();
        $r->Level = $zbp->Config('RegPage')->default_level;
        $r->Save();
    }
}

function RegPage_DelUsedCode()
{
    global $zbp;

    $sql = $zbp->db->sql->Delete($GLOBALS['RegPage_Table'], array(array('<>', 'reg_AuthorID', 0)));
    $zbp->db->Delete($sql);
}

function RegPage_EmptyCode()
{
    global $zbp;

    $sql = $zbp->db->sql->Delete($GLOBALS['RegPage_Table'], null);
    $zbp->db->Delete($sql);
}

function RegPage_CreateTable()
{
    global $zbp;
    if ($zbp->db->ExistTable($zbp->db->sql->ReplacePre($GLOBALS['RegPage_Table'])) == true) {
        return;
    }
    $s = $zbp->db->sql->CreateTable($GLOBALS['RegPage_Table'], $GLOBALS['RegPage_DataInfo']);
    $zbp->db->QueryMulti($s);
}

function RegPage_Main()
{
    global $zbp;

    if ($zbp->option['ZC_STATIC_MODE'] == 'REWRITE' && stripos($zbp->currenturl, $zbp->Config('RegPage')->rewrite_url) === 0) {
        RegPage_Page();
        die();
    } elseif (isset($_GET['reg'])) {
        RegPage_Page();
        die();
    }
}

function RegPage_CheckEmail($email)
{
    global $zbp;
    $email = trim(strtolower($email));
    $sql = $zbp->db->sql->Select($zbp->table['Member'], '*', array(array('LIKE', 'mem_Email', $email)), null, 1, null);
    $am = $zbp->GetListType('Member', $sql);
    if (count($am) > 0) {
        return true;
    }
}

function RegPage_Page()
{
    global $zbp;
    $hash = sha1($zbp->guid . GetVars('REMOTE_ADDR', 'SERVER'));

    $zbp->validcodeurl = $zbp->host . 'zb_users/plugin/RegPage/c_validcode.php' . '?hash=' . $hash;

    $zbp->header .= '<script src="' . $zbp->host . 'zb_users/plugin/RegPage/reg.js" type="text/javascript"></script>' . "\r\n";
    $zbp->header .= '<script src="' . $zbp->host . 'zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>' . "\r\n";
    $zbp->header .= '<link rel="stylesheet" type="text/css" href="' . $zbp->host . 'zb_system/css/jquery-ui.custom.css"/>' . "\r\n";

    $article = new Post();
    $article->Title = $zbp->Config('RegPage')->title_text;
    $article->IsLock = true;
    $article->Type = ZC_POST_TYPE_PAGE;

    $article->Content .= '<input type="hidden" name="hash" value="' . $hash . '" />';

    $article->Content .= '<table style="width:90%;border:none;font-size:1.1em;line-height:2.5em;">';
    $article->Content .= '<tr style=""><th style="border:none;" colspan="2" scope="col"><p>' . $zbp->Config('RegPage')->readme_text . '</p></th></tr>';
    $article->Content .= '<tr><td width="30%" style="text-align:right;border:none;">(*)名称：</td><td  style="border:none;" ><input required="required" type="text" name="name" style="width:250px;font-size:1.2em;" /></td></tr>';
    $article->Content .= '<tr><td style="text-align:right;border:none;">(*)密码：</td><td  style="border:none;" ><input required="required" type="password" name="password" style="width:250px;font-size:1.2em;" /></td></tr>';
    $article->Content .= '<tr><td style="text-align:right;border:none;">(*)确认密码：</td><td  style="border:none;" ><input required="required" type="password" name="repassword" style="width:250px;font-size:1.2em;" /></td></tr>';
    $article->Content .= '<tr><td style="text-align:right;border:none;">(*)邮箱：</td><td  style="border:none;" ><input type="text" name="email" style="width:250px;font-size:1.2em;" /></td></tr>';
    if ($zbp->Config('RegPage')->disable_website != true) {
        $article->Content .= '<tr><td style="text-align:right;border:none;">网站：</td><td  style="border:none;" ><input type="text" name="homepage" style="width:250px;font-size:1.2em;" /></td></tr>';
    }
    $article->Content .= '<tr><td style="text-align:right;border:none;">(*)邀请码：</td><td  style="border:none;" ><input required="required" type="text" name="invitecode" style="width:250px;font-size:1.2em;" />';

    if ($zbp->Config('RegPage')->open_reg) {
        $article->Content .= '&nbsp;&nbsp;<a onclick="$(\'#dialog-message\').dialog(\'open\');return false;" href="' . $zbp->host . 'zb_users/plugin/RegPage/getinvitecode.php" target="_blank">点击这里获取邀请码</a>.';
    }

    $article->Content .= '</td></tr>';
    if ($zbp->Config('RegPage')->disable_validcode != true) {
        $article->Content .= '<tr><td style="text-align:right;border:none;">(*)</td><td  style="border:none;" ><input required="required" type="text" name="verifycode" style="width:150px;font-size:1.2em;" />&nbsp;&nbsp;<img id="reg_verfiycode" style="border:none;vertical-align:middle;width:' . $zbp->option['ZC_VERIFYCODE_WIDTH'] . 'px;height:' . $zbp->option['ZC_VERIFYCODE_HEIGHT'] . 'px;cursor:pointer;" src="' . $zbp->validcodeurl . '&amp;id=RegPage" alt="" title="" onclick="javascript:this.src=\'' . $zbp->validcodeurl . '&amp;id=RegPage&amp;tm=\'+Math.random();"/></td></tr>';
    }

    $article->Content .= '<tr><td  style="border:none;" ></td><td  style="border:none;" ><input type="submit" style="width:100px;font-size:1.0em;padding:0.2em" value="提交" onclick="return RegPage()" /></td></tr>';

    $article->Content .= '</table>';
    //$article->Content .='<p>带星号为必填选项.</p>';

    $mt = microtime();
    $s = <<<js
<script type="text/javascript">
  $(function() {
    $( "#dialog-message" ).dialog({
      autoOpen: false,
      modal: true,
      buttons: {
        Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });
  });
</script>

<div id="dialog-message" title="{$article->Title}">
<iframe src ="{$zbp->host}zb_users/plugin/RegPage/getinvitecode.php?{$mt}" style="border:none;width:250px;"></iframe>
</div>
js;

    $article->Content .= $s;

    if ($zbp->template->hasTemplate('t_regpage')) {
        $article->Template = 't_regpage';
    }

    $zbp->template->SetTags('title', $article->Title);
    $zbp->template->SetTags('article', $article);
    $zbp->template->SetTags('type', $article->type = 0 ? 'article' : 'page');
    $zbp->template->SetTemplate($article->Template);
    $zbp->template->SetTags('page', 1);
    $zbp->template->SetTags('pagebar', null);
    $zbp->template->SetTags('comments', array());

    foreach ($GLOBALS['Filter_Plugin_ViewPost_Template'] as $fpname => &$fpsignal) {
        $fpreturn = $fpname($zbp->template);
    }

    $zbp->template->Display();
}
