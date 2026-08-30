<?php die(); ?>
<div class="sub">
<!-- 搜索 -->
<form class="search" id="search" method="post" action="#">
  <p>
    {$zbp.lang['msg']['search']}: {$zbp.lang['msg']['category']}
    <select class="edit" size="1" name="category" style="width:140px;">
      <option value="">{$zbp.lang['msg']['any']}</option>
      {foreach $zbp.categoriesbyorder as $id => $cate}
      <option value="{$cate->ID}">{$cate->SymbolName}</option>
      {/foreach}
    </select>
    {$zbp.lang['msg']['type']}
    <select class="edit" size="1" name="status" style="width:100px;">
      <option value="">{$zbp.lang['msg']['any']}</option>
      <option value="0">{$zbp.lang['post_status_name']['0']}</option>
      <option value="1">{$zbp.lang['post_status_name']['1']}</option>
      <option value="2">{$zbp.lang['post_status_name']['2']}</option>
    </select>

    <label>
      <input type="checkbox" name="istop" value="True" />{$zbp.lang['msg']['top']}
    </label>

    <input name="search" style="width:250px;" type="text" value="" />
    <input type="submit" class="button" value="{$zbp.lang['msg']['submit']}" />
  </p>
</form>
</div><!-- div class="sub" -->
<form method="post" action="{$zbp.host}zb_system/cmd.php?act=PostBat&type={$post_type}">
<!-- 文章列表 -->

<div class="postlist">

        <div class="tr thead">
          <div class="td-id">ID</div>
          <div class="td-cate">分类</div>
          <div class="td-author">作者</div>
          <div class="td-title">标题</div>
          <div class="td-date">日期</div>
          <div class="td-view">浏览</div>
          <div class="td-cmt">评论</div>
          <div class="td-status">状态</div>
          <div class="td-action">操作</div>
        </div>


  {foreach $articles as $article}

  <div class="tr">
    <div class="td-id">{$article.ID}</div>
    <div class="td-cate"><a href="">{$article.Category.Name}</a></div>
    <div class="td-author"><a href="">{$article.Author.Name}</a></div>
    <div class="td-title"><a href="{$article.Url}">欢迎使用Z-BlogPHP1.8</a></div>
    <div class="td-date">{$article.Time()}</div>
    <div class="td-cmt">{$article.ViewNums}</div>
    <div class="td-cmt">{$article.CommNums}</div>
    <div class="td-status"><span>{$article.StatusName}</span></div>
    <div class="td-action"><a href="../../cmd.php?act=ArticleEdt&amp;id={$article.ID}" class="edit">编辑</a><a href="return confirmDelete();" href="{BuildSafeCmdURL('act=ArticleDel&amp;id=' . $article->ID)}" class="del">删除</a></div>
  </div>

  {/foreach}
<!-- div class="postlist" -->

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
    const message = "{$zbp.lang['msg']['confirm_operating']}";
    const confirmed = window.confirm(message);
    return confirmed;
  }
  $("a.order_button").parent().bind("mouseenter mouseleave", function() {
    $(this).find("a.order_button").toggleClass("element-visibility-hidden");
  });
</script>