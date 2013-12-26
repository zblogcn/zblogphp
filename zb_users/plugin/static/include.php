<?php
RegisterPlugin("static","ActivePlugin_static");

function ActivePlugin_static() {
	if( $zbp->option['ZC_STATIC_MODE'] == REWRITE ){
		Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed','post_build');
		Add_Filter_Plugin('Filter_Plugin_PostPage_Succeed','post_build');
		Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed','cmt_build');
	}
}

function cmt_build($cmt){
	$post = new POST;
	$post->LoadInfoByID($cmt->LogID);
	post_build($post);
}

function post_build($article){
	global $zbp;
	$str = null;
	$post_url = $article->Url;
	if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
		$post_url = iconv("utf-8", "gbk",$post_url);
	}
	$url = str_replace($zbp->host, '', $post_url);
	$url = explode('/', $url);
	if(count($url) == 1){
		$str = file_get_contents($post_url);
		file_put_contents($zbp->path.$url[0], $str);
	}else{
		$exists_url = $zbp->path;
		for ($i=0; $i < (count($url)-1); $i++) {
			$exists_url .= ($url[$i].'/');
			if (!file_exists($exists_url)) {
				@mkdir($exists_url);
			}
		}
		$str = file_get_contents($post_url);
		file_put_contents($exists_url.end($url), $str);
	}
}


function InstallPlugin_static() {}
function UninstallPlugin_static() {}