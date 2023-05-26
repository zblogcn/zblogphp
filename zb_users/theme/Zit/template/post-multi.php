{$album=$cfg->ListAlbum&&$article.ImageCount>3}
<article class="log{if $article.IsTop} pin{/if}{if $album} album{elseif $article.Cover} poster{/if}">
{if $article.Cover}
  <figure>
  {if $album}
    <span>
    {foreach $article.AllImages as $k=>$v}
      {if $k<4}<img src="{$v}" alt="{$article.Title}" class="cover">{/if}
    {/foreach}
    </span>
  {else}
    <a href="{$article.Url}"><img src="{$article.Cover}" alt="{$article.Title}" class="cover{if $article.Cover===$host . 'zb_users/theme/Zit/style/bg.jpg'} hue{/if}"></a>
  {/if}
  </figure>
{/if}
  <section class="pane">
    <h4 class="zit">{if $article.IsTop}<b class="kico-flag">{$msg.sticky}</b> {/if}<a href="{$article.Category.Url}" title="{$article.Category.Name}">{$article.Category.Name}</a></h4>
    <h3><a href="{$article.Url}" title="{TransferHTML($article.Title,'[nohtml]')}">{$article.Title}</a></h3>
    <h5>{template:kit-loginfo}</h5>
    <div>{$article.Intro}</div>
    {if $cfg->ListTags&&$article.TagsName&&!$album}<div class="tags"><span class="tag kico-hash">{str_replace(',','</span> <span class="tag kico-hash">',$article.TagsName)}</span></div>{/if}
  </section>
</article>