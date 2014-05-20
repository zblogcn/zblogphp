<article class="article">
	<header class="article-header">
		<h1 class="article-title"><?php  echo $article->Title;  ?></h1>
		<p class="article-meta">
			<?php  echo $article->Time('m月d日');  ?>
			分类: <a href="<?php  echo $article->Category->Url;  ?>" title="查看 <?php  echo $article->Category->Name;  ?> 中的全部文章" rel="category tag"><?php  echo $article->Category->Name;  ?></a>
			<a class="comm" href="<?php  echo $article->Url;  ?>#comments" title="查看 <?php  echo $article->Title;  ?> 的评论"><?php  echo $article->CommNums;  ?>条评论</a>
			<span class="view"><?php  echo $article->ViewNums;  ?> ℃</span>
		</p>
	</header>
	<div class="db_post"><?php  echo $x2013_adheader;  ?></div>
	<div class="article-entry"><?php  echo $article->Content;  ?></div>
	<div class="db_post" style="margin:-15px 0 15px 0;"><?php  echo $x2013_adfooter;  ?></div>

	<?php if ($article->Tags) { ?>
	<br/>
	<footer class="article-footer"><div class="article-tags">标签:
	<?php  foreach ( $article->Tags as $tag) { ?> 
	<a href="<?php  echo $tag->Url;  ?>"><?php  echo $tag->Name;  ?></a>
	<?php  }   ?>
	</div></footer>
	<?php } ?>

</article>
<?php  include $this->GetTemplate('comments');  ?>