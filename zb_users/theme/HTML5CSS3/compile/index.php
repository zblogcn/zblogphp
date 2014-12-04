<?php  include $this->GetTemplate('header');  ?>
<body class="multi">
<header>
<h2><a href="<?php  echo $host;  ?>"><?php  echo $name;  ?></a> <sup><?php  echo $subname;  ?></sup></h2>
<nav>
<ul><?php  if(isset($modules['navbar'])){echo $modules['navbar']->Content;}  ?></ul>
</nav>
</header>
<section>
  <section id="main">
    <section>
<?php  foreach ( $articles as $article) { ?> 

<?php if ($article->IsTop) { ?>
<?php  include $this->GetTemplate('post-istop');  ?>
<?php }else{  ?>
<?php  include $this->GetTemplate('post-multi');  ?>
<?php } ?>

<?php  }   ?>
      <nav><?php  include $this->GetTemplate('pagebar');  ?></nav>
    </section>
    <aside>
      <?php  include $this->GetTemplate('sidebar');  ?>
    </aside>
  </section>
  <aside id="extra">
    <?php  include $this->GetTemplate('sidebar2');  ?>
  </aside>
</section>
<?php  include $this->GetTemplate('footer');  ?>