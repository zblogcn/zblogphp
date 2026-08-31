<?php die(); ?>
      <div class="head">
        <div class="menuico"><span></span><span></span><span></span></div>
        <a href="{$zbp->host}" class="backhome">浏览网站</a>
        <a href="" class="theme light"></a>
        <div class="user">
          <div class="userlink"><span class="userimg"><img src="{$zbp->user->Avatar}"
                alt=""></span><span class="username"><strong>{$zbp->user->StaticName}</strong><em>{$zbp->user->LevelName}</em></span></div>
          <div class="usermenu">
            <ul>
              <li><a href="" class="ico-set">系统设置</a></li>
              <li><a href="" class="ico-user">账户设置</a></li>
              <li><a href="" class="ico-logout">退出登录</a></li>
            </ul>
          </div>
        </div>
      </div>
