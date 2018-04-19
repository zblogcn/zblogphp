<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('wp2zbp')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = 'WordPress数据转移插件';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader2"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"> </div>
  <div id="divMain2">
    <?php if (preg_match("/sqlite/", $zbp->option['ZC_DATABASE_TYPE'])) {
    exit('请使用MySQL数据库转移emlog数据');
} ?>
    <p>&nbsp;</p>
    <p>1. 本程序不转换设置、不转换插件、不转换主题。全部的用户密码都将被重置。同时将完全清空Z-BlogPHP的数据库。</p>
    <p>&nbsp;</p>
    <p>2. 请确认您把Z-BlogPHP和WordPress装在同一数据库里。</p>
    <p>&nbsp;</p>
    <p>3. 程序基于WordPress 3.9.1数据结构所写，其他版本未测试。如有问题，请到应用中心反馈。</p>
    <p>&nbsp;</p>
    <p>4. 请在这里输入WordPress数据表的表前缀：</p>
    <p>&nbsp;</p>
    <p>
      <input type="text" name="prefix" id="prefix" value="wp_" />
      <input type="button" class="button" id="jump" value="提交"/>
    </p>
  </div>
</div>
<script type="text/javascript">
(function(){
	$("#jump").click(function(){
		location.href = "convert.php?prefix=" + $("#prefix").val();
	});
})();
</script>
<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/wp2zbp/logo.png'; ?>");</script>	
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
/*
0,1,2,7,10

SELECT * FROM `wp_users`,`wp_usermeta` WHERE `wp_users`.`ID`=`wp_usermeta`.`user_id`


SELECT * FROM `wp_users` INNER JOIN `wp_usermeta` WHERE `wp_users`.`ID`=`wp_usermeta`.`user_id`


SELECT * FROM `wp_users`,`wp_usermeta` WHERE `wp_users`.`ID`=`wp_usermeta`.`user_id` AND `wp_usermeta`.`meta_key`="wp_user_level"


SELECT * FROM `wp_posts`,`wp_postmeta` WHERE (`wp_posts`.`ID`=`wp_postmeta`.`post_id` AND `wp_posts`.`post_type`="attachment" AND `wp_postmeta`.`meta_key`="_wp_attached_file");

*/
?>
