<?php
#注册插件
RegisterPlugin("passwordvisit","ActivePlugin_passwordvisit");

function ActivePlugin_passwordvisit() {
	Add_Filter_Plugin('Filter_Plugin_Edit_Response3','passwordvisit_show_encrypt_button');
	Add_Filter_Plugin('Filter_Plugin_PostArticle_Core','passwordvisit_save_postpassword');
	//Add_Filter_Plugin('Filter_Plugin_ViewList_Template','x2013_tags_set');
	Add_Filter_Plugin('Filter_Plugin_ViewPost_Template','passwordvisit_input_password');
}

function passwordvisit_input_password(&$template){
	global $zbp;
	if(isset($_POST['password']) && $_POST['password'] != ''){
		$article = $template->GetTags('article');
		if (GetVars('password','POST') == $article->Metas->passwordvisit_password) {
			//$article->Content
		}else{
			die('密码错误');
		}

	}else{
		$article = $template->GetTags('article');
		if ($article->Metas->passwordvisit_enable_encrypt) {
			$article->Content = $zbp->Config('passwordvisit')->default_text.'<form id="form1" name="form1" method="post"><input name="password" type="password" width="100px" /><input name="submit" type="submit" value="查看"/></form>';
			$template->SetTags('article', $article);
		}
	}
}

function passwordvisit_save_postpassword(&$article){
	$article->Metas->passwordvisit_enable_encrypt = $_POST['enable_encrypt'];
	$article->Metas->passwordvisit_password = $_POST['password'];
}

function passwordvisit_show_encrypt_button(){
	if ($_GET['act'] == 'PageEdt') return;//去掉页面

	if(isset($_GET['id']) && $_GET['id'] != ''){
		global $zbp,$article;
		echo '<br>加密文章<input id="enable_encrypt" name="enable_encrypt" style="display:none;" type="text" value="'.$article->Metas->passwordvisit_enable_encrypt.'" class="checkbox">';
		echo '<p><label for="edtDateTime" class="editinputname">密码</label><input type="text" name="password" value="'.$article->Metas->passwordvisit_password.'" style="width:110px;" ><br>不输人密码则使用全局密码</p>';
	}else{
		echo '<br>加密文章<input id="enable_encrypt" name="enable_encrypt" style="display:none;" type="text" value="0" class="checkbox">';
		echo '<p><label for="edtDateTime" class="editinputname">密码</label><input type="text" name="password" value="" style="width:110px;" ><br>不输人密码则使用全局密码</p>';
	}

}

function InstallPlugin_passwordvisit() {
	global $zbp;
	if(!$zbp->Config('passwordvisit')->HasKey('Version')){
		$zbp->Config('passwordvisit')->Version = '1.0';
		$zbp->Config('passwordvisit')->default_password = 'imzhou';
		$zbp->Config('passwordvisit')->default_text = '<p>本篇文章已加密，请输入密码后查看。</p>';
		$zbp->Config('passwordvisit')->all_encrypt = '0';
		$zbp->SaveConfig('passwordvisit');
	}
	$zbp->SaveConfig('passwordvisit');
}
function UninstallPlugin_passwordvisit() {}