<?php exit(); ?>
<!-- 搜索 -->
<form class="search" id="search" method="post" action="#">
    <p>
        {$zbp.lang['msg']['search']}:
        <!-- 分类 -->
        <label>
            {$zbp.lang['msg']['category']}
            <select class="edit" size="1" name="category">
                <option value="">{$zbp.lang['msg']['any']}</option>
                {foreach $zbp.categoriesbyorder as $id => $cate}
                <option value="{$cate->ID}">{$cate->SymbolName}</option>
                {/foreach}
            </select>
        </label>
        <!-- 状态 -->
        <label>
            {$zbp.lang['msg']['type']}
            <select class="edit" size="1" name="status">
                <option value="">{$zbp.lang['msg']['any']}</option>
                <option value="0">{$zbp.lang['post_status_name']['0']}</option>
                <option value="1">{$zbp.lang['post_status_name']['1']}</option>
                <option value="2">{$zbp.lang['post_status_name']['2']}</option>
            </select>
        </label>
        <!-- 置顶 -->
        <label class="label-flex">
            <input type="checkbox" name="istop" value="True" />{$zbp.lang['msg']['top']}
        </label>

        <input aria-label="search" name="search" type="text" value="" />
        <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
    </p>
</form>

<form method="post" action="{$zbp.cmdurl}?act=PostBat&type={$post_type}">
    <!-- 文章列表 -->
    <table class="tableFull tableBorder table_hover table_striped thCenter">
        <!-- 表头 -->
        <tr>
            <th data-field="id">{$zbp.lang['msg']['id']}{$button_id_html}</th>
            <th data-field="category">{$zbp.lang['msg']['category']}{$button_cateid_html}</th>
            <th data-field="author">{$zbp.lang['msg']['author']}{$button_authorid_html}</th>
            <th data-field="title">{$zbp.lang['msg']['title']}</th>
            <th data-field="date">{$zbp.lang['msg']['date']}{$button_posttime_html}</th>
            <th data-field="comment">{$zbp.lang['msg']['comment']}</th>
            <th data-field="status">{$zbp.lang['msg']['status']}</th>
            <th data-field="actions"></th>
            {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
            <th data-field="select"><a href="javascript:;" onclick="BatchSelectAll();return false;">{$zbp.lang['msg']['select_all']}</a></th>
            {/if}
        </tr>
        <!-- 列表内容 -->
        {foreach $articles as $article}
        <tr data-id="{$article.ID}">
            <td class="td5" data-field="id">{$article.ID}</td>
            <td class="td10" data-field="category">{$article.Category.Name}</td>
            <td class="td10" data-field="author">{$article.Author.Name}</td>
            <td data-field="title">
                <a href="{$article.Url}" target="_blank"><i class="icon-link-45deg"></i></a> {$article.Title}
            </td>
            <td class="td20" data-field="date">{$article.Time()}</td>
            <td class="td5" data-field="comment">{$article.CommNums}</td>
            <td class="td5" data-field="status">
                {if $article.IsTop}
                {$zbp.lang.msg.top}|
                {/if}
                {$article.StatusName}
            </td>
            <td class="td10 tdCenter" data-field="actions">
                <a href="{$zbp.cmdurl}?act=ArticleEdt&amp;id={$article.ID}"><i class="icon-pencil-square"></i></a>
                <a onclick="return confirmDelete();" href="{BuildSafeCmdURL('act=ArticleDel&amp;id=' . $article->ID)}"><i class="icon-trash"></i></a>
            </td>
            {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
            <td class="td5 tdCenter" data-field="select">
                <input type="checkbox" id="id{$article.ID}" name="id[]" value="{$article.ID}">
            </td>
            {/if}
        </tr>
        {/foreach}
    </table>
    <!-- 分页 -->
    <p class="pagebar">
        {foreach $p->buttons as $k => $v}
        {if $k == $p->PageNow}
        <span class="now-page">{$k}</span>
        {else}
        <a href="{$v}">{$k}</a>
        {/if}
        {/foreach}

        <!-- 批量删除按钮 -->
        {if $zbp.CheckRights('PostBat') && $zbp.option['ZC_POST_BATCH_DELETE']}
        <input type="submit" class="button pull-right" value="{$zbp.lang['msg']['all_del']}" name="all_del" onclick="return confirmDelete();" />
        {/if}
    </p>

</form>
<script>
    function confirmDelete() {
        return window.confirm("{$zbp.lang['msg']['confirm_operating']}");
    }

    $("a.order_button").parent().bind("mouseenter mouseleave", function() {
        $(this).find("a.order_button").toggleClass("element-visibility-hidden");
    });
</script>
