<?php die(); ?>
<header class="header">
  <div class="logo">
    <a href="{$host}" title="{htmlspecialchars($name)}" target="_blank"><img src="{$host}zb_system/image/admin/none.gif" alt="Z-Blog" /></a>
  </div>
  <div class="user">
    <a class="usravatar" href="{$host}zb_system/cmd.php?act=MemberEdt&amp;id={$zbp->user->ID}" title="{$lang['msg']['edit']}"><img src="{$zbp->user->Avatar}" id="avatar" alt="Avatar" /></a>
    <div class="username"><span>{$zbp->user->LevelName}：{$zbp->user->StaticName}</span></div>
    <div class="userbtn">
      <a class="profile" href="{$host}" title="" target="_blank">
        <i class="icon-globe2"></i>
        <span>{$lang['msg']['return_to_site']}</span>
      </a>
      <a class="logout" href="{BuildSafeCmdURL('act=logout')}" title="">
        <i class="icon-power"></i>
        <span>{$lang['msg']['logout']}</span>
      </a>
    </div>
  </div>
  <div class="menu">
    <ul id="topmenu">
      {ResponseAdmin_TopMenu()}
    </ul>
  </div>
</header>
