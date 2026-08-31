  {$footer}
{php}HookFilterPlugin('Filter_Plugin_Admin_Footer');{/php}
  {php}<?php
if ($main->ActiveTopMenu != '') {
    echo '<script>ActiveTopMenu("'.$main->ActiveTopMenu.'");</script>';
}
if ($main->ActiveLeftMenu != '') {
    echo '<script>ActiveTopMenu("'.$main->ActiveLeftMenu.'");</script>';
}
  ?>{/php}
