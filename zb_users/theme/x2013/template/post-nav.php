{php}
$html='';
function navcate($id){
	global $html;
	$cate = new Category;
	$cate->LoadInfoByID($id);
	$html ='>>  <a href="' .$cate->Url.'" title="查看' .$cate->Name. '中的全部文章">' .$cate->Name. '</a> '.$html;
	if(($cate->ParentID)>0){navcate($cate->ParentID);}
}
navcate($article->Category->ID);
global $html;
echo $html;
{/php}