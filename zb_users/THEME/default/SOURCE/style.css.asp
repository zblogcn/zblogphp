<%@ CODEPAGE=65001 %>
<% Option Explicit %>
<% Response.Charset="UTF-8" %>
<% Response.Expires=0 %>
<% Response.ContentType = "text/css" %>
<!-- #include file="../../../c_option.asp" -->
<!-- #include file="../../../../zb_system/function/c_function.asp" -->
<%
Response.Write("@import url("""& GetCurrentHost & "zb_users/theme" & "/" & ZC_BLOG_THEME & "/style/" & ZC_BLOG_CSS & ".css" & """);") 
%>