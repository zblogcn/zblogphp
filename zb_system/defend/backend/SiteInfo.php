<?php die(); ?>
<table class="tableFull tableBorder table_striped table_hover" id="tbStatistic">
  <tbody>
    <tr>
      <th colspan="4" scope="col">
        <i class="icon-info-circle-fill"></i>
        {$zbp.lang['msg']['site_analyze']}

        {if $zbp->CheckRights('root')}
        <a href="javascript:statistic('{$reload_url}');" id="statistic" title="{$zbp.lang['msg']['refresh_cache']}" data-time="{$reload_time}"><i class="icon-arrow-repeat" style="font-size:small; margin-right: 0.2em;" alt="{$zbp.lang['msg']['refresh_cache']}"></i><small>{$zbp.lang['msg']['refresh_cache']}</small></a>
        {/if}

      </th>
    </tr>
    {if $zbp.isdebug}
    <!--debug_mode_note-->
    <tr>
      <td colspan='4' style='text-align: center'>{$zbp.lang['msg']['debugging_warning']}</td>
    </tr>
    {/if}
    <tr>
      <td class='td20'>{$zbp.lang['msg']['current_member']}</td>
      <td class='td30'>{$current_isroot}<a href='../cmd.php?act=misc&type=vrs' target='_blank' title="{$current_member}">{$current_member}</a></td>
      <td class='td20'>{$zbp.lang['msg']['current_version']}</td>
      <td class='td30'>{$current_version};</td>
    </tr>
    <tr>
      <td class='td20'>{$zbp.lang['msg']['all_artiles']}</td>
      <td>{$all_articles}</td>
      <td>{$zbp.lang['msg']['all_categorys']}</td>
      <td>{$all_categories}</td>
    </tr>
    <tr>
      <td class='td20'>{$zbp.lang['msg']['all_pages']}</td>
      <td>{$all_pages}</td>
      <td>{$zbp.lang['msg']['all_tags']}</td>
      <td>{$all_tags}</td>
    </tr>
    <tr>
      <td class='td20'>{$zbp.lang['msg']['all_comments']}</td>
      <td>{$all_comments}</td>
      <td>{$zbp.lang['msg']['all_views']}</td>
      <td>{$all_views}</td>
    </tr>
    <tr>
      <td class='td20'>{$zbp.lang['msg']['current_theme']}</td>
      <td>{$current_theme}/{$current_style} {$current_theme_version}</td>
      <td>{$zbp.lang['msg']['all_members']}</td>
      <td>{$all_members}</td>
    </tr>
    {if $zbp->CheckRights('root')}
    <!--debug_mode_moreinfo-->
    <tr>
      <td class='td20'>{$zbp.lang['msg']['protocol_address']}</td>
      <td>{$api_address}, {$xmlrpc_address}</td>
      <td>{$zbp.lang['msg']['system_environment']}</td>
      <td><a href='../cmd.php?act=misc&type=phpinfo' target='_blank'>{$system_environment}</a></td>
    </tr>
    {/if}
  </tbody>
</table>

<table class="tableFull tableBorder table_striped table_hover" id="tbUpdateInfo">
  <tbody>
    <tr>
      <th>
        <i class="icon-flower2"></i>
        {$zbp.lang['msg']['latest_news']}

        {if $zbp->CheckRights('root')}
        <a href="javascript:updateinfo('{$reload_reload_updateinfo_url}');" id="updateinfo" title="{$zbp.lang['msg']['refresh']}" data-time="{$reload_updateinfo_time}"><i class="icon-arrow-repeat" style="font-size:small; margin-right: 0.2em;" alt="{$zbp.lang['msg']['refresh']}"></i><small>{$zbp.lang['msg']['refresh']}</small></a>
        {/if}
      </th>
    </tr>
    {$reload_updateinfo}
  </tbody>
</table>
{if $zbp->CheckRights('root')}

<!-- 站点统计更新 -->
{if (time() - (int) $zbp->cache->reload_statistic_time) > (3600 * 23)}
<script>
  const $btnStatistic = document.getElementById('statistic');
  $btnStatistic.style.color = 'red';
  // 自动点击刷新
  setTimeout(() => {
    statistic('{$reload_url}');
  }, 1000);
</script>
{/if}
<!-- 公告信息更新 -->
{if (time() - (int) $zbp->cache->reload_updateinfo_time) > (3600 * 47)}
<script>
  const $btnUpdateInfo = document.getElementById('updateinfo');
  $btnUpdateInfo.style.color = 'red';
  // 自动点击刷新
  setTimeout(() => {
    updateinfo('{$reload_reload_updateinfo_url}');
  }, 1000);
</script>
{/if}

{/if}