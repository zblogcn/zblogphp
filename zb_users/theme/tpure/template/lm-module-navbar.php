<li class="{$id}-item {if count($item.subs)}subcate{/if}">
<a href="{$item.href}" target="{$item.target}" title="{$item.title}">{$item.ico}{$item.text}</a>
    {if count($item.subs)}
    <div class="subnav">
        {foreach $item.subs as $itemSub}
        <a href="{$itemSub.href}" target="{$itemSub.target}" title="{$itemSub.title}">{$itemSub.ico}{$itemSub.text}</a>
        {/foreach}
    </div>
    {/if}
</li>