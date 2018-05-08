<?php
require '../zb_system/function/c_system_base.php';
$zbp->Load();

if (isset($_GET['uid'])) {
    $m = $zbp->members[$_GET['uid']];
    $un = $m->Name;
    $zbp->user = $m;
    if ($blogversion > 131221) {
        $ps = md5($m->Password . $zbp->guid);
    } else {
        $ps = md5($m->Password . $zbp->path);
    }

    if ($blogversion >= 151910) {
        SetLoginCookie($m, 0);
    } else {
        setcookie("username", $un, 0, $zbp->cookiespath);
        setcookie("password", $ps, 0, $zbp->cookiespath);
    }

    if (isset($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'])) {
        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname();
        }
    }

    Redirect('../zb_system/admin/?act=admin');
    die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <title>Z-BlogPHP密码重置工具</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: normal;
        }

        input, textarea, select, label {
            font-family: microsoft yahei;
        }

        ul {
            list-style: none;
        }

        body {
            font-family: microsoft yahei;
            background: #f0f0f0;
        }

        .nologin {
            width: 600px;
            margin: 0 auto 0;
            background: #fff;
            position: fixed;
            top: 20%;
            right: 0;
            left: 0;
        }

        .loginhead {
            padding: 30px 0;
            color: #fff;
            text-align: center;
            background: #305e8d;
        }

        .loginhead h1 {
            font-size: 22px;
        }

        .loginhead h2 {
            margin-top: 10px;
            font-size: 14px;
        }

        .loginbody {
            padding: 30px;
        }

        .loginuser li {
            padding: 20px 0;
            line-height: 28px;
            border-bottom: 1px dotted #eee;
        }

        .loginuser em {
            font-size: 12px;
            color: #999;
            font-style: normal;
        }

        .loginuser label {
            font-size: 14px;
            color: #305e8d;
            font-weight: bold;
        }

        .loginuser input {
            float: right;
            padding: 0 20px;
            font-size: 14px;
            color: #fff;
            text-align: center;
            line-height: 30px;
            border: 0;
            border-radius: 2px;
            cursor: pointer;
            background: #305e8d;
        }

        .loginmsg {
            margin-top: 30px;
            font-size: 12px;
            color: red;
            line-height: 30px;
            text-align: center;
        }

        .loginmsg b {
            color: #333;
        }
    </style>
    <script type="text/javascript" src="./zb_system/script/jquery-1.8.3.min.js"></script>
    <script>
      $(function () {
        $loginh = $('.nologin').height()
        $('.nologin').css('margin-top', -$loginh / 2)
      })
    </script>
</head>
<body>
<div class="nologin">
    <div class="loginhead">
        <h1>Z-BlogPHP免输入密码登陆工具</h1>
        <h2>Powered By Z-BlogPHP <?php echo ZC_BLOG_VERSION; ?></h2>
    </div>
    <div class="loginbody">
        <form id="frmLogin" method="post">
            <div class="loginuser">
                <ul>
                    <input type="hidden" name="userid" id="userid" value="0"/>
                    <?php
                    foreach ($zbp->members as $key => $m) {
                        if ($m->Level < 2) {
                            echo '<li><em>[ 管理员 ]</em> <label for="">' . $m->Name . '</label><input type="button" onclick="window.location=\'?uid=' . $m->ID . '\'" value="登录" /></li>';
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="loginmsg">[注意] <b>此工具非常危险,使用后请立刻通过FTP删除或改名.</b></div>
        </form>
    </div>
</div>
</body>
</html>