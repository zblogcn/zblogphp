<?php


#注册插件
RegisterPlugin("CustomMeta","ActivePlugin_CustomMeta");


function ActivePlugin_CustomMeta() {

Add_Filter_Plugin('Filter_Plugin_Edit_Response','CustomMeta_Edit_Response');
Add_Filter_Plugin('Filter_Plugin_Category_Edit_Response','CustomMeta_Category_Edit_Response');
Add_Filter_Plugin('Filter_Plugin_Tag_Edit_Response','CustomMeta_Tag_Edit_Response');
Add_Filter_Plugin('Filter_Plugin_Member_Edit_Response','CustomMeta_Member_Edit_Response');

}

function InstallPlugin_CustomMeta(){
	global $zbp;
}

function UninstallPlugin_CustomMeta(){
	global $zbp;
}

function CustomMeta_Edit_Response(){
	global $zbp,$article;
	$array=$zbp->Config('CustomMeta')->post;
	if(is_array($array)==false)return null;
	if(count($array)==0)return null;
	echo '<label for="cmbTemplate" class="editinputname" >自定义作用域:</label>';
	foreach ($array as $key => $value) {
		echo '<p><input type="text" readonly="readonly" style="width:32%;border:none;" value="Metas.' . $value . '"/><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($article->Metas->$value).'" style="width:65%;"/></p>';
	}

}
function CustomMeta_Category_Edit_Response(){
	global $zbp,$cate;
	$array=$zbp->Config('CustomMeta')->category;
	if(is_array($array)==false)return null;
	if(count($array)==0)return null;
	echo '<label for="cmbTemplate" class="editinputname" >自定义作用域:</label>';
	foreach ($array as $key => $value) {
		echo '<p style="width:70%"><input type="text" readonly="readonly" style="width:32%;border:none;" value="Metas.' . $value . '"/><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($cate->Metas->$value).'"  style="width:65%;"/></p>';
	}

}
function CustomMeta_Tag_Edit_Response(){
	global $zbp,$tag;
	$array=$zbp->Config('CustomMeta')->tag;
	if(is_array($array)==false)return null;
	if(count($array)==0)return null;
	echo '<label for="cmbTemplate" class="editinputname" >自定义作用域:</label>';
	foreach ($array as $key => $value) {
		echo '<p style="width:70%"><input type="text" readonly="readonly" style="width:32%;border:none;" value="Metas.' . $value . '"/><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($tag->Metas->$value).'"  style="width:65%;"/></p>';
	}

}
function CustomMeta_Member_Edit_Response(){
	global $zbp,$member;
	$array=$zbp->Config('CustomMeta')->member;
	if(is_array($array)==false)return null;
	if(count($array)==0)return null;
	echo '<label for="cmbTemplate" class="editinputname" >自定义作用域:</label>';
	foreach ($array as $key => $value) {
		echo '<p style="width:70%"><input type="text" readonly="readonly" style="width:32%;border:none;" value="Metas.' . $value . '"/><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($member->Metas->$value).'"  style="width:65%;"/></p>';
	}

}



?>