<?php
function upyun_SubMenu($id){
	$arySubMenu = array(
		0 => array('空间设置', 'main.php', 'left', false),
		1 => array('略所图设置', 'image.php', 'left', false),
		2 => array('域名绑定设置', 'domain.php', 'left', false),
		3 => array('操作员设置', 'operator.php', 'left', false),
	);
	global $zbp;
	if ($zbp->Config('upyun')->upyun_storagetype == '1') {
		unset($arySubMenu[1]);
	}
	foreach($arySubMenu as $k => $v){
		echo '<a href="'.$v[1].'" '.($v[3]==true?'target="_blank"':'').'><span class="m-'.$v[2].' '.($id==$k?'m-now':'').'">'.$v[0].'</span></a>';
	}
}