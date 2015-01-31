<div class="widget widget_<?php  echo $module->HtmlID;  ?>">
  <?php if ((!$module->IsHideTitle)&&($module->Name)) { ?><h3 class="widget_tit"><?php  echo $module->Name;  ?></h3><?php } ?>
  <?php if ($module->Type=='div') { ?>
	<div><?php  echo $module->Content;  ?></div>
	<?php } ?>

	<?php if ($module->Type=='ul') { ?>
	<ul><?php  echo $module->Content;  ?></ul>
	<?php } ?>
</div>