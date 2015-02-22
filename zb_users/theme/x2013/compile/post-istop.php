<article class="excerpt  cate<?php  echo $article->Category->ID;  ?> auth<?php  echo $article->Author->ID;  ?>">
	<h2>
	  <a href="<?php  echo $article->Url;  ?>" title="<?php  echo $article->Title;  ?> - <?php  echo $name;  ?>">(置顶)<?php  echo $article->Title;  ?></a>
	</h2>
</article>