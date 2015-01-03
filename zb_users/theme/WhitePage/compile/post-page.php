<div class="post page">
	<h2 class="post-title"><?php  echo $article->Title;  ?></h2>
	<div class="post-body"><?php  echo $article->Content;  ?></div>
</div>

<?php if (!$article->IsLock) { ?>
<?php  include $this->GetTemplate('comments');  ?>
<?php } ?>