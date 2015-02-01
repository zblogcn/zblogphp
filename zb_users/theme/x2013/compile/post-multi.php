<article class="excerpt  cate<?php  echo $article->Category->ID;  ?> auth<?php  echo $article->Author->ID;  ?>">
	<h2>
	  <a href="<?php  echo $article->Url;  ?>" title="<?php  echo $article->Title;  ?> - <?php  echo $name;  ?>"><?php  echo $article->Title;  ?></a>
	</h2>
	<div class="info">
		<time class="time"><?php  echo $article->Time('m月d日');  ?></time> 
		<a class="comm" href="<?php  echo $article->Url;  ?>#comments" title="查看 <?php  echo $article->Title;  ?> 的评论">
		  <?php  echo $article->CommNums;  ?>条评论
		</a> 
		<span class="view"><?php  echo $article->ViewNums;  ?> ℃</span>
	</div>
	<div class="note"><?php  echo $article->Intro;  ?>...</div>
</article>