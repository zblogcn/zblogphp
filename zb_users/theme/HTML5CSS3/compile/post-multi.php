<article id="log<?php  echo $article->ID;  ?>" class="top cate<?php  echo $article->Category->ID;  ?> auth<?php  echo $article->Author->ID;  ?>">
  <header>
    <time><?php  echo $article->Time('Y年m月d日');  ?></time>
    <h2><a href="<?php  echo $article->Url;  ?>"><?php  echo $article->Title;  ?></a></h2>
  </header>
  <section><?php  echo $article->Intro;  ?></section>
  <footer>
<?php if ($article->Tags) { ?>
    <h4>标签: <?php  foreach ( $article->Tags as $tag) { ?> <a href="<?php  echo $tag->Url;  ?>"><?php  echo $tag->Name;  ?></a><?php  }   ?></h4>
<?php } ?>
    <h5><em>作者:<?php  echo $article->Author->StaticName;  ?></em> <em>分类:<?php  echo $article->Category->Name;  ?></em> <em>浏览:<?php  echo $article->ViewNums;  ?></em> <em>评论:<?php  echo $article->CommNums;  ?></em></h5>
  </footer>
</article>

