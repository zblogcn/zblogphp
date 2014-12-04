<article id="log<?php  echo $article->ID;  ?>">
  <header>
    <h2><?php  echo $article->Title;  ?></h2>
  </header>
  <section><?php  echo $article->Content;  ?></section>
  <footer></footer>
</article>

<?php if (!$article->IsLock) { ?>
<?php  include $this->GetTemplate('comments');  ?>
<?php } ?>