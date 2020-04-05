<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('x2013')) {
    $zbp->ShowError(48);
    die();
}
$blogtitle = '主题配置';

if ($zbp->Config('x2013')->FirstInstall == true) {
    $zbp->Config('x2013')->FirstInstall = '0';
    $zbp->SaveConfig('x2013');
    Redirect('about.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

if (isset($_POST['DisplayFeed']) && $_POST['DisplayFeed'] != '') {
    foreach ($_POST as $k => $v) {
        $$k = $v;
    }
    if ($SetWeiboSina != '') {
        if ($SetWeiboSina == ($zbp->Config('x2013')->SetWeiboSina)) {
            $tips = '新浪微博地址未更改；';
        } else {
            $zbp->Config('x2013')->SetWeiboSina = $SetWeiboSina;
            $tips = '新浪微博地址设置成功；';
        }
    } else {
        $zbp->Config('x2013')->SetWeiboSina = '';
        $zbp->ShowHint('bad', '新浪微博地址为空，前台将不显示此图标.');
        $tips = '';
    }
    if ($SetWeiboQQ != '') {
        if ($SetWeiboQQ == ($zbp->Config('x2013')->SetWeiboQQ)) {
            $tips .= '腾讯微博地址未更改；';
        } else {
            $zbp->Config('x2013')->SetWeiboQQ = $SetWeiboQQ;
            $tips .= '腾讯微博地址设置成功；';
        }
    } else {
        $zbp->Config('x2013')->SetWeiboQQ = '';
        $zbp->ShowHint('bad', '腾讯微博地址为空，前台将不显示此图标.');
    }
    $zbp->Config('x2013')->DisplayFeed = $DisplayFeed;
    $zbp->Config('x2013')->SetMailKey = $SetMailKey;
    $zbp->Config('x2013')->Css = $_POST['color'];
    $css = @file_get_contents('style.css.html');
    $css = str_replace('{%background%}', $zbp->Config('x2013')->Css, $css);
    @file_put_contents($zbp->path . 'zb_users/theme/x2013/style/style.css', $css);
    $zbp->SaveConfig('x2013');
    if (isset($tips)) {
        $zbp->ShowHint('good', $tips);
    }
    //var_dump($_POST);
}
?>
<style>
input.text{background:#FFF;border:1px double #aaa;font-size:1em;padding:0.25em;}
p{line-height:1.5em;padding:0.5em 0;}
.tc{border: solid 2px #E1E1E1;width: 50px;height: 23px;float: left;margin: 0.25em;cursor: pointer}
.tc:hover,.active{border: 2px solid #2694E8;}
</style>
<script type="text/javascript" src="farbtastic.js"></script>
<link rel="stylesheet" href="farbtastic.css" type="text/css" />
 <script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
    $('#picker').farbtastic('#color');
  });
 </script>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle; ?></div>
  	<div class="SubMenu"><?php x2013_SubMenu(0); ?></div>
	<div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>

  </tr>
  <tr>
    <td><b><label for="SetWeiboSina"><p align="center">新浪微博</p></label></b></td>
    <td><p align="left"><input name="SetWeiboSina" type="text" id="SetWeiboSina" size="100%" value="<?php echo $zbp->Config('x2013')->SetWeiboSina; ?>" /></p></td>

  </tr>
  <tr>
    <td><b><label for="SetWeiboQQ"><p align="center">腾讯微博</p></label></b></td>
    <td><p align="left"><input name="SetWeiboQQ" type="text" id="SetWeiboQQ" size="100%" value="<?php echo $zbp->Config('x2013')->SetWeiboQQ; ?>" /></p></td>
  </tr>
  <tr>
    <td><b><label for="DisplayFeed"><p align="center">是否显示邮件订阅</p></label></b></td>
    <td><p align="left"><input id="DisplayFeed" name="DisplayFeed" style="display:none;" type="text" value="<?php echo $zbp->Config('x2013')->DisplayFeed; ?>" class="checkbox"></p></td>
  </tr>
  <tr>
    <td><b><label for="SetMailKey"><p align="center"><a href="http://list.qq.com/" target="_black">QQMail邮件订阅key</a></p></label></b></td>
    <td><p align="left"><input name="SetMailKey" type="text" id="SetMailKey" size="100%" value="<?php echo $zbp->Config('x2013')->SetMailKey; ?>" /></p></td>
  </tr>  
</table>
<table width="100%" border="1" width="100%" class="tableBorder">
	<tr>
		<th scope="col" height="32" width="150px">颜色配置</th>
		<th scope="col" width="120px">
		<div style="float:left;margin: 0.25em">推荐颜色：</div>
		</th>
		<th >
			<div id="loadconfig">
			<div class="tc" onclick='$("#color").val("#38A3DB");$("#color").css("background-color","#38A3DB");' style="background-color:#38A3DB"></div>
			<div class="tc" onclick='$("#color").val("#D581A2");$("#color").css("background-color","#D581A2");' style="background-color:#D581A2"></div>
			<div class="tc" onclick='$("#color").val("#CC0000");$("#color").css("background-color","#CC0000");' style="background-color:#CC0000"></div>
			<div class="tc" onclick='$("#color").val("#00ccca");$("#color").css("background-color","#00ccca");' style="background-color:#00ccca"></div>
			<div class="tc" onclick='$("#color").val("#ccaf00");$("#color").css("background-color","#ccaf00");' style="background-color:#ccaf00"></div>
			<div class="tc" onclick='$("#color").val("#18ae04");$("#color").css("background-color","#18ae04");' style="background-color:#18ae04"></div>
			</div>
		</th>
	</tr>
	<tr>
		<td>主题色调</td>
		<td><input type="text" id="color" name="color" value="<?php echo $zbp->Config('x2013')->Css; ?>"/></div></td>
		<td><div id="picker"></div></td>
	</tr>
</table>
 <br />
   <input name="" type="Submit" class="button" value="保存"/>
    </form>
	</div>
</div></div>

<script type="text/javascript">ActiveTopMenu("topmenu_x2013");</script> 
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>