Option Strict Off
Option Explicit On
Imports System.IO
Imports System.Text.RegularExpressions

Friend Class frmUpdateTheme
    Inherits System.Windows.Forms.Form

    Dim strSource, strTemplateFolder, strXMLPath As String
    Dim aryTemplateFile() As String
    Dim aryPluginFile() As String
    Dim objAero As clsAero


    Private Sub cmdBrowse_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmdBrowse.Click
        Dim strTemp As String
        fbdDialog.Description = "请选择模板文件夹"
        fbdDialog.ShowDialog()
        strTemp = fbdDialog.SelectedPath
        If Not strTemp = "" Then
            strTemplateFolder = strTemp
            txtPath.Text = strTemp
            Log("选择模板文件夹：" & strTemp)
        End If
    End Sub

    Private Sub cmdOpen_Click(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles cmdOpen.Click
        Dim i As Short
        Log_Clear()
        strTemplateFolder = txtPath.Text
        If GetSubFolder(strTemplateFolder) Then
            Log("开始升级模板文件")
            For i = 0 To CShort(UBound(aryTemplateFile))
                If Trim(aryTemplateFile(i)) <> "" Then Update_Theme(aryTemplateFile(i), 1) : Update_Theme(aryTemplateFile(i), 4)
            Next
            Log("模板文件升级完毕")
            Log("开始升级source下asp")
            Update_Theme(strSource, 2)
            Log("source下asp升级完毕")
                Log("开始升级主题自带插件")

            If Directory.Exists(strTemplateFolder & "\plugin") Then

                frmUpdatePlugin.Show()
                frmUpdatePlugin.Enabled = True
                frmUpdatePlugin.cmdOpen.Enabled = False
                frmUpdatePlugin.cmdBrowse.Enabled = False
                frmUpdatePlugin.txtPath.Enabled = False
                frmUpdatePlugin.strTemplateFolder = strTemplateFolder & "\plugin"

                frmUpdatePlugin.GetSubFolder(strTemplateFolder & "\plugin", "..\..\..\", "..\..\..\..\", True)
                frmUpdatePlugin.Update_P(True)
                'frmUpdatePlugin.Hide()
            End If

                Log("主题自带插件升级完毕")

            '还差侧栏管理的升级
            'Log "升级XML信息"
            '升级XML是不是让APP升级好一点

            MsgBox(Replace("升级完毕！\n\n剩余以下部分没有升级，请自行修改：\n\n侧栏部分（须符合2.0侧栏规范）\n主题插件\nXML信息\n\n升级完成后，请在APP中心里编辑主题信息并保存，即可在2.0里激活主题。", "\n", vbCrLf), MsgBoxStyle.Information)
            Process.Start("http://www.zsxsoft.com/updatesuccess.html")
        End If

    End Sub

    Private Sub frmMain_Load(ByVal eventSender As System.Object, ByVal eventArgs As System.EventArgs) Handles MyBase.Load
        If Environment.OSVersion.Version.Major >= 6 Then bolAero = True

        If bolAero Then
            objAero = New clsAero
            objAero.Form = Me
            objAero.Go()
        End If




        strTemplateFolder = ""
        ReDim aryTemplateFile(0)
        ReDim aryPluginFile(0)
        strSource = ""
        lblNote.Text = "说明：" & vbCrLf & "升级前必须备份。" & vbCrLf & "您要升级的1.8模板必须符合以下要求：" & vbCrLf & "      1.模板在TEMPLATE文件夹下，扩展名为html" & vbCrLf & "      2.HTML标签全部闭合" & vbCrLf & "      3.未重写系统自带的common.js" & vbCrLf & "以上条件有任意一点不符合，则本程序无法升级你的主题。"
    End Sub


    Private Sub frmMain_FormClosed(ByVal eventSender As System.Object, ByVal eventArgs As System.Windows.Forms.FormClosedEventArgs) Handles Me.FormClosed
        End
    End Sub




    'Usage:日志
    'Param:str--日志内容
    Sub Log(ByVal str_Renamed As String)
        lstLog.Items.Add("【" & Now & "】" & str_Renamed)
    End Sub

    'Usage:清除日志
    Sub Log_Clear()
        lstLog.Items.Clear()
    End Sub

    'Usage:扫描文件夹
    'Param:Folder--文件夹
    Function GetSubFolder(ByVal Folder As String) As Boolean
        Dim objRegExp As New Regex("b_article-guestbook|b_article_trackback|guestbook|search", RegexOptions.IgnoreCase)
        GetSubFolder = False
        Dim objFor As Object
        If Directory.Exists(Folder) Then
            If File.Exists(Folder & "\theme.xml") Then
                strXMLPath = Folder & "\theme.xml"
                Log("找到主题XML信息")
            Else
                Log("主题XML不存在")
                Exit Function
            End If
            If Directory.Exists(Folder & "\template") Then
                For Each objFor In Directory.GetFiles(Folder & "\template")

                    '顺便做个删除吧

                    If objRegExp.Match(objFor).Success Then
                        Log("删除无用文件：" & objFor)
                        File.Delete(objFor)
                    Else
                        ReDim Preserve aryTemplateFile(UBound(aryTemplateFile) + 1)
                        '复制page模板
                        If objFor Like "single*" Then
                            If Not File.Exists(Folder & "\template\page.html") Then
                                File.Copy(objFor, Folder & "\template\page.html") : Log("复制PAGE模板")
                            End If
                        End If
                        aryTemplateFile(UBound(aryTemplateFile)) = objFor
                        Log("找到主题文件：" & objFor)
                    End If
                Next objFor
            End If
            If Directory.Exists(Folder & "\include") Then
                For Each objFor In Directory.GetFiles(Folder & "\include")
                    ReDim Preserve aryTemplateFile(UBound(aryTemplateFile) + 1)
                    aryTemplateFile(UBound(aryTemplateFile)) = objFor
                    Log("找到主题文件：" & objFor)
                Next objFor
            End If
            If Directory.Exists(Folder & "\source\style.css.asp") Then
                strSource = Folder & "\source\style.css.asp"
                Log("找到STYLE.CSS.ASP")
            End If
            GetSubFolder = True
        Else
            Log("文件夹不存在！")
        End If
    End Function


    'Usage:得到XML信息以判断是否Z-Blog
    'Param:XMLPath--XML地址
    Function LoadXMLInfo(ByVal XMLPath As String) As Boolean

    End Function




    'Usage:升级
    'Param:strFilePath--文件名,intType--升级类型
    Function Update_Theme(ByVal strFilePath As String, Optional ByRef intType As Short = 1) As Boolean
        Dim strFile As String = ""
        Dim objExec As Object = Nothing
        If File.Exists(strFilePath) Then

            Log("Update: " & strFilePath & "  type:" & intType)
            strFile = File.ReadAllText(strFilePath)
            If Trim(strFile) = "" Then Return False
            Select Case intType
                Case 1
                    '模板主体和INCLUDE文件夹升级


                    '替换zb_system下文件

                    For Each objExec In New Regex("\<\#ZC_BLOG_HOST\#\>(admin|script|function|image|cmd.asp|login.asp)", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "<#ZC_BLOG_HOST#>zb_system/" & objExec.Groups(1).Value, 1, 1)
                        Log(objExec.Groups(1).Value & "-->" & "zb_system/" & objExec.Groups(1).Value)
                    Next objExec

                    '替换zb_users下文件
                    For Each objExec In New Regex("\<\#ZC_BLOG_HOST\#\>(plugin|language|cache|upload)", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "<#ZC_BLOG_HOST#>zb_users/" & objExec.Groups(1).Value, 1, 1)
                        Log(objExec.Groups(1).Value & "-->" & "zb_users/" & objExec.Groups(1).Value)
                    Next objExec

                    '替换theme
                    For Each objExec In New Regex("(\<\#ZC_BLOG_HOST\#\>themes)", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Groups(1).Value, "<#ZC_BLOG_HOST#>zb_users/theme", 1, 1)
                        Log(objExec.Groups(1).Value & "-->" & "<#ZC_BLOG_HOST#>zb_users/theme")
                    Next objExec

                    '替换rss
                    For Each objExec In New Regex("(\<\#ZC_BLOG_HOST\#\>rss\.xml)", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Groups(1).Value, "<#ZC_BLOG_HOST#>feed.asp", 1, 1)
                        Log(objExec.Groups(1).Value & "-->" & "<#ZC_BLOG_HOST#>feed.asp")
                    Next objExec


                    '替换那些玩意
                    For Each objExec In New Regex("var (str0[0-9]|intMaxLen|strBatchView|strBatchInculde|strBatchCount|strFaceName|strFaceSize)=.+?;", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "", 1, 1)
                        Log(objExec.Value & "-->" & """""")
                    Next objExec

                    '强插c_html_js_add.asp
                    If InStr(LCase(strFile), "c_html_js_add.asp") = 0 And InStr(LCase(strFile), "</head>") > 0 Then
                        strFile = Replace(strFile, "</head>", "<script src=""<#ZC_BLOG_HOST#>zb_system/function/c_html_js_add.asp"" type=""text/javascript""></script>" & vbCrLf & "</head>")
                        Log("强制插入c_html_js_add.asp")
                    End If

                    '删除无用UBB部分
                    For Each objExec In New Regex("InsertQuote.+?\;|ExportUbbFrame\(\)\;?", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "", 1, 1)
                        Log(objExec.Value & "-->" & """""")
                    Next objExec


                    '替换计数
                    strFile = Replace(strFile, "strBatchCount+=""spn<#article/id#>=<#article/id#>,""", "AddViewCount(<#article/id#>)")
                    strFile = Replace(strFile, "strBatchView+=""spn<#article/id#>=<#article/id#>,""", "LoadViewCount(<#article/id#>)")
                    Log("计数部分修改")

                    '替换无用标签
                    For Each objExec In New Regex("<#template:article_trackback#>|<#article/pretrackback_url#>|<#ZC_MSG014#>|<#article/trackbacknums#>", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "", 1, 1)
                        Log(objExec.Value & "-->" & """""")
                    Next objExec

                    '替换Try--elScript
                    For Each objExec In New Regex("try{" & vbCrLf & ".+?elScript[\d\D]+?catch\(e\){};?", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "", 1, 1)
                        Log(objExec.Value & "-->" & """""")
                    Next objExec


                    '替换验证码
                    For Each objExec In New Regex("if.+?inpVerify[\d\D]+?Math.random\(\)[\d\D]+?}[\d\D]+?}", RegexOptions.IgnoreCase).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "", 1, 1)
                        Log(objExec.Value & "-->" & """""")
                    Next objExec

                    '替换空行
                    For Each objExec In New Regex("[" & vbTab & " ]+" & vbCrLf).Matches(strFile)
                        strFile = Replace(strFile, objExec.Value, "", 1, 1)
                        Log(objExec.Value & "-->" & """""")
                    Next objExec



                    '保存
                    File.WriteAllText(strFilePath, strFile)
                    Log("保存完毕")
                Case 2
                    'SOURCE\STYLE.CSS.ASP升级

                    '替换<%
                    strFile = Replace(strFile, "<%", "<!-- #include file=""..\..\..\..\zb_system\function\c_function.asp"" -->" & vbCrLf & "<%")
                    Log("引用c_function.asp")

                    '替换路径
                    strFile = Replace(strFile, """themes""", """zb_users/theme""")
                    Log("""themes"" --> ""zb_users/theme""")

                    '替换HOST
                    strFile = Replace(strFile, "ZC_BLOG_HOST", "GetCurrentHost()")
                    Log("ZC_BLOG_HOST --> GetCurrentHost()")


                    File.WriteAllText(strFilePath, strFile)
                    Log("保存完毕")

                Case 3
                    '插件\主题插件升级

                Case 4
                    '侧栏管理升级
                    '侧栏管理只按照默认主题的结构弄，非默认主题的结构不管他
                    '抽样调查20个主题，默认主题侧栏结构约占50%上下

                    Dim objRegExp As New Regex("<div id=""divSidebar"">[\d\D]+?<div class=""function""", RegexOptions.IgnoreCase)
                    '判断是否存在结构与默认主题相同的侧栏
                    If objRegExp.Match(strFile).Success Then

                        'objRegExp.Pattern = "<div id=""divSidebar"">[\d\D]+?</div>"

                    End If

                Case 5
                    'XML升级

            End Select
        Else
            Log(strFile & "找不到！")
        End If

    End Function
End Class