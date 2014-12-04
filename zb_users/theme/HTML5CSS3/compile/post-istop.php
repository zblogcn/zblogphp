<article class="top cate<?php  echo $article->Category->ID;  ?> auth<?php  echo $article->Author->ID;  ?>">
  <header>
    <time><?php  echo $article->Time('Y年m月d日');  ?></time>
    <h2><a href="<?php  echo $article->Url;  ?>">[置顶] <?php  echo $article->Title;  ?></a></h2>
  </header>
</article>