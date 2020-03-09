{if $cfg->Profile}
<div class="pane hidem" id="minfo">
  <img src="{$author.Avatar}">
  <h3><a href="{$author.Url}">{$author.Name}</a></h3>
  {if $author.Intro}<p>{$author.Intro}</p>{/if}
</div>
{/if}