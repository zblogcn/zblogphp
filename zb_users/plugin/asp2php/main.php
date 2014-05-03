<?php
header("Content-type: text/html; charset=utf-8");
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('asp2php')) {$zbp->ShowError(48);die();}
$blogtitle='Z-Blog(ASP)导入程序';

if($_POST && $_POST['str'] == 'php'){
$uploadfile = $zbp->usersdir . 'plugin/asp2php/asp2php.backup';
move_uploaded_file($_FILES['edtFileLoad']['tmp_name'], $uploadfile);
$json = file_get_contents($uploadfile);
$aspdata = json_decode($json,true);
$strstr = "";


$zbp->option['ZC_BLOG_NAME'] = $aspdata['blogconfig']['title'];
$zbp->option['ZC_BLOG_SUBNAME'] = $aspdata['blogconfig']['subtitle'];
$zbp->option['ZC_BLOG_COPYRIGHT'] = $aspdata['blogconfig']['copyright'];
$zbp->option['ZC_DISPLAY_COUNT'] = $aspdata['blogconfig']['display_count'];
$zbp->option['ZC_COMMENTS_DISPLAY_COUNT'] = $aspdata['blogconfig']['comments_count'];
$zbp->option['ZC_SEARCH_COUNT'] = $aspdata['blogconfig']['search_count'];
$zbp->option['ZC_PAGEBAR_COUNT'] = $aspdata['blogconfig']['pagebar_count'];
$zbp->option['ZC_RSS2_COUNT'] = $aspdata['blogconfig']['rss_count'];
$zbp->option['ZC_RSS_EXPORT_WHOLE'] = $aspdata['blogconfig']['rss_whole'];
$zbp->SaveOption();


foreach ($aspdata['cata'] as $a) {
	$o=new Category;
	$o->LoadInfoByID($a['cate_ID']);

	$o->Name=$a['cate_Name'];
	$o->Order=$a['cate_Order'];	
	$o->Count =$a['cate_Count'];	
	$o->Alias=$a['cate_URL'];
	$o->Intro=$a['cate_Intro'];
	$o->ParentID=$a['cate_ParentID'];
	
	if($o->ID == 0){
		$o->ID=$a['cate_ID'];
		$keys=array();
		foreach ($o->GetDataInfo() as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');
		
		foreach ($o->GetDataInfo() as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$o->$key;
			}else{
				$keyvalue[$value[0]]=$o->$key;
			}
		}
		
		$sql = $zbp->db->sql->Insert($o->GetTable(),$keyvalue);
		$zbp->db->Insert($sql);
	}else{
		$o->Save();
	}
	$strstr .= "已转换分类：{$a['cate_ID']}-->{$a['cate_Name']}<br>";
}

foreach ($aspdata['tag'] as $a) {
	$o=new Tag;
	$o->LoadInfoByID($a['tag_ID']);
	
	$o->Name=$a['tag_Name'];
	$o->Order=$a['tag_Order'];	
	$o->Count =$a['tag_Count'];	
	$o->Alias=$a['tag_URL'];
	$o->Intro=$a['tag_Intro'];
	
	if($o->ID == 0){
		$o->ID=$a['tag_ID'];
		$keys=array();
		foreach ($o->GetDataInfo() as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');
		
		foreach ($o->GetDataInfo() as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$o->$key;
			}else{
				$keyvalue[$value[0]]=$o->$key;
			}
		}
		
		$sql = $zbp->db->sql->Insert($o->GetTable(),$keyvalue);
		$zbp->db->Insert($sql);
	}else{
		$o->Save();
	}
	
	$strstr .= "已转换标签：{$a['tag_ID']}-->{$a['tag_Name']}<br>";
}

foreach ($aspdata['post'] as $a) {
	$o=new Post;
	$o->LoadInfoByID($a['log_ID']);
		
	$o->CateID=$a['log_CateID'];	
	$o->AuthorID=$a['log_AuthorID'];	
	$o->Alias=$a['log_Url'];
	$o->Content=$a['log_Content'];
	
	$o->Content=str_replace('src="image/face/','src="{#ZC_BLOG_HOST#}zb_users/emotion/face/',$o->Content);	
	$o->Content=str_replace('src="upload','src="{#ZC_BLOG_HOST#}zb_users/upload',$o->Content);
	$o->Content=str_replace('href="upload','href="{#ZC_BLOG_HOST#}zb_users/upload',$o->Content);
	$o->Content=str_replace('value="upload','value="{#ZC_BLOG_HOST#}zb_users/upload',$o->Content);
	$o->Content=str_replace('href="http://upload','value="{#ZC_BLOG_HOST#}zb_users/upload',$o->Content);
	$o->Content=str_replace('&lt;br/&gt;','<br/>',$o->Content);	
	$o->Content=str_replace('<#ZC_BLOG_HOST#>','{#ZC_BLOG_HOST#}',$o->Content);
	
	$o->Intro=$a['log_Intro'];
	$o->Title=$a['log_Title'];
	$o->PostTime=$a['log_PostTime'];//strtotime($a['log_PostTime']);
	$o->IP=$a['log_IP'];
	if($a['log_Level']==1){$o->Status=1;
	}elseif($a['log_Level']==3){$o->IsLock=1;
	}elseif($a['log_Level']==2){$o->Status=2;}
	$o->CommNums=$a['log_CommNums'];
	$o->ViewNums=$a['log_ViewNums'];
	$o->Type=$a['log_Type'];
	$o->Tag=$a['log_Tag'];
	$o->IsTop=$a['log_IsTop'];

	if($o->ID == 0){
		$o->ID=$a['log_ID'];
		$keys=array();
		foreach ($o->GetDataInfo() as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');
		
		foreach ($o->GetDataInfo() as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$o->$key;
			}else{
				$keyvalue[$value[0]]=$o->$key;
			}
		}
		
		$sql = $zbp->db->sql->Insert($o->GetTable(),$keyvalue);
		$zbp->db->Insert($sql);
	}else{
		$o->Save();
	}
	
	$strstr .= "已转换文章：{$a['log_ID']}-->{$a['log_Title']}<br>";
}

foreach ($aspdata['comm'] as $a) {
	$o=new Comment;
	$o->LoadInfoByID($a['comm_ID']);
	
	$o->LogID=$a['log_ID'];	
	$o->IsChecking=$a['comm_IsCheck'];	
	//$o->RootID=$a['comm_CateID'];	
	$o->ParentID=$a['comm_ParentID'];
	//$o->AuthorID=$a['comm_AuthorID'];	
	$o->Name=$a['comm_Author'];
	$o->Email=$a['comm_Email'];
	$o->HomePage=$a['comm_HomePage'];
	$o->Content=$a['comm_Content'];
	$o->PostTime=$a['comm_PostTime'];//strtotime($a['comm_PostTime']);
	$o->IP=$a['comm_IP'];
	$o->Agent=$a['comm_Agent'];

	if($o->ID == 0){
		$o->ID=$a['comm_ID'];	
		$keys=array();
		foreach ($o->GetDataInfo() as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');
		
		foreach ($o->GetDataInfo() as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$o->$key;
			}else{
				$keyvalue[$value[0]]=$o->$key;
			}
		}
		
		$sql = $zbp->db->sql->Insert($o->GetTable(),$keyvalue);
		$zbp->db->Insert($sql);
	}else{
		$o->Save();
	}
	
	$strstr .= "已转换评论：{$a['comm_ID']}-->{$a['comm_Content']}<br>";
}

foreach ($aspdata['upload'] as $a) {
	$o=new Upload;
	$o->LoadInfoByID($a['ul_ID']);
	
	$o->AuthorID=$a['ul_AuthorID'];	
	$o->Size=$a['ul_FileSize'];
	$o->Name=$a['ul_FileName'];
	$o->DownNums=$a['ul_DownNum'];
	$o->Intro=$a['ul_FileIntro'];
	$o->PostTime=$a['ul_PostTime'];//strtotime($a['ul_PostTime']);	
	
	if($o->ID == 0){
		$o->ID=$a['ul_ID'];	
		$keys=array();
		foreach ($o->GetDataInfo() as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');
		
		foreach ($o->GetDataInfo() as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$o->$key;
			}else{
				$keyvalue[$value[0]]=$o->$key;
			}
		}
		
		$sql = $zbp->db->sql->Insert($o->GetTable(),$keyvalue);
		$zbp->db->Insert($sql);
	}else{
		$o->Save();
	}
	
	$strstr .= "已转换附件：{$a['ul_ID']}-->{$a['ul_FileName']}<br>";
}

foreach ($aspdata['mem'] as $a) {
	$mem=new Member;
	$mem->LoadInfoByID($a['mem_ID']);
	
	$mem->Name=$a['mem_Name'];	
	$mem->Level=$a['mem_Level'];
	$mem->Password=$a['mem_Password'];
	$mem->Email=$a['mem_Email'];
	$mem->HomePage=$a['mem_HomePage'];
	$mem->IP=$a['mem_IP'];	
	$mem->Intro=$a['mem_Intro'];
	$mem->Articles=$a['mem_PostLogs'];
	$mem->Comments=$a['mem_PostComms'];
	$mem->Guid=$a['mem_Guid'];
	$mem->PostTime=$a['mem_LastVisit'];//strtotime($a['mem_LastVisit']);	

	if($o->ID == 0){
		$mem->ID=$a['mem_ID'];
		$keys=array();
		foreach ($o->GetDataInfo() as $key => $value) {
			$keys[]=$value[0];
		}
		$keyvalue=array_fill_keys($keys, '');
		
		foreach ($o->GetDataInfo() as $key => $value) {
			if($value[1]=='boolean'){
				$keyvalue[$value[0]]=(integer)$o->$key;
			}else{
				$keyvalue[$value[0]]=$o->$key;
			}
		}
		
		$sql = $zbp->db->sql->Insert($o->GetTable(),$keyvalue);
		$zbp->db->Insert($sql);
	}else{
		$mem->Save();
	}

	$strstr .= "已转换用户：{$a['mem_ID']}-->{$a['mem_Name']}<br>";
}

//var_dump($aspdata);
unlink($uploadfile);



$comm_list = $zbp->GetCommentList();
$post_list = $zbp->GetPostList();
$tag_list = $zbp->GetTagList();
$member_list = $zbp->GetMemberList();
$catagory_list = $zbp->GetCategoryList();

//关联评论用户ID
foreach($comm_list as $o){
	foreach($member_list as $m){
		if(($o->Name )== ($m->Name)){
			$comm = new Comment;
			$comm->LoadInfoByID($o->ID);
			$comm->AuthorID = $m->ID;
			$comm->Save();
		}
	}
}

//更新章统计
foreach($post_list as $p){
	$post = new Post;
	$post->LoadInfoByID($p->ID);
	$post->Save();
}

//更新标签统计
foreach($tag_list as $t){
	$tag = new Tag;
	$tag->LoadInfoByID($t->ID);
	$tag->Save();
}

//更新分类统计
foreach($catagory_list as $c){
	$cata = new Category;
	$cata->LoadInfoByID($c->ID);
	$cata->Save();
}

//更新用户统计
foreach($member_list as $m){
	$mem = new Member;
	$mem->LoadInfoByID($m->ID);
	$mem->Save();
}


//更新全局统计
$zbp->BuildTemplate();
$all_artiles=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Post'] . ' WHERE log_Type=0'),'num');
$all_pages=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(log_ID) AS num FROM ' . $GLOBALS['table']['Post'] . ' WHERE log_Type=1'),'num');	
$all_categorys=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(cate_ID) AS num FROM ' . $GLOBALS['table']['Category']),'num');
$all_comments=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(comm_ID) AS num FROM ' . $GLOBALS['table']['Comment']),'num');
$all_views=GetValueInArrayByCurrent($zbp->db->Query('SELECT SUM(log_ViewNums) AS num FROM ' . $GLOBALS['table']['Post']),'num');
$all_tags=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(tag_ID) as num FROM ' . $GLOBALS['table']['Tag']),'num');
$all_members=GetValueInArrayByCurrent($zbp->db->Query('SELECT COUNT(mem_ID) AS num FROM ' . $GLOBALS['table']['Member']),'num');

$s=$zbp->db->sql->Count($zbp->table['Post'],array(array('COUNT','*','num')),array(array('=','log_Type',0),array('=','log_IsTop',0),array('=','log_Status',0)));
$num=GetValueInArrayByCurrent($zbp->db->Query($s),'num');

$zbp->LoadConfigs();
$zbp->LoadCache();
$zbp->cache->normal_article_nums=$num;

$zbp->SaveCache();

$zbp->AddBuildModule('statistics',array($all_artiles,$all_pages,$all_categorys,$all_tags,$all_views,$all_comments));
$zbp->BuildModule();


}


//var_dump($comm_list);

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?>	<?php
	  $app = new App;
	  $app->LoadInfoByXml('plugin','asp2php');
	  $version = $app->version;
	  if($version >= '1.2'){
		  echo '<a href="upgrade_comment.php" class="button" style="color:red;">升级ASP导入评论数据</a>';
	  }
	  ?></div>
  <div class="SubMenu">
  </div>
<div id="divMain2">

<form border="1" name="edit" id="edit" method="post" enctype="multipart/form-data" action="">
<p>上传<b>Z-Blog To PHP</b>导出的asp2php.backup文件: </p>
<p>
	<input type="hidden" value="php" name="str">
	<input type="file" id="edtFileLoad" name="edtFileLoad" size="20">
	<input type="submit" class="button" value="提交" name="B1" onclick="">
</p><br>
</form>

<?php
if($_POST && $_POST['str'] == 'php'){echo $strstr;}
?>
</div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>