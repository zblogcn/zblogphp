<?php
function alipay_SubMenus($id){
	//m-now

	echo '<a href="main.php"><span class="m-left '.($id==1?'m-now':'').'">订单列表</span></a>';

	
	echo '<a href="setting.php"><span class="m-right '.($id==6?'m-now':'').'">设置</span></a>';
}

?>