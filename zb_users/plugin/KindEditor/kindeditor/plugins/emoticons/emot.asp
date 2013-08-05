<%@LANGUAGE="VBSCRIPT" CODEPAGE="65001"%>
<% Option Explicit %>
<% On Error Resume Next %>
<% Response.Charset="UTF-8" %>
<% Response.Buffer=True %>
<%' Response.ContentType="text/json" %>
<!-- #include file="../../../../../c_option.asp" -->
<!-- #include file="../../../../../../zb_system/function/c_function.asp" -->
<!-- #include file="../../../../../../zb_system/function/c_system_base.asp" -->
/*******************************************************************************
* @author panderman <panderman@163.com>
* @site http://www.xunwee.com/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/
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
KindEditor.plugin('emoticons', function (K) {
    var self = this, name = 'emoticons',arrEmots=[],
		path = (self.emoticonsPath || self.pluginsPath + 'emoticons/images/'),
		emoticonspluginsPath = self.pluginsPath + 'emoticons/',
		allowPreview = self.allowPreviewEmoticons === undefined ? true : self.allowPreviewEmoticons;
    self.clickToolbar(name, function () {
        var elements = [],
			menu = self.createMenu({
			    name: name
			}),
			
            html = '<div class="ke-plugin-emoticons">\
                        <link rel="stylesheet" type="text/css" href="' + emoticonspluginsPath + 'emoticon.css" />\
                        <div id="tabPanel" class="neweditor-tab">\
                            <div id="tabMenu" class="neweditor-tab-h">\
                                <%For x=0 To i-1%><div><%=f(x)%></div><%Next%>\
                            </div>\
                            <div id="tabContent" class="neweditor-tab-b">\
                                <%For x=0 To i-1%><div id="tab<%=x%>"></div><%Next%>\
                            </div>\
                        </div>\
                    <div id="tabIconReview"><img id="faceReview" class="review" src="<%=GetCurrentHost()%>zb_system/IMAGE/ADMIN/none.gif" /></div></div>\
                    </div>';
        menu.div.append(K(html));
        function removeEvent() {
            K.each(elements, function () {
                this.unbind();
            });
        }

		
			
        /*******************************************表情方法开始******************************************************/
        function initImgBox(box, str, len) {
            if (box.length) return;
            var tmpStr = "", i = 1;
            for (; i <= len; i++) {
                tmpStr = str;
                if (i < 10) tmpStr = tmpStr + '0';
                tmpStr = tmpStr + i + '.gif';
                box.push(tmpStr);
            }
        }
        function $G(id) {
            return document.getElementById(id)
        }
        function InsertSmiley(url) {
            var obj = {
                src: editor.options.emotionLocalization ? editor.options.UEDITOR_HOME_URL + "dialogs/emotion/" + url : url
            };
            obj.data_ue_src = obj.src;
            editor.execCommand('insertimage', obj);
            dialog.popup.hide();
        }
        function over(td, srcPath, posFlag) {
            td.style.backgroundColor = "#ACCD3C";
            $G('faceReview').style.backgroundImage = "url('" + srcPath + "')";
            if (posFlag == 1) $G("tabIconReview").className = "show";
            $G("tabIconReview").style.display = 'block';
        }
        function bindCellEvent(td) {//表情绑定事件
            td.mouseover(function () {
                var obj = K(this);
                var sUrl = obj.attr("sUrl"),
                    posFlag = obj.attr("posflag");
                obj.css({
                    "background-color": "#ACCD3C"
                });
                $G('faceReview').style.backgroundImage = "url(" + sUrl + ")";
                if (posFlag == "1") $G("tabIconReview").className = "show";
                $G("tabIconReview").style.display = 'block';
            });
            td.mouseout(function () {
                var obj = K(this);
                obj.css({
                    "background-color": "#FFFFFF"
                });
                var tabIconRevew = $G("tabIconReview");
                tabIconRevew.className = "";
                tabIconRevew.style.display = 'none';
            });
            td.click(function (e) {
                self.insertHtml('<img src="' + K(this).attr("realUrl") + '" border="0" alt="" />').hideMenu().focus();
            });
        }
        var emotion = {};
        emotion.SmileyPath = path;
        emotion.SmileyBox = {<%For x=0 To i-1%> tab<%=x%>: [<%
		e=""
		aryFileList=LoadIncludeFiles("zb_users\emotion\"&f(x)) 
		If IsArray(aryFileList) Then
			j=UBound(aryFileList)
			For f1=1 to j
				If InStr(ZC_EMOTICONS_FILETYPE,Right(aryFileList(f1),3))>0 Then 
					e =e&"'"&Replace(Replace(Server.URLEncode(aryFileList(f1)),"+","%20"),"%2E",".")&"'" & IIf(f1=j,"",",")
					p=i
				End If 
			Next
			'e=Left(e,Len(e)-1)
		End If 
		Response.Write e
		%>]<%=IIf(x=i-1,"",",")%><%Next%>};
        emotion.SmileyInfor = emotion.SmileyBox;
        var faceBox = emotion.SmileyBox;
        var inforBox = emotion.SmileyInfor;
        var sBasePath = emotion.SmileyPath;
        initImgBox(faceBox['tab0'], '', 84);
        initImgBox(faceBox['tab1'], '', 40);
   
        //大对象
        FaceHandler = {
            imageFolders: {<%For x=0 To i-1%> tab<%=x%>: '<%=f(x)%>/'<%=IIf(x=i-1,"",",")%><%Next%>},
            imageWidth: {<%For x=0 To i-1%> tab<%=x%>: 30<%=IIf(x=i-1,"",",")%><%Next%>},
            imageCols: {<%For x=0 To i-1%> tab<%=x%>: 11<%=IIf(x=i-1,"",",")%><%Next%> },
            imageColWidth: {<%For x=0 To i-1%> tab<%=x%>: 3<%=IIf(x=i-1,"",",")%><%Next%>},
            imageCss: {<%For x=0 To i-1%> tab<%=x%>: '<%=f(x)%>'<%=IIf(x=i-1,"",",")%><%Next%>},
            imageCssOffset: {<%For x=0 To i-1%> tab<%=x%>: 30<%=IIf(x=i-1,"",",")%><%Next%>},
            tabExist: [<%For x=0 To i-1%>0<%=IIf(x=i-1,"",",")%><%Next%>]
        };
        function switchTab(index) {
            if (FaceHandler.tabExist[index] == 0) {
                FaceHandler.tabExist[index] = 1;
                createTab('tab' + index);
            }
            //获取呈现元素句柄数组
            var tabMenu = $G("tabMenu").getElementsByTagName("div"),
                        tabContent = $G("tabContent").getElementsByTagName("div"),
                        i = 0,
                        L = tabMenu.length;
            //隐藏所有呈现元素
            for (; i < L; i++) {
                tabMenu[i].className = "";
                tabContent[i].style.display = "none";
            }
            //显示对应呈现元素
            tabMenu[index].className = "on";
            tabContent[index].style.display = "block";
        }
        function createTab(tabName) {
            var faceVersion = "?v=1.1", //版本号
                tab = $G(tabName), //获取将要生成的Div句柄
                imagePath = sBasePath + FaceHandler.imageFolders[tabName], //获取显示表情和预览表情的路径
                imageColsNum = FaceHandler.imageCols[tabName], //每行显示的表情个数
                positionLine = imageColsNum / 2, //中间数
                iWidth = iHeight = FaceHandler.imageWidth[tabName], //图片长宽
                iColWidth = FaceHandler.imageColWidth[tabName], //表格剩余空间的显示比例
                tableCss = FaceHandler.imageCss[tabName],
                cssOffset = FaceHandler.imageCssOffset[tabName],
                textHTML = ['<table class="smileytable" cellpadding="1" cellspacing="0" align="center" style="border-collapse:collapse;" border="1" bordercolor="#BAC498" width="100%">'],
                i = 0,
                imgNum = faceBox[tabName].length,
                imgColNum = FaceHandler.imageCols[tabName],
                faceImage,
                sUrl,
                realUrl,
                posflag,
                offset,
                infor;
            for (; i < imgNum; ) {
                textHTML.push('<tr>');
                for (var j = 0; j < imgColNum; j++, i++) {
                    faceImage = faceBox[tabName][i];
                    if (faceImage) {
                        sUrl = imagePath + faceImage + faceVersion;
                        realUrl = imagePath + faceImage;
                        posflag = j < positionLine ? 0 : 1;
                        offset = cssOffset * i * (-1) - 1;
                        infor = inforBox[tabName][i];
                        textHTML.push('<td  class="' + tableCss + '" sUrl="' + sUrl + '" posflag="' + posflag + '" realUrl="' + realUrl.replace(/'/g, "\\'") + '" border="1" width="' + iColWidth + '%" style="border-collapse:collapse;" align="center"  bgcolor="#FFFFFF">');
                        textHTML.push('<span  style="display:block;">');
                        textHTML.push('<img  style="background-position:left ' + offset + 'px;" title="' + infor + '" src="' + realUrl.replace(/'/g, "\\'") +'" width="' + iWidth + '"></img>');
                        textHTML.push('</span>');
                    } else {
                        textHTML.push('<td width="' + iColWidth + '%"   bgcolor="#FFFFFF">');
                    }
                    textHTML.push('</td>');
                }
                textHTML.push('</tr>');
            }
            textHTML.push('</table>');
            textHTML = textHTML.join("");
            tab.innerHTML = textHTML;
            K("td[class='" + tableCss + "']").each(function () {//表情绑定事件
                bindCellEvent(K(this));
            });
        }
        //初始显示第一个表情目录
        switchTab(0);
        K("div#tabMenu>div").each(function (index) {//标签切换绑定事件
            K(this).click(function () {
                switchTab(index);
            });
        });
        /**********************************************表情方法结束*****************************************************/
    });
});


<%Response.End(): Response.Clear %>