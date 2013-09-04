<article id="log<?php  echo $article->ID;  ?>">
  <header>
    <h2><?php  echo $article->Title;  ?></h2>
  </header>
  <section><?php  echo $article->Content;  ?></section>
  <footer></footer>
  <nav>
<?php if ($article->Prev) { ?>
<a class="l" href="<?php  echo $article->Prev->Url;  ?>" title="<?php  echo $article->Prev->Title;  ?>">« 上一篇</a>
<?php } ?>
<?php if ($article->Next) { ?>
<a class="r" href="<?php  echo $article->Next->Url;  ?>" title="<?php  echo $article->Next->Title;  ?>"> 下一篇 »</a>
<?php } ?>
  </nav>
</article>

<?php if (!$article->IsLock) { ?>
<?php  include $this->GetTemplate('comments');  ?>
<?php } ?>