<!-- Duoshuo Comment BEGIN -->
<?php echo $duoshuo_comments_wrapper_intro;?>
<div class="ds-thread" data-category="<?php echo $article->Category->ID?>" data-thread-key="<?php echo $article->ID?>" data-title="<?php echo TransferHTML($article->Title,'[html-format]')?>" data-author-key="<?php echo $article->Author->ID?>" data-url=""></div>
<?php echo $duoshuo_comments_wrapper_outro;?>
<!-- Duoshuo Comment END -->