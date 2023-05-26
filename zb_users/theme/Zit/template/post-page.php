<div id="topic">
  <nav id="path" class="kico-guide kico-gap"><a href="{$host}">{$msg.home}</a> | <a>{$title}</a></nav>
  <h1>{$article.Title}</h1>
  {if !$article.IsLock}<p><a href="#cmts" class="more"><span class="zit">{if $article.CommNums}{$article.CommNums}{$msg.partake}{else}{$msg.sofa}{/if}</span>{if $cfg.GbookID==$article.ID}{$msg.message}{else}{$msg.comment}{/if}</a></p>{/if}
</div>
<main id="main">
  <article id="cont">{$article.Content}</article>
{template:comments}
</main>
<aside id="side">
{if $cfg.GbookID==$article.ID}
  {template:sidebar5}
{else}
  {template:sidebar4}
{/if}
{template:kit-mside}
</aside>