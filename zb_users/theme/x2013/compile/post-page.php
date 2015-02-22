<article class="article">
	<header class="article-header">
		<h1 class="article-title"><?php  echo $article->Title;  ?></h1>
	</header>
	<div class="article-entry"><?php  echo $article->Content;  ?></div>
</article>
<?php  include $this->GetTemplate('comments');  ?>