{foreach $links as $link}
<li {if isset($link.li_id)}id="{$link.li_id}"{/if}><a href="{$link.href}" target="{$link.target}">{$link.content}</a></li>
{/foreach}