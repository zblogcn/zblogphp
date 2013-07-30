{foreach $sidebars4 as $module}
{if !$module.IsHidden}
{template:module}
{/if}
{/foreach}