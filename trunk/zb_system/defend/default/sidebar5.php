{foreach $sidebars5 as $module}
{if !$module.IsHidden}
{template:module}
{/if}
{/foreach}