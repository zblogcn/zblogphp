<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('Howl')) {$zbp->ShowError(48);die();}

$blogtitle='Z-Blog角色分配器';
$right = GetVars("key","GET");
if(!array_key_exists($right, $actions)) exit;

if($zbp->Config('Howl')->HasKey('User'))
{
	$rights = json_to_array(json_decode($zbp->Config('Howl')->User));
	if(isset($rights[$right]))
	{
		$rights = $rights[$right];
	}
}
else
{
	$rights = array();
}

if(count($_POST)>0){

	$a = array();

	foreach ($_POST as $key => $value)
	{
		if (CheckRegExp($key, '/^userlist_\d+$/u'))
		{
			$a[$value] = $value;
		}
	}

	$rights[$right] = $a;
	
	$zbp->Config('Howl')->User = json_encode($rights);
	$zbp->SaveConfig('Howl');
	echo '<script>window.close();</script>';
}

?>
您要配置的是权限：<?php echo $right;?>
<form action="set_user.php?key=<?php echo $right;?>" method="POST">
<p>以下用户允许使用该权限：</p>
<input type="submit" value="提交" />
<?php foreach($zbp->members as $obj){?>
<p>
  <label>
    <input type="checkbox" name="userlist_<?php echo $obj->ID?>" value="<?php echo $obj->ID?>" id="mem_<?php echo $obj->ID?>" <?php echo (array_key_exists($obj->ID, $rights)?' checked="checked" ':'')?>><?php echo $obj->Name?></label>
</p>
<?php } ?>
<input type="submit" value="提交"/>
<input type="hidden"  name="lanycs" value="123" />
</form>

<?php
function json_to_array($r){
	$arr = array();
	foreach($r as $k => $w){
		if(is_object($w)) $arr[$k]=json_to_array($w);
		else $arr[$k]=$w;
	}
	return $arr;
}