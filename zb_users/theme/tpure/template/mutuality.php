<?php die(); ?>{* Template Name:相关文章模块 *}
{if $zbp->Config('tpure')->PostRELATEON=='1'}
{$aid=$article.ID}
{$tagid=$article.Tags}
{$cid=$article.Category.ID}
{php}
if($zbp->Config('tpure')->PostRELATENUM){
	$relatenum = $zbp->Config('tpure')->PostRELATENUM;
}else{
	$relatenum = '6';
}
$str = '';
if(empty($tagid)){
	$tagrd=0;
}else{
	$tagrd=array_rand($tagid);
}
if( sizeof($tagid)>0 && ($tagid[$tagrd]->Count)>1){
	$tagi='%{'.$tagrd.'}%';
	$where = array(array('=','log_Status','0'),array('like','log_Tag',$tagi),array('<>','log_ID',$aid));
}else{
	$where = array(array('=','log_Status','0'),array('=','log_CateID',$cid),array('<>','log_ID',$aid));
}
switch ($zbp->option['ZC_DATABASE_TYPE']) {
	case 'mysql':
	case 'mysqli':
	case 'pdo_mysql':
		$order = array('rand()'=>'');
	break;
	case 'sqlite':
	case 'sqlite3':
		$order = array('random()'=>'');
	break;
}
$array = $zbp->GetArticleList(array('*'),$where,$order,array($relatenum),'');
{/php}
{if count($array)>0}
	<div class="block">
		<div class="posttitle"><h4>相关文章</h4></div>
		<div class="relatecon">
	{php}
		foreach ($array as $related) {
			$content = $related->Content;
			$intro = preg_replace('/[\r\n\s]+/', ' ', trim(SubStrUTF8(TransferHTML($content,'[nohtml]'),$zbp->Config('tpure')->PostINTRONUM)).'...');
			if(tpure_Thumb($related) != ''){
				$isimg = ' class="isimg"';
			}else{
				$isimg = '';
			}
			if(($related->ID)!=$aid){
				$str .= '<div class="relate">';
					if(tpure_Thumb($related) != ''){$str .= '<div class="relateimg"><a href="'.$related->Url.'" target="_blank" title="'.$related->Title.'"><img src="'.tpure_Thumb($related).'" alt="'.$related->Title.'"></a></div>';}
				$str .= '<div class="relateinfo">
						<h3><a href="'.$related->Url.'" target="_blank" title="'.$related->Title.'">'.$related->Title.'</a></h3>
						<p'.$isimg.'>';
						if(tpure_isMobile()){$str .= '<a href="'.$article->Url.'" target="_blank">';}
							$str .= $intro;
						if(tpure_isMobile()){$str .= '</a>';}
						$str .= '</p>';
					$str .= '</div>
				</div>';
			}
		}
	{/php}
	{$str}
		</div>
	</div>
{/if}
{/if}