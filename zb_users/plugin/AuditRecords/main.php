<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'ArticleAll';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AuditRecords')) {$zbp->ShowError(48);die();}

$blogtitle = '审核管理';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"></div>
  <div id="divMain2">

<?php

	echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter">';
	echo '<tr>
	<th>' . $zbp->lang['msg']['id'] . '</th>
	<th>' . $zbp->lang['msg']['category'] . '</th>
	<th>' . $zbp->lang['msg']['author'] . '</th>
	<th>' . $zbp->lang['msg']['title'] . '</th>
	<th>' . $zbp->lang['msg']['date'] . '</th>
	<th>审核回复</th>
	<th>' . $zbp->lang['msg']['status'] . '</th>
	<th></th>
	</tr>';

$p = new Pagebar('{%host%}zb_users/plugin/AuditRecords/main.php{?page=%page%}', false);
$p->PageCount = $zbp->managecount;
$p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');
$p->PageBarCount = $zbp->pagebarcount;

$p->UrlRule->Rules['{%category%}'] = GetVars('category');
$p->UrlRule->Rules['{%search%}'] = urlencode(GetVars('search'));
$p->UrlRule->Rules['{%status%}'] = GetVars('status');
$p->UrlRule->Rules['{%istop%}'] = (boolean) GetVars('istop');

$w = array();
if(!$zbp->CheckRights('ArticleAll')){
	$w[] = array('=', 'log_AuthorID', $zbp->user->ID);
}

$w[] = array('=', 'log_Status', 2);

$array = $zbp->GetArticleList(
	'',
	$w,
	array('log_PostTime' => 'DESC'),
	array(($p->PageNow - 1) * $p->PageCount, $p->PageCount),
	array('pagebar' => $p)
);

foreach ($array as $article) {

$sql = $zbp->db->sql->Select($table['AuditRecords'], '*', array(array('=', 'ar_LogID', $article->ID)), array('ar_PostTime' => 'DESC'), null, null);
$array = $zbp->GetList('AuditRecords', $sql);
$num = count($array);


	echo '<tr>';
	echo '<td class="td5">' . $article->ID .  '</td>';
	echo '<td class="td10">' . $article->Category->Name . '</td>';
	echo '<td class="td10">' . $article->Author->Name . '</td>';
	echo '<td><a href="'.$article->Url.'" target="_blank"><img src="'.$bloghost.'zb_system/image/admin/link.png" alt="" title="" width="16" /></a> ' . $article->Title;
	if($num > 0){
		echo '<p><i style="font-size:0.8em">最后一次审核或回复记录：</i></p><blockquote><p><b>'.$zbp->members[$array[0]->AuthorID]->Name.'</b>记录于'.date('c', $array[0]->PostTime).'</p><p>'.htmlspecialchars($array[0]->Logs).'</p></blockquote>';
	}
	echo '</td>';
	echo '<td class="td20">' .$article->Time() . '</td>';
	echo '<td class="td10">' . $num . '</td>';
	echo '<td class="td5">' . ($article->IsTop ? $zbp->lang['msg']['top'].'|' : '').$article->StatusName . '</td>';
	echo '<td class="td10 tdCenter">';
	echo '<a href="'.$bloghost.'zb_system/cmd.php?act=ArticleEdt&amp;id='. $article->ID .'"><img src="'.$bloghost.'zb_system/image/admin/page_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<a onclick="return window.confirm(\''.$zbp->lang['msg']['confirm_operating'] .'\');" href="'.$bloghost.'zb_system/cmd.php?act=ArticleDel&amp;id='. $article->ID  . '&amp;token='. $zbp->GetToken() .'"><img src="'.$bloghost.'zb_system/image/admin/delete.png" alt="'.$zbp->lang['msg']['del'] .'" title="'.$zbp->lang['msg']['del'] .'" width="16" /></a>';
	echo '</td>';

	echo '</tr>';
}
	echo '</table>';
	echo '<hr/><p class="pagebar">';

foreach ($p->buttons as $key => $value) {
	echo '<a href="'. $value .'">' . $key . '</a>&nbsp;&nbsp;';
}

	echo '</p>';
?>

	<script type="text/javascript">ActiveLeftMenu("aAuditRecords");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_system/image/common/article_32.png';?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>