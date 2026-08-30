{foreach $links as $link}
<li{if isset($link.li_id)} id="{$link.li_id}"{/if}><a href="{$link.href}"{if isset($link.target)} target="{$link.target}{/if}"{if isset($link.id)} id="{$link.id}"{/if}>{$link.content}</a></li>
{/foreach}