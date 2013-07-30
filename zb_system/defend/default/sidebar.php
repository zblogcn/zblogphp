{foreach $sidebars as $module}
{if !$module.IsHidden}
{template:module}
{/if}
{/foreach}