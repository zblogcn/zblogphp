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

function CustomMeta_Response($type,&$object){

	global $zbp;
	$array=$zbp->Config('CustomMeta')->$type;
	if(is_array($array)==false)return null;
	if(count($array)==0)return null;
	echo '<label for="cmbTemplate" class="editinputname" >自定义作用域:</label>';
	foreach ($array as $key => $value) {

$name_meta_intro=$type . '_' . $value . '_intro';
$name_meta_type=$type . '_' . $value . '_type';
$name_meta_option=$type . '_' . $value . '_option';

$single_meta_intro=$zbp->Config('CustomMeta')->$name_meta_intro;
$single_meta_type=$zbp->Config('CustomMeta')->$name_meta_type;
$single_meta_option=$zbp->Config('CustomMeta')->$name_meta_option;

if(!$single_meta_intro)$single_meta_intro='Metas.' . $value;

if(!$single_meta_type)$single_meta_type='text';

switch ($single_meta_type){
	case 'textarea':
		echo '<p><input type="text" readonly="readonly" style="width:98%;border:none;" value="'. $single_meta_intro .'"/><br><textarea style="width:98%;height:80px;" name="meta_' . $value . '" >'.htmlspecialchars($object->Metas->$value).'</textarea></p>';	
		break;
	default :
		echo '<p><input type="text" readonly="readonly" style="width:32%;border:none;" value="'. $single_meta_intro .'"/><input type="text" name="meta_' . $value . '" value="'.htmlspecialchars($object->Metas->$value).'" style="width:65%;"/></p>';	
		break;
}

	}

}

function CustomMeta_Edit_Response(){
	global $zbp,$article;
	CustomMeta_Response('post',$article);
}
function CustomMeta_Category_Edit_Response(){
	global $zbp,$cate;
	CustomMeta_Response('category',$cate);
}
function CustomMeta_Tag_Edit_Response(){
	global $zbp,$tag;
	CustomMeta_Response('tag',$tag);
}
function CustomMeta_Member_Edit_Response(){
	global $zbp,$member;
	CustomMeta_Response('member',$member);
}



?>