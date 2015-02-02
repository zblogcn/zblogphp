<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('WhitePage')) {$zbp->ShowError(48);die();}
$blogtitle='WhitePage主题配置';

if(count($_POST)>0){


	if(GetVars('pagetype','POST'))$zbp->Config('WhitePage')->custom_pagetype=GetVars('pagetype','POST');
	if(GetVars('pagewidth','POST'))$zbp->Config('WhitePage')->custom_pagewidth=GetVars('pagewidth','POST');
	if(GetVars('headtitle','POST'))$zbp->Config('WhitePage')->custom_headtitle=GetVars('headtitle','POST');
	if(GetVars('bgcolor','POST'))$zbp->Config('WhitePage')->custom_bgcolor=GetVars('bgcolor','POST');
	if(GetVars('text_indent','POST')!==false)$zbp->Config('WhitePage')->text_indent=GetVars('text_indent','POST');	
	$zbp->SaveConfig('WhitePage');

	$zbp->SetHint('good');
	Redirect($_SERVER["HTTP_REFERER"]);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

$percolors=array("ffffff","ffa07a","8fbc8b","a9a9a9","6699ff","ee82ee","9370db","ff7f50","deb887","ffe4c4","7fffd4","ffc0cb","bdb76b","d3d3d3","eee8aa","98fb98","ffb6c1","eeeeee","add8e6","e9967a");
?>

<link href="source/colpick.css" rel="stylesheet" /> 
<script src="source/colpick.js" type="text/javascript"></script>
<style>
	input.colorpicker { 
		border-right-width: 32px; 
		width: 50px; 
		height: 23px;
		cursor: pointer; 
		font-family: 'Lucida Console', Monaco, monospace;
	}
	.color-box {
		float:left;
		width:30px;
		height:30px;
		margin:5px;
		border: 1px solid white;
		cursor: pointer; 
	}
	.color-box-picker{ 		
		margin: 8px 10px;
		border: 1px solid #aaa; width: 90px;
	}
</style>
<!--#include file="..\..\..\..\zb_system\admin\admin_top.asp"-->
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle;?></div>
	<div class="SubMenu"></div>
	<div id="divMain2"> 
		<form action="?save" method="post">
			<table width="100%" border="1" width="100%" class="tableBorder">
				<tr>
					<th scope="col"  height="32" width="20%">整体设置</th>	
					<th></th>
				</tr>
				<tr>
					<td scope="col"  height="52">页面类型</td>					
					<td >
						<label><input type="radio" id="pt1" name="pagetype" value="1" <?php echo($zbp->Config('WhitePage')->custom_pagetype==1)?'checked="checked"':'';?>/>默认：图片阴影型(直角)</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pt2" name="pagetype" value="2" <?php echo($zbp->Config('WhitePage')->custom_pagetype==2)?'checked="checked"':'';?>/>CSS3阴影(直角)</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pt3" name="pagetype" value="3" <?php echo($zbp->Config('WhitePage')->custom_pagetype==3)?'checked="checked"':'';?>/>CSS3阴影(圆角)</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pt4" name="pagetype" value="4" <?php echo($zbp->Config('WhitePage')->custom_pagetype==4)?'checked="checked"':'';?>/>平面无阴影(直角)</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pt5" name="pagetype" value="5" <?php echo($zbp->Config('WhitePage')->custom_pagetype==5)?'checked="checked"':'';?>/>平面(CSS3圆角)</label>
						&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td scope="col"  height="52">页面宽度</td>					
					<td >
						<label><input type="radio" id="pw1" name="pagewidth" value="1200" <?php echo($zbp->Config('WhitePage')->custom_pagewidth==1200)?'checked="checked"':'';?>/>1200px</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pw2" name="pagewidth" value="1000" <?php echo($zbp->Config('WhitePage')->custom_pagewidth==1000)?'checked="checked"':'';?>/>1000px</label>
					</td>
				</tr>
				<tr>
					<td scope="col"  height="52">标题对齐</td>					
					<td >
						<label><input type="radio" id="ht1" name="headtitle" value="left" <?php echo($zbp->Config('WhitePage')->custom_headtitle=='left')?'checked="checked"':'';?>/>标题居左</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="ht2" name="headtitle" value="center" <?php echo($zbp->Config('WhitePage')->custom_headtitle=='center')?'checked="checked"':'';?>/>标题居中</label>
					</td>
				</tr>
				<tr>
					<td scope="col"  height="52">正文缩进</td>					
					<td >
						<label><input type="radio" id="text_indent1" name="text_indent" value="0" <?php echo($zbp->Config('WhitePage')->text_indent==0)?'checked="checked"':'';?>/>无</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="text_indent2" name="text_indent" value="2" <?php echo($zbp->Config('WhitePage')->text_indent==2)?'checked="checked"':'';?>/>标准</label>
					</td>
				</tr>
			</table>

			<table width="100%" border="1" width="100%" class="tableBorder">
				<tr>
					<th scope="col" height="32" width="20%">颜色配置</th>
					<th scope="col">				
					<div  style="float:left;margin: 0.25em"></div>
					<div id="loadconfig">				
					<?php
						foreach ($percolors as $value) {
							  echo "<div class='color-box' data-color='" . $value . "' style='background-color:#" . $value . "'></div>";
						}
					?>
					</div>
					</th>
				</tr>

				<tr>
					<td>背景色</td>
					<td>
						<div class="color-box-picker">
							<input type="text" id="bgpicker" class="colorpicker" name="bgcolor" value="<?php echo $zbp->Config('WhitePage')->custom_bgcolor;?>" style="border-color:#<?php echo $zbp->Config('WhitePage')->custom_bgcolor;?>" />
						</div>
					</td>
				</tr>
			</table>
			<input name="ok" type="submit" class="button" value="保存配置"/>
		</form>
	</div>
</div>
<!--#include file="..\..\..\..\zb_system\admin\admin_footer.asp"-->
<script type="text/javascript">
ActiveTopMenu("topmenu_WhitePage");
AddHeaderIcon("<?php echo $zbp->host?>zb_system/image/common/themes_32.png");
$('#bgpicker').colpick({
	layout:'hex',
	submit:0,
	onChange:function(hsb,hex,rgb,el,bySetColor) {
		$(el).css('border-color','#'+hex);
		if(!bySetColor) $(el).val(hex);
	}
}).keyup(function(){
	$(this).colpickSetColor(this.value);
});

$('.color-box').click(function() {
	    var c = $(this).data('color');
		$('#bgpicker').colpickSetColor(c);
		$('#bgpicker').val(c );
		$('#bgpicker').css('border-color', '#'+c); 
});
</script> 

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>