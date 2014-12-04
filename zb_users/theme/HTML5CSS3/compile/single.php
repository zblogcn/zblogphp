<?php  include $this->GetTemplate('header');  ?>
<body class="single">
<header>
<h2><a href="<?php  echo $host;  ?>"><?php  echo $name;  ?></a> <sup><?php  echo $subname;  ?></sup></h2>
<nav>
<ul><?php  echo $modules['navbar']->Content;  ?></ul>
</nav>
</header>
<section>
  <section id="main">
    <section>
<?php if ($article->Type==ZC_POST_TYPE_ARTICLE) { ?>
<?php  include $this->GetTemplate('post-single');  ?>
<?php }else{  ?>
<?php  include $this->GetTemplate('post-page');  ?>
<?php } ?>
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