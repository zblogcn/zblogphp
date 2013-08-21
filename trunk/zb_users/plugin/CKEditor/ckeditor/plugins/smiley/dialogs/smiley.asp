<%@LANGUAGE="VBSCRIPT" CODEPAGE="65001"%>
<% Option Explicit %>
<% On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<% Response.Buffer=True %>
<% Response.ContentType="text/javascript" %>
<!-- #include file="../../../../../../c_option.asp" -->
<!-- #include file="../../../../../../../zb_system/function/c_function.asp" -->
<!-- #include file="../../../../../../../zb_system/function/c_system_base.asp" -->
<%	
	Dim fso,f(),f1,fb,fc
	Dim aryFileList,a,i,j,e,x,y,p

	'f=Split(ZC_EMOTICONS_FILENAME,"|")
	Set fso = CreateObject("Scripting.FileSystemObject")
	Set fb = fso.GetFolder(BlogPath & "zb_users\emotion" & "\")

	Set fc = fb.SubFolders
		i=0
	For Each f1 in fc	
		ReDim Preserve f(i)
		f(i)=f1.name
		i=i+1
	Next
%>
/*
 Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
*/
CKEDITOR.dialog.add("smiley",
function(f) {
	var e = f.config,
	a = f.lang.smiley,
//	h = e.smiley_images,
	h,emotion = [<%For x=0 To i-1%>"<%
		e=""
		aryFileList=LoadIncludeFiles("zb_users\emotion\"&f(x)) 
		If IsArray(aryFileList) Then
			j=UBound(aryFileList)
			For f1=1 to j
				If InStr("gif|jpg|png",Right(aryFileList(f1),3))>0 Then 
					e =e&Replace(Replace(Server.URLEncode(aryFileList(f1)),"+","%20"),"%2E",".")& IIf(f1=j,""," ")
					p=i
				End If 
			Next
			'e=Left(e,Len(e)-1)
		End If 
		Response.Write e
		%>"<%=IIf(x=i-1,"",",")%><%Next%>],
	g = e.smiley_columns || 8,
	i, k = function(j) {
		var c = j.data.getTarget(),
		b = c.getName();
		if ("a" == b) c = c.getChild(0);
		else if ("img" != b) return;
		var b = c.getAttribute("cke_src"),
		a = c.getAttribute("title"),
		c = f.document.createElement("img", {
			attributes: {
				src: b,
				"data-cke-saved-src": b,
				title: a,
				alt: a,
				width: c.$.width,
				height: c.$.height
			}
		});
		f.insertElement(c);
		i.hide();
		j.data.preventDefault()
	},
	n = CKEDITOR.tools.addFunction(function(a, c) {
		var a = new CKEDITOR.dom.event(a),
		c = new CKEDITOR.dom.element(c),
		b;
		b = a.getKeystroke();
		var d = "rtl" == f.lang.dir;
		switch (b) {
		case 38:
			if (b = c.getParent().getParent().getPrevious()) b = b.getChild([c.getParent().getIndex(), 0]),
			b.focus();
			a.preventDefault();
			break;
		case 40:
			if (b = c.getParent().getParent().getNext())(b = b.getChild([c.getParent().getIndex(), 0])) && b.focus();
			a.preventDefault();
			break;
		case 32:
			k({
				data:
				a
			});
			a.preventDefault();
			break;
		case d ? 37 : 39 : if (b = c.getParent().getNext()) b = b.getChild(0),
			b.focus(),
			a.preventDefault(!0);
			else if (b = c.getParent().getParent().getNext())(b = b.getChild([0, 0])) && b.focus(),
			a.preventDefault(!0);
			break;
		case d ? 39 : 37 : if (b = c.getParent().getPrevious()) b = b.getChild(0),
			b.focus(),
			a.preventDefault(!0);
			else if (b = c.getParent().getParent().getPrevious()) b = b.getLast().getChild(0),
			b.focus(),
			a.preventDefault(!0)
		}
	});
<%For x=0 To i-1%>
	var d<%=x%>,e<%=x%>,p<%=x%> ='<%=GetCurrentHost()%>'+'zb_users/emotion/'+'<%=f(x)%>/';
	d<%=x%> = CKEDITOR.tools.getNextId() + "_smiley_emtions_label";
	d<%=x%> = ['<div><span id="' + d<%=x%> + '" class="cke_voice_label">' + a.options + "</span>", '<table role="listbox" aria-labelledby="' + d<%=x%> + '" style="width:100%;height:100%;border-collapse:separate;" cellspacing="2" cellpadding="2"', CKEDITOR.env.ie && CKEDITOR.env.quirks ? ' style="position:absolute;"': "", "><tbody>"];
	h = emotion[<%=x%>].split(" ");
	l = h.length; 
	for (a = 0; a < l; a++) {
		0 === a % g && d<%=x%>.push('<tr role="presentation">');
		var m = "cke_smile_label_" + a + "_" + CKEDITOR.tools.getNextNumber();
		d<%=x%>.push('<td class="cke_dark_background cke_centered" style="vertical-align: middle;" role="presentation"><a href="javascript:void(0)" role="option"', ' aria-posinset="' + (a + 1) + '"', ' aria-setsize="' + l + '"', ' aria-labelledby="' + m + '"', ' class="cke_smile cke_hand" tabindex="-1" onkeydown="CKEDITOR.tools.callFunction( ', n, ', event, this );">', '<img class="cke_hand" title="', e.smiley_descriptions[a], '" cke_src="', p<%=x%> + h[a], '" alt="', e.smiley_descriptions[a], '"', ' src="', p<%=x%> + h[a], '"', CKEDITOR.env.ie ? " onload=\"this.setAttribute('width', 2); this.removeAttribute('width');\" ": "", '><span id="' + m + '" class="cke_voice_label">' + e.smiley_descriptions[a] + "</span></a>", "</td>");
		a % g == g - 1 && d<%=x%>.push("</tr>")
	}
	if (a < g - 1) {
		for (; a < g - 1; a++) d<%=x%>.push("<td></td>");
		d<%=x%>.push("</tr>")
	}
	d<%=x%>.push("</tbody></table></div>");
	e<%=x%> = {
		type: "html",
		id: "smileySelector",
		html: d<%=x%>.join(""),
		onLoad: function(a) {
			i = a.sender
		},
		focus: function() {
			var a = this;
			setTimeout(function() {
				a.getElement().getElementsByTag("a").getItem(0).focus()
			},
			0)
		},
		onClick: k,
		style: "width: 100%; border-collapse: separate;"
	};
<%Next%>

	return {
		title: f.lang.smiley.title,
		minWidth: 420,
		minHeight: 120,
		contents: [<%For x=0 To i-1%>{
			id: "tab<%=x%>",
			label: "<%=f(x)%>",
			title: "<%=f(x)%>",
			expand: !0,
			padding: 0,
			elements: [e<%=x%>]
		}<%=IIf(x=i-1,"",",")%><%Next%>],
		buttons: [CKEDITOR.dialog.cancelButton]
	}
});
<%Response.End(): Response.Clear %>