<?php die(); ?>
      <div class="content">
        <div class="title">
          <h2><span>{$main->Header}</span> <ul id="topmenu">{if $main.Action=='admin'}{ResponseAdmin_TopMenu()}{/if}{$main->SubMenu}</ul></h2>
          {if $main.Header=='admin'}<p>欢迎回来，管理员！这里是您网站的概览信息</p>{/if}
        </div>
{php}$zbp->GetHint();{/php}
{php}HookFilterPlugin('Filter_Plugin_Admin_Hint');{/php}
        {$main.Content}
  </div>
