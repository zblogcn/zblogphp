{template:header}
<section id="wrap">
  <div class="inner">
    <div id="topic">
      <nav id="path" class="kico-guide kico-gap"><a href="{$host}">{$msg.home}</a> | <a>{$title}</a></nav>
      <h1>{$article.Title}</h1>
      {if !$article.IsLock}<p><a href="#cmts" class="more"><span class="zit">{if $article.CommNums}{$article.CommNums}{$msg.partake}{else}{$msg.sofa}{/if}</span>{if $cfg.GbookID==$article.ID}{$msg.message}{else}{$msg.comment}{/if}</a></p>{/if}
    </div>
    <article id="cont">{$article.Content}</article>
    {template:comments}
  </div>
</section>
{template:footer}