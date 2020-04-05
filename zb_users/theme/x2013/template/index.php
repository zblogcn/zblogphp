{template:header}
<link rel="alternate" type="application/rss+xml" href="{$host}feed.php" title="{$title}" />
</head>
  <body class="home blog" id="hasfixed">
    <header class="header">
      <div class="central">
        <h1 class="logo">
          <a href="{$host}" title="{$name}-{$title}">{$name}</a>
        </h1>
        <ul class="nav">
			{$modules['navbar'].Content}
        </ul>
        <ul class="header-menu">
            <li class="menu-follow">
			<a class="btn btn-arrow btn-headermenu" href="javascript:;">订阅关注</a>
            <div class="popup-layer">
              <div class="popup">
				{$zc_tm_setweibo}
                <div class="popup-follow-feed">
                <h4>订阅到：</h4>
                <a target="_blank" href="http://mail.qq.com/cgi-bin/feed?u={$host}">QQ邮箱</a>
                <a target="_blank" href="http://xianguo.com/subscribe?url={$host}">鲜果</a>
                <a target="_blank" href="http://reader.yodao.com/#url={$host}">有道</a>
                <h4>订阅地址：</h4>
                <input class="ipt" type="text" readonly="readonly" value="{$host}feed.php" /></div>
				{$zc_tm_setfeedtomail}
              </div>
            </div>
          </li>
        </ul>
        <form method="post" class="search-form" action="{$host}zb_system/cmd.php?act=search">
          <input class="search-input" name="q" type="text" placeholder="输入关键字搜索" autofocus="" x-webkit-speech="" />
          <input class="btn btn-primary search-submit" type="submit" value="搜索" />
        </form>
      </div>
    </header>
    <section class="focus">
      <div class="central">
        <div class="toptip">{$subname}</div>
      </div>
    </section>
    <section class="central container">
      <div class="content-wrap">
        <div class="content">
{foreach $articles as $article}

{if $article->IsTop}
{template:post-istop}
{else}
{template:post-multi}
{/if}

{/foreach}
			<div class="paging">
				{template:pagebar}
			</div>
        </div>
      </div>
      <aside class="sidebar">
		{template:sidebar}
      </aside>
    </section>
{template:footer}