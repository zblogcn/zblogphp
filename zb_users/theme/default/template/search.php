{* Template Name:搜索页 *}
<!DOCTYPE html>
<html lang="{$lang['lang_bcp47']}">

<head>
  {template:header}
</head>

<body class="multi {$type}">
  <div id="divAll">
    <div id="divPage">
      <div id="divMiddle">
        <div id="divTop">
          <h1 id="BlogTitle"><a href="{$host}">{$name}</a></h1>
          <h3 id="BlogSubTitle">{$subname}</h3>
        </div>
        <div id="divNavBar">
          <ul>
            {module:navbar}
          </ul>
        </div>
        <div id="divMain">
          <div class="post istop istop-post">
            <h2 class="post-title">{$article.Title}</h2>
          </div>
          {foreach $articles as $article}
          {template:post-search}
          {/foreach}
          {if count($articles)>0}
          <div class="pagebar">{template:pagebar}</div>
          {/if}
        </div>
        <div id="divSidebar">
          {template:sidebar}
        </div>
        {template:footer}
      </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
</body>

</html>