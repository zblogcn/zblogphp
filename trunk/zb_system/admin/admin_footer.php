</section>

<?php

foreach ($GLOBALS['Filter_Plugin_Admin_Footer'] as $fpname => &$fpsignal) {$fpname();}

?>
<script type="text/javascript">

$(document).ready(function(){ 
	$("#avatar").attr("src","<?php echo $zbp->user->Avatar?>");
});
</script>
</body>
</html>