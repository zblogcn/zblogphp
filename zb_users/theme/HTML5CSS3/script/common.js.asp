<%@ CODEPAGE=65001 %>
<%
'///////////////////////////////////////////////////////////////////////////////
'// 作　　者:    朱煊(zx.asd) | 吉光片羽
'// 版权所有:    RainbowSoft Studio | 吉光片羽
'// 技术支持:    www.jgpy.cn
'// 程序名称:    Z-Blog通用前端脚本
'// 程序版本:    1.0
'// 单元名称:    common.js.asp
'// 开始时间:    2012-11-23
'// 最后修改:    2013-01-25
'// 备　　注:    如需添加自定义脚本，请在主题SCRIPT文件夹中新建custom.js，程序会自动合并输出，需要页面加载完毕后（如JQuery的document.ready）执行的脚本可以使用blog.js.int函数导入。若无特殊需求，或除非你完全理解本文件内容，否则请勿轻易修改。
'///////////////////////////////////////////////////////////////////////////////
%>
<% Option Explicit %>
<% On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<% Response.Buffer=True %>
<% Response.ContentType="application/x-javascript" %>
<!-- #include file="../../../c_option.asp" -->
<!-- #include file="../../../../zb_system/function/c_function.asp" -->
<!-- #include file="../../../../zb_system/function/c_system_lib.asp" -->
<!-- #include file="../../../../zb_system/function/c_system_base.asp" -->
<!-- #include file="../../../../zb_system/function/c_system_plugin.asp" -->
<!-- #include file="../../../plugin/p_config.asp" -->
<!-- #include file="language.asp" -->
<%

Response.Clear

Dim strjQuery
strjQuery=LoadFromFile(BlogPath&"/zb_users/theme/"&ZC_BLOG_THEME&"/script/jquery.js","utf-8")
Response.Write strjQuery

%>

var blog={
    host:bloghost="<%=BlogHost%>",
    url:document.URL.toLowerCase().substr(<%=Len(BlogHost)%>),
    nav:"",
    cookies:{
        path:cookiespath="<%=CookiesPath()%>",
        set:function(sName, sValue,iExpireDays) {
            if (iExpireDays){
                var dExpire = new Date();
                dExpire.setTime(dExpire.getTime()+parseInt(iExpireDays*24*60*60*1000));
                document.cookie = sName + "=" + escape(sValue) + "; expires=" + dExpire.toGMTString()+ "; path="+cookiespath;
            }
            else{
                document.cookie = sName + "=" + escape(sValue)+ "; path="+cookiespath;
            }
        },
        get:function(sName){
            var arr = document.cookie.match(new RegExp("(^| )"+sName+"=([^;]*)(;|$)"));
            if(arr !=null){return unescape(arr[2])};
            return null;
        }
    },
    theme:bloghost+"zb_users/theme/<%=ZC_BLOG_THEME%>/",
    include:bloghost+"zb_users/include/",
    sys:bloghost+"zb_system/",
    avatar:bloghost+"zb_users/avatar/",
    msg:{
        _009:"<%=ZC_MSG009%>",
        _020:"<%=ZC_MSG020%>",
        _021:"<%=ZC_MSG021%>",
        _057:"<%=ZC_MSG057%>",
        _085:"<%=ZC_MSG085%>",
        _087:"<%=ZC_MSG087%>",
        _168:"<%=ZC_MSG168%>",
        _248:"<%=ZC_MSG248%>",
        noName:unescape("<%=blog.msg.noName%>"),
        notMe:unescape("<%=blog.msg.notMe%>"),
        noHtml5:unescape("<%=blog.msg.noHtml5%>"),
        ltIE9:unescape("<%=blog.msg.ltIE9%>"),
        cmt:{
            name:"<%=ZC_MSG033%>",
            email:"<%=ZC_MSG034%>",
            msg:"<%=ZC_MSG035%>",
            reply:unescape("<%=blog.msg.cmt.reply%>"),
            max:intMaxLen=<%=ZC_CONTENT_MAX%>,
            page:unescape("<%=blog.msg.cmt.page%>"),
            record:unescape("<%=blog.msg.cmt.record%>"),
            submiting:unescape("<%=blog.msg.cmt.submiting%>"),
            success:unescape("<%=blog.msg.cmt.success%>")
        },
        valid:{
        	beforeEnter:unescape("<%=blog.msg.valid.beforeEnter%>"),
            change:unescape("<%=blog.msg.valid.change%>")
        }
    },
    js:{
        include:"",
        view:"",
        count:"",
        int:function(){},
        ajaxCmt:function(){},
        sidebar:function(){},
        getCmt:function(){},
        navTab:function(nav){
            var crumbUrl=blog.url.split("/");
            nav.find("a").each(function(){
                var crumbNav=this.href.substr(<%=Len(BlogHost)%>).split("/");
                if(crumbUrl[0]==crumbNav[0])$(this).wrap("<b class='curpage'/>");
            });
            !nav.find("b.curpage")[0]&&nav.find("a:first").wrap("<b class='curpage'/>");
        }
    },
    replyID:0,
    user:function(s){
        var v={
            <%
			If (Request.Cookies("username")<>"") Then
				Call System_Initialize
				BlogUser.Verify()
			%>
            level_:<%=BlogUser.Level%>,
            level:"<%=ZVA_User_Level_Name(BlogUser.Level)%>",
            alias:"<%=BlogUser.Alias%>",
            id:<%=BlogUser.Id%>,
            name:"<%=BlogUser.FirstName%>",
            email:"<%=BlogUser.Email%>",
            homepage:"<%=BlogUser.HomePage%>",
            avatar:"<%=BlogUser.Avatar%>",
            visited:"<%=BlogUser.LastVisitTime%>"
            <%Else%>
            name:blog.cookies.get("inpName"),
            email:blog.cookies.get("inpEmail"),
            homepage:blog.cookies.get("inpHomePage"),
            avatar:blog.avatar+"0.png"
            <%End If%>
        };
        if(!v.name)v.name="<%=ZC_MSG018%>";
        return v[s];
    }
};

document.createElement("view");
document.createElement("count");

function LoadFunction(name){
	blog.js.include+="mod_"+name+"="+name+","
}

function GetComments(logid,page){
    $("a[href^='#AjaxComment']").hide().first().after("<center>"+blog.msg.cmt.page+"</center>");
    var $cmt=blog.cmt;
	$.get(blog.sys+"cmd.asp?act=CommentGet&logid="+logid+"&page="+page, function(data){
		$cmt.begin.nextUntil($cmt.end).remove()
        $cmt.begin.after(data)
        $cmt.list[0]&&$("body,html").animate({scrollTop:$cmt.list.offset().top})
        blog.js.getCmt()
	});
    return false;
}

function Reply(id){
	blog.replyID=id;
}

blog.ready=function(){

<%If ZC_SYNTAXHIGHLIGHTER_ENABLE Then%>
    var hiLiteUrl=blog.sys+"admin/ueditor/third-party/SyntaxHighlighter/";
    $("head").append("<link rel='stylesheet' type='text/css' href='"+hiLiteUrl+"shCoreDefault.pack.css'/>");
    $.getScript(hiLiteUrl+"shCore.pack.js",function(){
        //为了在编辑器之外能展示高亮代码
        SyntaxHighlighter.highlight();
        //调整左右对齐
        for(var i=0,di;di=SyntaxHighlighter.highlightContainers[i++];){
            var tds = di.getElementsByTagName('td');
            for(var j=0,li,ri;li=tds[0].childNodes[j];j++){
                ri = tds[1].firstChild.childNodes[j];
                ri.style.height = li.style.height = ri.offsetHeight + 'px';
            }
        }
    });
<%End If%>
    
    blog.form={
        cmt:$("form[action*='act=cmt']").submit(blog.js.ajaxCmt),
        id:$("input[name='inpId']"),
        location:$("input[name='inpLocation']").val(window.location+"#AjaxCommentBegin"),
        name:$("input[name='inpName']").val(function(){
            $(this).attr("readonly",!blog.user("id")?false:true)
            return blog.user("name")
        }),
        email:$("input[name='inpEmail']").val(blog.user("email")),
        homepage:$("input[name='inpHomePage']").val(blog.user("homepage")),
        verify:$("input[name='inpVerify']").val(""),
        validcode:$("img[src*='c_validcode.asp']").css("cursor","pointer").attr("title","点我更换验证码").click(function(){
            this.src=blog.sys+"function/c_validcode.asp?name=commentvalid"+"&amp;random="+Math.random();
        }),
        msg:$("input[name='inpArticle']"),
        txt:$("textarea[name='txaArticle']").blur(function(){
        	$("input[name='inpArticle']").val(this.value);
        }),
        txtmsg:$("textarea[name='inpArticle']"),
        search:$("form[action*='act=search']"),
        keyword:$("input[name='edtSearch']")
    };
    
    blog.cmt={
        post:$("#postcmt"),
        list:$("#comment"),
        box:$("blockquote[id^='cmt']"),
        begin:$("#AjaxCommentBegin"),
        end:$("#AjaxCommentEnd"),
        reply:function(id,bln){
            return bln===true?$("<ins id='AjaxReply"+id+"' onclick='Reply("+id+")'>"+blog.msg.cmt.reply+"</ins>"):$("#AjaxCommentEnd"+id);
        }
    };

    $("count").attr("id",function(){
    	blog.js.count+=this.id+"="+this.id+",";
    });
    $("view").each(function(){
        blog.js.view+=this.id+"="+this.id+",";
    });
    
    $.getScript(blog.sys+"function/c_html_js.asp?act=batch&view="+escape(blog.js.view)+"&inculde="+escape(blog.js.include)+"&count="+escape(blog.js.count)+"&r="+Math.random(),function(){
        <%
		Dim arrSidebar
		arrSidebar=ZC_SIDEBAR_ORDER&":"&ZC_SIDEBAR_ORDER2&":"&ZC_SIDEBAR_ORDER3&":"&ZC_SIDEBAR_ORDER4&":"&ZC_SIDEBAR_ORDER5
		If (Request.Cookies("username")<>"" And Instr(arrSidebar,"controlpanel")>0) Then
		%>
        $(".cp-vrs").html(function(){
        	this.className="cp-addpost";
            return "<a target='_blank' href='"+blog.sys+"cmd.asp?act=ArticleEdt&webedit=ueditor'>["+blog.msg._168+"]</a>";
        });
        $(".cp-login").find("a").attr("target","_blank").text("["+blog.msg._248+"]").end().before("<span class='cp-hello'><%=Replace(ZC_MSG023,"%s","")%><b>"+blog.user("name")+"</b> (<a href='"+blog.sys+"cmd.asp?act=vrs' title='"+blog.msg._021+"' class='cp-vrs'>"+blog.user("level")+"</a>) <a class='cp-logout' href='"+blog.sys+"cmd.asp?act=logout' title='"+blog.msg._020+"'><small>[退出]</small></a></span><br/>");
        $(".cp-logout").click(function(){
        	if(!confirm("确认退出？"))return false;
        });
		<%End If%>
        blog.js.sidebar()
    });
    blog.js.int();
    if(blog.url=="tags.asp")blog.cmt.list.remove();
    blog.nav[0]&&blog.js.navTab(blog.nav);
};

if(typeof jQuery=="undefined"){
	alert("请在主题的 script 文件夹内植入一枚靠谱的 jquery.js 文件\r\n以保证您的博客能够欢快的正常运行！\r\n如果你懂JS，还可以在同一位置再植入一枚 custom.js 来个性化您的博客运行！");
}else{
	(function($){
		$(blog.ready)
    })(jQuery);
}

<%
Dim strJS
strJS=LoadFromFile(BlogPath&"/zb_users/theme/"&ZC_BLOG_THEME&"/script/custom.js","utf-8")
Response.Write strJS
%>

<%=Response_Plugin_Html_Js_Add%>