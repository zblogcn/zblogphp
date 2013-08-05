<%@LANGUAGE="VBSCRIPT" CODEPAGE="65001"%>
<% Option Explicit %>
<% 'On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<!-- #include file="..\..\..\c_option.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_function.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_lib.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_base.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_event.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_manage.asp" -->
<!-- #include file="..\..\..\..\zb_system\function\c_system_plugin.asp" -->
<!-- #include file="..\..\..\plugin\p_config.asp" -->
<%
Call System_Initialize()
'检查非法链接
Call CheckReference("")
'检查权限
If BlogUser.Level>1 Then Call ShowError(6)

BlogTitle="Metro主题配置"
Dim strlayout
Dim strBodyBg,aryBodyBg
Dim strHdBg,aryHdBg
Dim strColor,aryColor
Dim c
Set c = New TConfig
c.Load("metro")
If c.Exists("vesion")=true Then
	strlayout=c.read("custom_layout")
	strBodyBg=c.read("custom_bodybg")
	strHdBg=c.read("custom_hdbg")
	strColor=c.read("custom_color")
	aryBodyBg=Split(strBodyBg,"|")
	aryHdBg=Split(strHdBg,"|")
	aryColor=Split(strColor,"|")
End If 

Dim i,a,r
a=Array("","左","中","右")
Randomize 
r="?"&Rnd()
%>
<!--#include file="..\..\..\..\zb_system\admin\admin_header.asp"-->
<script type="text/javascript" charset="utf-8" src="../../../../zb_system/admin/ueditor/ueditor.config.asp"></script>
<script type="text/javascript" charset="utf-8" src="../../../../zb_system/admin/ueditor/ueditor.all.min.js"></script>
<link href="evol.colorpicker.css" rel="stylesheet" /> 
<script src="evol.colorpicker.min.js" type="text/javascript"></script>
<script src="custom.js" type="text/javascript"></script>
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
	<div id="ShowBlogHint">
	<%Call GetBlogHint()%>
	</div>
	<div class="divHeader"><%=BlogTitle%></div>
	<div class="SubMenu"></div>
	<div id="divMain2"> 
		<form action="save.asp" method="post">
			<table width="100%" border="1" width="100%" class="tableBorder">
				<tr>
					<th scope="col"  height="32" >整体设置</th>	
					<th></th>
				</tr>
				<tr>
					<td scope="col"  height="52">外观模式</td>					
					<td >
						<div id="layoutset">
							<input type="radio" id="layoutl" name="layout" value="l" <%=IIf(strlayout="l","checked=""checked""","")%> /><label for="layoutl">侧栏居左</label>
							<input type="radio" id="layoutr" name="layout" value="r" <%=IIf(strlayout="r","checked=""checked""","")%> /><label for="layoutr">侧栏居右</label>
						</div>
					</td>
				</tr>
				<tr>
					<td>顶部背景</td>
					<td>
						<div >顶部高度：<input id="hdbgph" type="text" name="hdbg5"  size="3"  value="<%=aryHdBg(5)%>" />（单位：px）</div>
						<div id="hdbgcolor" >
							<input type="checkbox" id="hdbgc0" name="hdbg0" <%=IIf(aryHdBg(0)="transparent","checked=""checked""","")%> value="transparent"/><label for="hdbgc0"> 背景透明（不透明情况下使用主色为背景色）</label>
						</div>
						<div >
							<input type="checkbox" id="hdbgc6" name="hdbg6" <%=IIf(aryHdBg(6)="True","checked=""checked""","")%> value="True"/> <label for="hdbgc6">使用背景图</label>
						</div>
						<div id="hdbgmain" <%=IIf(aryHdBg(6)="","style=""display:none""","")%>>
							<div class="imageshow">
								<input  type="hidden"  id="url_updatapic2" name="hdbg1"  value="<%=aryHdBg(1)%>" /> 
								<img src="<%=ZC_BLOG_HOST&aryHdBg(1)%><%=r%>" width="190" height="120" border="0" alt="" id="pic_updatapic2">
								<input type="button"  id="updatapic2" class="button" value="更换图片" />
							</div>
							<div id="hdbgs">背景设定：
							<input type="checkbox" id="hdbg2r" name="hdbg2" <%=IIf(InStr(aryHdBg(2),"repeat")>0,"checked=""checked""","")%> value="repeat"/>
							<label for="hdbg2r">平铺</label>
							<input type="checkbox" id="hdbg2f" name="hdbg2" <%=IIf(InStr(aryHdBg(2),"fixed")>0,"checked=""checked""","")%> value="fixed"/>
							<label for="hdbg2f">固定</label>
							</div> 
							<div id="hdbgpx"> 对齐方式： 			  
								<%	For i=1 To 3	%>
										<input type="radio" id="hdbgpx<%=i%>" name="hdbg3" value="<%=i%>" <%=IIf(i=int(aryHdBg(3)),"checked=""checked""","")%> /><label for="hdbgpx<%=i%>">居<%=a(i)%></label>
								<%	Next 	%>
							</div> 
						<input id="hdbgpy" type="hidden" name="hdbg4"  value="<%=aryHdBg(4)%>" />
						</div>
					</td>
				</tr>
				<tr>
					<td width="150px">页面背景</td>
					<td>
						<div id="bgcolor">
							背景颜色：<input id="bodybgc0" name="bodybg0"  value="<%=aryBodyBg(0)%>" /> 
						</div>
						<div >
							<input type="checkbox" id="bodybgc5" name="bodybg5" <%=IIf(aryBodyBg(5)="True","checked=""checked""","")%> value="True"/> <label for="bodybgc5">使用背景图</label>
						</div>
						<div id="bodybgmain" <%=IIf(aryBodyBg(5)="","style=""display:none""","")%>>
							<div class="imageshow">
									<input type="hidden" id="url_updatapic1" name="bodybg1"  value="<%=aryBodyBg(1)%>" /> 
									<img src=<%=ZC_BLOG_HOST&aryBodyBg(1)%><%=r%>" width="190" height="120" border="0" alt="" id="pic_updatapic1">
									<input type="button"  id="updatapic1" class="button" value="更换图片"/>
							</div>
							<div id="bodybgs">背景设定：
							<input type="checkbox" id="bodybg2r" name="bodybg2" <%=IIf(InStr(aryBodyBg(2),"repeat")>0,"checked=""checked""","")%> value="repeat"/>
							<label for="bodybg2r">平铺</label>  
							<input type="checkbox" id="bodybg2f" name="bodybg2" <%=IIf(InStr(aryBodyBg(2),"fixed")>0,"checked=""checked""","")%> value="fixed"/>
							<label for="bodybg2f">固定</label>
							</div> 
							<div id="bgpx"> 对齐方式： 			  
							<%	For i=1 To 3	%>
							<input type="radio" id="bgpx<%=i%>" name="bodybg3" value="<%=i%>" <%=IIf(i=int(aryBodyBg(3)),"checked=""checked""","")%> /><label for="bgpx<%=i%>">居<%=a(i)%></label>
							<%	Next  %>
							</div> 
							<input type="hidden" id="bgpy" name="bodybg4"  value="<%=aryBodyBg(4)%>" />
						</div>
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
					<td><input id="colorP1" name="color"  value=<%=aryColor(0)%> /></td>
				</tr>
				<tr>
					<td>次色（浅色）</td>
					<td><input  id="colorP2" name="color"  value=<%=aryColor(1)%> /></td>
				</tr>
				<tr>
					<td>字体颜色</td>
					<td><input  id="colorP3" name="color"  value=<%=aryColor(2)%> /></td>
				</tr>
				<tr>
					<td>链接颜色</td>
					<td><input  id="colorP4" name="color"  value=<%=aryColor(3)%> /></td>
				</tr>
				<tr>
					<td>文章背景色</td>
					<td><input  id="colorP5" name="color"  value=<%=aryColor(4)%> /></td>
				</tr>
			</table>
			<input name="ok" type="submit" class="button" value="保存配置"/>
		</form>
		<textarea name="ueimg" id="ueimg" style="display:none"></textarea>
	</div>
</div>
<!--#include file="..\..\..\..\zb_system\admin\admin_footer.asp"-->
<%
	Dim strUPLOADDIR
	strUPLOADDIR = Replace(ZC_UPLOAD_DIRECTORY&"/"&Year(GetTime(Now()))&"/"&Month(GetTime(Now())),"\","/")&"/"
%>
<script type="text/javascript">
var ZC_BLOG_HOST="<%=ZC_BLOG_HOST%>";
var imagePath=ZC_BLOG_HOST+"<%=strUPLOADDIR%>";
ActiveTopMenu("ametroManage");
</script> 

<%Call System_Terminate()%>