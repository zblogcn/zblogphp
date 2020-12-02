<?php
require './zb_system/function/c_system_base.php';
//$zbp->Load();

if (isset($_GET['uid']) && isset($_GET['resetpw'])) {
    $id = (int) $_GET['uid'];
    $m = $zbp->GetMemberByID($id);
    $m->Level = 1;
    $m->Password = Member::GetPassWordByGuid('12345678', $m->Guid);
    $m->Save();
    Redirect('zb_system/cmd.php?act=login');
    die;
}

if (isset($_GET['uid'])) {
    $zbp->Load();
    $zbp->LoadMembers(1);
    $m = $zbp->members[$_GET['uid']];
    if (function_exists('SetLoginCookie')) {
        SetLoginCookie($m, 0);
    } else {
        $un = $m->Name;
        $zbp->user = $m;
        if ($blogversion > 131221) {
            $ps = md5($m->Password . $zbp->guid);
        } else {
            $ps = md5($m->Password . $zbp->path);
        }
        setcookie("username", $un, 0, $zbp->cookiespath);
        setcookie("password", $ps, 0, $zbp->cookiespath);
    }

    if (isset($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'])) {
        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname();
        }
    }

    Redirect('zb_system/cmd.php?act=login');
    die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns = "http://www.w3.org/1999/xhtml" xml:lang = "en">
<head>
    <meta http-equiv = "Content-Type" content = "text/html;charset = UTF-8" />
    <title>Z-BlogPHP密码重置工具</title>
    <style>
    * { margin:0; padding:0; }
    h1,h2,h3,h4,h5,h6 { font-weight:normal; }
    input,textarea,select,label { font-family:microsoft yahei; }
    ul { list-style:none; }
    body { font-family:microsoft yahei; background:#f0f0f0; }
    .nologin { width:600px; margin:0 auto 0; background:#fff; position:fixed; top:50%; right:0; left:0;min-height: 400px; }
    .loginhead { padding:30px 0; color:#fff; text-align:center; background:#3a6ea5; }
    .loginhead h1 { font-size:22px; }
    .loginhead h2 { margin-top:10px; font-size:14px; }
    .loginbody { padding:30px; }
    .loginuser li { padding:20px 0; line-height:28px; border-bottom:1px dotted #eee; }
    .loginuser em { font-size:12px; color:#999; font-style:normal; }
    .loginuser label { font-size:14px; color:#3a6ea5; font-weight:bold; }
    .loginuser input { margin-left:20px;float:right; padding:0 20px; font-size:14px; color:#fff; text-align:center; line-height:30px; border:0; border-radius:2px; cursor:pointer; background:#3a6ea5; }
    .loginmsg { margin-top:30px; font-size:12px; color:red; line-height:30px; text-align:center; }
    .loginmsg b { color:#333; }
    </style>
    <script type = "text/javascript" src = "./zb_system/script/jquery-1.8.3.min.js"></script>
    <script>
        $(function(){
            $loginh  =  $(".nologin").height();
            $(".nologin").css("margin-top",-$loginh/2);
        });
    </script>
</head>
<body>
    <div class = "nologin">
        <div class = "loginhead">
            <h1>Z-BlogPHP免输入密码登陆工具</h1>
            <h2><?php echo ZC_BLOG_VERSION; ?></h2>
        </div>
        <div class = "loginbody">
            <form id = "frmLogin" method = "post">
                <div class = "loginuser">
                    <ul>
                        <input type = "hidden" name = "userid" id = "userid" value = "0" />
<?php
$zbp->LoadMembers(1);
$i = 0;
foreach ($zbp->members as $key => $m) {
    if ($m->Level < 2) {
        $i += 1;
        echo '<li><em>[ 管理员 ]</em> <label for = "">' . $m->Name . '</label>
        <input type = "button" onclick = "location.href = \'?uid=' . $m->ID . '\'" value = "登录" />
        <input type = "button" onclick = "location.href = \'?uid=' . $m->ID . '&resetpw=1\'" value = "重置密码为12345678" />&nbsp;&nbsp;
        </li>';
    }
}
if ($i == 0) {
    $m = $zbp->GetMemberByID(1);
    echo '<li><em>[ 管理员 ]</em> <label for = "">' . $m->Name . '</label>
    <input type = "button" onclick = "location.href = \'?uid=' . $m->ID . '\'" value = "登录" />
    <input type = "button" onclick = "location.href = \'?uid=' . $m->ID . '&resetpw=1\'" value = "重置密码为12345678" />&nbsp;&nbsp;
    </li>';
}
?>
                    </ul>
                </div>
                <div class = "loginmsg">[注意]  <b>此工具非常危险,使用后请立刻通过<u>FTP删除</u>.</b></div>
            </form>
        </div>
    </div>
</body>
</html>