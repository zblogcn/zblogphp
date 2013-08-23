<dl class="function" id="<?php  echo $module->HtmlID;  ?>">
<?php if (!$module->IsHideTitle) { ?><dt class="function_t"><?php  echo $module->Name;  ?></dt><?php }else{  ?> <?php } ?>
<dd class="function_c">

<?php if ($module->Type=='div') { ?>
<div><?php  echo $module->Content;  ?></div>
<?php } ?>

<?php if ($module->Type=='ul') { ?>
<ul><?php  echo $module->Content;  ?></ul>
<?php } ?>

</dd>
</dl>