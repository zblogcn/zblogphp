<?php die(); ?>
<section class="main {$action}">
  <div id="divMain">
    <div class="divHeader">{$main.Header}</div>
    <div class="SubMenu">{$main.SubMenu}</div>
    <div id="divMain2">
        {$main.Content}
    </div>
    <script>
      AddHeaderFontIcon("{$main.HeaderIcon}");
      ActiveTopMenu("{$main.ActiveTopMenu}");
      ActiveLeftMenu("{$main.ActiveLeftMenu}");
    </script>
  </div>
</section>