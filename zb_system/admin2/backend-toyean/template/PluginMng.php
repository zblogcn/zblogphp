<?php die(); ?>


<table class="tableFull tableBorder tableBorder-thcenter table_hover table_striped plugin-list">
  <tr>
    <th></th>
    <th>{$zbp.lang['msg']['name']}</th>
    <th>{$zbp.lang['msg']['author']}</th>
    <th>{$zbp.lang['msg']['date']}</th>
    <th></th>
  </tr>

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
  <tr>
    <td class="td5 tdCenter{if $plugin.type == 'plugin'} plugin{/if}{if $plugin.IsUsed()} plugin-on{/if}" data-pluginid="{$pluginIdEscaped}">
      <img {if !$plugin.IsUsed()}style="opacity:0.2" {/if} src="{$plugin.GetLogo()}" alt="" width="32" height="32" />
    </td>
    <td class="td25">
      <span class="plugin-note" title="{$pluginNoteEscaped}">{$pluginNameEscaped} {$pluginVersionEscaped}</span>
    </td>
    <td class="td20">
      <a href="{$pluginAuthorUrlEscaped}" target="_blank">{$pluginAuthorNameEscaped}</a>
    </td>
    <td class="td20">{$pluginModifiedEscaped}</td>
    <td class="td10 tdCenter">
      {if $plugin.type == 'plugin'}
      {if $plugin.IsUsed()}
      <a href="{php}echo BuildSafeCmdURL('act=PluginDis&name='.$pluginIdEscaped);{/php}" title="{$zbp.lang['msg']['disable']}" class="btn-icon btn-disable" data-pluginid="{$pluginIdEscaped}">
        <i class="icon-cancel on"></i>
      </a>
      &nbsp;&nbsp;&nbsp;&nbsp;
      {else}
      <a href="{php}echo BuildSafeCmdURL('act=PluginEnb&name='.$pluginIdEscaped);{/php}" title="{$zbp.lang['msg']['enable']}" class="btn-icon btn-enable" data-pluginid="{$pluginIdEscaped}">
        <i class="icon-power off"></i>
      </a>
      {/if}
      {/if}

      {if $plugin.IsUsed() && $plugin.CanManage()}
      <a href="{$plugin.GetManageUrl()}" title="{$zbp.lang['msg']['manage']}" class="btn-icon btn-manage" data-pluginid="{$pluginIdEscaped}">
        <i class="icon-tools"></i>
      </a>
      {/if}
    </td>
  </tr>
  {/foreach}

</table>

<script>
  $(".plugin-note").tooltip();
</script>
