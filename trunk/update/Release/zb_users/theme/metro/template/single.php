{template:header}
<body class="single">
{template:top}
<div class="nevbarbg">
  <div class="navbar">
    <ul>
      {$modules['navbar'].Content}
    </ul>
  </div>
</div>
<div id="content">
  <div id="main">
    {if $article.Type==ZC_POST_TYPE_ARTICLE}
		{template:post-single}
	{else}
		{template:post-page}
	{/if}
  </div>
  <div id="sidebar">
    {template:sidebar}
  </div>
  <div class="clear"></div>
</div>
{template:footer}