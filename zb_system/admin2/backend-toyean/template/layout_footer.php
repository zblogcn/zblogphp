<?php exit(); ?>
<script {if isset($main.Js_Nonce)}nonce="{$main.Js_Nonce}"{/if}>
    {if strpos($main.HeaderIcon, '/')!==false}AddHeaderIcon("{$main.HeaderIcon}");{/if}
    {if strpos($main.HeaderIcon, '/')===false}AddHeaderFontIcon("{$main.HeaderIcon}");{/if}
    {if !empty($main.ActiveTopMenu)}ActiveTopMenu("{$main.ActiveTopMenu}");{/if}
    {if !empty($main.ActiveLeftMenu)}ActiveLeftMenu("{$main.ActiveLeftMenu}");{/if}
</script>
{php}if(isset($main->HtmlFooter)){$header.=$main->HtmlFooter;}{/php}
{$footer}
{php}HookFilterPlugin('Filter_Plugin_Admin_Footer');{/php}
