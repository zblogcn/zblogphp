<?php
define('CLINIC', true);
$clinic_register_array = array();
$clinic_register_cate = array();
RegisterPlugin("static","ActivePlugin_static");

function ActivePlugin_static() {
	global $zbp;
	if( $zbp->option['ZC_STATIC_MODE'] == 'REWRITE' ){
		Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'static_addviewnumscript');
		Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'static_replace_views');
		Add_Filter_Plugin('Filter_Plugin_ViewList_Template', 'static_replace_listviews');
		Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed', 'static_post_build');
		Add_Filter_Plugin('Filter_Plugin_PostPage_Succeed', 'static_post_build');
		Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed', 'static_cmt_build');
		Add_Filter_Plugin('Filter_Plugin_Cmd_Ajax', 'static_ajaxdo');
	}
}

function static_ajaxdo($src_type){
	global $zbp;
	if (isset($src_type) && $src_type == 'static') {
		if (isset($_POST) && $_POST['postid'] > 0) {
			if (isset($_POST['add'])) {
				$post = $zbp->GetPostByID($_POST['postid']);
				$post->ViewNums = $post->ViewNums + 1;
				$post->Save();
				echo $post->ViewNums;
			}else{
				$post = $zbp->GetPostByID($_POST['postid']);
				echo $post->ViewNums;
			}
		}else {
			echo "Post ID is wrong";
		}
	}else{
		return null;
	}
}

function static_addviewnumscript(){
	global $zbp;
	echo 'function LoadViewNums(postid){$.post(ajaxurl + "static",{"postid":postid,},function(data){$("#spn" + postid).html(data);});return false;'."\n}\n";
	echo 'function AddViewNums(postid){$.post(ajaxurl + "static",{"postid":postid,"add":1},function(data){$("#spn" + postid).html(data);});return false;'."\n}\n";
}

function static_replace_listviews(&$template){
	$articles = $template->GetTags('articles');
	foreach ($articles as $article) {
		$article->ViewNums = '<span id="spn'.$article->ID.'"></span><script type="text/javascript">LoadViewNums('.$article->ID.')</script>';
	}
}

function static_replace_views(&$template){
	$article = $template->GetTags('article');
	$article->ViewNums = '<span id="spn'.$article->ID.'"></span><script type="text/javascript">AddViewNums('.$article->ID.')</script>';
}

function static_cmt_build($cmt){
	$post = new Post;
	$post->LoadInfoByID($cmt->LogID);
	static_post_build($post);
}

function static_post_build($article){
	global $zbp;
	$str = null;
	$post_url = $article->Url;
	if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
		$post_url = iconv("utf-8", "gbk",$post_url);
	}
	$url = str_replace($zbp->host, '', $post_url);
	$url = explode('/', $url);
	if(count($url) == 1){
		$str = static_get_postcontent($article->ID);
		file_put_contents($zbp->path.$url[0], $str);
	}else{
		$exists_url = $zbp->path;
		for ($i=0; $i < (count($url)-1); $i++) {
			$exists_url .= ($url[$i].'/');
			if (!file_exists($exists_url)) {
				@mkdir($exists_url);
			}
		}
		$str = static_get_postcontent($article->ID);
		file_put_contents($exists_url.end($url), $str);

	}

}

function static_get_postcontent($postid){
	ob_start();
	ViewPost($postid, null, false);
	$data = ob_get_contents();
	ob_end_clean();
	return $data;
}

function InstallPlugin_static() {}
function UninstallPlugin_static() {}