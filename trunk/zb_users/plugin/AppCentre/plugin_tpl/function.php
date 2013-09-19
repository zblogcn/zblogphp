<?php
/**
 * <%appid%> MenuManage 子菜单
 * @id 选择导航id
 * return 导航菜单string
 */
function <%appid%>_SubMenu($id){
	$arySubMenu = array(
		0 => array('菜单', 'main.php', 'left', false),
		1 => array('设置', 'setting.php', 'right', true),
	);
	foreach($arySubMenu as $k => $v){
		echo '<a href="'.$v[1].'" '.($v[3]==true?'target="_blank"':'').'><span class="m-'.$v[2].' '.($id==$k?'m-now':'').'">'.$v[0].'</span></a>';
	}
}
?>