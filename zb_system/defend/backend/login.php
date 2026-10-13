<?php exit(); ?>
<!DOCTYPE html>
<html lang="{$language}">

<head>
    <meta charset="utf-8" />
    <meta name="generator" content="{$zblogphp}" />

    <title>{$name} - {$title}</title>
    <link rel="stylesheet" href="{$host}zb_system/admin2/{$backendtheme}/style/{$backendtheme}.css?v={$version}">
    <!-- <link rel="stylesheet" href="{$host}zb_system/image/icon/icon.css?v={$version}"> -->
    <script src="{$host}zb_system/script/jquery-2.2.4.min.js?v={$version}"></script>
    <!-- <script src="{$host}zb_system/script/zblogphp.js?v={$version}"></script> -->
    <script src="{$host}zb_system/script/md5.js?v={$version}"></script>
    <!-- <script>
        window.__ADMIN_JS_CONFIG_URL__ = '{$zbp.ajaxurl}admin2';
    </script> -->
    <!-- <script src="{$host}zb_system/admin2/script/c_admin_js_add.js?v={$version}"></script> -->
    <!-- <script src="{$host}zb_system/admin2/{$backendtheme}/script/{$backendtheme}.js?v={$version}"></script> -->
    <!-- <link rel="stylesheet" href="{$host}zb_system/css/admin.css"> -->
    {$header}
    {php}HookFilterPlugin('Filter_Plugin_Login_Header');{/php}
</head>

<body class="admin-login">
    <header class="login-header">
        <h1 class="brand"><img src="{$host}zb_system/image/admin/none.gif" title="{htmlspecialchars($blogname)}" alt="{htmlspecialchars($blogname)}" /></h1>
    </header>


    <main class="login-container">
        <form class="login-form" method="post" action="#">
            <input type="hidden" name="csrfToken" value="{$zbp.GetCSRFToken('login','minute')}">
            <div class="form-row">
                <div class="field username">
                    <label for="edtUserName">{$lang['msg']['username']}</label>
                    <input type="text" id="edtUserName" name="edtUserName" size="20" value="{php}echo GetVars('username', 'COOKIE');{/php}" tabindex="1" />
                </div>

                <div class="field password">
                    <label for="edtPassWord">{$lang['msg']['password']}</label>
                    <input type="password" id="edtPassWord" name="edtPassWord" size="20" tabindex="2" />
                </div>

                {if $zbp->option['ZC_LOGIN_VERIFY_ENABLE']}
                <div class="field validcode">
                    <label for="edtValidcode">{$lang['msg']['validcode']}</label>
                    <input type="text" maxlength="{$zbp.option['ZC_VERIFYCODE_LENGTH']}" id="edtValidcode" name="verify" size="20" tabindex="10" />
                    <img class="captcha-img" src="{$host}zb_system/script/c_validcode.php?id=login&time=m" onClick="javascript:this.src='{$host}zb_system/script/c_validcode.php?id=login&time=m&tm='+Math.random();" alt="validcode" />
                </div>
                <div class="field placeholder"></div>
                {/if}
            </div>

            <div class="form-row actions">
                <label class="remember"><input type="checkbox" name="chkRemember" id="chkRemember" tabindex="98" />{$lang['msg']['stay_signed_in']}</label>
                <input id="btnPost" name="btnPost" type="submit" value="{$lang['msg']['login']}" class="button" tabindex="99" />
            </div>
            <input type="hidden" name="username" id="username" value="" />
            <input type="hidden" name="password" id="password" value="" />
            <input type="hidden" name="savedate" id="savedate" value="1" />
        </form>
    </main>
    <script>
        $("#btnPost").click(function() {
            var strUserName = $("#edtUserName").val();
            var strPassWord = $("#edtPassWord").val();
            var strSaveDate = $("#savedate").val()

            if (strUserName === "" || strPassWord === "") {
                alert("{$lang['error']['66']}");
                return false;
            }

            if ($("#edtValidcode").val() === "" && "{$zbp->option['ZC_LOGIN_VERIFY_ENABLE']}" === "1") {
                alert("{$lang['error']['66']}");
                return false;
            }


            $("form").attr("action", "{$zbp->cmdurl}?act=verify");
            $("#edtUserName").val("");
            $("#edtPassWord").val("");
            $("#username").val(strUserName);
            $("#password").val(MD5(strPassWord));
            $("#savedate").val(strSaveDate);
        })

        $("#chkRemember").click(function() {
            $("#savedate").attr("value", $("#chkRemember").prop("checked") == true ? 30 : 1);
        })
    </script>
</body>

</html>
