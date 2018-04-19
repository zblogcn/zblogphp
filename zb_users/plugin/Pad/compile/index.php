<?php  include $this->GetTemplate('header'); ?>
<body class="multi">
<header>
<h2><a href="<?php  echo $host; ?>"><?php  echo $name; ?></a> <sup><?php  echo $subname; ?></sup></h2>
<nav>
<ul><li><a href="<?php  echo $host; ?>?mod=pad">首页(Pad版)</a></li></ul>
</nav>
</header>
<section>
  <section id="main">
    <section>
<?php  foreach ($articles as $article) {
    ?> 

<?php if ($article->IsTop) {
        ?>
<?php  include $this->GetTemplate('post-istop'); ?>
<?php
    } else {
        ?>
<?php  include $this->GetTemplate('post-multi'); ?>
<?php
    } ?>

<?php
}   ?>
      <nav><?php  include $this->GetTemplate('pagebar'); ?></nav>
    </section>
    <aside>
        <?php  include $this->GetTemplate('sidebar_pad'); ?>
    </aside>
  </section>
</section>
<?php  include $this->GetTemplate('footer'); ?>
