<div class="post multi">
	<h4 class="post-date"><?php  echo $article->Time('Y年m月d日');  ?></h4>
	<h2 class="post-title"><a href="<?php  echo $article->Url;  ?>"><?php  echo $article->Title;  ?></a></h2>
	<div class="post-body"><?php  echo $article->Intro;  ?></div>
	<h5 class="post-tags"></h5>
	<h6 class="post-footer">
		作者:<?php  echo $article->Author->StaticName;  ?>&nbsp;&nbsp;分类:<?php  echo $article->Category->Name;  ?>&nbsp;&nbsp;浏览:<?php  echo $article->ViewNums;  ?>&nbsp;&nbsp;评论:<?php  echo $article->CommNums;  ?>
	</h6>
</div>