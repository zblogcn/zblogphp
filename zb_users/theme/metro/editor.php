<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('metro')) {$zbp->ShowError(48);die();}
$blogtitle = 'Metro主题配置';

if ($zbp->Config('metro')->HasKey('version')) {
	$strlayout = $zbp->Config('metro')->custom_layout;
	$strBodyBg = $zbp->Config('metro')->custom_bodybg;
	$strHdBg = $zbp->Config('metro')->custom_hdbg;
	$strColor = $zbp->Config('metro')->custom_color;
	$aryBodyBg = explode('|', $strBodyBg);
	$aryHdBg = explode('|', $strHdBg);
	$aryColor = explode('|', $strColor);
}
 
$a = array("", "左", "中", "右");
$r = "?" . rand();

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<link href="source/evol.colorpicker.css" rel="stylesheet" />
<script src="source/evol.colorpicker.min.js" type="text/javascript"></script>
<script src="source/custom.js" type="text/javascript"></script>
<?php
if ($zbp->CheckPlugin('UEditor')) {
?>
<script type="text/javascript" src="<?php echo $zbp->host;?>zb_users/plugin/UEditor/ueditor.config.php"></script>
<script type="text/javascript" src="<?php echo $zbp->host;?>zb_users/plugin/UEditor/ueditor.all.min.js"></script>
<?php
}
?>
<style>
table input{padding: 0;margin:0.25em 0;}
table input#hdbgph{padding: 2px 5px;}
table .button{padding: 2px 12px 5px 12px; margin: 0.25em 0;}
.tc{border: solid 2px #E1E1E1;width: 50px;height: 23px;float: left;margin: 0.25em;cursor: pointer}
.tc:hover,.active{border: 2px solid #2694E8;}
.upinfo{position: relative;left: 3px;top: -19px;color: white;background: #5EAAE4;width: 190px;height: 23px;display: inline-block;text-align: center;opacity: 0.8;filter: alpha(opacity=80);}
.imageshow{margin:0.25em 0;}.imageshow img{margin:0 10px;margin-bottom:-10px;}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
	<div class="divHeader">
		<?php echo $blogtitle;?></div>
	<div class="SubMenu"></div>
	<div id="divMain2">
		<form action="save.php" method="post">
			<table width="100%" border="1" width="100%" class="tableBorder">
				<tr>
					<th scope="col"  height="32" >整体设置</th>
					<th></th>
				</tr>
				<tr>
					<td scope="col"  height="52">外观模式</td>
					<td >
						<div id="layoutset">
							<input type="radio" id="layoutl" name="layout" value="l" <?php echo $strlayout == 'l' ? 'checked="checked"' : ''; ?> />
							<label for="layoutl">侧栏居左</label>
							<input type="radio" id="layoutr" name="layout" value="r" <?php echo $strlayout == 'r' ? 'checked="checked"' : ''; ?> />
							<label for="layoutr">侧栏居右</label>
						</div>
					</td>
				</tr>
				<tr>
					<td>顶部背景</td>
					<td>
						<div >
							顶部高度：
							<input id="hdbgph" type="text" name="hdbg5"  size="3"  value="<?php echo $aryHdBg[5];?>" />（单位：px）</div>
						<div id="hdbgcolor" >
							<input type="checkbox" id="hdbgc0" name="hdbg0" <?php echo $aryHdBg[0] == 'transparent' ? 'checked="checked"' : ''; ?> value="transparent"/>
							<label for="hdbgc0">背景透明（不透明情况下使用主色为背景色）</label>
						</div>
						<div >
							<input type="checkbox" id="hdbgc6" name="hdbg6" <?php echo $aryHdBg[6] == 'True' ? 'checked="checked"' : ''; ?> value="True"/>
							<label for="hdbgc6">使用背景图</label>
						</div>
						<div id="hdbgmain" <?php echo $aryHdBg[6] == "" ? 'style="display:none"' : "" ?>>
							<div class="imageshow">
								<input  type="hidden"  id="url_updatapic2" name="hdbg1"  value="<?php echo $aryHdBg[1] ?>" />
								<img src="<?php echo $aryHdBg[1] . $r ?>" width="190" height="120" border="0" alt="" id="pic_updatapic2">
								<input type="button"  id="updatapic2" class="button" value="更换图片" />
							</div>
							<div id="hdbgs">
								背景设定：
								<input type="checkbox" id="hdbg2r" name="hdbg2[]" <?php echo(stripos($aryHdBg[2], "repeat") > -1 ? 'checked="checked"' : ""); ?> value="repeat"/>
								<label for="hdbg2r">平铺</label>
								<input type="checkbox" id="hdbg2f" name="hdbg2[]" <?php echo(stripos($aryHdBg[2], "fixed") > -1 ? 'checked="checked"' : ""); ?> value="fixed"/>
								<label for="hdbg2f">固定</label>
								<input type="checkbox" id="hdbg2g" name="hdbg2[]" <?php echo(stripos($aryHdBg[2], "cover") > -1 ? 'checked="checked"' : ""); ?> value="cover"/>
								<label for="hdbg2g">拉伸(不支持IE678)</label>
							</div>
							<div id="hdbgpx">
								对齐方式：
								<?php	for($i = 1;$i <= 3;$i++){	 ?>

								<input type="radio" id="hdbgpx<?php echo $i ?>" name="hdbg3" value="<?php echo $i ?>" <?php echo $i == (int) $aryHdBg[3] ? 'checked="checked"' : ""; ?> />
								<label for="hdbgpx<?php echo $i ?>">居<?php echo $a[$i] ?></label>
								
								<?php	}   ?></div>
							<input id="hdbgpy" type="hidden" name="hdbg4"  value="<?php echo $aryHdBg[4] ?>" /></div>
					</td>
				</tr>
				<tr>
					<td width="150px">页面背景</td>
					<td>
						<div id="bgcolor">
							背景颜色：
							<input id="bodybgc0" name="bodybg0"  value="<?php echo $aryBodyBg[0] ?>" /></div>
						<div >
							<input type="checkbox" id="bodybgc5" name="bodybg5" <?php echo $aryBodyBg[5] == 'True' ? 'checked="checked"' : ''; ?> value="True"/>
							<label for="bodybgc5">使用背景图</label>
						</div>
						<div id="bodybgmain" <?php echo $aryBodyBg[5] == "" ? 'style="display:none"' : ""; ?> >
							<div class="imageshow">
								<input type="hidden" id="url_updatapic1" name="bodybg1"  value="<?php echo $aryBodyBg[1] ?>" />
								<img src="<?php echo $aryBodyBg[1] ?><?php echo $r ?>" width="190" height="120" border="0" alt="" id="pic_updatapic1">
								<input type="button"  id="updatapic1" class="button" value="更换图片"/>
							</div>
							<div id="bodybgs">
								背景设定：
								<input type="checkbox" id="bodybg2r" name="bodybg2[]" <?php echo(stripos($aryBodyBg[2], "repeat") > -1 ? 'checked="checked"' : ""); ?> value="repeat"/>
								<label for="bodybg2r">平铺</label>
								<input type="checkbox" id="bodybg2f" name="bodybg2[]" <?php echo(stripos($aryBodyBg[2], "fixed") > -1 ? 'checked="checked"' : ""); ?> value="fixed"/>
								<label for="bodybg2f">固定</label>
								<input type="checkbox" id="bodybg2g" name="bodybg2[]" <?php echo(stripos($aryBodyBg[2], "cover") > -1 ? 'checked="checked"' : ""); ?> value="cover"/>
								<label for="bodybg2g">拉伸(不支持IE678)</label>
							</div>
							<div id="bgpx">
								对齐方式：
								<?php	for($i = 1;$i <= 3;$i++){	 ?>
								
								<input type="radio" id="bgpx<?php echo $i ?>" name="bodybg3" value="<?php echo $i ?>" <?php echo $i == (int) $aryBodyBg[3] ? 'checked="checked"' : ""; ?> />
								<label for="bgpx<?php echo $i ?>">居<?php echo $a[$i] ?></label>
								
								<?php	}   ?></div>
							<input type="hidden" id="bgpy" name="bodybg4"  value="<?php echo $aryBodyBg[4] ?>" /></div>
					</td>
				</tr>
			</table>

			<table width="100%" border="1" width="100%" class="tableBorder">
				<tr>
					<th scope="col" height="32" width="150px">颜色配置</th>
					<th scope="col">
						<div  style="float:left;margin: 0.25em">预设方案：</div>
						<div id="loadconfig"></div>
					</th>
				</tr>
				<tr>
					<td>主色（深色）</td>
					<td>
						<input id="colorP1" name="color[]"  value="<?php echo $aryColor[0] ?>" /></td>
				</tr>
				<tr>
					<td>次色（浅色）</td>
					<td>
						<input  id="colorP2" name="color[]"  value="<?php echo $aryColor[1] ?>" /></td>
				</tr>
				<tr>
					<td>字体颜色</td>
					<td>
						<input  id="colorP3" name="color[]"  value="<?php echo $aryColor[2] ?>" /></td>
				</tr>
				<tr>
					<td>链接颜色</td>
					<td>
						<input  id="colorP4" name="color[]"  value="<?php echo $aryColor[3] ?>" /></td>
				</tr>
				<tr>
					<td>文章背景色</td>
					<td>
						<input  id="colorP5" name="color[]"  value="<?php echo $aryColor[4] ?>" /></td>
				</tr>
			</table>
			<input name="ok" type="submit" class="button" value="保存配置"/>
		</form>
		<textarea name="ueimg" id="ueimg" style="display:none"></textarea>
	</div>
</div>
<script type="text/javascript">ActiveTopMenu("topmenu_metro");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>