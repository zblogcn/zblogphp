<?php  include $this->GetTemplate('header'); ?>
<body class="single">
<header>
<h2><a href="<?php  echo $host; ?>"><?php  echo $name; ?></a> <sup><?php  echo $subname; ?></sup></h2>
<nav>
<ul><li><a href="<?php  echo $host; ?>?mod=pad">首页(Pad版)</a></li></ul>
</nav>
</header>
<section>
  <section id="main">
    <section>
<?php if ($article->Type == ZC_POST_TYPE_ARTICLE) {
    ?>
<?php  include $this->GetTemplate('post-single'); ?>
<?php
} else {
        ?>
<?php  include $this->GetTemplate('post-page'); ?>
<?php
    } ?>
    </section>
    <aside>
        <?php  include $this->GetTemplate('sidebar_pad'); ?>
    </aside>
  </section>
</section>
<?php  include $this->GetTemplate('footer'); ?>
