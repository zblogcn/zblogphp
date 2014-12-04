<?php if ($pagebar) { ?>
<?php  foreach ( $pagebar->buttons as $k=>$v) { ?> 
  <?php if ($pagebar->PageNow==$k) { ?>
	<span class="page now-page"><?php  echo $k;  ?></span>
  <?php }else{  ?>
	<a href="<?php  echo $v;  ?>"><span class="page"><?php  echo $k;  ?></span></a>
  <?php } ?>
<?php  }   ?>
<?php } ?>