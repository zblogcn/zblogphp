<?php
#注册插件
RegisterPlugin("AutoPublic","ActivePlugin_AutoPublic");

function ActivePlugin_AutoPublic() {
	Add_Filter_Plugin('Filter_Plugin_PostArticle_Succeed','AutoPublic_PostArticle_Succeed');
	Add_Filter_Plugin('Filter_Plugin_ViewList_Begin','AutoPublic_Begin');
}

function AutoPublic_PostArticle_Succeed(&$article){
	if ($article->PostTime > ( time() + (6*3600) )) {
		$article->Status = 2;
		$article->Metas->AutoPublic = true;
		$article->Save();
	}
}

function AutoPublic_Begin($page,$cate,$auth,$date,$tags){
	global $zbp;
	$articles = $zbp->GetArticleList(array('*'), array(array('<','log_PostTime',time()), array('=','log_Status',2)), array('log_PostTime'=>'DESC'),null, null);
	foreach ($articles as $key => $value) {
		if($value->Metas->AutoPublic){
			$value->Status = 0;
			$value->Metas->Del('AutoPublic');
			$value->Save();
		}
	}
}

function InstallPlugin_AutoPublic() {}
function UninstallPlugin_AutoPublic() {}