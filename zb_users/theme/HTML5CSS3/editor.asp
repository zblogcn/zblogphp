<%@LANGUAGE="VBSCRIPT" CODEPAGE="65001"%>
<%
'************************************
' Powered by ThemePluginEditor 1.1
' zsx http://www.zsxsoft.com
'************************************
%>
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
<!-- #include file="..\source\language.asp" -->

<%

Call System_Initialize()
'检查非法链接
Call CheckReference("")
'检查权限
If BlogUser.Level>1 Then Call ShowError(6)

BlogTitle="主题配置"
%>
<!--#include file="..\..\..\..\zb_system\admin\admin_header.asp"-->
<style type="text/css">
input[type="text"] {
	width: 80%
}
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
              <thead>
                <tr>
                  <th scope="col" height="32" width="400px">配置项</th>
                  <th scope="col">配置内容</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td scope="col" height="32">评论名为空的文字</td>
                  <td scope="col"><input name="msg_noName" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.noName),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">评论用户名非我的文字</td>
                  <td scope="col"><input name="msg_notMe" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.notMe),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">评论回复文字</td>
                  <td scope="col"><input name="msg.cmt.reply" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.cmt.reply),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">评论成功后自动保存cookies提示文字</td>
                  <td scope="col"><input name="msg.cmt.record" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.cmt.record),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">评论提交中的提示文字</td>
                  <td scope="col"><input name="msg.cmt.submiting" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.cmt.submiting),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">评论发表成功的提示文字</td>
                  <td scope="col"><input name="msg.cmt.success" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.cmt.success),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">评论翻页中提示</td>
                  <td scope="col"><input name="msg.cmt.page" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.cmt.page),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">不支持HTML5浏览器的提示文字</td>
                  <td scope="col"><input name="msg_noHtml5" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.noHtml5),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">针对IE的不支持HTML5浏览器的提示文字</td>
                  <td scope="col"><input name="msg_ltIE9" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.ltIE9),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">请输入验证码提示</td>
                  <td scope="col"><input name="msg_valid_beforeEnter" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.valid.beforeEnter),"[html-format]")%>" /></td>
                </tr>
                <tr>
                  <td scope="col" height="32">更换验证码提示</td>
                  <td scope="col"><input name="msg_valid_change" type="text" value="<%=TransferHTML(vbsunescape(blog.msg.valid.change),"[html-format]")%>" /></td>
                </tr>
              </tbody>
              <tfoot>
              </tfoot>
              </table>
              <input name="ok" type="submit" class="button" value="提交"/>
            </form>
            <script type="text/javascript">ActiveTopMenu("aHTML5CSS3Manage")</tr><tr></script> 
          </div>
        </div>
        <!--#include file="..\..\..\..\zb_system\admin\admin_footer.asp"-->

<%Call System_Terminate()%>
