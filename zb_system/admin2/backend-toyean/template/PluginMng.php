<?php exit(); ?>

<div class="postlist">
  <div class="tr thead"> 
    <div class="td-5"></div>
    <div class="td-full">{$zbp.lang['msg']['name']}</div>
		<div class="td-15 td-author">{$zbp.lang['msg']['author']}</div>
		<div class="td-10">{$zbp.lang['msg']['date']}</div>
    <div class="td-full"></div>
  </div>
</div>

{foreach $plugins as $plugin}
{php}<?php
      $pluginIdEscaped = htmlspecialchars($plugin->id);
      $pluginNoteEscaped = htmlspecialchars($plugin->note);
      $pluginNameEscaped = htmlspecialchars($plugin->name);
      $pluginVersionEscaped = htmlspecialchars($plugin->version);
      $pluginAuthorUrlEscaped = htmlspecialchars($plugin->author_url);
      $pluginAuthorNameEscaped = htmlspecialchars($plugin->author_name);
      $pluginModifiedEscaped = htmlspecialchars($plugin->modified);
?>{/php}
<div class="tr"> 
  <div class="td-5{if $plugin.type == 'plugin'} plugin{/if}{if $plugin.IsUsed()} plugin-on{/if}"><img {if !$plugin.IsUsed()}style="opacity:0.2" {/if} src="{$plugin.GetLogo()}" alt="" width="32" height="32"></div>
  <div class="td-full"><span class="plugin-note" title="{$pluginNoteEscaped}">{$pluginNameEscaped} {$pluginVersionEscaped}</span></div>
  <div class="td-15"><a href="{$pluginAuthorUrlEscaped}" target="_blank">{$pluginAuthorNameEscaped}</a></div>
  <div class="td-10">{$pluginModifiedEscaped}</div>
  <div class="td-full td-action">
    {if $plugin.type == 'plugin'}
      {if $plugin.IsUsed()}
      <a href="{php}echo BuildSafeCmdURL('act=PluginDis&name='.$pluginIdEscaped);{/php}" title="{$zbp.lang['msg']['disable']}" class="btn-icon btn-disable" data-pluginid="{$pluginIdEscaped}">
        关闭
      </a>
      &nbsp;&nbsp;&nbsp;&nbsp;
      {else}
      <a href="{php}echo BuildSafeCmdURL('act=PluginEnb&name='.$pluginIdEscaped);{/php}" title="{$zbp.lang['msg']['enable']}" class="btn-icon btn-enable" data-pluginid="{$pluginIdEscaped}">
        开启
      </a>
      {/if}
    {/if}

      {if $plugin.IsUsed() && $plugin.CanManage()}
      <a href="{$plugin.GetManageUrl()}" title="{$zbp.lang['msg']['manage']}" class="btn-icon btn-manage" data-pluginid="{$pluginIdEscaped}">
        配置
      </a>
      {/if}

  </div>
</div>

{/foreach}



<script>
  $(".plugin-note").tooltip();
</script>
