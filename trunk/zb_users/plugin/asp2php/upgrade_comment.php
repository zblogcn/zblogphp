<?php
header("Content-type: text/html; charset=utf-8");
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('asp2php')) {$zbp->ShowError(48);die();}
$blogtitle='Z-Blog(ASP)导入程序';

$strstr = '';
$comm_list = $zbp->GetCommentList();

foreach($comm_list as $o){
	if($o->ParentID == 0)continue;
	$rootid = find_comment_rootid($o->ParentID);
	$o->RootID = $rootid;
	$o->Save();
	$strstr .= '<br>已转换评论ID：'.$o->ID;
}

function find_comment_rootid($id){
	$comment = new Comment;
	$comment->LoadInfoByID($id);
	if($comment->ParentID == 0){
		return $id;
	}else{
		return find_comment_rootid($comment->ParentID);
	}
}
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
	<div id="divMain">
		<div class="divHeader"><?php echo $blogtitle;?></div>
		<div class="SubMenu">
		</div>
		<div id="divMain2">
			<?php
				echo $strstr;
			?>
		</div>
	</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>