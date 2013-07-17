<?php
require_once './function/c_system_base.php';

$zbp->Initialize();
$blogtitle=$lang['ZC_MSG']['009'];
?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
	<meta name="generator" content="Z-BlogPHP" />
	<meta name="robots" content="none" />
	<link rel="stylesheet" rev="stylesheet" href="css/admin.css" type="text/css" media="screen" />
	<script language="JavaScript" src="script/common.js" type="text/javascript"></script>
	<script language="JavaScript" src="script/md5.js" type="text/javascript"></script>
	<title><?php echo $option['ZC_BLOG_TITLE'] . '-' . $blogtitle?></title>
</head>
<body>
<div class="bg">
<div id="wrapper">
  <div class="logo"><img src="image/admin/none.gif" title="Z-BlogPHP" alt="Z-BlogPHP"/></div>
  <div class="login">
    <form id="frmLogin" method="post" action="">
    <dl>
      <dd><label for="edtUserName"><?php echo $lang['ZC_MSG']['003']?>:</label><input type="text" id="edtUserName" name="edtUserName" size="20" tabindex="1" /></dd>
      <dd><label for="edtPassWord"><?php echo $lang['ZC_MSG']['002']?>:</label><input type="password" id="edtPassWord" name="edtPassWord" size="20" tabindex="2" /></dd>
    </dl>
    <dl>
      <dd class="checkbox"><input type="checkbox" name="chkRemember" id="chkRemember"  tabindex="3" /><label for="chkRemember"><?php echo $lang['ZC_MSG']['114']?></label></dd>
      <dd class="submit"><input id="btnPost" name="btnPost" type="submit" value="<?php echo $lang['ZC_MSG']['260']?>" class="button" tabindex="4"/></dd>
    </dl>
	<input type="hidden" name="username" id="username" value="" />
	<input type="hidden" name="password" id="password" value="" />
	<input type="hidden" name="savedate" id="savedate" value="0" />
    </form>
  </div>
</div>
</div>

<script language="JavaScript" type="text/javascript">

if(GetCookie("username")){$("#edtUserName").val(unescape(GetCookie("username")))};

$("#btnPost").click(function(){

	var strUserName=$("#edtUserName").val();
	var strPassWord=$("#edtPassWord").val();
	var strSaveDate=$("#savedate").val()

	if((strUserName=="")||(strPassWord=="")){
		alert("<?php echo $lang['ZC_MSG']['010']?>");
		return false;
	}

	$("#edtUserName").remove();
	$("#edtPassWord").remove();

	strUserName=strUserName;
	strPassWord=MD5(strPassWord);

	$("#frmLogin").attr("action","cmd.php?act=verify");
	$("#username").val(strUserName);
	$("#password").val(strPassWord);
	$("#savedate").val(strSaveDate);
})

$(document).ready(function(){ 
	if($.browser.msie){
		$(":checkbox").css("margin-top","4px");
	}
});

$("#chkRemember").click(function(){
	$("#savedate").attr("value",$("#chkRemember").attr("checked")=="checked"?30:0);
})
</script>
</body>
</html>
<?php

$zbp->Terminate();

RunTime();
?>