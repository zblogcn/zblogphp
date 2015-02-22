<?php  include $this->GetTemplate('header');  ?>
<link rel="alternate" type="application/rss+xml" href="<?php  echo $host;  ?>feed.asp" title="<?php  echo $title;  ?>" />
</head>
  <body class="home blog" id="hasfixed">
    <header class="header">
      <div class="central">
        <h1 class="logo">
          <a href="<?php  echo $host;  ?>" title="<?php  echo $name;  ?>-<?php  echo $title;  ?>"><?php  echo $name;  ?></a>
        </h1>
        <ul class="nav">
			<?php  echo $modules['navbar']->Content;  ?>
        </ul>
        <ul class="header-menu">
            <li class="menu-follow">
			<a class="btn btn-arrow btn-headermenu" href="javascript:;">订阅关注</a>
            <div class="popup-layer">
              <div class="popup">
				<?php  echo $zc_tm_setweibo;  ?>
                <div class="popup-follow-feed">
                <h4>订阅到：</h4>
                <a target="_blank" href="http://mail.qq.com/cgi-bin/feed?u=<?php  echo $host;  ?>">QQ邮箱</a>
                <a target="_blank" href="http://xianguo.com/subscribe?url=<?php  echo $host;  ?>">鲜果</a>
                <a target="_blank" href="http://reader.yodao.com/#url=<?php  echo $host;  ?>">有道</a>
                <h4>订阅地址：</h4>
                <input class="ipt" type="text" readonly="readonly" value="<?php  echo $host;  ?>feed.php" /></div>
				<?php  echo $zc_tm_setfeedtomail;  ?>
              </div>
            </div>
          </li>
        </ul>
        <form method="post" class="search-form" action="<?php  echo $host;  ?>zb_system/cmd.php?act=search">
          <input class="search-input" name="q" type="text" placeholder="输入关键字搜索" autofocus="" x-webkit-speech="" />
          <input class="btn btn-primary search-submit" type="submit" value="搜索" />
        </form>
      </div>
    </header>
    <section class="focus">
      <div class="central">
        <div class="toptip"><?php  echo $subname;  ?></div>
      </div>
    </section>
    <section class="central container">
      <div class="content-wrap">
        <div class="content">
<?php  foreach ( $articles as $article) { ?> 

<?php if ($article->IsTop) { ?>
<?php  include $this->GetTemplate('post-istop');  ?>
<?php }else{  ?>
<?php  include $this->GetTemplate('post-multi');  ?>
<?php } ?>

<?php  }   ?>
			<div class="paging">
				<?php  include $this->GetTemplate('pagebar');  ?>
			</div>
        </div>
      </div>
      <aside class="sidebar">
		<?php  include $this->GetTemplate('sidebar');  ?>
      </aside>
    </section>
<?php  include $this->GetTemplate('footer');  ?>