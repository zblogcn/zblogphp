<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
$appid = 'tpure';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin($appid)) {
    $zbp->ShowError(48);
    die();
}

//配置页标题
$blogtitle = $zbp->themeapp->name.$zbp->lang['tpure']['set'];

$act = $_GET['act'] == "base" ? 'base' : $_GET['act'];

require $zbp->path . 'zb_system/admin/admin_header.php';
require $zbp->path . 'zb_system/admin/admin_top.php';

//判断是否安装“UEditor”插件，配置页图片上传依赖此插件
$ueUrl = $zbp->host .'zb_users/plugin/AppCentre/main.php?id=228';
if($zbp->LoadApp('plugin', 'UEditor')->isloaded){
    echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
    echo '<script type="text/javascript" src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
}else{
    $zbp->ShowHint('bad', $zbp->lang['tpure']['pleaseinstall'].' (<a href="'. $ueUrl .'">'. $zbp->lang['tpure']['ueditor'] .'</a>) '. $zbp->lang['tpure']['plugin'] .'！');
}
?>
<link rel="stylesheet" href="./script/admin.css?v=<?php echo $zbp->themeapp->version?>">
<script type="text/javascript" src="./script/custom.js?v=<?php echo $zbp->themeapp->version?>"></script>
<script>window.theme = {ajaxpost:<?php if($zbp->Config('tpure')->PostAJAXPOSTON == '0'){echo 0;}else{echo 1;}?>}</script>
<div class="twrapper">
<div class="theader">
	<div class="theadbg"><div class="tips">提示：<strong>Ctrl + S</strong> 可快速保存设置</div></div>
	<div class="tuser">
		<div class="tuserimg"><img src="style/images/sethead.png" /></div>
		<div class="tusername"><?php echo $blogtitle; ?></div>
	</div>
	<div class="tmenu">
		<ul>
		<?php tpure_SubMenu($act); ?>
		</ul>
	</div>
</div>
<div class="tmain">
<?php
if ($act == 'base') {
    if (isset($_POST['PostLOGO'])) {
        CheckIsRefererValid();
        $zbp->Config('tpure')->PostLOGO = $_POST['PostLOGO'];                   //网站LOGO
        $zbp->Config('tpure')->PostNIGHTLOGO = $_POST['PostNIGHTLOGO'];					//网站夜间模式LOGO
        $zbp->Config('tpure')->PostLOGOON = $_POST['PostLOGOON'];					//图片LOGO开关
        $zbp->Config('tpure')->PostLOGOHOVERON = $_POST['PostLOGOHOVERON'];         //LOGO划过动效开关
        $zbp->Config('tpure')->PostFAVICON = $_POST['PostFAVICON'];				//浏览器标签栏图标
        $zbp->Config('tpure')->PostFAVICONON = $_POST['PostFAVICONON'];				//浏览器标签栏图标开关
        $zbp->Config('tpure')->PostTHUMB = $_POST['PostTHUMB'];				//固定缩略图
        $zbp->Config('tpure')->PostTHUMBON = $_POST['PostTHUMBON'];             //默认缩略图开关(无图默认)
        $zbp->Config('tpure')->PostRANDTHUMBON = $_POST['PostRANDTHUMBON'];             //随机缩略图开关(无图默认)
        $zbp->Config('tpure')->PostIMGON = $_POST['PostIMGON'];             //列表缩略图总开关(有则展示)
        $zbp->Config('tpure')->PostSIDEIMGON = $_POST['PostSIDEIMGON'];         //侧栏主题模块缩略图
        $zbp->Config('tpure')->PostTHUMBNEWON = $_POST['PostTHUMBNEWON'];			//Z-Blog1.7缩略图方式开关
        $zbp->Config('tpure')->PostBANNERON = $_POST['PostBANNERON'];     //首页banner总开关
        $zbp->Config('tpure')->PostBANNER = $_POST['PostBANNER'];             //首页banner图片
        $zbp->Config('tpure')->PostBANNERDISPLAYON = $_POST['PostBANNERDISPLAYON'];         //首页banner视差滚动效果开关
        $zbp->Config('tpure')->PostBANNERALLON = $_POST['PostBANNERALLON'];         //banner全站展示开关
        $zbp->Config('tpure')->PostBANNERFONT = $_POST['PostBANNERFONT'];         //首页banner文字内容
        $zbp->Config('tpure')->PostBANNERPCHEIGHT = $_POST['PostBANNERPCHEIGHT'];         //首页banner电脑端高度
        $zbp->Config('tpure')->PostBANNERMHEIGHT = $_POST['PostBANNERMHEIGHT'];         //首页banner移动端端高度
        $zbp->Config('tpure')->PostBANNERSEARCHWORDS = $_POST['PostBANNERSEARCHWORDS'];           //Banner搜索推荐词
        $zbp->Config('tpure')->PostBANNERSEARCHLABEL = $_POST['PostBANNERSEARCHLABEL'];           //Banner搜索推荐词提示：
        $zbp->Config('tpure')->PostBANNERSEARCHON = $_POST['PostBANNERSEARCHON'];           //Banner搜索功能开关
        $zbp->Config('tpure')->PostSEARCHON = $_POST['PostSEARCHON'];           //导航搜索功能开关
        $zbp->Config('tpure')->PostSCHTXT = $_POST['PostSCHTXT'];				//导航搜索默认文字
        $zbp->Config('tpure')->PostVIEWALLON = $_POST['PostVIEWALLON'];	//内容页查看全部开关
        $zbp->Config('tpure')->PostVIEWALLHEIGHT = $_POST['PostVIEWALLHEIGHT'];
        $zbp->Config('tpure')->PostVIEWALLSTYLE = isset($_POST['PostVIEWALLSTYLE']) ? $_POST['PostVIEWALLSTYLE'] : '1';
        $zbp->Config('tpure')->PostVIEWALLSINGLEON = $_POST['PostVIEWALLSINGLEON'];
        $zbp->Config('tpure')->PostVIEWALLPAGEON = $_POST['PostVIEWALLPAGEON'];
        $zbp->Config('tpure')->PostLISTINFO = json_encode($_POST['post_list_info']);		//列表辅助信息
        $zbp->Config('tpure')->PostARTICLEINFO = json_encode($_POST['post_article_info']);	//文章辅助信息
        $zbp->Config('tpure')->PostPAGEINFO = json_encode($_POST['post_page_info']);		//页面辅助信息
        $zbp->Config('tpure')->PostSINGLEKEY = $_POST['PostSINGLEKEY']; //上下篇左右键翻页
        $zbp->Config('tpure')->PostPAGEKEY = $_POST['PostPAGEKEY'];			//列表底部分页条左右键翻页
        $zbp->Config('tpure')->PostRELATEON = $_POST['PostRELATEON'];			//文章页相关文章开关
        $zbp->Config('tpure')->PostRELATETITLE = $_POST['PostRELATETITLE'];         //文章页相关文章标题
        $zbp->Config('tpure')->PostRELATECATE = $_POST['PostRELATECATE'];			//文章页相关文章仅显示当前分类相关
        $zbp->Config('tpure')->PostRELATENUM = $_POST['PostRELATENUM'];         //文章页相关文章展示个数
        $zbp->Config('tpure')->PostRELATESTYLE = $_POST['PostRELATESTYLE'];			//文章页相关文章样式(图文|精简)
        $zbp->Config('tpure')->PostRELATEDIALLEL = $_POST['PostRELATEDIALLEL'];	//文章页相关文章精简列数
        $zbp->Config('tpure')->PostAJAXON = $_POST['PostAJAXON'];               //分页AJAX加载开关
        $zbp->Config('tpure')->PostLOADPAGENUM = $_POST['PostLOADPAGENUM']; //AJAX自动加载页数
        $zbp->Config('tpure')->PostARTICLECMTON = $_POST['PostARTICLECMTON'];       //文章评论开关
        $zbp->Config('tpure')->PostPAGECMTON = $_POST['PostPAGECMTON'];         //页面评论开关
        $zbp->Config('tpure')->PostCMTMAILON = $_POST['PostCMTMAILON'];       //文章评论邮箱字段开关
        $zbp->Config('tpure')->PostCMTSITEON = $_POST['PostCMTSITEON'];       //文章评论网址字段开关
        $zbp->Config('tpure')->PostCMTLOGINON = $_POST['PostCMTLOGINON'];       //登录后评论开关
        $zbp->Config('tpure')->PostCMTIPON = $_POST['PostCMTIPON'];             //评论者IP
        $zbp->Config('tpure')->VerifyCode = $_POST['VerifyCode'];			//自定义验证码出现的字符集
        $zbp->Config('tpure')->PostINDEXSTYLE = $_POST['PostINDEXSTYLE'];       //首页列表样式
        $zbp->Config('tpure')->PostSEARCHSTYLE = $_POST['PostSEARCHSTYLE'];       //搜索列表样式
        $zbp->Config('tpure')->PostFILTERCATEGORY = $_POST['PostFILTERCATEGORY'];       //首页过滤分类
        $zbp->Config('tpure')->PostISTOPSIMPLEON = $_POST['PostISTOPSIMPLEON'];             //精简置顶开关
        $zbp->Config('tpure')->PostISTOPINDEXON = $_POST['PostISTOPINDEXON'];             //仅第一页置顶
        $zbp->Config('tpure')->PostGREYON = $_POST['PostGREYON'];               //整站变灰开关
        $zbp->Config('tpure')->PostGREYSTATE = $_POST['PostGREYSTATE'];             //0.首页变灰，1.整站变灰
        $zbp->Config('tpure')->PostGREYDAY = $_POST['PostGREYDAY'];             //设置指定日期网站变灰
        $zbp->Config('tpure')->PostSETNIGHTON = $_POST['PostSETNIGHTON'];   //网站开关灯
        $zbp->Config('tpure')->PostSETNIGHTAUTOON = $_POST['PostSETNIGHTAUTOON'];   //网站自动开关灯
        $zbp->Config('tpure')->PostSETNIGHTSTART = $_POST['PostSETNIGHTSTART']; //关灯开始时间
        $zbp->Config('tpure')->PostSETNIGHTOVER = $_POST['PostSETNIGHTOVER'];   //关灯结束时间
        $zbp->Config('tpure')->PostTIMESTYLE = $_POST['PostTIMESTYLE'];			//日期时间样式
        $zbp->Config('tpure')->PostTIMEFORMAT = $_POST['PostTIMEFORMAT'];			//时间格式
        $zbp->Config('tpure')->PostCOPYNOTICEON = $_POST['PostCOPYNOTICEON'];               //版权声明开关
        $zbp->Config('tpure')->PostCOPYNOTICEMOBILEON = $_POST['PostCOPYNOTICEMOBILEON'];				//版权声明移动端开关
        $zbp->Config('tpure')->PostCOPYURLON = $_POST['PostCOPYURLON'];	//版权声明链接地址
        $zbp->Config('tpure')->PostQRON = $_POST['PostQRON'];	//二维码开关
        $zbp->Config('tpure')->PostQRSIZE = $_POST['PostQRSIZE'];	//二维码尺寸
		$zbp->Config('tpure')->PostCOPYNOTICE = $_POST['PostCOPYNOTICE'];				//版权声明内容
        $zbp->Config('tpure')->PostSHAREARTICLEON = $_POST['PostSHAREARTICLEON'];				//文章分享按钮开关
        $zbp->Config('tpure')->PostSHAREPAGEON = $_POST['PostSHAREPAGEON'];				//独立页面分享按钮开关
		$zbp->Config('tpure')->PostSHARE = $_POST['PostSHARE'];				//文章分享按钮
        $zbp->Config('tpure')->PostARCHIVEINFOON = $_POST['PostARCHIVEINFOON']; //归档总数量
        $zbp->Config('tpure')->PostARCHIVEFOLDON = $_POST['PostARCHIVEFOLDON']; //自动折叠归档
        $zbp->Config('tpure')->PostAUTOARCHIVEON = $_POST['PostAUTOARCHIVEON'];	//自动文章归档
        $zbp->Config('tpure')->PostARCHIVEDATEON = $_POST['PostARCHIVEDATEON'];	//归档文章日期开关
        $zbp->Config('tpure')->PostARCHIVEDATETYPE = $_POST['PostARCHIVEDATETYPE'];	//归档文章日期类型
        $zbp->Config('tpure')->PostARCHIVEDATESORT = $_POST['PostARCHIVEDATESORT'];	//归档月份排序
        $zbp->Config('tpure')->PostFRIENDLINKON = $_POST['PostFRIENDLINKON'];           //首页友情链接开关
        $zbp->Config('tpure')->PostFRIENDLINKMON = $_POST['PostFRIENDLINKMON'];           //移动端友情链接开关
        $zbp->Config('tpure')->PostERRORTOPAGE = $_POST['PostERRORTOPAGE'];           //网站无权限时自动跳转自定义页面
        $zbp->Config('tpure')->PostCLOSESITEBG = $_POST['PostCLOSESITEBG'];           //网站关闭页面背景
        $zbp->Config('tpure')->PostCLOSESITEBGMASKON = $_POST['PostCLOSESITEBGMASKON'];   //网站关闭页面背景蒙版开关
        $zbp->Config('tpure')->PostCLOSESITETITLE = $_POST['PostCLOSESITETITLE'];           //网站关闭页面标题
        $zbp->Config('tpure')->PostCLOSESITECON = $_POST['PostCLOSESITECON'];           //网站关闭页面内容
        $zbp->Config('tpure')->PostSIGNON = $_POST['PostSIGNON'];           //导航自定义登录按钮开关
        $zbp->Config('tpure')->PostSIGNBTNTEXT = $_POST['PostSIGNBTNTEXT'];           //导航自定义登录按钮文字
        $zbp->Config('tpure')->PostSIGNBTNURL = $_POST['PostSIGNBTNURL'];           //导航自定义登录按钮链接
        $zbp->Config('tpure')->PostSIGNUSERSTYLE = $_POST['PostSIGNUSERSTYLE'];           //导航用户登录后样式[0:常规带下拉,1:精简仅头像]
        $zbp->Config('tpure')->PostSIGNUSERURL = $_POST['PostSIGNUSERURL'];           //导航用户头像链接跳转
        $zbp->Config('tpure')->PostSIGNUSERMENU = $_POST['PostSIGNUSERMENU'];           //导航用户下拉菜单
        $zbp->Config('tpure')->PostSITEMAPON = $_POST['PostSITEMAPON'];           //面包屑开关
        $zbp->Config('tpure')->PostSITEMAPSTYLE = $_POST['PostSITEMAPSTYLE'];           //面包屑尾巴
        $zbp->Config('tpure')->PostSITEMAPTXT = $_POST['PostSITEMAPTXT'];           //面包屑首页文字
        $zbp->Config('tpure')->PostZBAUDIOON = $_POST['PostZBAUDIOON'];           //音频播放器开关
        $zbp->Config('tpure')->PostVIDEOON = $_POST['PostVIDEOON'];           //视频播放器开关
        $zbp->Config('tpure')->PostMEDIAICONON = $_POST['PostMEDIAICONON'];           //列表标题媒体图标开关
        $zbp->Config('tpure')->PostMEDIAICONSTYLE = $_POST['PostMEDIAICONSTYLE'];           //列表标题媒体图标位置
        $zbp->Config('tpure')->PostREADERSNUM = $_POST['PostREADERSNUM'];           //读者墙页面读者个数限制
        $zbp->Config('tpure')->PostREADERSURLON = $_POST['PostREADERSURLON'];           //读者墙页面仅输出填写网址的读者
        $zbp->Config('tpure')->PostINTROSOURCE = $_POST['PostINTROSOURCE'];           //摘要调用方式
        $zbp->Config('tpure')->PostINTRONUM = (int)($_POST['PostINTRONUM']);           //摘要字数限制
        $zbp->Config('tpure')->PostBACKTOTOPON = $_POST['PostBACKTOTOPON'];     //返回顶部开关
        $zbp->Config('tpure')->PostBACKTOTOPVALUE = $_POST['PostBACKTOTOPVALUE'];     //返回顶部下拉距离
        $zbp->Config('tpure')->PostBLANKSTYLE = $_POST['PostBLANKSTYLE'];             //链接打开方式
        $zbp->Config('tpure')->PostLOGINON = $_POST['PostLOGINON'];				//主题自带登录样式开关
        $zbp->Config('tpure')->PostFILTERON = $_POST['PostFILTERON'];		//列表排序功能开关
        $zbp->Config('tpure')->PostMOREBTNON = $_POST['PostMOREBTNON'];             //列表查看全文按钮开关
        $zbp->Config('tpure')->PostBIGPOSTIMGON = $_POST['PostBIGPOSTIMGON'];				//放大列表缩略图开关
        $zbp->Config('tpure')->PostFIXMENUON = $_POST['PostFIXMENUON'];				//导航悬浮开关
        $zbp->Config('tpure')->PostFANCYBOXON = $_POST['PostFANCYBOXON'];				//图片灯箱开关
        $zbp->Config('tpure')->PostLAZYLOADON = $_POST['PostLAZYLOADON'];           //全局图片延时加载开关
        $zbp->Config('tpure')->PostLAZYLINEON = $_POST['PostLAZYLINEON'];           //顶部滚动进度条开关
        $zbp->Config('tpure')->PostLAZYNUMON = $_POST['PostLAZYNUMON'];           //底部滚动进度数开关
        $zbp->Config('tpure')->PostINDENTON = $_POST['PostINDENTON'];           //首行缩进开关
        $zbp->Config('tpure')->PostTAGSON = $_POST['PostTAGSON'];           //文章标签开关
        $zbp->Config('tpure')->PostPREVNEXTON = $_POST['PostPREVNEXTON'];           //文章上下页开关
        $zbp->Config('tpure')->PostCATEPREVNEXTON = $_POST['PostCATEPREVNEXTON'];           //文章上下页仅限本分类开关
        $zbp->Config('tpure')->PostTFONTSIZEON = $_POST['PostTFONTSIZEON'];           //正文字号控件开关
        $zbp->Config('tpure')->PostREMOVEPON = $_POST['PostREMOVEPON'];			//隐藏文章空段落开关
        $zbp->Config('tpure')->PostSELECTON = $_POST['PostSELECTON'];           //全局鼠标选中限制开关
        $zbp->Config('tpure')->PostCHECKDPION = $_POST['PostCHECKDPION'];           //分辨率提示开关
        $zbp->Config('tpure')->PostLANGON = $_POST['PostLANGON'];           //繁简体转换功能开关
        $zbp->SaveConfig('tpure');
        tpure_ArchiveAutoCache();
        tpure_delArchive();
        $zbp->BuildTemplate();
        tpure_CreateModule();
        $zbp->ShowHint('good');
    } ?>
<script type="text/javascript">
	var editor = new baidu.editor.ui.Editor({ toolbars:[['source','insertimage','Paragraph','FontFamily','FontSize','Bold','Italic','ForeColor', "backcolor", "link",'justifyleft','justifycenter','justifyright']],initialFrameHeight:100 });
	var closesite = new baidu.editor.ui.Editor({ toolbars:[['source','insertimage','Paragraph','FontFamily','FontSize','Bold','Italic','ForeColor', "backcolor", "link",'justifyleft','justifycenter','justifyright']],initialFrameHeight:100 });
	editor.render("PostCOPYNOTICE");
	closesite.render("PostCLOSESITECON");
</script>
<dl>
	<form method="post" class="setting">
		<input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
		<dt>基本设置</dt>
		<dd>
			<label for="PostLOGO">图片LOGO</label>
			<table>
				<tbody>
					<tr>
						<th width="25%">缩略图</th>
						<th width="35%">图片地址</th>
						<th width="15%">上传</th>
						<th width=20%>操作</th>
					</tr>
					<tr>
						<td style="position:relative;"><b>常规</b><?php if ($zbp->Config('tpure')->PostLOGO) { ?><img src="<?php echo $zbp->Config('tpure')->PostLOGO; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/logo.svg" width="120" class="thumbimg" /><?php } ?></td>
						<td><input type="text" id="PostLOGO" name="PostLOGO" value="<?php if ($zbp->Config('tpure')->PostLOGO) {
        echo $zbp->Config('tpure')->PostLOGO;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/logo.svg';
    } ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg format" value="上传"></td>
						<td rowspan="2"><br>是否启用 <input type="text" id="PostLOGOON" name="PostLOGOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLOGOON; ?>" /><br><hr>启用动效 <input type="text" id="PostLOGOHOVERON" name="PostLOGOHOVERON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLOGOHOVERON; ?>" /><br><br></td>
					</tr>
                    <tr>
                        <td style="background:#202020; position:relative;"><b>夜间</b><?php if ($zbp->Config('tpure')->PostNIGHTLOGO) { ?><img src="<?php echo $zbp->Config('tpure')->PostNIGHTLOGO; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/nightlogo.svg" width="120" class="thumbimg" /><?php } ?></td>
                        <td><input type="text" id="PostNIGHTLOGO" name="PostNIGHTLOGO" value="<?php if ($zbp->Config('tpure')->PostNIGHTLOGO) {
        echo $zbp->Config('tpure')->PostNIGHTLOGO;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/nightlogo.svg';
    } ?>" class="urltext thumbsrc"></td>
                        <td><input type="button" class="uploadimg format" value="上传"></td>
                    </tr>
				</tbody>
			</table>
			<i class="help"></i><span class="helpcon">上传图片LOGO，关闭图片LOGO则使用文字LOGO(调用网站名称)；<br>
				图片LOGO与后台登录页LOGO同步开启展示；<br>图片LOGO支持鼠标滑过显示高光特效，支持开启与关闭。</span>
		</dd>
		<dd>
			<label for="PostFAVICON">标签栏图标</label>
			<table>
				<tbody>
					<tr>
						<th width="25%">缩略图</th>
						<th width="35%">图片地址</th>
						<th width="15%">上传</th>
						<th width="20%">操作</th>
					</tr>
					<tr>
						<td><?php if ($zbp->Config('tpure')->PostFAVICON) { ?><img src="<?php echo $zbp->Config('tpure')->PostFAVICON; ?>" width="16" class="thumbimg" /><?php } else { ?><img src="style/images/favicon.ico" width="16" class="thumbimg" /><?php } ?></td>
						<td><input type="text" id="PostFAVICON" name="PostFAVICON" value="<?php if ($zbp->Config('tpure')->PostFAVICON) {
        echo $zbp->Config('tpure')->PostFAVICON;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/favicon.ico';
    } ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg format" value="上传"></td>
						<td>是否启用：<input type="text" id="PostFAVICONON" name="PostFAVICONON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFAVICONON; ?>" /></td>
					</tr>
				</tbody>
			</table>
			<i class="help"></i><span class="helpcon">浏览器标签栏图标最佳尺寸：16x16px的ICO格式图标。</span>
		</dd>
		<dd>
			<label for="PostTHUMB">缩略图设置</label>
			<table>
				<tbody>
					<tr>
						<th width="25%">默认缩略图</th>
						<th width="35%">图片地址</th>
						<th width="15%">上传</th>
						<th width="20%">操作</th>
					</tr>
					<tr>
						<td><?php if ($zbp->Config('tpure')->PostTHUMB) { ?><img src="<?php echo $zbp->Config('tpure')->PostTHUMB; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/thumb.png" width="120" class="thumbimg" /><?php } ?></td>
						<td><input type="text" id="PostTHUMB" name="PostTHUMB" value="<?php if ($zbp->Config('tpure')->PostTHUMB) {
        echo $zbp->Config('tpure')->PostTHUMB;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/thumb.png';
    } ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg format" value="上传"></td>
						<td>无图用默认 <input type="text" id="PostTHUMBON" name="PostTHUMBON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostTHUMBON; ?>" /><br>无图用随机 <input type="text" id="PostRANDTHUMBON" name="PostRANDTHUMBON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostRANDTHUMBON; ?>" /><br>仅显示有图 <input type="text" id="PostIMGON" name="PostIMGON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostIMGON; ?>" /><br>侧栏缩略图 <input type="text" id="PostSIDEIMGON" name="PostSIDEIMGON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSIDEIMGON; ?>" /><br>新版缩略图 <input type="text" id="PostTHUMBNEWON" name="PostTHUMBNEWON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostTHUMBNEWON; ?>" /><br></td>
					</tr>
				</tbody>
			</table>
			<i class="help"></i><span class="helpcon">使用默认缩略图，请开启“无图用默认”和“仅显示有图”开关；<br>使用随机缩略图，请开启“无图用随机”和“仅显示有图”开关；<br>仅显示文章缩略图，只需开启“仅显示有图”开关；<br>如不需要任何缩略图，请全部关闭；<br>侧栏模块缩略图继承以上设置。<br>Z-Blog1.7版可选择使用新版缩略图方式(1.6版本无效)。</span>
		</dd>
		<dd>
			<label for="PostBANNER">首页Banner</label>
			<table>
				<tbody>
					<tr>
						<th width="25%">缩略图</th>
						<th width="35%">图片地址</th>
						<th width="15%">上传</th>
						<th width="20%">操作</th>
					</tr>
					<tr>
						<td><img src="<?php echo $zbp->Config('tpure')->PostBANNER; ?>" width="120" class="thumbimg" /></td>
						<td><input type="text" id="PostBANNER" name="PostBANNER" value="<?php echo $zbp->Config('tpure')->PostBANNER; ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg format" value="上传"></td>
						<td>是否启用 <input type="text" id="PostBANNERON" name="PostBANNERON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBANNERON; ?>" /><br>视差滚动 <input type="text" id="PostBANNERDISPLAYON" name="PostBANNERDISPLAYON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBANNERDISPLAYON; ?>" /><br>全站展示 <input type="text" id="PostBANNERALLON" name="PostBANNERALLON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBANNERALLON; ?>" /><br></td>
					</tr>
					<tr>
						<th colspan="2">Banner文字内容</th>
						<th>电脑端高度</th>
						<th>移动端高度</th>
					</tr>
					<tr>
						<td colspan="2"><input type="text" id="PostBANNERFONT" name="PostBANNERFONT" value="<?php echo $zbp->Config('tpure')->PostBANNERFONT; ?>" class="urltext thumbsrc" style="width:390px;"></td>
						<td><input type="number" id="PostBANNERPCHEIGHT" name="PostBANNERPCHEIGHT" value="<?php echo $zbp->Config('tpure')->PostBANNERPCHEIGHT; ?>" style="width:80px;" /></td>
						<td><input type="number" id="PostBANNERMHEIGHT" name="PostBANNERMHEIGHT" value="<?php echo $zbp->Config('tpure')->PostBANNERMHEIGHT; ?>" style="width:80px;" /></td>
					</tr>
                    <tr>
                        <th colspan="2">热搜词组（关键词之间用竖线|分隔）</th>
                        <th>热搜标题</th>
                        <th>搜索开关</th>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="text" id="PostBANNERSEARCHWORDS" name="PostBANNERSEARCHWORDS" value="<?php echo $zbp->Config('tpure')->PostBANNERSEARCHWORDS; ?>" class="urltext" style="width:390px;"></td>
                        <td><input type="text" id="PostBANNERSEARCHLABEL" name="PostBANNERSEARCHLABEL" value="<?php echo $zbp->Config('tpure')->PostBANNERSEARCHLABEL; ?>" style="width:80px;" /></td>
                        <td><input type="text" id="PostBANNERSEARCHON" name="PostBANNERSEARCHON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBANNERSEARCHON; ?>" /></td>
                    </tr>
				</tbody>
			</table>
			<i class="help"></i><span class="helpcon">展示首页Banner，请将启用开关设置为"ON"；<br>展示视差滚动效果，请将视差开关设置为"ON"；<br>全站每页都显示，请将全站展示开关设置为"ON"；<br>支持PC与移动端独立设置高度；<br>Banner不需要文字请留空；<br>启用搜索时，PC或移动端高度小于150将不显示banner;</span>
		</dd>
		<dt>导航搜索设置</dt>
		<dd class="half">
			<label>导航搜索</label>
			<input type="text" name="PostSEARCHON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSEARCHON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示导航搜索；<br>“OFF”为关闭导航搜索。</span>
		</dd>
		<dd class="half">
			<label for="PostSCHTXT">搜索默认文字</label>
			<input type="text" id="PostSCHTXT" name="PostSCHTXT" value="<?php echo $zbp->Config('tpure')->PostSCHTXT; ?>" class="settext" />
			<i class="help"></i><span class="helpcon">导航搜索条中默认显示的文字</span>
		</dd>
		<dt>正文“阅读更多”设置</dt>
		<dd data-stretch="viewall">
			<label>阅读更多</label>
			<input type="text" id="PostVIEWALLON" name="PostVIEWALLON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostVIEWALLON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为启用长文章正文自动折叠；<br>“OFF”为加载全部正文。</span>
		</dd>
        <div class="viewallinfo"<?php echo $zbp->Config('tpure')->PostVIEWALLON == 1 ? '' : ' style="display:none"'; ?>>
		<dd class="half">
			<label for="PostVIEWALLHEIGHT">自动阅读高度</label>
			<input type="number" id="PostVIEWALLHEIGHT" name="PostVIEWALLHEIGHT" value="<?php echo $zbp->Config('tpure')->PostVIEWALLHEIGHT; ?>" min="1" step="1" class="settext" />
			<i class="help"></i><span class="helpcon">设置页面已读区域高度(单位px)。</span>
		</dd>
		<dd class="half">
			<label>阅读更多样式</label>
			<div class="layoutset">
				<input type="radio" id="viewallstyle1" name="PostVIEWALLSTYLE" value="1" <?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '1' ? 'checked="checked"' : ''; ?> class="hideradio" />
				<label for="viewallstyle1"<?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '1' ? ' class="on"' : ''; ?>><img src="style/images/viewallstyle1.png" alt="显示未读百分比" /></label>
				<input type="radio" id="viewallstyle0" name="PostVIEWALLSTYLE" value="0" <?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '0' ? 'checked="checked"' : ''; ?> class="hideradio" />
				<label for="viewallstyle0"<?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '0' ? ' class="on"' : ''; ?>><img src="style/images/viewallstyle0.png" alt="按钮样式" /></label>
			</div>
			<i class="help"></i><span class="helpcon">请设置“阅读更多”样式，默认为显示未读百分比。</span>
		</dd>
		<dd class="half">
			<label>文章阅读更多</label>
			<input type="text" id="PostVIEWALLSINGLEON" name="PostVIEWALLSINGLEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostVIEWALLSINGLEON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为启用文章页长文章正文自动折叠；<br>“OFF”为文章页加载全部正文。</span>
		</dd>
		<dd class="half">
			<label>页面阅读更多</label>
			<input type="text" id="PostVIEWALLPAGEON" name="PostVIEWALLPAGEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostVIEWALLPAGEON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为启用页面长文章正文自动折叠；<br>“OFF”为页面加载全部正文。</span>
		</dd>
        </div>
		<dt>列表页辅助信息设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
		<dd class="ckbox">
		<?php
        $post_info = array(
            'user' => '用户名称',
            'date' => '日期',
            'cate' => '分类名称',
            'view' => '浏览数',
            'cmt' => '评论数',
            'edit' => '编辑(仅管理可见)',
            'del' => '删除(仅管理可见)',
        );
    $list_info = json_decode($zbp->Config('tpure')->PostLISTINFO, true);
    if (count((array)$list_info) == 7) {
        foreach ($list_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $post_info[$key] . '<input name="post_list_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">用户名称<input name="post_list_info[user]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">日期<input name="post_list_info[date]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">分类名称<input name="post_list_info[cate]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">浏览数<input name="post_list_info[view]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">评论数<input name="post_list_info[cmt]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">编辑(仅管理可见)<input name="post_list_info[edit]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">删除(仅管理可见)<input name="post_list_info[del]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">列表页面辅助信息，自定义选择启用，支持拖拽排序；<br><em>仅支持默认和贴图列表页样式</em>。</span>
		</dd>
		<dt>文章页辅助信息设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
		<dd class="ckbox">
		<?php
        $article_info = json_decode($zbp->Config('tpure')->PostARTICLEINFO, true);
    if (count((array)$article_info) == 7) {
        foreach ($article_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $post_info[$key] . '<input name="post_article_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">用户名称<input name="post_article_info[user]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">日期<input name="post_article_info[date]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">分类名称<input name="post_article_info[cate]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">浏览数<input name="post_article_info[view]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">评论数<input name="post_article_info[cmt]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">编辑(仅管理可见)<input name="post_article_info[edit]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">删除(仅管理可见)<input name="post_article_info[del]" value="0"></div>';
    } ?>
			<i class="help"></i><span class="helpcon">文章页面辅助信息，自定义选择启用，支持拖拽排序。</span>
		</dd>
		<dt>独立页面辅助信息设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
		<dd class="ckbox">
		<?php
        $page_info = json_decode($zbp->Config('tpure')->PostPAGEINFO, true);
    if (count((array)$page_info) == 6) {
        foreach ($page_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $post_info[$key] . '<input name="post_page_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">用户名称<input name="post_page_info[user]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">日期<input name="post_page_info[date]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">浏览数<input name="post_page_info[view]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">评论数<input name="post_page_info[cmt]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">编辑(仅管理可见)<input name="post_page_info[edit]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">删除(仅管理可见)<input name="post_page_info[del]" value="0"></div>';
    } ?>
			<i class="help"></i><span class="helpcon">独立页面辅助信息，自定义选择启用，支持拖拽排序。</span>
		</dd>
		<dt>键盘左右键翻页设置</dt>
		<dd class="half">
			<label>文章左右翻页</label>
			<input type="text" id="PostSINGLEKEY" name="PostSINGLEKEY" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSINGLEKEY; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为开启文章上一篇与下一篇左右键翻页；<br>“OFF”为关闭文章上一篇与下一篇左右键翻页；<br><em>文章中存在视频时，左右键优先控制视频进度不进行翻页。</em></span>
		</dd>
		<dd class="half">
			<label>列表左右翻页</label>
			<input type="text" id="PostPAGEKEY" name="PostPAGEKEY" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostPAGEKEY; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为开启列表底部分页条左右键翻页；<br>“OFF”为关闭列表底部分页条左右键翻页。</span>
		</dd>
		<dt>相关文章设置</dt>
		<dd data-stretch="relate" class="half">
			<label>相关文章</label>
			<input type="text" id="PostRELATEON" name="PostRELATEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostRELATEON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示文章页相关文章；<br>“OFF”为文章页不加载相关文章。</span>
		</dd>
        <div class="relateinfo"<?php echo $zbp->Config('tpure')->PostRELATEON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label>文章标题相关</label>
            <input type="text" id="PostRELATETITLE" name="PostRELATETITLE" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostRELATETITLE; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为相关文章模块标题前追加文章标题，如：<br>XXXX的相关文章；<br>“OFF”为相关文章模块标题仅显示“相关文章”四字。</span>
        </dd>
		<dd class="half">
			<label>仅限本分类</label>
			<input type="text" id="PostRELATECATE" name="PostRELATECATE" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostRELATECATE; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为仅显示文章页所在分类下的随机文章；<br>“OFF”为显示含有同标签的随机文章(不限分类)。</span>
		</dd>
        <dd class="half">
            <label for="PostRELATENUM">相关文章条数</label>
            <input type="number" id="PostRELATENUM" name="PostRELATENUM" value="<?php echo $zbp->Config('tpure')->PostRELATENUM; ?>" min="1" step="1" class="settext" />
            <i class="help"></i><span class="helpcon">设置文章页相关文章条数，默认6条。</span>
        </dd>
		<dd class="half">
            <label for="PostRELATESTYLE">相关文章版式</label>
            <select size="1" name="PostRELATESTYLE" id="PostRELATESTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostRELATESTYLE == '0'){echo ' selected="selected"';}?>>图文版</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostRELATESTYLE == '1'){echo ' selected="selected"';}?>>精简版</option>
            </select>
            <i class="help"></i><span class="helpcon">设置相关文章展现形式，默认为图文版。</span>
        </dd>
        <div class="relatestyleinfo"<?php echo $zbp->Config('tpure')->PostRELATESTYLE == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label for="PostRELATEDIALLEL">精简版列数</label>
            <select size="1" name="PostRELATEDIALLEL" id="PostRELATEDIALLEL">
                <option value="0"<?php if($zbp->Config('tpure')->PostRELATEDIALLEL == '0'){echo ' selected="selected"';}?>>单列</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostRELATEDIALLEL == '1'){echo ' selected="selected"';}?>>双列</option>
            </select>
            <i class="help"></i><span class="helpcon">设置相关文章精简版展示列数，默认为单列。</span>
        </dd>
        </div>
    	</div>
        <dt>列表滚动加载设置</dt>
        <dd data-stretch="ajax" class="half">
            <label>列表滚动加载</label>
            <input type="text" id="PostAJAXON" name="PostAJAXON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostAJAXON;?>" />
            <i class="help"></i><span class="helpcon">“ON”为滚动列表底部自动加载下一页；<br>“OFF”为手动点击翻页。</span>
        </dd>
        <div class="ajaxinfo"<?php echo $zbp->Config('tpure')->PostAJAXON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label for="PostLOADPAGENUM">自动加载页数</label>
            <input type="number" id="PostLOADPAGENUM" name="PostLOADPAGENUM" value="<?php echo $zbp->Config('tpure')->PostLOADPAGENUM;?>" min="1" step="1" class="settext" />
            <i class="help"></i><span class="helpcon">需启用列表滚动加载，设置自动加载N页后手动加载下一页。</span>
        </dd>
        </div>
        <dt>独立评论设置</dt>
        <dd class="half">
            <label>文章评论</label>
            <input type="text" id="PostARTICLECMTON" name="PostARTICLECMTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostARTICLECMTON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为显示文章（article）评论功能；<br>“OFF”为关闭文章（article）评论功能。</span>
        </dd>
        <dd class="half">
            <label>页面评论</label>
            <input type="text" id="PostPAGECMTON" name="PostPAGECMTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostPAGECMTON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为显示页面（page）评论功能；<br>“OFF”为关闭页面（page）评论功能。</span>
        </dd>
        <dd class="half">
            <label>评论邮箱</label>
            <input type="text" id="PostCMTMAILON" name="PostCMTMAILON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCMTMAILON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为显示评论表单中邮箱文本框；<br>“OFF”为关闭评论表单中邮箱文本框；<br>若关闭邮箱，则评论无法使用QQ或gravatar头像。<br><em>注：输入邮箱会自动显示符合类型的头像；</em></span>
        </dd>
        <dd class="half">
            <label>评论网址</label>
            <input type="text" id="PostCMTSITEON" name="PostCMTSITEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCMTSITEON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为显示评论表单中网址文本框；<br>“OFF”为关闭评论表单中网址文本框；<br><em>注：网址在评论头像 / 姓名和读者墙中可点击。</em></span>
        </dd>
        <dd class="half">
            <label>登录后可评论</label>
            <input type="text" id="PostCMTLOGINON" name="PostCMTLOGINON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCMTLOGINON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为仅在登录后可发表评论；<br>“OFF”为无需登录即可评论；<br><em>适用于第三方用户插件注册登录后评论。</em></span>
        </dd>
        <dd class="half">
            <label>评论者IP归属</label>
            <input type="text" id="PostCMTIPON" name="PostCMTIPON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCMTIPON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为文章页面评论模块显示评论者IP归属；<br>“OFF”为文章页面评论模块不显示评论者IP归属。</span>
        </dd>
        <dd>
            <label for="VerifyCode">验证码字符集</label>
            <input type="text" id="VerifyCode" name="VerifyCode" value="<?php echo $zbp->Config('tpure')->VerifyCode; ?>" class="settext" />
            <i class="help"></i><span class="helpcon">自定义zblog验证码出现的字符集，不区分大小写。</span>
        </dd>
        <dt>列表页样式设置</dt>
        <dd class="half">
            <label for="PostINDEXSTYLE">首页列表样式</label>
            <select size="1" name="PostINDEXSTYLE" id="PostINDEXSTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostINDEXSTYLE == '0'){echo ' selected="selected"';}?>>默认样式</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostINDEXSTYLE == '1'){echo ' selected="selected"';}?>>社区样式</option>
                <option value="2"<?php if($zbp->Config('tpure')->PostINDEXSTYLE == '2'){echo ' selected="selected"';}?>>图集样式</option>
                <option value="3"<?php if($zbp->Config('tpure')->PostINDEXSTYLE == '3'){echo ' selected="selected"';}?>>贴纸样式</option>
                <option value="4"<?php if($zbp->Config('tpure')->PostINDEXSTYLE == '4'){echo ' selected="selected"';}?>>热点样式</option>
            </select>
            <i class="help"></i><span class="helpcon">设置首页列表展现形式，默认为默认样式。</span>
        </dd>
        <dd class="half">
            <label for="PostSEARCHSTYLE">搜索列表样式</label>
            <select size="1" name="PostSEARCHSTYLE" id="PostSEARCHSTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostSEARCHSTYLE == '0'){echo ' selected="selected"';}?>>默认样式</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostSEARCHSTYLE == '1'){echo ' selected="selected"';}?>>社区样式</option>
                <option value="2"<?php if($zbp->Config('tpure')->PostSEARCHSTYLE == '2'){echo ' selected="selected"';}?>>图集样式</option>
                <option value="3"<?php if($zbp->Config('tpure')->PostSEARCHSTYLE == '3'){echo ' selected="selected"';}?>>贴纸样式</option>
                <option value="4"<?php if($zbp->Config('tpure')->PostSEARCHSTYLE == '4'){echo ' selected="selected"';}?>>热点样式</option>
            </select>
            <i class="help"></i><span class="helpcon">设置搜索列表展现形式，默认为默认样式。</span>
        </dd>
        <dt>首页过滤分类文章设置</dt>
        <dd class="half">
            <label for="PostFILTERCATEGORY">首页过滤分类</label>
            <select size="1" name="PostFILTERCATEGORY" id="PostFILTERCATEGORY"><?php echo tpure_Exclude_CategorySelect($zbp->Config('tpure')->PostFILTERCATEGORY); ?></select>
            <i class="help"></i><span class="helpcon">设置首页不显示此分类下的文章。</span>
        </dd>
        <dd class="half<?php if ($zbp->Config('tpure')->PostFILTERCATEGORY != '0') {
        echo " hide";
    } ?>">
            <label for="PostFILTERCATEGORYID">过滤的分类ID</label>
            <input type="text" id="PostFILTERCATEGORYID"<?php if ($zbp->Config('tpure')->PostFILTERCATEGORY == '0') {
        echo ' name="PostFILTERCATEGORY"';
    } ?> value="<?php echo $zbp->Config('tpure')->PostFILTERCATEGORY; ?>" placeholder="不过滤请留空" class="PostFILTERCATEGORY settext" />
            <i class="help"></i><span class="helpcon">请填写首页需要屏蔽的分类ID，<br>多个分类ID之间用英文逗号分隔，首尾不加逗号，<br>不限个数及顺序，留空提交则不过滤。<br><em>分类ID在左侧分类管理中查看。</em></span>
        </dd>
        <dt>置顶文章设置</dt>
        <dd class="half">
            <label>精简置顶</label>
            <input type="text" id="PostISTOPSIMPLEON" name="PostISTOPSIMPLEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostISTOPSIMPLEON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为不显示置顶缩略图、摘要及查看全文按钮；<br>“OFF”为常规列表显示；<br>仅针对首页默认列表样式生效。</span>
        </dd>
        <dd class="half">
            <label>仅第一页置顶</label>
            <input type="text" id="PostISTOPINDEXON" name="PostISTOPINDEXON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostISTOPINDEXON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为置顶文章仅出现在首页和列表第一页；<br>“OFF”为每个分页都置顶(常规)。</span>
        </dd>
        <dt>页面变灰设置</dt>
        <dd data-stretch="grey">
            <label>页面变灰</label>
            <input type="text" id="PostGREYON" name="PostGREYON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostGREYON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为开启变灰效果；<br>“OFF”为关闭变灰效果。</span>
        </dd>
        <div class="greyinfo"<?php echo $zbp->Config('tpure')->PostGREYON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label for="PostGREYSTATE">变灰范围</label>
            <select size="1" name="PostGREYSTATE" id="PostGREYSTATE">
                <option value="0"<?php if($zbp->Config('tpure')->PostGREYSTATE == '0'){echo ' selected="selected"';}?>>首页变灰</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostGREYSTATE == '1'){echo ' selected="selected"';}?>>整站变灰</option>
            </select>
            <i class="help"></i><span class="helpcon">设置页面变灰范围，支持仅首页或全部页面变灰。</span>
        </dd>
        <dd class="half">
            <label for="PostGREYDAY">仅该日期变灰</label>
            <input type="text" id="PostGREYDAY" name="PostGREYDAY" placeholder="设置仅该日期当天变灰，不限制日期请留空" value="<?php echo $zbp->Config('tpure')->PostGREYDAY; ?>" class="selectdate settext" />
            <i class="help"></i><span class="helpcon">设置仅该日期当天变灰，如不限制日期请留空，需开启“页面变灰”开关。</span>
        </dd>
        </div>
        <dt>网站开关灯设置</dt>
        <dd class="half">
            <label>开关灯</label>
            <input type="text" id="PostSETNIGHTON" name="PostSETNIGHTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSETNIGHTON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用开关灯功能；<br>“OFF”为关闭开关灯功能。</span>
        </dd>
        <dd data-stretch="setnightauto" class="half">
            <label>自动开关灯</label>
            <input type="text" id="PostSETNIGHTAUTOON" name="PostSETNIGHTAUTOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSETNIGHTAUTOON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为网站在设置时间段自动开关灯；<br>“OFF”为关闭自动开关灯。</span>
        </dd>
        <div class="setnightautoinfo"<?php echo $zbp->Config('tpure')->PostSETNIGHTAUTOON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label for="PostSETNIGHTSTART">关灯开始时间</label>
            <select size="1" name="PostSETNIGHTSTART" id="PostSETNIGHTSTART">
                <?php
                $nightstr = '';
                $nightstart = $zbp->Config('tpure')->PostSETNIGHTSTART;
                for($i=1; $i<24; $i++){
                    $nightstartselect = $nightstart == $i ? 'selected="selected"' : '';
                    $nightstr .= '<option value="'.$i.'" '.$nightstartselect.'>'.$i.':00:00</option>';
                }
                echo $nightstr;
                ?>
            </select>
            <i class="help"></i><span class="helpcon">设置页面关灯开始的小时数。</span>
        </dd>
        <dd class="half">
            <label for="PostSETNIGHTOVER">关灯结束时间</label>
            <select size="1" name="PostSETNIGHTOVER" id="PostSETNIGHTOVER">
                <?php
                $nightstr = '';
                $nightstart = $zbp->Config('tpure')->PostSETNIGHTOVER;
                for($i=1; $i<24; $i++){
                    $nightstartselect = $nightstart == $i ? 'selected="selected"' : '';
                    $nightstr .= '<option value="'.$i.'" '.$nightstartselect.'>'.$i.':00:00</option>';
                }
                echo $nightstr;
                ?>
            </select>
            <i class="help"></i><span class="helpcon">设置页面关灯截止的小时数。</span>
        </dd>
        </div>
        <dt>日期时间设置</dt>
        <dd class="half">
            <label for="PostTIMESTYLE">日期时间样式</label>
            <select size="1" name="PostTIMESTYLE" id="PostTIMESTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostTIMESTYLE == '0'){echo ' selected="selected"';}?>>友好化时间</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostTIMESTYLE == '1'){echo ' selected="selected"';}?>>传统时间</option>
            </select>
            <i class="help"></i><span class="helpcon">设置日期时间样式。</span>
        </dd>
        <div class="timestyleinfo"<?php echo $zbp->Config('tpure')->PostTIMESTYLE == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label for="PostTIMEFORMAT">传统时间格式</label>
            <select size="1" name="PostTIMEFORMAT" id="PostTIMEFORMAT">
                <option value="0"<?php if($zbp->Config('tpure')->PostTIMEFORMAT == '0'){echo ' selected="selected"';}?>><?php echo date('Y-m-d',time());?></option>
                <option value="1"<?php if($zbp->Config('tpure')->PostTIMEFORMAT == '1'){echo ' selected="selected"';}?>><?php echo date('Y-m-d H:i',time());?></option>
                <option value="2"<?php if($zbp->Config('tpure')->PostTIMEFORMAT == '2'){echo ' selected="selected"';}?>><?php echo date('Y-m-d H:i:s',time());?></option>
                <option value="3"<?php if($zbp->Config('tpure')->PostTIMEFORMAT == '3'){echo ' selected="selected"';}?>><?php echo date('Y年m月d日',time());?></option>
                <option value="4"<?php if($zbp->Config('tpure')->PostTIMEFORMAT == '4'){echo ' selected="selected"';}?>><?php echo date('Y年m月d日 H:i',time());?></option>
                <option value="5"<?php if($zbp->Config('tpure')->PostTIMEFORMAT == '5'){echo ' selected="selected"';}?>><?php echo date('Y年m月d日 H:i:s',time());?></option>
            </select>
            <i class="help"></i><span class="helpcon">设置传统时间格式；<br>关闭友好化时间时采用传统时间。</span>
        </dd>
        </div>
        <dt>文章版权声明设置</dt>
        <dd data-stretch="copynotice" class="half">
			<label>版权声明</label>
			<input type="text" id="PostCOPYNOTICEON" name="PostCOPYNOTICEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCOPYNOTICEON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示文章页版权声明；<br>“OFF”为关闭文章页版权声明；<br><em>注：版权声明仅文章页显示。</em></span>
		</dd>
        <div class="copynoticeinfo"<?php echo $zbp->Config('tpure')->PostCOPYNOTICEON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label>移动端关闭</label>
            <input type="text" id="PostCOPYNOTICEMOBILEON" name="PostCOPYNOTICEMOBILEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCOPYNOTICEMOBILEON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为移动端关闭文章版权声明；<br>“OFF”为PC和手机端均显示文章页版权声明；</span>
        </dd>
		<dd class="half">
			<label>本页链接地址</label>
			<input type="text" id="PostCOPYURLON" name="PostCOPYURLON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCOPYURLON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示版权声明当前页面链接地址；<br>“OFF”为关闭版权声明当前页面链接地址；</span>
		</dd>
		<dd class="half">
			<label>二维码功能</label>
			<input type="text" id="PostQRON" name="PostQRON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostQRON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为开启二维码功能；<br>“OFF”为关闭二维码功能；</span>
		</dd>
		<dd>
			<label for="PostQRSIZE">二维码尺寸</label>
			<input type="number" id="PostQRSIZE" name="PostQRSIZE" value="<?php echo $zbp->Config('tpure')->PostQRSIZE; ?>" min="70" step="10" class="settext" />
			<i class="help"></i><span class="helpcon">设置二维码图片尺寸，默认70(单位px)。</span>
		</dd>
		<dd>
			<label for="PostCOPYNOTICE">声明内容<br></label>
			<textarea name="PostCOPYNOTICE" id="PostCOPYNOTICE" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->PostCOPYNOTICE;?></textarea>
			<i class="help"></i><span class="helpcon">请填写页面版权声明内容，支持html。</span>
		</dd>
        </div>
        <dt>文章页面分享设置</dt>
		<dd class="half">
			<label>文章分享</label>
			<input type="text" id="PostSHAREARTICLEON" name="PostSHAREARTICLEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSHAREARTICLEON;?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示文章分享功能；<br>“OFF”为关闭文章分享功能。</span>
		</dd>
		<dd class="half">
			<label>页面分享</label>
			<input type="text" id="PostSHAREPAGEON" name="PostSHAREPAGEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSHAREPAGEON;?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示独立页面分享功能；<br>“OFF”为关闭独立页面分享功能。</span>
		</dd>
		<dd>
			<label for="PostSHARE">分享按钮代码<br><a href="https://www.toyean.com/help.html" target="_blank" class="tips">参考说明</a></label>
			<textarea name="PostSHARE" id="PostSHARE" cols="30" rows="5" class="setinput"><?php echo $zbp->Config('tpure')->PostSHARE;?></textarea>
			<i class="help"></i><span class="helpcon">请填写分享代码，可点击左侧“参考说明”获取相关分享代码；<br>class属性值均为小写字母。</span>
		</dd>
        <dt>文章归档设置 <span>文章超过1000篇不建议使用此功能</span></dt>
        <dd class="half">
            <label>折叠控件</label>
            <input type="text" id="PostARCHIVEINFOON" name="PostARCHIVEINFOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostARCHIVEINFOON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为归档页面显示文章总数量和折叠控件；<br>“OFF”为不显示文章总数量和折叠控件。</span>
        </dd>
        <dd class="half">
            <label>折叠历史归档</label>
            <input type="text" id="PostARCHIVEFOLDON" name="PostARCHIVEFOLDON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostARCHIVEFOLDON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为展示当月归档，并自动折叠历史月份归档；<br>“OFF”为不折叠历史月份归档。</span>
        </dd>
        <dd class="half">
			<label>自动缓存归档</label>
			<input type="text" id="PostAUTOARCHIVEON" name="PostAUTOARCHIVEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostAUTOARCHIVEON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为修改或删除文章时自动缓存归档；<br>网站文章数量超过1000篇不建议开启；<br>“OFF”时仅读取缓存归档(无归档时自动创建)。</span>
		</dd>
		<dd class="half">
			<label>归档文章日期</label>
			<input type="text" id="PostARCHIVEDATEON" name="PostARCHIVEDATEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostARCHIVEDATEON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示归档文章前的日期；<br>“OFF”为不显示归档文章前的日期。</span>
		</dd>
		<dd class="half">
            <label for="PostARCHIVEDATETYPE">文章日期格式</label>
            <select size="1" name="PostARCHIVEDATETYPE" id="PostARCHIVEDATETYPE">
                <option value="0"<?php if($zbp->Config('tpure')->PostARCHIVEDATETYPE == '0'){echo ' selected="selected"';}?>>[<?php echo date('m/d');?>]</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostARCHIVEDATETYPE == '1'){echo ' selected="selected"';}?>>[<?php echo date('m月d日');?>]</option>
            </select>
            <i class="help"></i><span class="helpcon">设置文章归档模板日期排序，默认为日期降序。</span>
        </dd>
		<dd class="half">
            <label for="PostARCHIVEDATESORT">归档月份排序</label>
            <select size="1" name="PostARCHIVEDATESORT" id="PostARCHIVEDATESORT">
                <option value="DESC"<?php if($zbp->Config('tpure')->PostARCHIVEDATESORT == 'DESC'){echo ' selected="selected"';}?>>年月降序</option>
                <option value="ASC"<?php if($zbp->Config('tpure')->PostARCHIVEDATESORT == 'ASC'){echo ' selected="selected"';}?>>年月升序</option>
            </select>
            <i class="help"></i><span class="helpcon">设置文章归档模板年月排序，默认为年月降序。</span>
        </dd>
        <dt>首页友情链接设置</dt>
        <dd data-stretch="friendlink" class="half">
			<label>友情链接开关</label>
			<input type="text" id="PostFRIENDLINKON" name="PostFRIENDLINKON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFRIENDLINKON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示首页底部友情链接模块；<br>“OFF”为关闭首页底部友情链接模块。</span>
		</dd>
        <div class="friendlinkinfo"<?php echo $zbp->Config('tpure')->PostFRIENDLINKON == 1 ? '' : ' style="display:none"'; ?>>
		<dd class="half">
			<label>移动端展示</label>
			<input type="text" id="PostFRIENDLINKMON" name="PostFRIENDLINKMON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFRIENDLINKMON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为移动端显示友情链接；<br>“OFF”为移动端不显示友情链接。</span>
		</dd>
        </div>
		<dt>网站无权限时自动跳转至以下地址</dt>
		<dd>
			<label for="PostERRORTOPAGE">跳转网址</label>
			<input type="text" id="PostERRORTOPAGE" name="PostERRORTOPAGE" value="<?php echo $zbp->Config('tpure')->PostERRORTOPAGE; ?>" placeholder="http(s)://" class="settext" />
			<i class="help"></i><span class="helpcon">网站无权限时自动跳转到该地址，留空时默认跳转管理登录页面。</span>
		</dd>
		<dt>网站关站设置 <span>（使用此功能请在"网站设置" - "全局设置"中关闭网站）</span></dt>
		<dd>
			<label for="PostCLOSESITEBG">关站背景图</label>
			<table>
				<tbody>
					<tr>
						<th width="25%">缩略图</th>
						<th width="35%">图片地址</th>
						<th width="15%">上传</th>
						<th width=20%>操作</th>
					</tr>
					<tr>
						<td><?php if ($zbp->Config('tpure')->PostCLOSESITEBG) { ?><img src="<?php echo $zbp->Config('tpure')->PostCLOSESITEBG; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/banner.jpg" width="120" class="thumbimg" /><?php } ?></td>
						<td><input type="text" id="PostCLOSESITEBG" name="PostCLOSESITEBG" value="<?php if ($zbp->Config('tpure')->PostCLOSESITEBG) { echo $zbp->Config('tpure')->PostCLOSESITEBG; } else { echo $zbp->host . 'zb_users/theme/tpure/style/images/banner.jpg'; } ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg format" value="上传"></td>
						<td><br>启用蒙版 <input type="text" id="PostCLOSESITEBGMASKON" name="PostCLOSESITEBGMASKON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCLOSESITEBGMASKON; ?>" /><br><br></td>
					</tr>
				</tbody>
			</table>
			<i class="help"></i><span class="helpcon">上传网站关站页面背景图片，可设置灰色透明蒙版；<br>留空则显示默认背景色，背景色可在“色彩设置”中设置。</span>
		</dd>
		<dd>
			<label for="PostCLOSESITETITLE">关站标题</label>
			<input type="text" id="PostCLOSESITETITLE" name="PostCLOSESITETITLE" value="<?php echo $zbp->Config('tpure')->PostCLOSESITETITLE; ?>" class="settext" />
			<i class="help"></i><span class="helpcon">请填写网站关站标题文字。</span>
		</dd>
		<dd>
			<label for="PostCLOSESITECON">关站公告<br></label>
			<textarea name="PostCLOSESITECON" id="PostCLOSESITECON" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->PostCLOSESITECON;?></textarea>
			<i class="help"></i><span class="helpcon">请填写网站关站公告内容，支持html。</span>
		</dd>
        <dt>导航登录控件设置 <span>（此功能请配合其他相关用户中心插件使用）</span></dt>
        <dd data-stretch="sign">
            <label>导航登录控件</label>
            <input type="text" id="PostSIGNON" name="PostSIGNON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSIGNON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为开启导航登录控件；<br>开启时，登录前显示登录按钮，登录后显示头像和用户名；<br>“OFF”为关闭导航登录控件。</span>
        </dd>
        <div class="signinfo"<?php echo $zbp->Config('tpure')->PostSIGNON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label for="PostSIGNBTNTEXT">登录按钮文字</label>
            <input type="text" id="PostSIGNBTNTEXT" name="PostSIGNBTNTEXT" value="<?php echo $zbp->Config('tpure')->PostSIGNBTNTEXT; ?>" class="settext" />
            <i class="help"></i><span class="helpcon">请填写登录按钮文字，默认为："登录/注册"；<br>未登录时显示此按钮。</span>
        </dd>
        <dd class="half">
            <label for="PostSIGNBTNURL">登录按钮链接</label>
            <input type="text" id="PostSIGNBTNURL" name="PostSIGNBTNURL" value="<?php echo $zbp->Config('tpure')->PostSIGNBTNURL; ?>" class="settext" />
            <i class="help"></i><span class="helpcon">请填写点击登录按钮跳转的链接地址，留空默认为zblog登录地址。</span>
        </dd>
        <dd class="half">
            <label for="PostSIGNUSERSTYLE">登录后样式</label>
            <select size="1" name="PostSIGNUSERSTYLE" id="PostSIGNUSERSTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostSIGNUSERSTYLE == '0'){echo ' selected="selected"';}?>>常规版</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostSIGNUSERSTYLE == '1'){echo ' selected="selected"';}?>>精简版</option>
            </select>
            <i class="help"></i><span class="helpcon">请选择登录后的导航用户控件样式；<br>常规版为头像、用户名和下拉菜单；<br>精简版仅显示头像，无下拉菜单。</span>
        </dd>
        <dd class="half">
            <label for="PostSIGNUSERURL">用户主页链接</label>
            <input type="text" id="PostSIGNUSERURL" name="PostSIGNUSERURL" value="<?php echo $zbp->Config('tpure')->PostSIGNUSERURL; ?>" class="settext" />
            <i class="help"></i><span class="helpcon">请填写点击用户头像跳转的链接地址。</span>
        </dd>
        <dd>
            <label for="PostSIGNUSERMENU">用户菜单代码<br><a href="https://www.toyean.com/help.html" target="_blank" class="tips">参考说明</a></label>
            <textarea name="PostSIGNUSERMENU" id="PostSIGNUSERMENU" cols="30" rows="5" class="setinput"><?php echo $zbp->Config('tpure')->PostSIGNUSERMENU;?></textarea>
            <i class="help"></i><span class="helpcon">请填写导航用户控件自定义下拉菜单代码，格式为：&lt;a href="链接">菜单文字&lt;/a></span>
        </dd>
        </div>
        <dt>面包屑导航设置</dt>
        <dd data-stretch="sitemap">
            <label>面包屑导航</label>
            <input type="text" id="PostSITEMAPON" name="PostSITEMAPON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSITEMAPON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用面包屑导航；<br>“OFF”为关闭面包屑导航。</span>
        </dd>
        <div class="sitemapinfo"<?php echo $zbp->Config('tpure')->PostSITEMAPON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label>面包屑尾巴</label>
            <select size="1" name="PostSITEMAPSTYLE" id="PostSITEMAPSTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostSITEMAPSTYLE == '0'){echo ' selected="selected"';}?>>"正文内容"尾巴</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostSITEMAPSTYLE == '1'){echo ' selected="selected"';}?>>"文章标题"尾巴</option>
            </select>
            <i class="help"></i><span class="helpcon">设置面包屑尾巴；"正文内容"尾巴显示“正文内容”四个字，可在语言包修改文字；<br>"文章标题"尾巴显示文章标题。</span>
        </dd>
        <dd class="half">
            <label for="PostSITEMAPTXT">面包屑首页字</label>
            <input type="text" id="PostSITEMAPTXT" name="PostSITEMAPTXT" value="<?php echo $zbp->Config('tpure')->PostSITEMAPTXT; ?>" class="settext" />
            <i class="help"></i><span class="helpcon">请填写面包屑首页自定义文字；如：<br><em>当前位置：<b>面包屑首页字</b> > 分类名 > 文章标题</em></span>
        </dd>
        </div>
        <dt>音视频媒体设置</dt>
        <dd class="half">
            <label>音频播放器</label>
            <input type="text" id="PostZBAUDIOON" name="PostZBAUDIOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostZBAUDIOON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用（文章 / 页面）音频播放器及相关设置；<br>“OFF”为关闭音频播放器。<br><em>注：启用后，新建或编辑（文章 / 页面）时显示音频相关设置。</em></span>
        </dd>
        <dd class="half">
            <label>视频播放器</label>
            <input type="text" id="PostVIDEOON" name="PostVIDEOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostVIDEOON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用（文章 / 页面）视频播放器及相关设置；<br>“OFF”为关闭视频播放器。<br><em>注：启用后，新建或编辑（文章 / 页面）时显示视频相关设置。</em></span>
        </dd>
        <dd data-stretch="mediaicon" class="half">
            <label>媒体图标</label>
            <input type="text" id="PostMEDIAICONON" name="PostMEDIAICONON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostMEDIAICONON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为列表文章标题显示媒体图标；<br>“OFF”为列表文章标题不显示媒体图标。</span>
        </dd>
        <div class="mediaiconinfo"<?php echo $zbp->Config('tpure')->PostMEDIAICONON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half">
            <label>媒体图标位置</label>
            <select size="1" name="PostMEDIAICONSTYLE" id="PostMEDIAICONSTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostMEDIAICONSTYLE == '0'){echo ' selected="selected"';}?>>在文章标题前显示媒体图标</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostMEDIAICONSTYLE == '1'){echo ' selected="selected"';}?>>在文章标题后显示媒体图标</option>
            </select>
            <i class="help"></i><span class="helpcon">文章中添加自定义音频或视频时，设置媒体图标在列表文章标题显示的前后位置。</span>
        </dd>
        </div>
        <dt>读者墙页面设置 <span>（readers模板）</span></dt>
        <dd class="half">
            <label>最大读者数量</label>
            <input type="number" id="PostREADERSNUM" name="PostREADERSNUM" value="<?php echo $zbp->Config('tpure')->PostREADERSNUM; ?>" min="1" step="1" class="settext" />
            <i class="help"></i><span class="helpcon">设置读者墙页面最大输出读者的数量，默认为100。<br><em>注：审核中的评论不参与读者墙。</em></span>
        </dd>
        <dd class="half">
            <label>筛选网址读者</label>
            <input type="text" id="PostREADERSURLON" name="PostREADERSURLON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostREADERSURLON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为筛选评论中填写网址的读者进行输出；<br>“OFF”为读者无论是否填写网址都进行输出。</span>
        </dd>
        <dt>列表摘要设置</dt>
        <dd class="half">
            <label for="PostINTROSOURCE">摘要来源</label>
            <select size="1" name="PostINTROSOURCE" id="PostINTROSOURCE">
                <option value="0"<?php if($zbp->Config('tpure')->PostINTROSOURCE == '0'){echo ' selected="selected"';}?>>调用摘要内容</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostINTROSOURCE == '1'){echo ' selected="selected"';}?>>调用正文内容</option>
            </select>
            <i class="help"></i><span class="helpcon">选择列表摘要来源，默认调用摘要内容。</span>
        </dd>
        <dd class="half">
            <label for="PostINTRONUM">摘要字数</label>
            <input type="number" id="PostINTRONUM" name="PostINTRONUM" value="<?php echo $zbp->Config('tpure')->PostINTRONUM; ?>" step="10" class="settext" />
            <i class="help"></i><span class="helpcon">列表摘要字数限制，留空则显示系统摘要。</span>
        </dd>
        <dt>返回顶部设置</dt>
        <dd data-stretch="backtotop" class="half">
            <label>返回顶部</label>
            <input type="text" id="PostBACKTOTOPON" name="PostBACKTOTOPON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBACKTOTOPON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用"返回顶部"功能；<br>“OFF”为取消"返回顶部"功能。</span>
        </dd>
        <div class="backtotopinfo"<?php echo $zbp->Config('tpure')->PostBACKTOTOPON == 1 ? '' : ' style="display:none"'; ?>>
            <dd class="half">
                <label for="PostBACKTOTOPVALUE">下拉距离</label>
                <input type="number" id="PostBACKTOTOPVALUE" name="PostBACKTOTOPVALUE" value="<?php echo $zbp->Config('tpure')->PostBACKTOTOPVALUE; ?>" class="settext" />
                <i class="help"></i><span class="helpcon">设置页面向下滚动指定距离时，显示返回顶部按钮；<br><em>设置为0时始终显示返回顶部按钮。</em></span>
            </dd>
        </div>
		<dt>其他设置</dt>
        <dd class="half">
            <label>链接打开方式</label>
            <select size="1" name="PostBLANKSTYLE" id="PostBLANKSTYLE">
                <option value="0"<?php if($zbp->Config('tpure')->PostBLANKSTYLE == '0'){echo ' selected="selected"';}?>>站内链接当前窗口打开</option>
                <option value="1"<?php if($zbp->Config('tpure')->PostBLANKSTYLE == '1'){echo ' selected="selected"';}?>>全部链接新窗口打开</option>
                <option value="2"<?php if($zbp->Config('tpure')->PostBLANKSTYLE == '2'){echo ' selected="selected"';}?>>利于SEO链接自动选择</option>
            </select>
            <i class="help"></i><span class="helpcon">设置网站链接的打开方式。</span>
        </dd>
		<dd class="half">
			<label>个性登录</label>
			<input type="text" id="PostLOGINON" name="PostLOGINON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLOGINON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为使用主题自带登录样式；<br>“OFF”为使用zblog原始登录页。</span>
		</dd>
		<dd class="half">
			<label>分类列表排序</label>
			<input type="text" id="PostFILTERON" name="PostFILTERON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFILTERON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示分类列表排序功能，支持按“最新/浏览/评论”排序；<br>“OFF”为关闭分类列表排序功能。</span>
		</dd>
		<dd class="half">
			<label>查看全文按钮</label>
			<input type="text" id="PostMOREBTNON" name="PostMOREBTNON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostMOREBTNON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为显示列表查看全文按钮；<br>“OFF”为关闭列表查看全文按钮；<br><em>仅(catalog默认、sticker贴图)列表样式生效。</em></span>
		</dd>
        <dd class="half">
            <label>PC大图列表</label>
            <input type="text" id="PostBIGPOSTIMGON" name="PostBIGPOSTIMGON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBIGPOSTIMGON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为放大列表缩略图，摘要最多显示4行；<br>“OFF”为默认缩略图大小，摘要最多显示3行；<br><em>仅PC端放大缩略图，侧栏和相关文章缩略图大小不受影响；<br>仅(catalog默认、sticker贴图)列表样式生效。</em></span>
        </dd>
		<dd class="half">
			<label>导航悬浮</label>
			<input type="text" id="PostFIXMENUON" name="PostFIXMENUON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFIXMENUON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为开启导航菜单浮动屏幕顶部；<br>“OFF”为导航固定页面顶部。</span>
		</dd>
        <dd class="half">
            <label>文章图片灯箱</label>
            <input type="text" id="PostFANCYBOXON" name="PostFANCYBOXON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFANCYBOXON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用文章图片灯箱；<br>“OFF”为关闭文章图片灯箱；<br>注：文章中图片使用灯箱须用UEditor插件上传，<br>或者手动为img标签增加(class="ue-image")。</span>
        </dd>
        <dd class="half">
            <label>图片延迟加载</label>
            <input type="text" id="PostLAZYLOADON" name="PostLAZYLOADON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLAZYLOADON;?>" />
            <i class="help"></i><span class="helpcon">“ON”为图片按需加载；<br>“OFF”为图片全部加载。</span>
        </dd>
        <dd class="half">
            <label>滚动进度条</label>
            <input type="text" id="PostLAZYLINEON" name="PostLAZYLINEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLAZYLINEON;?>" />
            <i class="help"></i><span class="helpcon">“ON”为显示页面顶部滚动进度条；<br>“OFF”为关闭页面顶部滚动进度条;<br>需开启悬浮导航。</span>
        </dd>
        <dd class="half">
            <label>滚动进度数</label>
            <input type="text" id="PostLAZYNUMON" name="PostLAZYNUMON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLAZYNUMON;?>" />
            <i class="help"></i><span class="helpcon">“ON”为显示页面底部滚动进度数；<br>“OFF”为关闭页面底部滚动进度数。</span>
        </dd>
        <dd class="half">
            <label>段落首行缩进</label>
            <input type="text" id="PostINDENTON" name="PostINDENTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostINDENTON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为文章页面正文首行缩进2个字符；<br>“OFF”为关闭首行缩进。</span>
        </dd>
        <dd class="half">
            <label>文章tags标签</label>
            <input type="text" id="PostTAGSON" name="PostTAGSON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostTAGSON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为文章页显示tags标签；<br>“OFF”为文章页关闭tags标签。</span>
        </dd>
        <dd class="half">
            <label>文章上下篇</label>
            <input type="text" id="PostPREVNEXTON" name="PostPREVNEXTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostPREVNEXTON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为显示文章页上下篇文章及返回列表按钮；<br>“OFF”为关闭文章页上下篇文章及返回列表按钮。</span>
        </dd>
        <dd class="half">
            <label>同分类上下篇</label>
            <input type="text" id="PostCATEPREVNEXTON" name="PostCATEPREVNEXTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCATEPREVNEXTON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为文章页上下篇仅限当前分类文章；<br>“OFF”为全站文章上下文顺序。</span>
        </dd>
        <dd class="half">
            <label>文章字号控件</label>
            <input type="text" id="PostTFONTSIZEON" name="PostTFONTSIZEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostTFONTSIZEON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用文章正文字号放大缩小控件；<br>“OFF”为关闭文章正文字号放大缩小控件。<br><em>若字号无法控制，请检查该文章段落，清除格式再试；<br>字号控件仅在文章页显示。</em></span>
        </dd>
		<dd class="half">
			<label>清理空段落</label>
			<input type="text" id="PostREMOVEPON" name="PostREMOVEPON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostREMOVEPON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为清理文章页空白段落；<br>“OFF”为显示文章页空白段落。</span>
		</dd>
        <dd class="half">
            <label>内容保护</label>
            <input type="text" id="PostSELECTON" name="PostSELECTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSELECTON;?>" />
            <i class="help"></i><span class="helpcon">“ON”为启用内容保护(禁止选中文字、禁止鼠标右键，禁止调试)；<br>“OFF”为关闭保护；</span>
        </dd>
        <dd class="half">
            <label>分辨率提醒</label>
            <input type="text" id="PostCHECKDPION" name="PostCHECKDPION" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCHECKDPION;?>" />
            <i class="help"></i><span class="helpcon">“ON”为浏览器缩放页面时页面底部提醒；<br>“OFF”为关闭分辨率提醒；</span>
        </dd>
        <dd class="half">
            <label>繁简体转换</label>
            <input type="text" id="PostLANGON" name="PostLANGON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLANGON;?>" />
            <i class="help"></i><span class="helpcon">“ON”为开启繁简字体切换功能；<br>“OFF”为关闭繁简体转换功能；<br><em>繁简体转换按钮展示在网页右侧悬浮位置。</em></span>
        </dd>
		<dd class="setok"><input type="submit" value="保存设置" class="setbtn" /></dd>
	</form>
</dl>

<?php
}
if ($act == 'seo') {
    if (isset($_POST['SEOON'])) {
        $zbp->Config('tpure')->SEOON = $_POST['SEOON'];                     //关键词设置
        $zbp->Config('tpure')->SEODIVIDE = $_POST['SEODIVIDE'];						//关键词分隔符设置
        $zbp->Config('tpure')->SEOTITLE = $_POST['SEOTITLE'];						//关键词设置
        $zbp->Config('tpure')->SEOKEYWORDS = $_POST['SEOKEYWORDS'];				//关键词设置
        $zbp->Config('tpure')->SEODESCRIPTION = $_POST['SEODESCRIPTION'];			//描述设置

        $zbp->Config('tpure')->SEOCATALOGINFO = json_encode($_POST['catalog_info']);
        $zbp->Config('tpure')->SEOARTICLEINFO = json_encode($_POST['article_info']);
        $zbp->Config('tpure')->SEOPAGEINFO = json_encode($_POST['page_info']);
        $zbp->Config('tpure')->SEOTAGINFO = json_encode($_POST['tag_info']);
        $zbp->Config('tpure')->SEOUSERINFO = json_encode($_POST['user_info']);
        $zbp->Config('tpure')->SEODATEINFO = json_encode($_POST['date_info']);
        $zbp->Config('tpure')->SEOSEARCHINFO = json_encode($_POST['search_info']);
        $zbp->Config('tpure')->SEOOTHERINFO = json_encode($_POST['other_info']);

        $zbp->Config('tpure')->SEOTITLENOCODEON = $_POST['SEOTITLENOCODEON'];         //标题unicode转实体
        $zbp->Config('tpure')->SEORETITLEON = $_POST['SEORETITLEON'];         //分页标题倒置
        $zbp->Config('tpure')->SEODESCRIPTIONDATA = $_POST['SEODESCRIPTIONDATA'];			//描述数据选择，[0:全文,1:摘要]
        $zbp->Config('tpure')->SEODESCRIPTIONNUM = $_POST['SEODESCRIPTIONNUM'];         //描述字数限制
        $zbp->Config('tpure')->PostHEADERCODE = $_POST['PostHEADERCODE'];           //页头自定义代码$header
        $zbp->Config('tpure')->PostFOOTERCODE = $_POST['PostFOOTERCODE'];           //页尾自定义代码$footer
        $zbp->Config('tpure')->PostSINGLETOPCODE = $_POST['PostSINGLETOPCODE'];         //文章正文顶部代码
        $zbp->Config('tpure')->PostSINGLEBTMCODE = $_POST['PostSINGLEBTMCODE'];			//文章正文底部代码
        $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        tpure_CreateModule();
        $zbp->ShowHint('good');
    } ?>
<form name="seo" method="post" class="setting">
	<input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
<dl>
	<dt>SEO设置</dt>
	<dd data-stretch="seo" class="half">
		<label>自定义SEO</label>
		<input type="text" id="SEOON" name="SEOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->SEOON; ?>" />
		<i class="help"></i><span class="helpcon">“ON”为启用首页/分类/标签/文章自定义SEO信息；<br>“OFF”为关闭自定义SEO信息。<br>开启后，编辑文章/分类/标签时可设置自定义SEO信息。</span>
	</dd>
    <div class="seoinfo"<?php echo $zbp->Config('tpure')->SEOON == 1 ? '' : ' style="display:none"'; ?>>
        <dd class="half"><label for="SEODIVIDE">标题分隔符</label><input type="text" name="SEODIVIDE" id="SEODIVIDE" placeholder="默认 - (可添加前后空格)" class="settext" value="<?php echo $zbp->Config('tpure')->SEODIVIDE; ?>" /><i class="help"></i><span class="helpcon">设置标题[title]分隔符，默认“ - ”。</span></dd>
        <dd><label for="SEOTITLE">首页标题</label><input type="text" name="SEOTITLE" id="SEOTITLE" class="settext" value="<?php echo $zbp->Config('tpure')->SEOTITLE; ?>" /><i class="help"></i><span class="helpcon">设置网站首页标题[title]。</span></dd>
        <dd><label for="SEOKEYWORDS">首页关键词</label><input type="text" name="SEOKEYWORDS" id="SEOKEYWORDS" class="settext" value="<?php echo $zbp->Config('tpure')->SEOKEYWORDS; ?>" /><i class="help"></i><span class="helpcon">设置网站首页关键词[keywords]。</span></dd>
        <dd><label for="SEODESCRIPTION">首页描述</label><textarea name="SEODESCRIPTION" id="SEODESCRIPTION" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->SEODESCRIPTION; ?></textarea><i class="help"></i><span class="helpcon">设置网站首页描述[description]。</span></dd>
        <dd class="half">
            <label>实体标题符号</label>
            <input type="text" id="SEOTITLENOCODEON" name="SEOTITLENOCODEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->SEOTITLENOCODEON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为自动将标题中( &amp;nbsp; | &amp;quot; | &amp;lt; | &amp;gt; | &amp;amp; )转换成实体的( 空格 | " | < | > | & )；<br>“OFF”为不自动转换。</span>
        </dd>
        <dd class="half">
			<label>分页标题倒置</label>
			<input type="text" id="SEORETITLEON" name="SEORETITLEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->SEORETITLEON; ?>" />
			<i class="help"></i><span class="helpcon">“ON”为分页标题倒置（标题-第N页）；<br>“OFF”为常规分页标题（第N页-标题）；<br><em>分页条中第2页及其后的页面标题，搜索分页除外。</em></span>
		</dd>
		<dd class="half">
            <label for="SEODESCRIPTIONDATA">文章描述选择</label>
            <select size="1" name="SEODESCRIPTIONDATA" id="SEODESCRIPTIONDATA">
                <option value="0"<?php if($zbp->Config('tpure')->SEODESCRIPTIONDATA == '0'){echo ' selected="selected"';}?>>获取正文内容</option>
                <option value="1"<?php if($zbp->Config('tpure')->SEODESCRIPTIONDATA == '1'){echo ' selected="selected"';}?>>获取摘要内容</option>
            </select>
            <i class="help"></i><span class="helpcon">设置文章页描述[description]的内容来源，默认为获取正文内容。<br><em>注：此设置仅限文章页。</em></span>
        </dd>
        <dd class="half">
            <label for="SEODESCRIPTIONNUM">描述字数限制</label>
            <input type="number" id="SEODESCRIPTIONNUM" name="SEODESCRIPTIONNUM" value="<?php echo $zbp->Config('tpure')->SEODESCRIPTIONNUM;?>" min="1" step="1" class="settext" />
            <i class="help"></i><span class="helpcon">文章和页面的描述[description]字数限制，默认调用前200个字符。<br><em>注：此设置用于文章和页面。</em></span>
        </dd>
<?php
$seo_info = array(
    'catalog' => '分类名称',
    'article' => '文章标题',
    'page' => '页面标题',
    'tag' => '标签名称',
    'user' => '用户名称',
    'date' => '日期',
    'search' => '搜索词',
    'other' => '其他页面标题',
    'title' => '网站标题',
    'subtitle' => '网站副标题',
);
?>
        <dt>分类页标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $catalog_info = json_decode($zbp->Config('tpure')->SEOCATALOGINFO, true);
    if (count((array)$catalog_info)) {
        foreach ($catalog_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="catalog_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">分类名称<input name="catalog_info[catalog]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="catalog_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="catalog_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">分类页标题排序，自定义选择启用项，支持拖拽排序。</span>
        </dd>
        <dt>文章页标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $article_info = json_decode($zbp->Config('tpure')->SEOARTICLEINFO, true);
    if (count((array)$article_info)) {
        foreach ($article_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="article_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">文章标题<input name="article_info[article]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">分类名称<input name="article_info[catalog]" value="0"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="article_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="article_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">文章页标题排序，自定义选择启用项，支持拖拽排序。</span>
        </dd>
        <dt>页面标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $page_info = json_decode($zbp->Config('tpure')->SEOPAGEINFO, true);
    if (count((array)$page_info)) {
        foreach ($page_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="page_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">页面标题<input name="page_info[page]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="page_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="page_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">独立页面的标题排序，自定义选择启用项，支持拖拽排序。</span>
        </dd>
        <dt>标签页标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $tag_info = json_decode($zbp->Config('tpure')->SEOTAGINFO, true);
    if (count((array)$tag_info)) {
        foreach ($tag_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="tag_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">标签名称<input name="tag_info[tag]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="tag_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="tag_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">标签页标题排序，自定义选择启用项，支持拖拽排序。</span>
        </dd>
        <dt>用户页标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $user_info = json_decode($zbp->Config('tpure')->SEOUSERINFO, true);
    if (count((array)$user_info)) {
        foreach ($user_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="user_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">用户名称<input name="user_info[user]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="user_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="user_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">用户页标题排序，自定义选择启用项，支持拖拽排序。</span>
        </dd>
        <dt>日期页标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $date_info = json_decode($zbp->Config('tpure')->SEODATEINFO, true);
    if (count((array)$date_info)) {
        foreach ($date_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="date_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">日期<input name="date_info[date]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="date_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="date_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">日期页标题排序，自定义选择启用项，支持拖拽排序。<br><em>注：日期页是系统侧栏文章归档模块的链接页面。</em></span>
        </dd>
        <dt>搜索结果页标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $search_info = json_decode($zbp->Config('tpure')->SEOSEARCHINFO, true);
    if (count((array)$search_info)) {
        foreach ($search_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="search_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">搜索词<input name="search_info[search]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="search_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="search_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">搜索结果页标题排序，自定义选择启用项，支持拖拽排序；<br><em>注：搜索页分页标题不变。</em></span>
        </dd>
        <dt>其他页面标题顺序设置 <span>(勾选要展示的项目，可拖拽排序)</span></dt>
        <dd class="ckbox">
        <?php
        $other_info = json_decode($zbp->Config('tpure')->SEOOTHERINFO, true);
    if (count((array)$other_info)) {
        foreach ($other_info as $key => $info) {
            echo '<div class="checkui' . ($info == 1 ? ' on' : '') . '">' . $seo_info[$key] . '<input name="other_info[' . $key . ']" value="' . $info . '"></div>';
        }
    }else{
        echo '<div class="checkui ui-sortable-handle">其他页面标题<input name="other_info[other]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站标题<input name="other_info[title]" value="1"></div>';
        echo '<div class="checkui ui-sortable-handle">网站副标题<input name="other_info[subtitle]" value="0"></div>';
    } ?>
            <i class="help"></i><span class="helpcon">其他页面标题排序，自定义选择启用项，支持拖拽排序。</span>
        </dd>
	</div>
    <dt>自定义代码块 <span>(高阶玩法)</span></dt>
    <dd>
        <label for="PostHEADERCODE">页头通用代码</label><textarea name="PostHEADERCODE" id="PostHEADERCODE" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->PostHEADERCODE; ?></textarea><i class="help"></i><span class="helpcon">设置网站页头通用代码。</span>
    </dd>
    <dd>
        <label for="PostFOOTERCODE">页尾通用代码</label><textarea name="PostFOOTERCODE" id="PostFOOTERCODE" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->PostFOOTERCODE; ?></textarea><i class="help"></i><span class="helpcon">设置网站页尾通用代码。</span>
    </dd>
    <dd>
        <label for="PostSINGLETOPCODE">正文起始代码</label><textarea name="PostSINGLETOPCODE" id="PostSINGLETOPCODE" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->PostSINGLETOPCODE; ?></textarea><i class="help"></i><span class="helpcon">设置文章正文起始自定义代码。</span>
    </dd>
    <dd>
        <label for="PostSINGLEBTMCODE">正文结束代码</label><textarea name="PostSINGLEBTMCODE" id="PostSINGLEBTMCODE" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->PostSINGLEBTMCODE; ?></textarea><i class="help"></i><span class="helpcon">设置文章正文结束自定义代码。</span>
    </dd>
	<dd class="setok"><input type="submit" value="保存设置" class="setbtn" /></dd>
</dl>
</form>

<?php
}
if ($act == 'color') {
    if (isset($_POST['PostCOLORON'])) {
        $zbp->Config('tpure')->PostCOLORON = $_POST['PostCOLORON'];				//自定义配色开关
        $zbp->Config('tpure')->PostFONT = $_POST['PostFONT'];       //自定义字体
        $zbp->Config('tpure')->PostCOLOR = $_POST['PostCOLOR'];					//主色调
        $zbp->Config('tpure')->PostSIDELAYOUT = isset($_POST['PostSIDELAYOUT']) ? $_POST['PostSIDELAYOUT'] : 'r';   //侧栏位置
        $zbp->Config('tpure')->PostBGCOLOR = $_POST['PostBGCOLOR'];         //页面背景色
        $zbp->Config('tpure')->PostBGIMG = $_POST['PostBGIMG'];         //页面背景图片
        $zbp->Config('tpure')->PostBGIMGON = $_POST['PostBGIMGON'];         //页面背景开关
        $zbp->Config('tpure')->PostBGIMGSTYLE = $_POST['PostBGIMGSTYLE'];           //页面背景样式
        $zbp->Config('tpure')->PostHEADBGCOLOR = $_POST['PostHEADBGCOLOR'];         //页头背景色
        $zbp->Config('tpure')->PostFOOTBGCOLOR = $_POST['PostFOOTBGCOLOR'];         //页尾背景色
        $zbp->Config('tpure')->PostFOOTFONTCOLOR = $_POST['PostFOOTFONTCOLOR'];			//页尾文字颜色
        $zbp->Config('tpure')->PostCUSTOMCSS = $_POST['PostCUSTOMCSS'];			//自定义CSS
        $tpure_color = tpure_color();
        @file_put_contents($zbp->path . 'zb_users/theme/tpure/include/skin.css', $tpure_color);
        $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        tpure_CreateModule();
        $zbp->ShowHint('good');
    } ?>
<script type="text/javascript" src="./plugin/jscolor/jscolor.js"></script>
<link rel="stylesheet" type="text/css" href="./plugin/codemirror/codemirror.css">
<link rel="stylesheet" type="text/css" href="./plugin/codemirror/default.css">
<script type="text/javascript" src="./plugin/codemirror/codemirror.js"></script>
<script type="text/javascript" src="./plugin/codemirror/active-line.js"></script>
<script type="text/javascript" src="./plugin/codemirror/placeholder.js"></script>
<script type="text/javascript" src="./plugin/codemirror/matchbrackets.js"></script>
<script type="text/javascript" src="./plugin/codemirror/css.js"></script>
<form name="color" method="post" class="setting">
	<input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
<dl>
	<dt>色彩设置 <span>(若未生效请在前台按' Ctrl+F5 '刷新页面或清除浏览器缓存)</span></dt>
	<dd data-stretch="color">
		<label>自定义配色</label>
		<input type="text" id="PostCOLORON" name="PostCOLORON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCOLORON; ?>" />
		<i class="help"></i><span class="helpcon">“ON”为启用自定义配色；<br>“OFF”为使用主题默认颜色。</span>
	</dd>
	<div class="colorinfo"<?php echo $zbp->Config('tpure')->PostCOLORON == 1 ? '' : ' style="display:none"'; ?>>
    <dd>
        <label for="PostFONT">自定义字体<br><a href="https://www.toyean.com/help.html" target="_blank" class="tips">名称规则</a></label>
        <textarea name="PostFONT" cols="30" rows="3" placeholder="此处填写自定义字体名称" id="PostFONT" class="setinput"><?php echo $zbp->Config('tpure')->PostFONT; ?></textarea>
        <i class="help"></i><span class="helpcon">请设置网站字体，按照前后优先级顺序设置；<br>具体名称可点击“命名规则”查看；<br>留空时使用主题默认字体设置。</span>
    </dd>
	<dd class="half">
		<label for="PostCOLOR">主色调</label>
		<input type="text" name="PostCOLOR" id="PostCOLOR" class="color settext" value="<?php echo $zbp->Config('tpure')->PostCOLOR; ?>" />
		<i class="help"></i><span class="helpcon">请设置网站主色调,默认为 0188FB。</span>
	</dd>
    <dd class="half">
        <label for="">侧栏位置</label>
        <div class="layoutset">
            <input type="radio" id="sideleft" name="PostSIDELAYOUT" value="l" <?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'l' ? 'checked="checked"' : ''; ?> class="hideradio" />
            <label for="sideleft"<?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'l' ? ' class="on"' : ''; ?>><img src="style/images/sideleft.png" alt="侧栏居左" /></label>
            <input type="radio" id="sideright" name="PostSIDELAYOUT" value="r" <?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'r' ? 'checked="checked"' : ''; ?> class="hideradio" />
            <label for="sideright"<?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'r' ? ' class="on"' : ''; ?>><img src="style/images/sideright.png" alt="侧栏居右" /></label>
        </div>
        <i class="help"></i><span class="helpcon">请设置侧栏位置，默认为侧栏居右。</span>
    </dd>
	<dd>
		<label for="PostBGCOLOR">页面背景色</label>
		<input type="text" name="PostBGCOLOR" id="PostBGCOLOR" class="color settext" value="<?php echo $zbp->Config('tpure')->PostBGCOLOR; ?>" />
		<i class="help"></i><span class="helpcon">请设置页面body背景色，默认为 FFFFFF；<br>背景色仅针对日间模式，对夜间模式无效。</span>
	</dd>
    <dd>
        <label for="PostBGIMG">背景图片设置</label>
        <table>
            <tbody>
                <tr>
                    <th width="25%">缩略图</th>
                    <th width="35%">图片地址</th>
                    <th width="15%">上传</th>
                    <th width=20%>操作</th>
                </tr>
                <tr>
                    <td><?php if ($zbp->Config('tpure')->PostBGIMG) { ?><img src="<?php echo $zbp->Config('tpure')->PostBGIMG; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/background.jpg" width="120" class="thumbimg" /><?php } ?></td>
                    <td><input type="text" id="PostBGIMG" name="PostBGIMG" value="<?php if ($zbp->Config('tpure')->PostBGIMG) {
        echo $zbp->Config('tpure')->PostBGIMG;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/background.jpg';
    } ?>" class="urltext thumbsrc"></td>
                    <td><input type="button" class="uploadimg format" value="上传"></td>
                    <td>启用背景图 <input type="text" id="PostBGIMGON" name="PostBGIMGON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBGIMGON; ?>" /><br>
        <select size="1" name="PostBGIMGSTYLE" id="PostBGIMGSTYLE" style="width:130px;">
            <option value="1"<?php if($zbp->Config('tpure')->PostBGIMGSTYLE == '1'){echo ' selected="selected"';}?>>平铺</option>
            <option value="2"<?php if($zbp->Config('tpure')->PostBGIMGSTYLE == '2'){echo ' selected="selected"';}?>>自适应</option>
        </select></td>
                </tr>
            </tbody>
        </table>
        <i class="help"></i><span class="helpcon">上传页面背景图片并设置背景图的参数；<br>背景图片仅针对日间模式，对夜间模式无效。</span>
    </dd>
	<dd>
        <label for="PostHEADBGCOLOR">页头背景色</label>
        <input type="text" name="PostHEADBGCOLOR" id="PostHEADBGCOLOR" class="color settext" value="<?php echo $zbp->Config('tpure')->PostHEADBGCOLOR; ?>" />
        <i class="help"></i><span class="helpcon">请设置页头背景色，默认为 FFFFFF；<br>背景色仅针对日间模式，对夜间模式无效。</span>
    </dd>
    <dd class="half">
        <label for="PostFOOTBGCOLOR">页尾背景色</label>
        <input type="text" name="PostFOOTBGCOLOR" id="PostFOOTBGCOLOR" class="color settext" value="<?php echo $zbp->Config('tpure')->PostFOOTBGCOLOR ? $zbp->Config('tpure')->PostFOOTBGCOLOR : 'e4e8eb'; ?>" />
        <i class="help"></i><span class="helpcon">请设置页尾背景色，默认为 E4E8EB；<br>背景色仅针对日间模式，对夜间模式无效。</span>
    </dd>
    <dd class="half">
        <label for="PostFOOTFONTCOLOR">页尾文字颜色</label>
        <input type="text" name="PostFOOTFONTCOLOR" id="PostFOOTFONTCOLOR" class="color settext" value="<?php echo $zbp->Config('tpure')->PostFOOTFONTCOLOR ? $zbp->Config('tpure')->PostFOOTFONTCOLOR : '999999'; ?>" />
        <i class="help"></i><span class="helpcon">请设置页尾文字颜色，默认为 999999；<br>文字颜色仅针对日间模式，对夜间模式无效。</span>
    </dd>
	<dd>
		<label for="PostCUSTOMCSS">自定义CSS</label>
		<div class="codearea">
			<textarea name="PostCUSTOMCSS" id="PostCUSTOMCSS" cols="30" rows="5" placeholder="此处填写自定义CSS" class="setinput"><?php echo $zbp->Config('tpure')->PostCUSTOMCSS; ?></textarea>
		</div>
		<i class="help"></i><span class="helpcon">自定义CSS，辅助配色与布局。</span>
	</dd>
	</div>
	<dd class="setok"><input type="submit" value="保存设置" class="setbtn" /></dd>
</dl>
</form>
<?php
}
if ($act == 'side') {
    if (isset($_POST['PostFIXSIDEBARON'])) {
        $zbp->Config('tpure')->PostFIXSIDEBARON = $_POST['PostFIXSIDEBARON'];	//侧栏悬浮开关
        $zbp->Config('tpure')->PostFIXSIDEBARSTYLE = $_POST['PostFIXSIDEBARSTYLE']; //侧栏悬浮样式
        $zbp->Config('tpure')->PostSIDEMOBILEON = $_POST['PostSIDEMOBILEON'];	//移动端侧栏开关
        $zbp->Config('tpure')->PostSIDECMTDAY = $_POST['PostSIDECMTDAY'];			//侧栏热评文章模块指定天数
        $zbp->Config('tpure')->PostSIDEVIEWDAY = $_POST['PostSIDEVIEWDAY'];			//侧栏热门文章模块指定天数
        $zbp->Config('tpure')->PostSIDERECID = $_POST['PostSIDERECID'];			//侧栏热推文章模块指定ID
        $zbp->Config('tpure')->PostSIDEUSERBG = $_POST['PostSIDEUSERBG'];			//侧栏站长简介模块背景图片
        $zbp->Config('tpure')->PostSIDEUSERIMG = $_POST['PostSIDEUSERIMG'];			//侧栏站长简介模块头像
        $zbp->Config('tpure')->PostSIDEUSERNAME = $_POST['PostSIDEUSERNAME'];			//侧栏站长简介模块站长名称
        $zbp->Config('tpure')->PostSIDEUSERINTRO = $_POST['PostSIDEUSERINTRO'];			//侧栏站长简介模块站长简介
        $zbp->Config('tpure')->PostSIDEUSERQQ = $_POST['PostSIDEUSERQQ'];			//侧栏站长简介模块QQ号码
        $zbp->Config('tpure')->PostSIDEUSERWECHAT = $_POST['PostSIDEUSERWECHAT'];			//侧栏站长简介模块微信二维码
        $zbp->Config('tpure')->PostSIDEUSEREMAIL = $_POST['PostSIDEUSEREMAIL'];			//侧栏站长简介模块邮箱地址
        $zbp->Config('tpure')->PostSIDEUSERWEIBO = $_POST['PostSIDEUSERWEIBO'];			//侧栏站长简介模块微博URL
        $zbp->Config('tpure')->PostSIDEUSERGROUP = $_POST['PostSIDEUSERGROUP'];			//侧栏站长简介模块QQ群链接
        $zbp->Config('tpure')->PostSIDEUSERCOUNT = $_POST['PostSIDEUSERCOUNT'];			//侧栏站长简介模块底部统计信息
        $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        tpure_CreateModule();
        $zbp->ShowHint('good');
    } ?>
<form name="side" method="post" class="setting">
	<input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
<dl>
	<dt>侧栏悬浮设置</dt>
	<dd data-stretch="fixsidebar" class="half">
		<label>侧栏悬浮</label>
		<input type="text" id="PostFIXSIDEBARON" name="PostFIXSIDEBARON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFIXSIDEBARON; ?>" />
		<i class="help"></i><span class="helpcon">“ON”为开启侧栏悬浮；<br>“OFF”为关闭侧栏悬浮。</span>
	</dd>
    <div class="fixsidebarinfo"<?php echo $zbp->Config('tpure')->PostFIXSIDEBARON == 1 ? '' : ' style="display:none"'; ?>>
	<dd class="half">
        <label for="PostFIXSIDEBARSTYLE">侧栏悬浮样式</label>
        <select size="1" name="PostFIXSIDEBARSTYLE" id="PostFIXSIDEBARSTYLE">
            <option value="0"<?php if($zbp->Config('tpure')->PostFIXSIDEBARSTYLE == '0'){echo ' selected="selected"';}?>>整体粘性悬浮</option>
            <option value="1"<?php if($zbp->Config('tpure')->PostFIXSIDEBARSTYLE == '1'){echo ' selected="selected"';}?>>末尾模块悬浮</option>
        </select>
        <i class="help"></i><span class="helpcon">设置侧栏悬浮的展现形式，默认为整体粘性悬浮；<br>侧栏整体高度大于内容区高度时不悬浮；<br><em>移动端显示侧栏时不悬浮。</em></span>
    </dd>
    </div>
    <dt>移动端侧栏</dt>
    <dd>
        <label>移动端侧栏</label>
        <input type="text" id="PostSIDEMOBILEON" name="PostSIDEMOBILEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSIDEMOBILEON; ?>" />
        <i class="help"></i><span class="helpcon">“ON”为开启移动端侧栏；<br>“OFF”为关闭移动端侧栏。</span>
    </dd>
    <dt>侧栏“热评文章”模块设置</dt>
	<dd>
		<label for="PostSIDECMTDAY">距今范围天数</label>
		<input type="number" id="PostSIDECMTDAY" name="PostSIDECMTDAY" value="<?php echo $zbp->Config('tpure')->PostSIDECMTDAY; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置“热评文章”模块距离当前指定天数内评论最多的文章。</span>
	</dd>
	<dt>侧栏“热门阅读”模块设置</dt>
	<dd>
		<label for="PostSIDEVIEWDAY">距今范围天数</label>
		<input type="number" id="PostSIDEVIEWDAY" name="PostSIDEVIEWDAY" value="<?php echo $zbp->Config('tpure')->PostSIDEVIEWDAY; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置“热门阅读”模块距离当前指定天数内浏览最多的文章。</span>
	</dd>
	<dt>侧栏“推荐阅读”模块设置</dt>
	<dd>
        <label for="PostSIDERECID">热推文章ID</label>
        <input type="text" id="PostSIDERECID" name="PostSIDERECID" value="<?php echo $zbp->Config('tpure')->PostSIDERECID; ?>" class="settext" />
        <i class="help"></i><span class="helpcon">请填写侧栏“推荐阅读”模块文章ID，<br>多个文章ID之间用英文逗号分隔，首尾不加逗号。</span>
    </dd>
    <dt>侧栏“站长简介”模块设置</dt>
    <dd>
		<label for="PostSIDEUSERBG">背景图片</label>
		<table>
			<tbody>
				<tr>
					<th width="25%">缩略图(375x100px)</th>
					<th width="55%">图片地址</th>
					<th width="15%">上传</th>
				</tr>
				<tr>
					<td><?php if ($zbp->Config('tpure')->PostSIDEUSERBG) { ?><img src="<?php echo $zbp->Config('tpure')->PostSIDEUSERBG; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/banner.jpg" width="120" class="thumbimg" /><?php } ?></td>
					<td><input type="text" id="PostSIDEUSERBG" name="PostSIDEUSERBG" value="<?php if ($zbp->Config('tpure')->PostSIDEUSERBG) {
    echo $zbp->Config('tpure')->PostSIDEUSERBG;
} else {
    echo $zbp->host . 'zb_users/theme/tpure/style/images/banner.jpg';
} ?>" class="urltext thumbsrc"></td>
					<td><input type="button" class="uploadimg format" value="上传"></td>
				</tr>
			</tbody>
		</table>
		<i class="help"></i><span class="helpcon">侧栏“站长简介”模块背景图。</span>
	</dd>
	<dd>
		<label for="PostSIDEUSERIMG">站长头像</label>
		<table>
			<tbody>
				<tr>
					<th width="25%">缩略图(80x80px)</th>
					<th width="55%">图片地址</th>
					<th width="15%">上传</th>
				</tr>
				<tr>
					<td><?php if ($zbp->Config('tpure')->PostSIDEUSERIMG) { ?><img src="<?php echo $zbp->Config('tpure')->PostSIDEUSERIMG; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/sethead.png" width="120" class="thumbimg" /><?php } ?></td>
					<td><input type="text" id="PostSIDEUSERIMG" name="PostSIDEUSERIMG" value="<?php if ($zbp->Config('tpure')->PostSIDEUSERIMG) {
    echo $zbp->Config('tpure')->PostSIDEUSERIMG;
} else {
    echo $zbp->host . 'zb_users/theme/tpure/style/images/sethead.png';
} ?>" class="urltext thumbsrc"></td>
					<td><input type="button" class="uploadimg format" value="上传"></td>
				</tr>
			</tbody>
		</table>
		<i class="help"></i><span class="helpcon">侧栏“站长简介”模块头像。</span>
	</dd>
	<dd class="half">
		<label for="PostSIDEUSERNAME">站长名称</label>
		<input type="text" id="PostSIDEUSERNAME" name="PostSIDEUSERNAME" value="<?php echo $zbp->Config('tpure')->PostSIDEUSERNAME; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置侧栏“站长简介”模块站长名称。</span>
	</dd>
	<dd class="half">
		<label for="PostSIDEUSERINTRO">站长简介</label>
		<input type="text" id="PostSIDEUSERINTRO" name="PostSIDEUSERINTRO" value="<?php echo $zbp->Config('tpure')->PostSIDEUSERINTRO; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置侧栏“站长简介”模块介绍内容。</span>
	</dd>
	<dd class="half">
		<label for="PostSIDEUSERQQ">QQ号码</label>
		<input type="text" id="PostSIDEUSERQQ" name="PostSIDEUSERQQ" value="<?php echo $zbp->Config('tpure')->PostSIDEUSERQQ; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置侧栏“站长简介”模块社交QQ号码，若没有请留空。</span>
	</dd>
	<dd class="half">
		<label for="PostSIDEUSEREMAIL">邮箱地址</label>
		<input type="text" id="PostSIDEUSEREMAIL" name="PostSIDEUSEREMAIL" value="<?php echo $zbp->Config('tpure')->PostSIDEUSEREMAIL; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置侧栏“站长简介”模块社交邮箱地址，若没有请留空。</span>
	</dd>
	<dd class="half">
		<label for="PostSIDEUSERWEIBO">微博链接</label>
		<input type="text" id="PostSIDEUSERWEIBO" name="PostSIDEUSERWEIBO" value="<?php echo $zbp->Config('tpure')->PostSIDEUSERWEIBO; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置侧栏“站长简介”模块社交微博链接，若没有请留空。</span>
	</dd>
	<dd class="half">
		<label for="PostSIDEUSERGROUP">QQ群链接</label>
		<input type="text" id="PostSIDEUSERGROUP" name="PostSIDEUSERGROUP" value="<?php echo $zbp->Config('tpure')->PostSIDEUSERGROUP; ?>" min="1" step="1" class="settext" />
		<i class="help"></i><span class="helpcon">设置侧栏“站长简介”模块社交QQ群链接，若没有请留空。</span>
	</dd>
    <dd>
        <label for="PostSIDEUSERWECHAT">微信二维码</label>
        <table>
            <tbody>
                <tr>
                    <th width="25%">缩略图(120x120px)</th>
                    <th width="55%">图片地址</th>
                    <th width="15%">上传</th>
                </tr>
                <tr>
                    <td><?php if ($zbp->Config('tpure')->PostSIDEUSERWECHAT) { ?><img src="<?php echo $zbp->Config('tpure')->PostSIDEUSERWECHAT; ?>" width="120" class="thumbimg" /><?php } else { ?><img src="style/images/qr.png" width="120" class="thumbimg" /><?php } ?></td>
                    <td><input type="text" id="PostSIDEUSERWECHAT" name="PostSIDEUSERWECHAT" value="<?php if ($zbp->Config('tpure')->PostSIDEUSERWECHAT) {
    echo $zbp->Config('tpure')->PostSIDEUSERWECHAT;
} else {
    echo $zbp->host . 'zb_users/theme/tpure/style/images/qr.png';
} ?>" class="urltext thumbsrc"></td>
                    <td><input type="button" class="uploadimg format" value="上传"></td>
                </tr>
            </tbody>
        </table>
        <i class="help"></i><span class="helpcon">设置侧栏“站长简介”模块社交微信图片，若没有请留空。</span>
    </dd>
	<dd>
		<label>底部统计信息</label>
		<input type="text" id="PostSIDEUSERCOUNT" name="PostSIDEUSERCOUNT" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSIDEUSERCOUNT; ?>" />
		<i class="help"></i><span class="helpcon">“ON”为开启“站长简介”模块底部统计信息；<br>“OFF”为关闭“站长简介”模块底部统计信息。</span>
	</dd>
	<dd class="setok"><input type="submit" value="保存设置" class="setbtn" /></dd>
</dl>
</form>

<?php
}
if ($act == 'slide'){
	if ($_POST && isset($_POST['img'])) {
		if(!$_POST["img"] or !$_POST["title"] or !$_POST["url"] or !$_POST["color"]){
			$zbp->SetHint('bad','图片/标题/链接/颜色不能为空');
			Redirect('./main.php?act=slide');
			exit();
		}
		if ($_GET && isset($_GET['type'])) {
			if ($_GET['type'] == 'add') {
				if($zbp->Config('tpure')->HasKey('PostSLIDEDATA')){
					$slidedata = json_decode($zbp->Config('tpure')->PostSLIDEDATA,true);
				}
                $slidedata[] = $_POST;
                foreach ($slidedata as $key => $row){
                    $order[$key] = $row['order'];
                }
                if(is_array($order)){
                    array_multisort($order, SORT_ASC,$slidedata);
                }
				$zbp->Config('tpure')->PostSLIDEDATA = json_encode($slidedata);
				$zbp->SaveConfig('tpure');
                header("Refresh:0");
			}elseif($_GET['type'] == 'edit'){
				$slidedata = json_decode($zbp->Config('tpure')->PostSLIDEDATA,true);
				$editid = $_POST['editid'];
				unset($_POST['editid']);
				$slidedata[$editid] =$_POST;
				foreach ($slidedata as $key => $row) {
					$order[$key] = $row['order'];
				}
				array_multisort($order, SORT_ASC,$slidedata);
				$zbp->Config('tpure')->PostSLIDEDATA = json_encode($slidedata);
				$zbp->SaveConfig('tpure');
				$zbp->ShowHint('修改成功');
			}
		}
	}elseif ($_GET && isset($_GET['type'])) {
		if ($_GET['type'] == 'del') {
			$slidedata = json_decode($zbp->Config('tpure')->PostSLIDEDATA,true);
			$editid = $_GET['id'];
			unset($slidedata[$editid]);
			$zbp->Config('tpure')->PostSLIDEDATA = json_encode($slidedata);
			$zbp->SaveConfig('tpure');
			$zbp->ShowHint('删除成功');
		}
	}
	if(isset($_POST['PostSLIDEON'])){
		$zbp->Config('tpure')->PostSLIDEON = $_POST['PostSLIDEON'];						//幻灯开关
        $zbp->Config('tpure')->PostSLIDEPLACE = $_POST['PostSLIDEPLACE'];               //幻灯位置{0:'首页列表顶部',1:'替换首页Banner'}
		$zbp->Config('tpure')->PostSLIDETITLEON = $_POST['PostSLIDETITLEON'];		//首页列表顶部幻灯标题开关
		$zbp->Config('tpure')->PostSLIDETIME = $_POST['PostSLIDETIME'];					//自动切换时间:默认2500毫秒
		$zbp->Config('tpure')->PostSLIDEDISPLAY = $_POST['PostSLIDEDISPLAY'];		//幻灯视差滚动
		$zbp->Config('tpure')->PostSLIDEPAGEON = $_POST['PostSLIDEPAGEON'];		//幻灯分页指示
		$zbp->Config('tpure')->PostSLIDEPAGETYPE = $_POST['PostSLIDEPAGETYPE'];	//幻灯分页类型{0:'鼠标点击切换',1:'鼠标划过切换'}
		$zbp->Config('tpure')->PostSLIDEBTNON = $_POST['PostSLIDEBTNON'];			//幻灯左右箭头
		$zbp->Config('tpure')->PostSLIDEEFFECTON = $_POST['PostSLIDEEFFECTON'];	//幻灯左右滚动
		$zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        tpure_CreateModule();
		$zbp->ShowHint('good');
	}
?>
	<script type="text/javascript" src="./plugin/jscolor/jscolor.js"></script>
	<dl>
		<dt>幻灯设置 <span>(修改幻灯请点击幻灯右侧 <img src="../../../zb_system/image/admin/ok.png" alt="对号"> 图标保存修改)</span></dt>
		<dd>
			<table width="100%" border="0" class="slidetable">
				<thead>
					<tr>
						<th scope="col" width="9%" height="32" nowrap="nowrap">序号</th>
						<th scope="col" width="58%">幻灯信息</th>
						<th scope="col" width="10%">排序</th>
						<th scope="col" width="9%">显示</th>
						<th scope="col" width="14%">操作</th>
					</tr>
				</thead>
				<tbody>
					<tr>
                        <td colspan="5" style="padding:0;">
                            <form action="?act=slide&type=add" method="post">
                            <table class="slidetable" style="border:0;">
                                <tr>
                                    <td align="center" width="9%">0</td>
                                    <td width="58%">
                                        <div class="slideleft">
                                            <span class="slideimg dftimg">
                                                <span><input type="hidden" name="img" value="" class="thumbsrc" /><button type="button" value="" class="uploadimg uploadico">上传图片</button></span>
                                                <img src="style/images/uploadimg.png" class="thumbimg" />
                                            </span>
                                        </div>
                                        <span class="slideset"><input type="text" name="title" value="" placeholder="请输入幻灯标题 (必填)" required="required" class="slidetext"><input type="text" name="url" value="" placeholder="请输入链接地址 (必填)" required="required" class="slidetext slidetexttitle"><input type="text" name="color" value="" placeholder="请选择背景色 (必填)" required="required" class="color slidetext slidetexttitle"></span>
                                        </td>
                                    <td width="10%"><input type="text" name="order" value="99" class="slidetext slideorder"></td>
                                    <td width="9%"><input type="text" class="checkbox" name="isused" value="1" /></td>
                                    <td width="14%"><input type="hidden" name="editid" value=""><input name="add" type="submit" class="format" value="增加"></td>
                                </tr>
                            </table>
                            </form>
                        </td>
                    </tr>
<?php
    $slidedata = json_decode($zbp->Config('tpure')->PostSLIDEDATA,true);
    if(is_array($slidedata)){
    foreach ($slidedata as $key => $value) {
?>
                    <tr>
                        <td colspan="5" style="padding:0;">
                            <form action="?act=slide&type=edit" class="setting slideitem" method="post" name="slide">
                                <table class="slidetable" style="border:0; border-top:1px solid #ddd;">
                                <tr>
                                <td align="center" width="9%"><?php echo $value['order']; ?></td>
                                <td width="58%">
                                    <div class="slideleft">
                                        <span class="slideimg" style="background-color:#<?php echo $value['color']; ?>">
                                            <span><input type="hidden" name="img" value="<?php echo $value['img']; ?>" class="thumbsrc" /><button type="button" value="" class="uploadimg uploadico">上传图片</button></span>
                                            <img src="<?php echo $value['img']; ?>" class="thumbimg" />
                                        </span>
                                    </div>
                                    <span class="slideset"><input type="text" name="title" value="<?php echo $value['title']; ?>" placeholder="请输入幻灯标题 (必填)" required="required" class="slidetext"><input type="text" name="url" value="<?php echo $value['url']; ?>" placeholder="请输入链接地址 (必填)" required="required" class="slidetext slidetexttitle"><input type="text" name="color" value="<?php echo $value['color']; ?>" placeholder="请选择背景色 (必填)" required="required" class="color slidetext slidetexttitle"></span>
                                </td>
                                <td width="10%"><input type="text" name="order" value="<?php echo $value['order']; ?>" class="slidetext slideorder"></td>
                                <td width="9%"><input type="text" class="checkbox" name="isused" value="<?php echo $value['isused']; ?>" /></td>
                                <td width="14%" nowrap="nowrap">
                                    <input type="hidden" name="editid" value="<?php echo $key; ?>">
                                    <input name="edit" type="submit" value="" class="setokicon">
                                    <input name="del" type="button" class="setdelicon" value="" onclick="if(confirm('您确定要进行删除操作吗？')){location.href='?act=slide&type=del&id=<?php echo $key; ?>'}"/>
                                </td>
                                </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
<?php }} ?>
                </tbody>
            </table>
		</dd>
		<form name="slide" method="post" class="setting">
			<dt>幻灯参数设置</dt>
			<dd data-stretch="slide">
				<label>启用幻灯</label>
				<input type="text" id="PostSLIDEON" name="PostSLIDEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSLIDEON;?>" />
				<i class="help"></i><span class="helpcon">“ON”为启用幻灯，“OFF”为关闭幻灯。</span>
			</dd>
            <div class="slideinfo"<?php echo $zbp->Config('tpure')->PostSLIDEON == 1 ? '' : ' style="display:none"'; ?>>
			<dd class="half">
				<label for="PostSLIDEPLACE">幻灯位置</label>
				<select size="1" name="PostSLIDEPLACE" id="PostSLIDEPLACE">
					<option value="0"<?php if($zbp->Config('tpure')->PostSLIDEPLACE == '0'){echo ' selected="selected"';}?>>首页列表顶部</option>
					<option value="1"<?php if($zbp->Config('tpure')->PostSLIDEPLACE == '1'){echo ' selected="selected"';}?>>替换首页Banner</option>
				</select>
				<i class="help"></i><span class="helpcon">设置幻灯在首页的展示位置。</span>
			</dd>
            <div class="slideplaceinfo"<?php echo $zbp->Config('tpure')->PostSLIDEPLACE == 0 ? '' : ' style="display:none"'; ?>>
                <dd class="half right">
                    <label>幻灯标题展示</label>
                    <input type="text" id="PostSLIDETITLEON" name="PostSLIDETITLEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSLIDETITLEON;?>" />
                    <i class="help"></i><span class="helpcon">“ON”为启用幻灯标题，“OFF”为关闭幻灯标题；<br><em>标题仅局限于幻灯位置为 "首页列表顶部" 时生效。</em></span>
                </dd>
            </div>
            <div class="slidedisplayinfo"<?php echo $zbp->Config('tpure')->PostSLIDEPLACE == 1 ? '' : ' style="display:none"'; ?>>
                <dd class="half right">
                    <label>幻灯视差效果</label>
                    <input type="text" id="PostSLIDEDISPLAY" name="PostSLIDEDISPLAY" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSLIDEDISPLAY;?>" />
                    <i class="help"></i><span class="helpcon">“ON”为启用视差效果，“OFF”为关闭视差效果；<br><em>效果仅局限于幻灯位置为 "替换首页Banner" 时生效。</em></span>
                </dd>
            </div>
			<dd class="half">
				<label for="PostSLIDETIME">自动切换时间</label>
				<input type="number" id="PostSLIDETIME" name="PostSLIDETIME" value="<?php echo $zbp->Config('tpure')->PostSLIDETIME;?>" min="1000" step="10" class="settext" />
				<i class="help"></i><span class="helpcon">设置幻灯自动切换的时间，默认2500毫秒。</span>
			</dd>
            <dd class="half">
                <label for="PostSLIDEPAGETYPE">幻灯分页类型</label>
                <select size="1" name="PostSLIDEPAGETYPE" id="PostSLIDEPAGETYPE">
                    <option value="0"<?php if($zbp->Config('tpure')->PostSLIDEPAGETYPE == '0'){echo ' selected="selected"';}?>>鼠标点击切换</option>
                    <option value="1"<?php if($zbp->Config('tpure')->PostSLIDEPAGETYPE == '1'){echo ' selected="selected"';}?>>鼠标划过切换</option>
                </select>
                <i class="help"></i><span class="helpcon">设置幻灯分页切换事件类型，默认为鼠标划过切换。</span>
            </dd>
			<dd class="half">
				<label>幻灯分页条</label>
				<input type="text" id="PostSLIDEPAGEON" name="PostSLIDEPAGEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSLIDEPAGEON;?>" />
				<i class="help"></i><span class="helpcon">“ON”为显示幻灯底部分页条，“OFF”为关闭幻灯底部分页条</span>
			</dd>
			
			<dd class="half">
				<label>幻灯左右箭头</label>
				<input type="text" id="PostSLIDEBTNON" name="PostSLIDEBTNON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSLIDEBTNON;?>" />
				<i class="help"></i><span class="helpcon">“ON”为显示幻灯左右箭头，“OFF”为关闭幻灯左右箭头；<br><em>手机端不展示左右箭头。</em></span>
			</dd>
			<dd>
				<label>淡入淡出切换</label>
				<input type="text" id="PostSLIDEEFFECTON" name="PostSLIDEEFFECTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSLIDEEFFECTON;?>" />
				<i class="help"></i><span class="helpcon">“ON”为启用淡入淡出切换，“OFF”为默认左右切换</span>
			</dd>
            </div>
			<dd class="setok"><input type="submit" value="保存设置" class="setbtn" /></dd>
            </form>
		</dl>

<?php
}
if ($act == 'mail') {
    if (isset($_POST['PostMAILON'])) {
        $zbp->Config('tpure')->PostMAILON = $_POST['PostMAILON'];   //邮件通知开关
        $zbp->Config('tpure')->SMTP_SSL = $_POST['SMTP_SSL'];         //使用SSL
        $zbp->Config('tpure')->SMTP_HOST = $_POST['SMTP_HOST']; //SMTP服务器地址
        $zbp->Config('tpure')->SMTP_PORT = $_POST['SMTP_PORT'];           //SMTP服务器端口(25, 465, 587)
        $zbp->Config('tpure')->FROM_EMAIL = $_POST['FROM_EMAIL'];         //发信邮箱
        $zbp->Config('tpure')->SMTP_PASS = $_POST['SMTP_PASS'];         //发信邮箱密码
        $zbp->Config('tpure')->FROM_NAME = $_POST['FROM_NAME'];         //发件人名称
        $zbp->Config('tpure')->MAIL_TO = $_POST['MAIL_TO'];         //收信邮箱
        $zbp->Config('tpure')->PostNEWARTICLEMAILSENDON = $_POST['PostNEWARTICLEMAILSENDON'];           //新文章通知开关
        $zbp->Config('tpure')->PostEDITARTICLEMAILSENDON = $_POST['PostEDITARTICLEMAILSENDON'];           //编辑文章通知开关
        $zbp->Config('tpure')->PostCMTMAILSENDON = $_POST['PostCMTMAILSENDON'];           //评论通知开关
        $zbp->Config('tpure')->PostREPLYMAILSENDON = $_POST['PostREPLYMAILSENDON'];         //回复通知开关
        $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        tpure_CreateModule();
        $zbp->ShowHint('good');
    } ?>
<form name="side" method="post" class="setting">
    <input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
<dl>
    <dt>邮件通知设置<div class="mailinfo"<?php echo $zbp->Config('tpure')->PostMAILON == 1 ? '' : ' style="display:none"'; ?>> <input type="button" id="testmail" value="发送测试邮件"> <span id="testmailresult"></span></div></dt>
    <dd data-stretch="mail" class="half">
        <label>邮件通知</label>
        <input type="text" id="PostMAILON" name="PostMAILON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostMAILON; ?>" />
        <i class="help"></i><span class="helpcon">“ON”为开启邮件通知；<br>“OFF”为关闭邮件通知。</span>
    </dd>
    <div class="mailinfo"<?php echo $zbp->Config('tpure')->PostMAILON == 1 ? '' : ' style="display:none"'; ?>>
    <dd class="half">
        <label>使用SSL</label>
        <input type="text" id="SMTP_SSL" name="SMTP_SSL" class="checkbox" value="<?php echo $zbp->Config('tpure')->SMTP_SSL;?>" />
        <i class="help"></i><span class="helpcon">“ON”为使用SSL连接，端口号可设置465或587；<br>“OFF”为关闭SSL，端口号设置25；</span>
    </dd>
    <dd class="half">
        <label for="SMTP_HOST">SMTP服务器</label>
        <input type="text" id="SMTP_HOST" name="SMTP_HOST" placeholder="默认为smtp.163.com" value="<?php echo $zbp->Config('tpure')->SMTP_HOST; ?>" class="settext" />
        <i class="help"></i><span class="helpcon">请填写SMTP服务器地址，默认为smtp.163.com。</span>
    </dd>
    <dd class="half">
        <label for="SMTP_PORT">SMTP端口号</label>
        <input type="number" id="SMTP_PORT" name="SMTP_PORT" value="<?php echo $zbp->Config('tpure')->SMTP_PORT; ?>" min="1" step="1" class="settext" />
        <i class="help"></i><span class="helpcon">设置SMTP服务器端口号，默认为25，使用SSL为465或587。</span>
    </dd>
    <dd class="half">
        <label for="FROM_EMAIL">发信邮箱</label>
        <input type="text" id="FROM_EMAIL" name="FROM_EMAIL" value="<?php echo $zbp->Config('tpure')->FROM_EMAIL; ?>" class="settext" />
        <i class="help"></i><span class="helpcon">填写发信邮箱账号。</span>
    </dd>
    <dd class="half">
        <label for="SMTP_PASS">密码/授权码</label>
        <input type="password" id="SMTP_PASS" name="SMTP_PASS" value="<?php echo $zbp->Config('tpure')->SMTP_PASS; ?>" class="settext" />
        <i class="help"></i><span class="helpcon">填写SMTP邮箱账号密码/授权码；<br>QQ、163等邮箱使用的密码是独立的“授权码”(非密码)。</span>
    </dd>
    <dd class="half">
        <label for="FROM_NAME">发件人名称</label>
        <input type="text" id="FROM_NAME" name="FROM_NAME" value="<?php echo $zbp->Config('tpure')->FROM_NAME; ?>" class="settext" />
        <i class="help"></i><span class="helpcon">填写发件人名称，在邮件中会显示该名称。</span>
    </dd>
    <dd class="half">
        <label for="MAIL_TO">收信邮箱</label>
        <input type="text" id="MAIL_TO" name="MAIL_TO" value="<?php echo $zbp->Config('tpure')->MAIL_TO; ?>" class="settext" />
        <i class="help"></i><span class="helpcon">填写收信邮箱，不能与发信邮箱相同；<br><em>*收信邮箱用于接收测试邮件、发布文章和编辑文章通知。</em></span>
    </dd>
    <dd class="half">
        <label>发布文章通知</label>
        <input type="text" id="PostNEWARTICLEMAILSENDON" name="PostNEWARTICLEMAILSENDON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostNEWARTICLEMAILSENDON;?>" />
        <i class="help"></i><span class="helpcon">“ON”为发布新文章时向收信邮箱发送通知；<br>“OFF”为关闭新文章的通知；</span>
    </dd>
    <dd class="half">
        <label>编辑文章通知</label>
        <input type="text" id="PostEDITARTICLEMAILSENDON" name="PostEDITARTICLEMAILSENDON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostEDITARTICLEMAILSENDON;?>" />
        <i class="help"></i><span class="helpcon">“ON”为文章被编辑时向收信邮箱发送通知；<br>“OFF”为关闭编辑文章的通知；</span>
    </dd>
    <dd class="half">
        <label>评论通知作者</label>
        <input type="text" id="PostCMTMAILSENDON" name="PostCMTMAILSENDON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCMTMAILSENDON;?>" />
        <i class="help"></i><span class="helpcon">“ON”为文章收到评论或回复时，邮件通知文章作者；<br>“OFF”为不通知作者；<br><em>*文章作者邮箱为空或“null@null.com”时不通知；</em></span>
    </dd>
    <dd class="half">
        <label>回复通知游客</label>
        <input type="text" id="PostREPLYMAILSENDON" name="PostREPLYMAILSENDON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostREPLYMAILSENDON;?>" />
        <i class="help"></i><span class="helpcon">“ON”为评论收到回复时，邮件通知评论者；<br>“OFF”为新回复不通知评论者；<br>需在“<strong>基本设置-独立评论设置</strong>”中开启“<strong>评论邮箱</strong>”；<br><em>*评论者未填写邮箱或邮箱为“null@null.com”时不发送通知；</em></span>
    </dd>
    </div>
    <dd class="setok"><input type="submit" value="保存设置" class="setbtn" /></dd>
</dl>
</form>

<?php
}
if ($act == 'config') {
    if (isset($_POST['PostAJAXPOSTON'])) {
        $zbp->Config('tpure')->PostAJAXPOSTON = $_POST['PostAJAXPOSTON'];
        $zbp->Config('tpure')->PostSAVECONFIG = $_POST['PostSAVECONFIG'];           //保留配置开关
        $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        tpure_CreateModule();
        $zbp->ShowHint('good');
    } ?>
<dl>
    <form enctype="multipart/form-data" method="post" action="plugin/import.php" class="dtform">
    <dt>主题配置管理 <a href="plugin/export.php" target="_blank">导出主题设置</a> <a href="https://www.toyean.com/readset/" target="_blank">配置文件解析</a></dt>
    <dd>
        <label for="">导入主题设置</label>
        <table width="100%" border="0">
            <thead>
            <tr>
                <th>选择配置文件（<?php echo $zbp->themeapp->id?>.config）</th>
                <th>导入</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input name="configjson" type="file"/></td>
                <td><input type="Submit" class="format" value="导入配置"/></td>
            </tr>
            </tbody>
        </table>
        <i class="help"></i><span class="helpcon">喵~</span>
    </dd>
    </form>
    <form method="post" class="setting">
        <input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
        <dt>主题设置信息 <a href="plugin/resetconfig.php" data-confirm="重置默认设置前，建议导出当前主题设置备份，如需备份，请点击取消。">重置默认设置</a></dt>
        <dd class="half">
            <label>无刷新提交</label>
            <input type="text" id="PostAJAXPOSTON" name="PostAJAXPOSTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostAJAXPOSTON; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为主题设置页保存时采用Ajax无刷新提交；<br>“OFF”为常规表单提交方式；<br>初始时未开启也支持无刷新提交。</span>
        </dd>
        <dd class="half">
            <label>保留设置信息</label>
            <input type="text" id="PostSAVECONFIG" name="PostSAVECONFIG" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSAVECONFIG; ?>" />
            <i class="help"></i><span class="helpcon">“ON”为切换主题时保留配置信息；<br>“OFF”为切换主题时删除配置信息。<br>若不再使用当前主题，请选择"OFF"提交，切换主题自动清理主题设置。</span>
        </dd>
        <dd class="setok"><input type="submit" value="保存设置" class="setbtn" /></dd>
    </form>
</dl>
<?php } ?>
</div>
</div>
<div class="tfooter">
    <ul>
        <li><a href="https://www.toyean.com/advice.html" target="_blank">意见反馈</a></li>
        <li><a href="https://www.toyean.com/help.html" target="_blank">帮助说明</a></li>
        <li><a href="./style/fonts/demo_index.html" target="_blank">主题图标库</a></li>
        <li><a href="../../plugin/AppCentre/main.php?auth=e9210072-2342-4f96-91e7-7a6f35a7901e" target="_blank">更多作品</a></li>
        <li><a href="https://jq.qq.com/?_wv=1027&k=44zyTKi" target="_blank">主题交流群</a></li>
    </ul>
	<p>Copyright &copy; 2010-<script>document.write(new Date().getFullYear());</script> <a href="https://www.toyean.com/" target="_blank">拓源网</a> all rights reserved.</p>
</div>

<script type="text/javascript">ActiveTopMenu("topmenu_tpure");</script>
<?php
require $zbp->path . 'zb_system/admin/admin_footer.php';
RunTime();
?>