{template:header}
<meta name="Keywords" content="<#article/tagtoname#>">
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
	{if $article.Type==ZC_POST_TYPE_ARTICLE}
		<section class="focus">
		  <div class="central">
			<div class="toptip">
			<strong>当前位置:&nbsp;&nbsp;</strong>
			<strong><a href="{$host}">网站首页</a></strong> 
			{if $article.Category.ParentID > 0}{template:post-nav}{else}>>  <a href="{$article.Category.Url}" title="查看' {$article.Category.Name} '中的全部文章">{$article.Category.Name}</a>{/if}
			</div>
		  </div>
		</section>
	{/if}
    <section class="central container">
      <div class="content-wrap">
        <div class="content">
			{if $article.Type==ZC_POST_TYPE_ARTICLE}
				{template:post-single}
			{else}
				{template:post-page}
			{/if}
        </div>
      </div>
      <aside class="sidebar">
		{template:sidebar}
      </aside>
    </section>
{template:footer}