{* Template Name:侧栏模板 *}
{foreach $sidebar as $module}
{$module->Content = str_replace(array_keys($this->replaceTags),array_values($this->replaceTags),$module->Content)}
{template:module}
{/foreach}