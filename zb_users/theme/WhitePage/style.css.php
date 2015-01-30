<?php
header('Content-type: text/css');

require '../../../zb_system/function/c_system_base.php';

echo '@import url("' . $bloghost . 'zb_users/theme/' . $blogtheme . '/style/' . $blogstyle . '.css' . '");';


if($zbp->Config('WhitePage')->HasKey("custom_bgcolor")){
	echo  "body{background-color:" . $zbp->Config('WhitePage')->custom_bgcolor . ";}";
}
if($zbp->Config('WhitePage')->HasKey("custom_headtitle")){
	echo "#BlogTitle,#BlogSubTitle{text-align:" . $zbp->Config('WhitePage')->custom_headtitle . ";}";
}
if($zbp->Config('WhitePage')->HasKey("custom_pagewidth")){
	if($zbp->Config('WhitePage')->custom_pagewidth==1000){
		echo "#divAll{width:1000px;}#divMiddle{width:940px;padding:0 30px;}#divSidebar{width:240px;padding:0 0 0 20px;}#divMain{width:670px;padding:0 0 20px 0;}#divTop{padding-top:30px;}body{font-size:15px;}";
	}
}
if($zbp->Config('WhitePage')->HasKey("custom_pagetype")){
	if($zbp->Config('WhitePage')->custom_pagetype==1){
		if($zbp->Config('WhitePage')->custom_pagewidth==1000){
			echo "#divAll{background:url('style/default/bg1000-1.png') no-repeat 50% top;}#divPage{background:url('style/default/bg1000-2.png') no-repeat 50% bottom;}#divMiddle{background:url('style/default/bg1000-3.png') repeat-y 50% 50%;}";
		}
	}
	if($zbp->Config('WhitePage')->custom_pagetype==2){
		echo "#divAll{box-shadow: 0 0 5px #666;background-color:white;border-radius: 0px;}";
		echo "#divAll{background:white;}#divPage{background:none;}#divMiddle{background:none;}";
	}
	if($zbp->Config('WhitePage')->custom_pagetype==3){
		echo "#divAll{box-shadow: 0 0 5px #666;background-color:white;border-radius: 5px;}";
		echo "#divAll{background:white;}#divPage{background:none;}#divMiddle{background:none;}";
	}
	if($zbp->Config('WhitePage')->custom_pagetype==4){
		echo "#divAll{box-shadow:none;background-color:white;border-radius: 0;}";
		echo "#divAll{background:white;}#divPage{background:none;}#divMiddle{background:none;}";
		echo "#divTop{padding-top:30px;}";
	}
}

die();
?>