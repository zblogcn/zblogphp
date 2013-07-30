{foreach $sidebars3 as $module}
{if !$module.IsHidden}
{template:module}
{/if}
{/foreach}