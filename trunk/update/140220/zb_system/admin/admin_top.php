</head>
<body>
<?php if($zbp->option['ZC_ADMIN_HTML5_ENABLE']){?><header class="header"><?php }else{?><div class="header"><?php }?>
    <div class="logo"><a href="http://www.zblogcn.com/" title="<?php echo $option['ZC_BLOG_PRODUCT'];?>" target="_blank"><img src="<?php echo $bloghost?>zb_system/image/admin/none.gif" alt="Z-Blog"/></a></div>
    <div class="user"> <a href="<?php echo $bloghost?>zb_system/cmd.php?act=MemberEdt&amp;id=<?php echo $zbp->user->ID?>" title="<?php echo $lang['msg']['edit']?>"><img src="<?php echo $zbp->user->Avatar?>" width="40" height="40" id="avatar" alt="Avatar" /></a>
      <div class="username"><?php echo $zbp->user->LevelName?>：<?php echo $zbp->user->Name?></div>
      <div class="userbtn"><a class="profile" href="<?php echo $bloghost?>" title="" target="_blank"><?php echo $lang['msg']['return_to_site']?></a>&nbsp;&nbsp;<a class="logout" href="<?php echo $bloghost?>zb_system/cmd.php?act=logout" title=""><?php echo $lang['msg']['logout']?></a></div>
    </div>
    <div class="menu">
      <ul id="topmenu">
<?php
ResponseAdmin_TopMenu()
?>
      </ul>
    </div>
<?php if($zbp->option['ZC_ADMIN_HTML5_ENABLE']){?></header><?php }else{?></div><?php }?>
<?php
require $blogpath . 'zb_system/admin/admin_left.php';
?>
<?php if($zbp->option['ZC_ADMIN_HTML5_ENABLE']){?><section class="main"><?php }else{?><div class="main"><?php }?>
<?php
/*
if(GetVars('batch','COOKIE')){
?>
<div id="batch">
<iframe style="width:20px;height:20px;" frameborder="0" scrolling="no" src="<?php echo $bloghost?>zb_system/cmd.php?act=batch"></iframe><p><?php echo $lang['msg']['batch_operation']?>...</p>
</div>
<?php
	}else{
?>
<!--<div id="batch"><img src="<?php echo $bloghost?>zb_system/image/admin/error.png" width="16"/><p><?php echo $lang['msg']['previous_operation_not_finished']?></p></div>-->
<?php
}
*/
$zbp->GetHint();
?>