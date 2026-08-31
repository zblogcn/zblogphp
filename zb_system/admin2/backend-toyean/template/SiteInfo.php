<?php exit(); ?>


<div class="listcard four">
  <ul>
    <li>
      <p>文章数</p>
      <p class="num">{$all_articles}</p>
      <span class="icon ico-newpost"></span>
    </li>
    <li>
      <p>页面数</p>
      <p class="num">{$all_pages}</p>
      <span class="icon ico-page"></span>
    </li>
    <li>
      <p>浏览总数</p>
      <p class="num">{$all_views}</p>
      <span class="icon ico-view"></span>
    </li>
    <li>
      <p>评论总数</p>
      <p class="num">{$all_comments}</p>
      <span class="icon ico-cmt"></span>
    </li>
    <li>
      <p>用户总数</p>
      <p class="num">{$all_members}</p>
      <span class="icon ico-user"></span>
    </li>
    <li>
      <p>分类总数</p>
      <p class="num">{$all_categories}</p>
      <span class="icon ico-category"></span>
    </li>
    <li>
      <p>标签总数</p>
      <p class="num">{$all_tags}</p>
      <span class="icon ico-tag"></span>
    </li>
    <li>
      <p>附件总数</p>
      <p class="num">{$all_uploads}</p>
      <span class="icon ico-file"></span>
    </li>
  </ul>
</div>

<div class="listcard two">
  <dl>
          <dt>最新动态<a href="javascript:updateinfo('{$reload_reload_updateinfo_url}');" id="updateinfo" title="{$zbp.lang['msg']['refresh']}" class="refresh" data-time="{$reload_updateinfo_time}">刷新</a></dt>
          <dd>{$reload_updateinfo}</dd>
  </dl>
  <dl>
          <dt>系统信息</dt>
          <dd>
            <ul class="configinfo">
              <li><em>当前版本</em><span>{$current_version}</span></li>
              <li><em>当前主题</em><span>{$current_theme} {$current_theme_version}/{$current_style}</span></li>
              <li><em>系统环境</em><span>{$system_environment1};{$system_environment2}</span></li>
              <li><em>API协议地址</em><span>http://localhost/zb_system/api.php</span></li>
              <li><em>服务器IP</em><span>{gethostbyname(gethostname())}</span></li>
              {if $zbp.isdebug}
              <!--debug_mode_note-->
              <li><em>调试模式</em><span class="on">已启用</span></li>
              {else}
              <li><em>调试模式</em><span class="off">关闭</span></li>
              {/if}
            </ul>
          </dd>
  </dl>
</div>

<div class="listcard one">
        <dl>
          <dt>Z-BLOG网站和程序开发</dt>
          <dd>
{php}<?php
  // thanks
  $pattern = '/<thead>(.*?)<\/thead>/s';
  $replacement = '';
  $result = preg_replace($pattern, $replacement, $thanksinfo);
  echo $thanksinfo = $result;
?>{/php}
</dd>
</dl>
</div>
{if $zbp->CheckRights('root')}

<!-- 站点统计更新 -->
{if (time() - (int) $zbp->cache->reload_statistic_time) > (3600 * 23)}
<script>
  const $btnStatistic = document.getElementById('statistic');
  if ($btnStatistic) {
    $btnStatistic.style.color = 'red';
    // 自动点击刷新
    setTimeout(() => {
        statistic('{$reload_url}');
    }, 1000);
  }
</script>
{/if}
<!-- 公告信息更新 -->
{if (time() - (int) $zbp->cache->reload_updateinfo_time) > (3600 * 47)}
<script>
  const $btnUpdateInfo = document.getElementById('updateinfo');
  if ($btnUpdateInfo) {
    $btnUpdateInfo.style.color = 'red';
    // 自动点击刷新
    setTimeout(() => {
        updateinfo('{$reload_reload_updateinfo_url}');
    }, 1000);
  }
</script>
{/if}

{/if}