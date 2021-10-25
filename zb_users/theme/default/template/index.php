{* Template Name:首页及列表页 *}
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
          <h2 id="BlogSubTitle">{$subname}</h2>
        </div>
        <div id="divNavBar">
          <ul>
            {module:navbar}
          </ul>
        </div>
        <div id="divMain">
          {foreach $articles as $article}

          {if $article.TopType}
          {template:post-istop}
          {else}
          {template:post-multi}
          {/if}

          {/foreach}
          <div class="pagebar">{template:pagebar}</div>
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