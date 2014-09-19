</head>
<body>

<nav class="navbar navbar-inverse navbar-static-top visible-xs-block" role="navigation">
    <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="<?php echo $bloghost?>" title="<?php echo htmlspecialchars($blogname)?>" target="_blank" class="navbar-brand">
			Z-Blog管理后台
		</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
        </div><!--/.nav-collapse -->
    </div>
</nav>


<header class="header">
	<div class="logo">
		<a href="<?php echo $bloghost?>" title="<?php echo htmlspecialchars($blogname)?>" target="_blank">
			<img src="<?php echo $bloghost?>zb_system/image/admin/none.gif" alt="Z-Blog"/></a>
	</div>
	<div class="user">
		<a href="<?php echo $bloghost?>zb_system/cmd.php?act=MemberEdt&amp;id=<?php echo $zbp->user->ID?>" title="<?php echo $lang['msg']['edit']?>">
			<img src="<?php echo $zbp->user->Avatar?>" width="40" height="40" id="avatar" alt="Avatar" />
		</a>
		<div class="username">
			<?php echo $zbp->user->LevelName?>：
			<?php echo $zbp->user->StaticName?></div>
		<div class="userbtn">
			<a class="profile" href="<?php echo $bloghost?>" title="" target="_blank">
				<span class="glyphicon glyphicon-home"></span> <?php echo $lang['msg']['return_to_site']?></a>
			&nbsp;&nbsp;
			<a class="logout" href="<?php echo $bloghost?>zb_system/cmd.php?act=logout" title="">
				<span class="glyphicon glyphicon-off"></span> <?php echo $lang['msg']['logout']?></a>
		</div>
	</div>
	<nav class="menu">
		<ul id="topmenu">
			<?php
ResponseAdmin_TopMenu()
?></ul>
	</nav>
</header>
<?php
require $blogpath . 'zb_system/admin/admin_left.php';
?>
<section class="main">
	<?php
$zbp->GetHint();

?>
