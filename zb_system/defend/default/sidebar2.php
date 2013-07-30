{foreach $sidebars2 as $module}
{if !$module.IsHidden}
{template:module}
{/if}
{/foreach}