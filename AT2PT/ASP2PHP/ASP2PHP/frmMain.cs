using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.Collections;
using System.IO;
using System.Text.RegularExpressions;


namespace ASP2PHP
{
    public partial class frmMain : Form
    {
        string strSource;
        string strTemplateFolder;
        string strXMLPath;
        string[] aryTemplateFile;
        string[] aryPluginFile;
        string[] strMsg;

        clsAero objAero;

        private void cmdBrowse_Click(System.Object eventSender, System.EventArgs eventArgs)
        {
            string strTemp = null;
            fbdDialog.Description = "请选择模板文件夹";
            fbdDialog.ShowDialog();
            strTemp = fbdDialog.SelectedPath;
            if (!string.IsNullOrEmpty(strTemp))
            {
                strTemplateFolder = strTemp;
                txtPath.Text = strTemp;
                Log("选择模板文件夹：" + strTemp);
            }
        }

        private void cmdOpen_Click(System.Object eventSender, System.EventArgs eventArgs)
        {
            short i = 0;
            Log_Clear();
            strTemplateFolder = txtPath.Text;
            if (GetSubFolder(strTemplateFolder))
            {
                Log("开始升级模板文件");
                for (i = 0; i < aryTemplateFile.Length; i++)
                {
                    if (!string.IsNullOrEmpty(aryTemplateFile[i]))
                    {
                        Update_Theme(aryTemplateFile[i], 1);
                        Update_Theme(aryTemplateFile[i], 4);
                    }
                }
                Log("模板文件升级完毕");



                MessageBox.Show("啊", "转换完毕", MessageBoxButtons.OK, MessageBoxIcon.Information);
                //Process.Start("http://www.zsxsoft.com/updatesuccess.html");
            }

        }

        private void frmMain_Load(System.Object eventSender, System.EventArgs eventArgs)
        {

            if (Environment.OSVersion.Version.Major >= 6)
            {
                objAero = new clsAero();
                objAero.Form = this;
                objAero.Go();
            }

            init_language();

            strTemplateFolder = "";
            aryTemplateFile = new string[1];
            aryPluginFile = new string[1];
            strSource = "";
            //lblNote.Text = "说明：\n升级前必须备份。\n您要升级的1.8模板必须符合以下要求：\n      1.模板在TEMPLATE文件夹下，扩展名为html\n      2.HTML标签全部闭合\n      3.未重写系统自带的common.js\n以上条件有任意一点不符合，则本程序无法升级你的主题。";
        }


        private void frmMain_FormClosed(System.Object eventSender, System.Windows.Forms.FormClosedEventArgs eventArgs)
        {
            System.Environment.Exit(0);
        }




        //Usage:日志
        //Param:str--日志内容
        public void Log(string str_Renamed)
        {
            lstLog.Items.Add("【" + DateTime.Now + "】" + str_Renamed);
        }

        //Usage:清除日志
        public void Log_Clear()
        {
            lstLog.Items.Clear();
        }

        //Usage:扫描文件夹
        //Param:Folder--文件夹
        public bool GetSubFolder(string Folder)
        {
            Regex objRegExp = new Regex("b_article-guestbook|b_article_trackback|guestbook|search", RegexOptions.IgnoreCase);

            if (Directory.Exists(Folder))
            {
                if (File.Exists(Folder + "\\theme.xml"))
                {
                    strXMLPath = Folder + "\\theme.xml";
                    Log("找到主题XML信息");
                }
                else
                {
                    Log("主题XML不存在");
                    return false;
                }

                if (Directory.Exists(Folder + "\\template"))
                {
                    foreach (String objFor in Directory.GetFiles(Folder + "\\template"))
                    {
                        Array.Resize(ref aryTemplateFile, aryTemplateFile.Length + 1);
                        aryTemplateFile[aryTemplateFile.Length - 1] = objFor;
                        Log("找到主题文件：" + objFor);
                    }
                }
                return true;

            }
            else
            {
                Log("文件夹不存在！");
            }
            return true;
        }


        //Usage:升级
        //Param:strFilePath--文件名,intType--升级类型
        public bool Update_Theme(string strFilePath, short intType = 1)
        {
            string strFile = "";

            if (File.Exists(strFilePath))
            {
                Log("Update: " + strFilePath + "  type:" + intType);
                strFile = File.ReadAllText(strFilePath);
                if (string.IsNullOrEmpty(strFile)) return false;
                switch (intType)
                {
                    case 1:
                        //模板主体和INCLUDE文件夹升级


                        //<#xxx/xxx/xxx#>换{$xxx->xxx->xxx}

                        foreach (Match objExec in new Regex("<#(.+?)#>", RegexOptions.IgnoreCase).Matches(strFile))
                        {
                            GroupCollection groups = objExec.Groups;
                            strFile = strFile.Replace(objExec.Value, "{$" + groups[1].Value.Replace("/", "->") + "}");
                            //Log(groups[1].Value + "-->" + "" + groups[1].Value);
                        }

                        foreach (Match objExec in new Regex("{\\$template:(.+?)}", RegexOptions.IgnoreCase).Matches(strFile))
                        {
                            GroupCollection groups = objExec.Groups;
                            strFile = strFile.Replace(objExec.Value, "{template:" + groups[1].Value + "}");
                            //Log(groups[1].Value + "-->" + "" + groups[1].Value);
                        }

                        //保存
                        File.WriteAllText(strFilePath, strFile);
                        Log("保存完毕");
                        break;
                    case 2:
                        //SOURCE\STYLE.CSS.ASP升级

                        //替换HOST
                        strFile = strFile.Replace("ZC_BLOG_HOST", "GetCurrentHost()");
                        Log("ZC_BLOG_HOST --> GetCurrentHost()");


                        File.WriteAllText(strFilePath, strFile);
                        Log("保存完毕");

                        break;
                    case 3:
                        break;
                    //插件\主题插件升级

                    case 4:
                        //侧栏管理升级
                        //侧栏管理只按照默认主题的结构弄，非默认主题的结构不管他
                        //抽样调查20个主题，默认主题侧栏结构约占50%上下

                        Regex objRegExp = new Regex("<div id=\"divSidebar\">[\\d\\D]+?<div class=\"function\"", RegexOptions.IgnoreCase);
                        //判断是否存在结构与默认主题相同的侧栏

                        if (objRegExp.Match(strFile).Success)
                        {
                            //objRegExp.Pattern = "<div id=""divSidebar"">[\d\D]+?</div>"

                        }

                        break;
                    case 5:
                        break;
                    //XML升级

                }
            }
            else
            {
                Log(strFile + "找不到！");
            }
            return true;

        }

        public frmMain()
        {
            InitializeComponent();
            FormClosed += frmMain_FormClosed;
            Load += frmMain_Load;
        }




        public void init_language()
        {
            strMsg = new string[308];
            strMsg[001] = "名称";
            strMsg[002] = "密码";
            strMsg[003] = "用户名";
            strMsg[004] = "保存";
            strMsg[005] = "当前用户";
            strMsg[006] = "官方网站";
            strMsg[007] = "侧栏管理";
            strMsg[008] = "侧栏";
            strMsg[009] = "用户登录";
            strMsg[010] = "用户名,密码不能为空";
            strMsg[011] = "发布";
            strMsg[012] = "分类";
            strMsg[013] = "评论";
            strMsg[014] = "引用";
            strMsg[015] = "查看";
            strMsg[016] = "摘要";
            strMsg[017] = "显示";
            strMsg[018] = "来宾";
            strMsg[019] = "超级管理%s登录%s验证身份%s注销%s后台管理%s发表评论%s查看权限%s查看RSS输出%s批量操作%s索引重建%s文章重建%s管理文章%s编辑文章%s发布文章%s删除文章%s管理分类%s修改分类%s删除分类%s管理评论%s删除评论%s管理用户%s编辑用户%s创建用户%s修改用户%s删除用户%s附件管理%s附件上传%s附件删除%s站内搜索%sTag管理%sTag编辑%sTag保存%sTag删除%s网站设置%s网站设置保存%s插件管理%s侧栏模块管理%s所有文件管理%s所有评论管理%s所有附件管理%s" ;
            strMsg[020] = "退出登录";
            strMsg[021] = "查看权限";
            strMsg[022] = "管理";
            strMsg[023] = "您好,%s";
            strMsg[024] = "发表留言";
            strMsg[025] = "控制面板";
            strMsg[026] = "网站分类";
            strMsg[027] = "最新留言";
            strMsg[028] = "文章归档";
            strMsg[029] = "站点统计";
            strMsg[030] = "网站收藏";
            strMsg[031] = "友情链接";
            strMsg[032] = "最近发表";
            strMsg[033] = "名称不能为空";
            strMsg[034] = "名称或邮箱,网址格式不对";
            strMsg[035] = "留言不能为空或过长";
            strMsg[036] = "%s";
            strMsg[037] = "UBB标签";
            strMsg[038] = "密码不能小于8位";
            strMsg[039] = "图标汇集";
            strMsg[040] = "◎欢迎参与讨论，请在这里发表您的看法、交流您的观点。";
            strMsg[041] = "大小";
            strMsg[042] = "分页";
            strMsg[043] = "私人文章，登录状态下方可查看。";
            strMsg[044] = "-";
            strMsg[045] = "错误提示";
            strMsg[046] = "加入导航栏菜单";
            strMsg[047] = "文章编辑";
            strMsg[048] = "文章";
            strMsg[049] = "记住我,下次回复时不用重新输入个人信息";
            strMsg[050] = "日历";
            strMsg[051] = "置顶";
            strMsg[052] = "导航栏菜单";
            strMsg[053] = "邮箱";
            strMsg[054] = "网站链接";
            strMsg[055] = "正文";
            strMsg[056] = "留言最长字数";
            strMsg[057] = "显示UBB表情>>";
            strMsg[058] = "单击“确定”继续。单击“取消”停止。";
            strMsg[059] = "未分类";
            strMsg[060] = "标题";
            strMsg[061] = "类型";
            strMsg[062] = "日期";
            strMsg[063] = "删除";
            strMsg[064] = "传送引用";
            strMsg[065] = "返回网站";
            strMsg[066] = "分类管理";
            strMsg[067] = "文章管理";
            strMsg[068] = "评论管理";
            strMsg[069] = "引用管理";
            strMsg[070] = "用户管理";
            strMsg[071] = "附件管理";
            strMsg[072] = "索引重建";
            strMsg[073] = "文件重建";
            strMsg[074] = "不指定给侧栏";
            strMsg[075] = "时间";
            strMsg[076] = "ID";
            strMsg[077] = "新建分类";
            strMsg[078] = "编辑";
            strMsg[079] = "排序";
            strMsg[080] = "IP";
            strMsg[081] = "URL";
            strMsg[082] = "文章总数";
            strMsg[083] = "当前样式";
            strMsg[084] = "当前语言";
            strMsg[085] = "Search";
            strMsg[086] = "搜索%s，共找到%s个结果";
            strMsg[087] = "提交";
            strMsg[088] = "重置";
            strMsg[089] = "验证";
            strMsg[090] = "内容";
            strMsg[091] = "通过审核";
            strMsg[092] = "加入审核";
            strMsg[093] = "网站的标题";
            strMsg[094] = "网站的子标题";
            strMsg[095] = "回复评论ID";
            strMsg[096] = "BLOG版权说明(可以放置备案号和统计代码,支持HTML代码,可用'&lt;br/&gt;'标签换行)";
            strMsg[097] = "正常评论管理";
            strMsg[098] = "错误原因";
            strMsg[099] = "未命名文章";
            strMsg[100] = "可视编辑";
            strMsg[101] = "UBB编辑";
            strMsg[102] = "检测到%s备份的数据还未使用!";
            strMsg[103] = "播放/隐藏 媒体";
            strMsg[104] = "待审核评论管理";
            strMsg[105] = "基础设置";
            strMsg[106] = "高级设置";
            strMsg[107] = "插件管理";
            strMsg[108] = "上传图片、影音及其它类型的文件";
            strMsg[109] = " 操作正在进行中,请稍候...";
            strMsg[110] = "批量操作";
            strMsg[111] = "页面管理";
            strMsg[112] = "如果你更换了主题模板或修改了某些必需文件重建才能生效的网站设置,请使用'文件重建'功能.<br/><br/>重建所有日志可能需要很长时间,请点击'提交'按钮执行.";
            strMsg[113] = "新建页面";
            strMsg[114] = "保存我的登录信息";
            strMsg[115] = "分类不能为空";
            strMsg[116] = "正文不能为空";
            strMsg[117] = "数据获取中";
            strMsg[118] = "名称不能为空";
            strMsg[119] = "密码不能为空";
            strMsg[120] = "邮箱不能为空";
            strMsg[121] = "添加新日志";
            strMsg[122] = "添加新分类";
            strMsg[123] = "添加新用户";
            strMsg[124] = "评论总数";
            strMsg[125] = "页面总数";
            strMsg[126] = "网站地址(默认自动读取当前网址,如果系统的识别功能出现问题,或是需要固化网站域名,请点锁定按钮后输入网址并提交保存.)";
            strMsg[127] = "新建用户";
            strMsg[128] = "作者";
            strMsg[129] = "浏览总数";
            strMsg[130] = "浏览";
            strMsg[131] = "自动命名上传文件";
            strMsg[132] = "首页及分类页翻页条数量";
            strMsg[133] = "没有备份内容";
            strMsg[134] = "添加Tags";
            strMsg[135] = "新建关键字";
            strMsg[136] = "新建Tags";
            strMsg[137] = "ID值是html页面唯一的,请不要与其它模块的ID重复,建议以fn或div为前缀加文件名.";
            strMsg[138] = "Tags";
            strMsg[139] = "显示常用Tags";
            strMsg[140] = "为0则不限制LI的输出项数";
            strMsg[141] = "Tags管理";
            strMsg[142] = "新建模块";
            strMsg[143] = "UL内LI的最大行数";
            strMsg[144] = "模块编辑";
            strMsg[145] = "请拖动需要的模块到右侧区域指定侧栏。侧栏中的模块可排序，也可拖至左侧区域移除。";
            strMsg[146] = "« 上一篇";
            strMsg[147] = "别名";
            strMsg[148] = "下一篇 »";
            strMsg[149] = "回复";
            strMsg[150] = "当前版本";
            strMsg[151] = "最后更新";
            strMsg[152] = "回复ID";
            strMsg[153] = "引自";
            strMsg[154] = "最近引用";
            strMsg[155] = "更早的文章 »";
            strMsg[156] = "« 之后的文章";
            strMsg[157] = "任意";
            strMsg[158] = "搜索文章";
            strMsg[159] = "信息摘要";
            strMsg[160] = "页面";
            strMsg[161] = "页面编辑";
            strMsg[162] = "分类总数";
            strMsg[163] = "Tags总数";
            strMsg[164] = "最新动态信息";
            strMsg[165] = "正在获取中，请稍候。";
            strMsg[166] = "用户总数";
            strMsg[167] = "站内统计摘要";
            strMsg[168] = "新建文章";
            strMsg[169] = "总计耗时%n秒";
            strMsg[170] = "文件名";
            strMsg[171] = "日志页面显示评论的数量(设为0则评论不分页,全部输出在同一页.)";
            strMsg[172] = "备份与更新";
            strMsg[173] = "全局设置";
            strMsg[174] = "所选项目通过审核";
            strMsg[175] = "BLOG用户所在的时区";
            strMsg[176] = "BLOG 页面语言";
            strMsg[177] = "所选项目加入审核";
            strMsg[178] = "文章存放目录";
            strMsg[179] = "此目录下文章的默认模板";
            strMsg[180] = "无";
            strMsg[181] = "单次重建文件数目";
            strMsg[182] = "单次重建文件后的间隔秒数";
            strMsg[183] = "允许上传文件的类型(以|做为分隔)";
            strMsg[184] = "上传文件的最大字节数";
            strMsg[185] = "发表评论时启用验证码";
            strMsg[186] = "页面设置";
            strMsg[187] = "默认模板";
            strMsg[188] = "模板";
            strMsg[189] = " 提示:删除用户会将该用户所有的文章和评论及附件全部删除。";
            strMsg[190] = "首页及分类页显示文章的数量";
            strMsg[191] = "管理页显示记录的数量";
            strMsg[192] = "« 上一页评论";
            strMsg[193] = "下一页评论 »";
            strMsg[194] = "翻页条的条目数量";
            strMsg[195] = "父分类";
            strMsg[196] = "样式";
            strMsg[197] = "原作";
            strMsg[198] = "简介";
            strMsg[199] = "主题自带插件";
            strMsg[200] = "手动生成摘要";
            strMsg[201] = "状态";
            strMsg[202] = "启用";
            strMsg[203] = "停用";
            strMsg[204] = "当前主题";
            strMsg[205] = "";
            strMsg[206] = "主机服务器所在的时区";
            strMsg[207] = "返回源地址";
            strMsg[208] = "逗号分割";
            strMsg[209] = "启用单日志页面前后文章导航";
            strMsg[210] = "文件管理";
            strMsg[211] = "留言列表";
            strMsg[212] = "查看评论";
            strMsg[213] = "首页";
            strMsg[214] = "分类查看";
            strMsg[215] = "评论设置";
            strMsg[216] = "在正文插入分隔符&quot;&lt;hr class=&quot;more&quot; /&gt;&quot;可以让系统识别摘要内容。如需另外指定摘要内容，请点击 ";
            strMsg[217] = "";
            strMsg[218] = "";
            strMsg[219] = "";
            strMsg[220] = "";
            strMsg[221] = "";
            strMsg[222] = "删除全部未审核评论";
            strMsg[223] = "主题管理";
            strMsg[224] = "要查询的内容";
            strMsg[225] = "更新";
            strMsg[226] = "启用RSS的全文输出";
            strMsg[227] = "批量操作已完成.";
            strMsg[228] = "删除所选项目";
            strMsg[229] = "全选";
            strMsg[230] = "日志页面相关文章的数量(设为0则不输出相关文章内容)";
            strMsg[231] = "相关文章";
            strMsg[232] = "点击这里获取该日志的TrackBack引用地址";
            strMsg[233] = "%y年";
            strMsg[234] = "搜索符合条件的评论";
            strMsg[235] = "&laquo;";
            strMsg[236] = "&raquo;";
            strMsg[237] = "确认密码";
            strMsg[238] = "文件大小";
            strMsg[239] = "回上级目录";
            strMsg[240] = "当前路径";
            strMsg[241] = "Tags编辑";
            strMsg[242] = "用户编辑";
            strMsg[243] = "分类编辑";
            strMsg[244] = "留言编辑";
            strMsg[245] = "后台首页";
            strMsg[246] = "文件编辑";
            strMsg[247] = "网站设置";
            strMsg[248] = "后台管理";
            strMsg[249] = "等级";
            strMsg[250] = "正在保存";
            strMsg[251] = "秒后自动保存";
            strMsg[252] = "恢复";
            strMsg[253] = "已恢复";
            strMsg[254] = "这将覆盖你原有的内容！继续？";
            strMsg[255] = "静态化设置";
            strMsg[256] = "无内容";
            strMsg[257] = "Z-Blog提示";
            strMsg[258] = "自动保存成功";
            strMsg[259] = "重建首页缓存文件";
            strMsg[260] = "登录";
            strMsg[261] = " 提示:'未分类'分类是系统默认加入的分类,不能删除;未指定分类的文章都归入'未分类'下,该分类下没有文章的话将不显示在前台分类列表中.";
            strMsg[262] = "关闭评论功能";
            strMsg[263] = "启用Chrome推送通知";
            strMsg[264] = "取消";
            strMsg[265] = "回复该留言";
            strMsg[266] = " 操作成功.";
            strMsg[267] = " 操作失败.";
            strMsg[268] = " 提示:需要进行'[索引重建]'.";
            strMsg[269] = " 提示:需要进行'<a href=\"%u\">文件重建</a>'.";
            strMsg[270] = "所属文章";
            strMsg[271] = "启用评论倒序输出";
            strMsg[272] = "评论编辑";
            strMsg[273] = " 之前的批量操作还未全部执行,请点击\"<a href='#'>继续</a>\"执行完所有的操作.";
            strMsg[274] = "显示搜索文章的数量";
            strMsg[275] = "发布于";
            strMsg[276] = "正在为您加载编辑器";
            strMsg[277] = "系统模块";
            strMsg[278] = "配置";
            strMsg[279] = "侧栏加载方式";
            strMsg[280] = "自动";
            strMsg[281] = "缓存";
            strMsg[282] = "这些应用未启用,无法启用此应用:";
            strMsg[283] = "该应用无法被停用,如果要停用请先停用这些应用:";
            strMsg[284] = "该应用无法被安装,这些应用与它冲突:";
            strMsg[285] = "该应用可能与这些应用冲突,请关注作者官方网站:";
            strMsg[286] = "用户自定义模块";
            strMsg[287] = "主题定义模块";
            strMsg[288] = "插件定义模块";
            strMsg[289] = "碎片模块";
            strMsg[290] = "默认侧栏";
            strMsg[291] = "侧栏2";
            strMsg[292] = "侧栏3";
            strMsg[293] = "侧栏4";
            strMsg[294] = "侧栏5";
            strMsg[295] = "内含<span>%n</span>个模块";
            strMsg[296] = "手动修改并锁定网站地址";
            strMsg[297] = "自动识别网站地址";
            strMsg[298] = "隐藏模块标题";
            strMsg[299] = "该模块在模版中的独立调用标签为:";
            strMsg[300] = "语言包(部分模板和插件可能依然显示其它的语言。)";
            strMsg[301] = "打开代码高亮(系统自带编辑器使用SyntaxHighLighter代码高亮库，使用其它编辑器可能不同)";
            strMsg[302] = "您当前的编辑内容还未保存！";
            strMsg[303] = "Z-Blog网站和程序开发";
            strMsg[304] = "程序";
            strMsg[305] = "设计";
            strMsg[306] = "支持";
            strMsg[307] = "感谢";
            strMsg[308] = "等朋友";
        }
    }
}