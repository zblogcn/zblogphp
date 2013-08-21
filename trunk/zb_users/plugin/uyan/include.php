<?php


#注册插件
RegisterPlugin("uyan","ActivePlugin_uyan");


function ActivePlugin_uyan() {

Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','uyan_addjs');

}

function InstallPlugin_uyan(){

}

function UninstallPlugin_uyan(){

}

function uyan_addjs(&$template){

	$s='<!-- UY BEGIN -->
	<div id="uyan_frame"></div>
	<script type="text/javascript" src="http://v2.uyan.cc/code/uyan.js"></script>
	<!-- UY END -->';

	$template->SetTags('socialcomment',$s);

}


?>