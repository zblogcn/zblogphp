{template:header}
<section id="wrap">
  <div class="inner">
  {if $article.Type==ZC_POST_TYPE_ARTICLE}
  {template:post-single}
  {else}
  {template:post-page}
  {/if}
  </div>
</section>
{template:footer}
