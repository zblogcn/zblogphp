{template:header}
<body class="multi default">
{template:top}
<div class="nevbarbg">
  <div class="navbar">
    <ul>
      {module:navbar}
    </ul>
  </div>
</div>
<div id="content">  
  <div id="main">
    {foreach $articles as $article}
		{if $article.IsTop}
		{template:post-istop}
		{else}
		{template:post-multi}
		{/if}
	{/foreach}
    <div class="pagebar">
      {template:pagebar}
    </div>   
  </div> 
  <div id="sidebar">
    {template:sidebar}
  </div>
  <div class="clear"></div>
</div>
{template:footer}