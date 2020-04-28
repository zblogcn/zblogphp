{$logTitle=$article.Title}
{$logIntro=SubstrUTF8(TransferHTML($article.Intro,'[nohtml]'),120) . '...'}
{if $logIntro==='...'}{$logIntro=''}{/if}
{if $type==='search'}
{$logTitle=preg_replace('/' . preg_quote(GetVars('q'),'/') . '/i',"<mark>$0</mark>",$logTitle)}
{$logIntro=preg_replace('/' . preg_quote(GetVars('q'),'/') . '/i',"<mark>$0</mark>",$logIntro)}
{/if}
<article class="log{if $article.IsTop} pin{/if}{if $article.Cover} poster{/if}">
  {if $article.Cover}
  <figure><a href="{$article.Url}"><img src="{$article.Cover}" alt="{$article.Title}" class="cover{if $article.Cover===$host . 'zb_users/theme/Zit/style/bg.jpg'} hue{/if}"></a></figure>
  {/if}
  <section class="pane">
    <h4 class="zit">{if $article.IsTop}<b>{$msg.sticky}</b> {/if}<a href="{$article.Category.Url}" title="{$article.Category.Name}">{$article.Category.Name}</a></h4>
    <h3><a href="{$article.Url}" title="{$article.Title}">{$logTitle}</a></h3>
    <h5>{template:kit-loginfo}</h5>
    <div{if $cfg.HideIntro} class="hidem"{/if}>{$logIntro}</div>
    {if $cfg->ListTags&&$article.TagsName}<div class="tags"><span class="tag">{str_replace(',','</span> <span class="tag">',$article.TagsName)}</span></div>{/if}
  </section>
</article>