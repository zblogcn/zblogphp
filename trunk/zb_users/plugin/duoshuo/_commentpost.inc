<?php
if(!isset($duoshuo)) global $duoshuo;
if ($duoshuo->cfg->short_name != '')
{
?>
<!-- Duoshuo Comment BEGIN -->
<?php echo $duoshuo_comments_wrapper_intro;?>
<div class="ds-thread" data-category="<?php echo $article->Category->ID?>" data-thread-key="<?php echo $article->ID?>" data-title="<?php echo TransferHTML($article->Title,'[html-format]')?>" data-author-key="<?php echo $article->Author->ID?>" data-url=""></div>
<?php echo $duoshuo_comments_wrapper_outro;?>
<!-- Duoshuo Comment END -->
<?php
}
else
{
?>
请进入后台->评论管理以配置你的多说插件。
<?php
}