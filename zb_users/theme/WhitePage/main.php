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
?>

<link href="source/evol.colorpicker.css" rel="stylesheet" /> 
<script src="source/evol.colorpicker.min.js" type="text/javascript"></script>
<script src="source/custom.js" type="text/javascript"></script>
<style>
table input{padding: 0;margin:0.25em 0;}
table input#hdbgph{padding: 2px 5px;}
table .button{padding: 2px 12px 5px 12px; margin: 0.25em 0;}
.tc{border: solid 2px #E1E1E1;width: 50px;height: 23px;float: left;margin: 0.25em;cursor: pointer}
.tc:hover,.active{border: 2px solid #2694E8;}
.upinfo{position: relative;left: 3px;top: -19px;color: white;background: #5EAAE4;width: 190px;height: 23px;display: inline-block;text-align: center;opacity: 0.8;filter: alpha(opacity=80);}
.imageshow{margin:0.25em 0;}.imageshow img{margin:0 10px;margin-bottom:-10px;}
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
						<label><input type="radio" id="pt1" name="pagetype" value="1" <?php echo($zbp->Config('WhitePage')->custom_pagetype==1)?'checked="checked"':'';?>/>默认：图片阴影型（直角）</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pt2" name="pagetype" value="2" <?php echo($zbp->Config('WhitePage')->custom_pagetype==2)?'checked="checked"':'';?>/>CSS3阴影（直角）</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pt3" name="pagetype" value="3" <?php echo($zbp->Config('WhitePage')->custom_pagetype==3)?'checked="checked"':'';?>/>CSS3阴影（圆角）</label>
						&nbsp;&nbsp;
						<label><input type="radio" id="pt4" name="pagetype" value="4" <?php echo($zbp->Config('WhitePage')->custom_pagetype==4)?'checked="checked"':'';?>/>平面无阴影（直角）</label>
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
					<div id="loadconfig"></div>
					</th>
				</tr>
				<tr>
					<td>背景色</td>
					<td>
						<label><input type="radio" id="bg0"  name="bgcolor" value="#FFFFFF" /><font color="#FFFFFF" style="background-color:#ccc;">#FFFFFF</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg1"  name="bgcolor" value="#FFA07A" /><font color="#FFA07A">#FFA07A</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg2"  name="bgcolor" value="#8FBC8B" /><font color="#8FBC8B">#8FBC8B</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg3"  name="bgcolor" value="#A9A9A9" /><font color="#A9A9A9">#A9A9A9</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg4"  name="bgcolor" value="#6699FF" /><font color="#6699FF">#6699FF</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg5"  name="bgcolor" value="#EE82EE" /><font color="#EE82EE">#EE82EE</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg6"  name="bgcolor" value="#9370DB" /><font color="#9370DB">#9370DB</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg7"  name="bgcolor" value="#FF7F50" /><font color="#FF7F50">#FF7F50</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg8"  name="bgcolor" value="#DEB887" /><font color="#DEB887">#DEB887</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg9"  name="bgcolor" value="#FFE4C4" /><font color="#FFE4C4">#FFE4C4</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg10" name="bgcolor" value="#7FFFD4" /><font color="#7FFFD4">#7FFFD4</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg11" name="bgcolor" value="#FFC0CB" /><font color="#FFC0CB">#FFC0CB</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg12" name="bgcolor" value="#BDB76B" /><font color="#BDB76B">#BDB76B</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg13" name="bgcolor" value="#D3D3D3" /><font color="#D3D3D3">#D3D3D3</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg14" name="bgcolor" value="#EEE8AA" /><font color="#EEE8AA">#EEE8AA</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg15" name="bgcolor" value="#98FB98" /><font color="#98FB98">#98FB98</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg16" name="bgcolor" value="#FFB6C1" /><font color="#FFB6C1">#FFB6C1</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg17" name="bgcolor" value="#EEEEEE" /><font color="#EEEEEE">#EEEEEE</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg18" name="bgcolor" value="#ADD8E6" /><font color="#ADD8E6">#ADD8E6</font></label>&nbsp;&nbsp;
						<label><input type="radio" id="bg19" name="bgcolor" value="#E9967A" /><font color="#E9967A">#E9967A</font></label>&nbsp;&nbsp;
						
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
</script> 
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>