<?php if($zbp->option['ZC_ADMIN_HTML5_ENABLE']){?></section><?php }else{?></div><?php }?>
<?php
foreach ($GLOBALS['Filter_Plugin_Admin_Footer'] as $fpname => &$fpsignal) {$fpname();}
?>
</body>
</html>