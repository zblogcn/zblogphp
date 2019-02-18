<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('LargeData')) {
    $zbp->ShowError(48);
    die();
}

if (GetVars('build_post_index', 'POST') === '0') {
    $zbp->db->Query("ALTER TABLE " . $table['Post'] . " DROP INDEX  " . $zbp->db->dbpre . "log_TISC ;");
    $zbp->db->Query("ALTER TABLE " . $table['Post'] . " DROP INDEX  " . $zbp->db->dbpre . "log_PT ;");
    $zbp->db->Query("ALTER TABLE " . $table['Post'] . " DROP INDEX  " . $zbp->db->dbpre . "log_TPISC ;");
    $zbp->db->Query("ALTER TABLE " . $table['Post'] . " ADD INDEX  " . $zbp->db->dbpre . "log_LD_Title(log_Title) ;");
    $zbp->db->Query("ALTER TABLE " . $table['Post'] . " ADD INDEX  " . $zbp->db->dbpre . "log_LD_PCATISID(log_PostTime,log_CateID,log_AuthorID,log_Type,log_IsTop,log_Status,log_ID) ;");
    $zbp->SetHint('good');
    Redirect('./main.php');
}

if (GetVars('build_comment_index', 'POST') === '0') {
    $zbp->db->Query("ALTER TABLE " . $table['Comment'] . " DROP INDEX  " . $zbp->db->dbpre . "comm_PT ;");
    $zbp->db->Query("ALTER TABLE " . $table['Comment'] . " DROP INDEX  " . $zbp->db->dbpre . "comm_RIL ;");
    $zbp->db->Query("ALTER TABLE " . $table['Comment'] . " ADD INDEX  " . $zbp->db->dbpre . "comm_LD_LRIID(comm_LogID,comm_RootID,comm_IsChecking,comm_ID) ;");
    $zbp->SetHint('good');
    Redirect('./main.php');
}

if (GetVars('build_post2tag_table', 'POST') === '0') {
    LargeData_CreateTable();
    Redirect('./main.php');
}

if (GetVars('convert_post2tag_table', 'POST') === '0') {
    LargeData_ConvertTable_Post2Tag();
    Redirect('./main.php');
}

if (count($_POST) > 0) {
    $zbp->option['ZC_LARGE_DATA'] = (bool) $_POST['ZC_LARGE_DATA'];
    $zbp->SaveOption();
    $zbp->SetHint('good');
    Redirect('./main.php');
}

$blogtitle = 'LargeData';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader2"><?php echo $blogtitle; ?></div>
<div class="SubMenu"></div>
  <div id="divMain2">
<form method="post" action="main.php">
<?php
echo '<table style="padding:0px;margin:0px;width:100%;">';
echo '<tr><td class="td25"><p><b>开启大数据支持</b></p></td>
	<td><p><input id="ZC_LARGE_DATA" name="ZC_LARGE_DATA" type="text" value="' . $zbp->option['ZC_LARGE_DATA'] . '" class="checkbox"/></p></td></tr>';
echo '</table>';
?>
     <hr/>
    <p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p>
</form>

<form method="post" action="main.php">
<input id="build_post_index" name="build_post_index" type="hidden" value="0" />
<?php
echo '<table style="padding:0px;margin:0px;width:100%;">';
echo '<tr><td class="td25"><p><b>重建文章表的索引</b></p></td>
	<td><p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p></td></tr>';
echo '</table>';
?>
     <hr/>
</form>

<form method="post" action="main.php">
<input id="build_comment_index" name="build_comment_index" type="hidden" value="0" />
<?php
echo '<table style="padding:0px;margin:0px;width:100%;">';
echo '<tr><td class="td25"><p><b>重建评论表的索引</b></p></td>
	<td><p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p></td></tr>';
echo '</table>';
?>
     <hr/>
</form>

<form method="post" action="main.php">
<input id="build_post2tag_table" name="build_post2tag_table" type="hidden" value="0" />
<?php
echo '<table style="padding:0px;margin:0px;width:100%;">';
echo '<tr><td class="td25"><p><b>建立文章标签关联表和索引</b></p></td>
	<td><p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p></td></tr>';
echo '</table>';
?>
     <hr/>
</form>

<form method="post" action="main.php">
<input id="convert_post2tag_table" name="convert_post2tag_table" type="hidden" value="0" />
<?php
echo '<table style="padding:0px;margin:0px;width:100%;">';
echo '<tr><td class="td25"><p><b>转换原文章表的标签关联到新表</b></p></td>
	<td><p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p></td></tr>';
echo '</table>';
?>
     <hr/>
</form>


    <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/LargeData/logo.png'; ?>");</script>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
