</head>
<body>
<div id="header">
  <div class="top">
    <div class="logo"><a href="http://www.rainbowsoft.org/" title="Z-Blog" target="_blank"><img src="<?php echo $bloghost?>zb_system/image/admin/logo.png" alt="Z-Blog"/></a></div>
    <div class="user"> <a href="<?php echo $bloghost?>zb_system/cmd.php?act=UserEdt&amp;id=<?php echo $zbp->user->ID?>" title="<?php echo $lang['ZC_MSG']['078']?>"><img src="<?php echo $bloghost?>zb_system/image/admin/avatar.png" width="40" height="40" id="avatar" alt="Avatar" /></a>
      <div class="username"><?php echo $zbp->user->LevelName?>：<?php echo $zbp->user->Name?></div>
      <div class="userbtn"><a class="profile" href="<?php echo $bloghost?>" title="" target="_blank"><?php echo $lang['ZC_MSG']['065']?></a>&nbsp;&nbsp;<a class="logout" href="<?php echo $bloghost?>ZB_SYSTEM/cmd.php?act=logout" title=""><?php echo $lang['ZC_MSG']['020']?></a></div>
    </div>
    <div class="menu">
      <ul id="topmenu">
<?php
ResponseAdminTopMenu()
?>
      </ul>
    </div>
  </div>
</div>
<div id="main">
<?php
require_once $blogpath . 'zb_system/admin/admin_left.php';
?>
<div class="main_right">
  <div class="yui">
    <div class="content">
<?php

if(GetVars('batch','COOKIE')>0){
	if(GetVars('batch','COOKIE') == GetVars('batchorder','COOKIE')){
		#Session("batchtime")=0
?>
<div id="batch">
<iframe style="width:20px;height:20px;" frameborder="0" scrolling="no" src="<?php echo $bloghost?>zb_system/cmd.php?act=batch"></iframe><p><?php echo $lang['ZC_MSG']['110']?>...</p>
</div>
<?php
	}else{
?>
<div id="batch"><img src="<?php echo $bloghost?>zb_system/image/admin/error.png" width="16"/><p><?php echo $lang['ZC_MSG']['273']?></p></div>
<?php
	}
}else{
	setcookie("batchorder", 0);
}

?>