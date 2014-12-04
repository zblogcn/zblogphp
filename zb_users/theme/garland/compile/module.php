<dl class="function" id="<?php  echo $module->HtmlID;  ?>">
<?php if ((!$module->IsHideTitle)&&($module->Name)) { ?><dt class="function_t"><?php  echo $module->Name;  ?></dt><?php }else{  ?><dt style="display:none;"></dt><?php } ?>
<dd class="function_c">

<?php if ($module->Type=='div') { ?>
<div><?php  echo $module->Content;  ?></div>
<?php } ?>

<?php if ($module->Type=='ul') { ?>
<ul><?php  echo $module->Content;  ?></ul>
<?php } ?>

</dd>
</dl>