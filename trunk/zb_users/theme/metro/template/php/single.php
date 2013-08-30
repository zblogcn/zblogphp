<?php  include $this->GetTemplate('header');  ?>
<body class="single">
<?php  include $this->GetTemplate('top');  ?>
<div class="nevbarbg">
  <div class="navbar">
    <ul>
      <?php  echo $modules['navbar']->Content;  ?>
    </ul>
  </div>
</div>
<div id="content">
  <div id="main">
    <?php if ($article->Type==ZC_POST_TYPE_ARTICLE) { ?>
		<?php  include $this->GetTemplate('post-single');  ?>
	<?php }else{  ?>
		<?php  include $this->GetTemplate('post-page');  ?>
	<?php } ?>
  </div>
  <div id="sidebar">
    <?php  include $this->GetTemplate('sidebar');  ?>
  </div>
  <div class="clear"></div>
</div>
<?php  include $this->GetTemplate('footer');  ?>