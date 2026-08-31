<?php die(); ?>
      <div class="content">
        <div class="title">
          <h2>仪表盘 <a href="" class="rebuild">清空缓存、重建编译</a></h2>
          <p>欢迎回来，管理员！这里是您网站的概览信息</p>
        </div>

    {ResponseAdmin_TopMenu()}

{php}$zbp->GetHint();{/php}
{php}HookFilterPlugin('Filter_Plugin_Admin_Hint');{/php}

        {$main.Content}

  </div>
