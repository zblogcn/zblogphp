<?php exit(); ?>


<div class="listcard four">
    <ul>
        <li>
            <p>{$zbp.lang['msg']['all_artiles']}</p>
            <p class="num">{$all_articles}</p>
            <span class="icon ico-newpost"></span>
        </li>
        <li>
            <p>{$zbp.lang['msg']['all_pages']}</p>
            <p class="num">{$all_pages}</p>
            <span class="icon ico-page"></span>
        </li>
        <li>
            <p>{$zbp.lang['msg']['all_views']}</p>
            <p class="num">{$all_views}</p>
            <span class="icon ico-view"></span>
        </li>
        <li>
            <p>{$zbp.lang['msg']['all_comments']}</p>
            <p class="num">{$all_comments}</p>
            <span class="icon ico-cmt"></span>
        </li>
        <li>
            <p>{$zbp.lang['msg']['all_members']}</p>
            <p class="num">{$all_members}</p>
            <span class="icon ico-user"></span>
        </li>
        <li>
            <p>{$zbp.lang['msg']['all_categorys']}</p>
            <p class="num">{$all_categories}</p>
            <span class="icon ico-category"></span>
        </li>
        <li>
            <p>{$zbp.lang['msg']['all_tags']}</p>
            <p class="num">{$all_tags}</p>
            <span class="icon ico-tag"></span>
        </li>
        <li>
            <p>{$zbp.lang['msg']['all_uploads']}</p>
            <p class="num">{$all_uploads}</p>
            <span class="icon ico-file"></span>
        </li>
    </ul>
</div>

<div class="listcard two">
    <dl>
        <dt>{$zbp.lang['msg']['latest_news']}<a href="javascript:updateinfo('{$reload_reload_updateinfo_url}');" id="updateinfo" title="{$zbp.lang['msg']['refresh']}" class="refresh" data-time="{$reload_updateinfo_time}">{$zbp.lang['msg']['refresh']}</a></dt>
        <dd>{$reload_updateinfo}</dd>
    </dl>
    <dl>
        <dt>{$zbp.lang['msg']['site_analyze']}</dt>
        <dd>
            <ul class="configinfo">
                <li><em>{$zbp.lang['msg']['current_version']}</em><span>{$current_version}</span></li>
                <li><em>{$zbp.lang['msg']['current_theme']}</em><span>{$current_theme} {$current_theme_version}/{$current_style}</span></li>
                <li><em>{$zbp.lang['msg']['system_environment']}</em><span>{$system_environment1};{$system_environment2}</span></li>
                <li><em>{$zbp.lang['msg']['api_address']}</em><span>{$zbp->apiurl}</span></li>
                <li><em>{$zbp.lang['msg']['xmlrpc_address']}</em><span>{$zbp->xmlrpcurl}</span></li>
                <li><em>服务器IP</em><span>{gethostbyname(gethostname())}</span></li>
                {if $zbp.isdebug}
                <li><em>{$zbp.lang['msg']['debug_mode']}</em><span class="on">已启用</span></li>
                {else}
                <li><em>{$zbp.lang['msg']['debug_mode']}</em><span class="off">关闭</span></li>
                {/if}
            </ul>
        </dd>
    </dl>
</div>

<div class="listcard one">
    <dl>
        <dt>{$zbp->lang['msg']['develop_intro']}</dt>
        <dd>
            <table class="tableFull tableBorder table_hover table_striped" id="thankslist">
                <tbody>
                    {foreach $thanksInfo as $group}
                    <tr>
                        <td class="td20">
                            {if isset($group['icon']) && $group['icon']}
                                <i class="{$group['icon']}"></i>&nbsp;&nbsp;
                            {/if}
                            {$group['category']}
                        </td>
                        <td>
                            {foreach $group['items'] as $item}
                                {if isset($item['url'])}
                                    <a target="_blank" href="{$item['url']}" title="{if isset($item['title'])}{$item['title']}{/if}" rel="noreferrer">{$item['name']}</a>&nbsp;
                                {else}
                                    <span title="{if isset($item['title'])}{$item['title']}{/if}">{$item['name']}</span>
                                {/if}
                            {/foreach}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </dd>
    </dl>
</div>
{if $zbp->CheckRights('root')}

{if (time() - (int) $zbp->cache->reload_statistic_time) > (3600 * 23)}
<script>
    const $btnStatistic = document.getElementById('statistic');
    if ($btnStatistic) {
        $btnStatistic.style.color = 'red';
        setTimeout(() => {
            statistic('{$reload_url}');
        }, 1000);
    }
</script>
{/if}
{if (time() - (int) $zbp->cache->reload_updateinfo_time) > (3600 * 47)}
<script>
    const $btnUpdateInfo = document.getElementById('updateinfo');
    if ($btnUpdateInfo) {
        $btnUpdateInfo.style.color = 'red';
        setTimeout(() => {
            updateinfo('{$reload_reload_updateinfo_url}');
        }, 1000);
    }
</script>
{/if}

{/if}
