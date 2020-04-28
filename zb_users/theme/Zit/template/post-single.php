{$userName=$article.Author.Name}
{if $cfg->StaticName}{$userName=$article.Author.StaticName}{/if}
<div id="topic">
  <nav id="path" class="kico-dao kico-gap"><a href="{$host}">{$msg.home}</a> | <a href="{$article.Category.Url}">{$article.Category.Name}</a> | <a href="{$article->TimeUrl}">{$article.Time('Y-m-d')}</a></nav>
  <h4>
    {foreach $article.Tags as $tag}
    <a href="{$tag.Url}" class="tag">{$tag.Name}</a> 
    {/foreach}
  </h4>
  <h1>{$article.Title}</h1>
  <h5><img src="{$article.Author.Avatar}" class="avatar" alt="{$userName}"> <a href="{$article.Author.Url}" title="{$msg.view} {$userName}{$msg.logs}">{$userName}</a> <span class="kico-time"><dfn>{$msg.post}</dfn>{$article.Time('Y-m-d H:i:s')}</span> <span class="kico-eye"><dfn>{$msg.views}</dfn>{$article.ViewNums}</span> <span  class="kico-ping"><dfn>{$msg.comments}</dfn>{$article.CommNums}</span></h5>
  <p><a href="#cmts" class="more"><span class="zit">{if $article.CommNums}{$article.CommNums}{$msg.partake}{else}{$msg.sofa}{/if}</span>{$msg.comment}</a></p>
</div>
{$blank='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=='}
<main id="main">
  <article id="cont">{$article.Content}</article>
  <div id="rel">
    <h3 class="zit kico-zi">{$cfg->RelatedTitle}</h3>
    <ul>
    {if $article.Prev}
    <li class="log">
      <a href="{$article.Prev.Url}" title="{$msg.view}{$article.Prev.Title}》{$msg.details}">
        <img src="{if $article.Prev.Cover}{$article.Prev.Cover}{else}{$blank}{/if}" alt="{$article.Prev.Title}" class="cover">
        <span class="pane">
          <em class="zit kico-prev">{$msg.prev}</em>
          <b>{$article.Prev.Title}</b>
          <span><small class="kico-time"><dfn>{$msg.post}</dfn>{$article.Prev.Time('Y-m-d')}</small> <small class="kico-eye"><dfn>{$msg.views}</dfn>{$article.Prev.ViewNums}</small> <small class="kico-ping"><dfn>{$msg.comments}</dfn>{$article.Prev.CommNums}</small></span>
        </span>
      </a>
    </li>
    {/if}
    {if $article.Next}
    <li class="log">
      <a href="{$article.Next.Url}" title="{$msg.view}《{$article.Next.Title}》{$msg.details}">
        <img src="{if $article.Next.Cover}{$article.Next.Cover}{else}{$blank}{/if}" alt="{$article.Next.Title}" class="cover">
        <span class="pane">
          <em class="zit kico-next">{$msg.next}</em>
          <b>{$article.Next.Title}</b>
          <span><small class="kico-time"><dfn>{$msg.post}</dfn>{$article.Next.Time('Y-m-d')}</small> <small class="kico-eye"><dfn>{$msg.views}</dfn>{$article.Next.ViewNums}</small> <small class="kico-ping"><dfn>{$msg.comments}</dfn>{$article.Next.CommNums}</small></span>
        </span>
      </a>
    </li>
    {/if}
    {foreach $article.RelatedList as $related}
      <li class="log">
        <a href="{$related.Url}" title="{$msg.view}《{$related.Title}》{$msg.details}">
          <img src="{if $related.Cover}{$related.Cover}{else}{$blank}{/if}" alt="{$related.Title}" class="cover">
          <span class="pane">
            <time class="zit">{$related.Time('Y-m-d')}</time>
            <b>{$related.Title}</b>
            <span><small class="kico-eye"><dfn>{$msg.views}</dfn>{$related.ViewNums}</small> <small class="kico-ping"><dfn>{$msg.comments}</dfn>{$related.CommNums}</small>
            {$tagi=0}
            {foreach $related.Tags as $tag}
              {php}if($tagi++>2) break;{/php}
              <small class="kico-hash">{$tag.Name}</small>
            {/foreach}
            {$tagi=null}
            </span>
          </span>
        </a>
      </li>
    {/foreach}
    </ul>
  </div>
  {template:comments}
</main>
<aside id="side">
{template:sidebar3}
{template:kit-mside}
</aside>