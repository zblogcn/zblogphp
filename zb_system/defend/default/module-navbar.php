{foreach $links as $link}
<li{if isset($link.li_id)} id="{$link.li_id}"{/if}>{if ('<dl' == substr($link.content, 0, 3) || '<ul' == substr($link.content, 0, 3) || '<ol' == substr($link.content, 0, 3))} {$link.content} {else} <a href="{$link.href}"{if isset($link.target)} target="{$link.target}{/if}"{if isset($link.id)} id="{$link.id}"{/if}>{$link.content}</a> {/if}</li>
{/foreach}