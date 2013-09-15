<?php
//****************************************
// MenuManage 子菜单
//****************************************
function alipay_SubMenu($id){
	$arySubMenu = array(
		0 => array('订单列表', 'main.php', 'left', false),
		1 => array('设置', 'setting.php', 'right', false),
		2 => array('帮助说明', 'help.php', 'right', false),
	);
	foreach($arySubMenu as $k => $v){
		echo '<a href="'.$v[1].'" '.($v[3]==true?'target="_blank"':'').'><span class="m-'.$v[2].' '.($id==$k?'m-now':'').'">'.$v[0].'</span></a>';
	}
}
?>