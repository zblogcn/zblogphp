<div class="post page">
	<h2 class="post-title"><?php  echo $article->Title;  ?></h2>
	<div class="post-body"><?php  echo $article->Content;  ?></div>
</div>

<?php if ($article->CommNums>0) { ?>
<#评论输出#>
<?php } ?>

<?php if (!$article->IsLock) { ?>
<#评论框#>
<?php } ?>