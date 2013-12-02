<?php
require '../../zb_system/function/c_system_base.php';
require '../../zb_system/function/c_system_admin.php';

$zbp->Load();
$blogtitle='验证码';

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';


if(isset($tips)){$zbp->ShowHint('good', $tips);}
?>
<div id="divMain">
<?php
echo $zbp->CheckValidCode(GetVars('SetWeiboSina'),'theme');
?>
	<div class="divHeader"><?php echo $blogtitle;?></div>
	<div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
    
  </tr>
  <tr>
    <td><b><label for="SetWeiboSina"><p align="center">验证码<label><img src="../../zb_system/script/c_validcode.php?id=theme&amp;" onclick="javascript:this.src='../../zb_system/script/c_validcode.php?id=theme&amp;tm='+Math.random();" /></label> </p></label></b></td>
    <td><p align="left"><input name="SetWeiboSina" type="text" id="SetWeiboSina" size="100%" value="" /></p></td>
    
  </tr>
  
</table>
 <br />
   <input name="" type="Submit" class="button" value="保存"/>
    </form>
	</div>
</div></div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>