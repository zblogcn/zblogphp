</head>
<body>
<?php if($zbp->option['ZC_ADMIN_HTML5_ENABLE']){?><header class="header"><?php }else{?><div class="header"><?php }?>
    <div class="logo"><a href="<?php echo $bloghost?>" title="<?php echo htmlspecialchars($blogname)?>" target="_blank"><img src="<?php echo $bloghost?>zb_system/image/admin/none.gif" alt="Z-Blog"/></a></div>
    <div class="user"> <a href="<?php echo $bloghost?>zb_system/cmd.php?act=MemberEdt&amp;id=<?php echo $zbp->user->ID?>" title="<?php echo $lang['msg']['edit']?>"><img src="<?php echo $zbp->user->Avatar?>" width="40" height="40" id="avatar" alt="Avatar" /></a>
      <div class="username"><?php echo $zbp->user->LevelName?>：<?php echo $zbp->user->StaticName?></div>
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
$zbp->GetHint();
?>