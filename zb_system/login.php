<?php

/**
 * Z-Blog with PHP.
 *
 * @author Z-BlogPHP Team
 */

require 'function/c_system_base.php';

$zbp->Load();
if ($zbp->CheckRights('admin')) {
    Redirect302('cmd.php?act=admin');
}
?><!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge,chrome=1" />
    <meta name="robots" content="none" />
    <meta name="viewport" content="width=device-width,viewport-fit=cover" />
    <meta name="generator" content="<?php echo $option['ZC_BLOG_PRODUCT_FULL']; ?>" />
    <meta name="renderer" content="webkit" />
    <link rel="stylesheet" href="css/admin.css?<?php echo $blogversion; ?>" type="text/css" media="screen" />
    <script src="script/jquery-latest.min.js?<?php echo $blogversion; ?>"></script>
    <script src="script/zblogphp.js?<?php echo $blogversion; ?>"></script>
    <script src="script/md5.js?<?php echo $blogversion; ?>"></script>
    <script src="script/c_admin_js_add.php?hash=<?php echo $zbp->html_js_hash; ?>&<?php echo $blogversion; ?>"></script>
    <title><?php echo $blogname . '-' . $lang['msg']['login']; ?></title>
<?php
HookFilterPlugin('Filter_Plugin_Login_Header');
?>
</head>
<body class="body-login">
<div class="bg">
<div id="wrapper">
  <div class="logo"><img src="image/admin/none.gif" title="<?php echo htmlspecialchars($blogname); ?>" alt="<?php echo htmlspecialchars($blogname); ?>"/></div>
  <div class="login">
    <form method="post" action="#">
    <input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken('login', 'minute');?>">
    <dl>
      <dt></dt>
      <dd class="username"><label for="edtUserName"><?php echo $lang['msg']['username']; ?></label><input type="text" id="edtUserName" name="edtUserName" size="20" value="<?php echo GetVars('username', 'COOKIE'); ?>" tabindex="1" /></dd>

      <dd class="password"><label for="edtPassWord"><?php echo $lang['msg']['password']; ?></label><input type="password" id="edtPassWord" name="edtPassWord" size="20" tabindex="2" /></dd>

      <?php if ($zbp->option['ZC_LOGIN_VERIFY_ENABLE']) : ?>
      <dd class="validcode"><label for="edtValidcode"><?php echo $lang['msg']['validcode']; ?></label><input type="text" id="edtValidcode" name="verify" size="20" tabindex="2" />
          <img src="<?php echo $zbp->host; ?>zb_system/script/c_validcode.php?id=login&time=m" onClick="javascript:this.src='<?php echo $zbp->host; ?>zb_system/script/c_validcode.php?id=login&time=m&tm='+Math.random();" alt="validcode"/>
      </dd>
      <?php endif; ?>

    </dl>
    <dl>
      <dt></dt>
      <dd class="checkbox"><input type="checkbox" name="chkRemember" id="chkRemember"  tabindex="98" /><label for="chkRemember"><?php echo $lang['msg']['stay_signed_in']; ?></label></dd>
      <dd class="submit"><input id="btnPost" name="btnPost" type="submit" value="<?php echo $lang['msg']['login']; ?>" class="button" tabindex="99"/></dd>
    </dl>
    <input type="hidden" name="username" id="username" value="" />
    <input type="hidden" name="password" id="password" value="" />
    <input type="hidden" name="savedate" id="savedate" value="1" />
    </form>
  </div>
</div>
</div>
<script>
$("#btnPost").click(function(){

    var strUserName=$("#edtUserName").val();
    var strPassWord=$("#edtPassWord").val();
    var strSaveDate=$("#savedate").val()

    if (strUserName=== "" || strPassWord === ""){
        alert("<?php echo $lang['error']['66']; ?>");
        return false;
    }

    <?php if ($zbp->option['ZC_LOGIN_VERIFY_ENABLE']) : ?>
    if ($("#edtValidcode").val() === ""){
        alert("<?php echo $lang['error']['66']; ?>");
        return false;
    }
    <?php endif; ?>
    $("form").attr("action","cmd.php?act=verify");
    $("#edtUserName").val("");
    $("#edtPassWord").val("");
    $("#username").val(strUserName);
    $("#password").val(MD5(strPassWord));
    $("#savedate").val(strSaveDate);
})

$("#chkRemember").click(function(){
    $("#savedate").attr("value", $("#chkRemember").prop("checked") == true ? 30 : 1);
})

</script>
</body>
</html>
<?php
RunTime();

