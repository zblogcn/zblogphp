<?php  include $this->GetTemplate('header');  ?>
</head>
<body class="multi default">
<?php  include $this->GetTemplate('c_top');  ?>
		<div id="mission"><?php  if(isset($modules['new'])){echo $modules['new']->Content;}  ?></div><ins></ins>
<?php  foreach ( $articles as $article) { ?> 
<?php if ($article->IsTop) { ?>
<?php  include $this->GetTemplate('post-istop');  ?>
<?php }else{  ?>
<?php  include $this->GetTemplate('post-multi');  ?>
<?php } ?>
<?php  }   ?>
		<div class="pager"><span class="pager-list"><?php  include $this->GetTemplate('pagebar');  ?></span></div> 

		<span class="clear"></span><a href="<?php  echo $feedurl;  ?>" class="feed-icon"><img src="<?php  echo $host;  ?>zb_users/theme/garland/style/rss-sq.png" alt="聚合内容" title="聚合内容" height="16" width="16"></a>

<?php  include $this->GetTemplate('footer');  ?>