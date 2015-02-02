<?php  include $this->GetTemplate('header');  ?>
<body class="multi default">
<?php  include $this->GetTemplate('top');  ?>
<div class="nevbarbg">
  <div class="navbar">
    <ul>
      <?php  if(isset($modules['navbar'])){echo $modules['navbar']->Content;}  ?>
    </ul>
  </div>
</div>
<div id="content">  
  <div id="main">
    <?php  foreach ( $articles as $article) { ?>
		<?php if ($article->IsTop) { ?>
		<?php  include $this->GetTemplate('post-istop');  ?>
		<?php }else{  ?>
		<?php  include $this->GetTemplate('post-multi');  ?>
		<?php } ?>
	<?php }   ?>
    <div class="pagebar">
      <?php  include $this->GetTemplate('pagebar');  ?>
    </div>   
  </div> 
  <div id="sidebar">
    <?php  include $this->GetTemplate('sidebar');  ?>
  </div>
  <div class="clear"></div>
</div>
<?php  include $this->GetTemplate('footer');  ?>