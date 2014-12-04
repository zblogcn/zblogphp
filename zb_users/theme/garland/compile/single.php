<?php  include $this->GetTemplate('header');  ?>
	<link rel="alternate" type="application/rss+xml" href="<?php  echo $feedurl;  ?>" title="<?php  echo $name;  ?>" />
</head>
<body class="multi default">
<?php  include $this->GetTemplate('c_top');  ?>
		<div id="mission"><?php  echo $modules['new']->Content;  ?></div><ins></ins>
<?php if ($article->Type==ZC_POST_TYPE_ARTICLE) { ?>
<?php  include $this->GetTemplate('post-single');  ?>
<?php }else{  ?>
<?php  include $this->GetTemplate('post-page');  ?>
<?php } ?>
		<span class="clear"></span><a href="<?php  echo $feedurl;  ?>" class="feed-icon"><img src="<?php  echo $host;  ?>zb_users/theme/garland/style/rss-sq.png" alt="聚合内容" title="聚合内容" height="16" width="16"></a>

<?php  include $this->GetTemplate('footer');  ?>