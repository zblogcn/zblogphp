<?php exit(); ?>
<script>
    AddHeaderFontIcon("{$main.HeaderIcon}");
    ActiveTopMenu("{$main.ActiveTopMenu}");
    ActiveLeftMenu("{$main.ActiveLeftMenu}");
</script>
{$footer}
{php}HookFilterPlugin('Filter_Plugin_Admin_Footer');{/php}
