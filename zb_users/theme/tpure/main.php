<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('tpure')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = '拓源纯净主题设置';

$dependplugin = array('UEditor'=>'UEditor编辑器');
foreach ($dependplugin as $key=>$pluginname) {
    if (!$zbp->CheckPlugin($key)) {
        $zbp->ShowHint('bad', '请您安装或启用 ' . $pluginname . ' (' . $key . ') 插件！');
    }
}

$act = $_GET['act'] == "base" ? 'base' : $_GET['act'];

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
if ($zbp->CheckPlugin('UEditor')) {
    echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.config.php"></script>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';
}
?>
<link rel="stylesheet" href="./script/admin.css">
<script src="./script/custom.js"></script>
<div class="twrapper">
<div class="theader">
	<div class="theadbg"><div class="tips">提示：<strong>Ctrl + S</strong> 可快速保存设置</div></div>
	<div class="tuser">
		<div class="tuserimg"><img src="style/images/sethead.png" alt=""></div>
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
        $zbp->Config('tpure')->PostLOGO = $_POST['PostLOGO'];					//网站LOGO
        $zbp->Config('tpure')->PostLOGOON = $_POST['PostLOGOON'];					//图片LOGO开关
        $zbp->Config('tpure')->PostFAVICON = $_POST['PostFAVICON'];				//浏览器标签栏图标
        $zbp->Config('tpure')->PostFAVICONON = $_POST['PostFAVICONON'];				//浏览器标签栏图标开关
        $zbp->Config('tpure')->PostTHUMB = $_POST['PostTHUMB'];				//固定缩略图
        $zbp->Config('tpure')->PostTHUMBON = $_POST['PostTHUMBON'];             //随机缩略图开关
        $zbp->Config('tpure')->PostBANNERON = $_POST['PostBANNERON'];             //启用首页banner
        $zbp->Config('tpure')->PostBANNER = $_POST['PostBANNER'];             //首页banner图片
		$zbp->Config('tpure')->PostBANNERDISPLAYON = $_POST['PostBANNERDISPLAYON'];			//首页banner滚动效果开关
		$zbp->Config('tpure')->PostBANNERFONT = $_POST['PostBANNERFONT'];         //首页banner文字内容
        $zbp->Config('tpure')->PostBANNERPCHEIGHT = $_POST['PostBANNERPCHEIGHT'];         //首页banner电脑端高度
        $zbp->Config('tpure')->PostBANNERMHEIGHT = $_POST['PostBANNERMHEIGHT'];         //首页banner移动端端高度
        $zbp->Config('tpure')->PostIMGON = $_POST['PostIMGON'];				//列表缩略图总开关
        $zbp->Config('tpure')->PostSEARCHON = $_POST['PostSEARCHON'];			//导航搜索功能开关
        $zbp->Config('tpure')->PostSCHTXT = $_POST['PostSCHTXT'];				//导航搜索默认文字
        $zbp->Config('tpure')->PostVIEWALLON = $_POST['PostVIEWALLON'];	//内容页查看全部开关
        $zbp->Config('tpure')->PostVIEWALLHEIGHT = $_POST['PostVIEWALLHEIGHT'];
        $zbp->Config('tpure')->PostVIEWALLSTYLE = $_POST['PostVIEWALLSTYLE'];
        $zbp->Config('tpure')->PostLISTINFO = json_encode($_POST['post_list_info']);		//列表辅助信息
        $zbp->Config('tpure')->PostARTICLEINFO = json_encode($_POST['post_article_info']);	//文章辅助信息
        $zbp->Config('tpure')->PostPAGEINFO = json_encode($_POST['post_page_info']);		//页面辅助信息
        $zbp->Config('tpure')->PostSINGLEKEY = $_POST['PostSINGLEKEY']; //上下篇左右键翻页
        $zbp->Config('tpure')->PostPAGEKEY = $_POST['PostPAGEKEY'];			//列表底部分页条左右键翻页
        $zbp->Config('tpure')->PostRELATEON = $_POST['PostRELATEON'];			//文章页相关文章开关
		$zbp->Config('tpure')->PostRELATECATE = $_POST['PostRELATECATE'];			//仅限本分类开关
        $zbp->Config('tpure')->PostRELATENUM = $_POST['PostRELATENUM'];			//文章页相关文章展示个数
		$zbp->Config('tpure')->PostFILTERCATEGORY = $_POST['PostFILTERCATEGORY'];		//首页过滤分类
        $zbp->Config('tpure')->PostINTRONUM = $_POST['PostINTRONUM'];			//摘要字数限制
		$zbp->Config('tpure')->PostSITEMAPON = $_POST['PostSITEMAPON'];			//面包屑导航
        $zbp->Config('tpure')->PostMOREBTNON = $_POST['PostMOREBTNON'];				//列表查看全文按钮开关
        $zbp->Config('tpure')->PostARTICLECMTON = $_POST['PostARTICLECMTON'];		//文章评论开关
        $zbp->Config('tpure')->PostPAGECMTON = $_POST['PostPAGECMTON'];			//页面评论开关
        $zbp->Config('tpure')->PostFIXMENUON = $_POST['PostFIXMENUON'];				//导航悬浮开关
        $zbp->Config('tpure')->PostLOGOHOVERON = $_POST['PostLOGOHOVERON'];			//LOGO划过动效开关
        $zbp->Config('tpure')->PostBLANKON = $_POST['PostBLANKON'];				//全局链接新窗口开关
        $zbp->Config('tpure')->PostGREYON = $_POST['PostGREYON'];				//整站变灰开关
        $zbp->Config('tpure')->PostREMOVEPON = $_POST['PostREMOVEPON'];			//隐藏文章空段落开关
        $zbp->Config('tpure')->PostTIMEAGOON = $_POST['PostTIMEAGOON'];			//个性化时间
        $zbp->Config('tpure')->PostBACKTOTOPON = $_POST['PostBACKTOTOPON'];		//返回顶部开关
		$zbp->Config('tpure')->PostAJAXPOSTON = $_POST['PostAJAXPOSTON'];		//无刷新提交
        $zbp->Config('tpure')->PostSAVECONFIG = $_POST['PostSAVECONFIG'];			//保存配置开关
        $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        $zbp->ShowHint('good');
    } ?>
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
						<th width=20%>图片LOGO与动效</th>
					</tr>
					<tr>
						<td><?php if ($zbp->Config('tpure')->PostLOGO) { ?><img src="<?php echo $zbp->Config('tpure')->PostLOGO; ?>" alt="" width="120" class="thumbimg"><?php } else { ?><img src="style/images/logo.png" alt="" width="120" class="thumbimg"><?php } ?></td>
						<td><input type="text" id="PostLOGO" name="PostLOGO" value="<?php if ($zbp->Config('tpure')->PostLOGO) {
        echo $zbp->Config('tpure')->PostLOGO;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/logo.png';
    } ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg" value="上传"></td>
						<td>是否启用 <input type="text" id="PostLOGOON" name="PostLOGOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLOGOON; ?>"><br>启用动效 <input type="text" id="PostLOGOHOVERON" name="PostLOGOHOVERON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostLOGOHOVERON; ?>"></td>
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
						<th width="20%">是否启用图标</th>
					</tr>
					<tr>
						<td><?php if ($zbp->Config('tpure')->PostFAVICON) { ?><img src="<?php echo $zbp->Config('tpure')->PostFAVICON; ?>" alt="" width="16" class="thumbimg"><?php } else { ?><img src="style/images/favicon.ico" alt="" width="16" class="thumbimg"><?php } ?></td>
						<td><input type="text" id="PostFAVICON" name="PostFAVICON" value="<?php if ($zbp->Config('tpure')->PostFAVICON) {
        echo $zbp->Config('tpure')->PostFAVICON;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/favicon.ico';
    } ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg" value="上传"></td>
						<td><input type="text" id="PostFAVICONON" name="PostFAVICONON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFAVICONON; ?>"></td>
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
						<th width="25%">缩略图</th>
						<th width="35%">图片地址</th>
						<th width="15%">上传</th>
						<th width="20%">是否启用默认图</th>
					</tr>
					<tr>
						<td><?php if ($zbp->Config('tpure')->PostTHUMB) { ?><img src="<?php echo $zbp->Config('tpure')->PostTHUMB; ?>" alt="" width="120" class="thumbimg"><?php } else { ?><img src="style/images/thumb.png" alt="" width="120" class="thumbimg"><?php } ?></td>
						<td><input type="text" id="PostTHUMB" name="PostTHUMB" value="<?php if ($zbp->Config('tpure')->PostTHUMB) {
        echo $zbp->Config('tpure')->PostTHUMB;
    } else {
        echo $zbp->host . 'zb_users/theme/tpure/style/images/thumb.png';
    } ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg" value="上传"></td>
						<td>无图默认 <input type="text" id="PostTHUMBON" name="PostTHUMBON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostTHUMBON; ?>"><br>有则展示 <input type="text" id="PostIMGON" name="PostIMGON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostIMGON; ?>"></td>
					</tr>
				</tbody>
			</table>
			<i class="help"></i><span class="helpcon">上传默认缩略图，需开启“无图默认”开关。<br>仅显示文章缩略图，请开启“有则展示”开关。<br>如不需要任何缩略图，请关闭“有则展示”开关。</span>
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
						<td><img src="<?php echo $zbp->Config('tpure')->PostBANNER; ?>" alt="" width="120" class="thumbimg"></td>
						<td><input type="text" id="PostBANNER" name="PostBANNER" value="<?php echo $zbp->Config('tpure')->PostBANNER; ?>" class="urltext thumbsrc"></td>
						<td><input type="button" class="uploadimg format" value="上传"></td>
						<td>是否启用：<input type="text" id="PostBANNERON" name="PostBANNERON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBANNERON; ?>"><br>视差滚动：<input type="text" id="PostBANNERDISPLAYON" name="PostBANNERDISPLAYON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBANNERDISPLAYON; ?>"></td>
					</tr>
					<tr>
						<th colspan="2">Banner文字内容</th>
						<th>PC高度(px)</th>
						<th>移动端高度(px)</th>
					</tr>
					<tr>
						<td colspan="2"><input type="text" id="PostBANNERFONT" name="PostBANNERFONT" value="<?php echo $zbp->Config('tpure')->PostBANNERFONT; ?>" class="urltext thumbsrc" style="width:390px;"></td>
						<td><input type="number" id="PostBANNERPCHEIGHT" name="PostBANNERPCHEIGHT" value="<?php echo $zbp->Config('tpure')->PostBANNERPCHEIGHT; ?>" style="width:80px;"></td>
						<td><input type="number" id="PostBANNERMHEIGHT" name="PostBANNERMHEIGHT" value="<?php echo $zbp->Config('tpure')->PostBANNERMHEIGHT; ?>" style="width:80px;"></td>
					</tr>
				</tbody>
			</table>
			<i class="help"></i><span class="helpcon">“ON”为开启首页Banner背景视差滚动效果；<br>“OFF”为关闭Banner背景视差滚动效果。<br>Banner文字调用后台右上角“网站设置-网站副标题”。</span>
		</dd>
		<dt>搜索设置</dt>
		<dd class="half">
			<label>导航搜索开关</label>
			<input type="text" id="PostSEARCHON" name="PostSEARCHON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSEARCHON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为显示导航搜索；<br>“OFF”为隐藏导航搜索。</span>
		</dd>
		<dd class="half">
			<label for="PostSCHTXT">搜索默认文字</label>
			<input type="text" id="PostSCHTXT" name="PostSCHTXT" value="<?php echo $zbp->Config('tpure')->PostSCHTXT; ?>" class="settext">
			<i class="help"></i><span class="helpcon">导航搜索条中默认显示的文字</span>
		</dd>
		<dt>文章页阅读更多设置</dt>
		<dd data-stretch="viewall">
			<label>阅读更多开关</label>
			<input type="text" id="PostVIEWALLON" name="PostVIEWALLON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostVIEWALLON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为启用长文章正文自动折叠；<br>“OFF”为加载全部正文。</span>
		</dd>
		<div class="viewallinfo"<?php echo $zbp->Config('tpure')->PostVIEWALLON == 1 ? '' : ' style="display:none"'; ?>>
		<dd class="half">
			<label for="PostVIEWALLHEIGHT">自动阅读高度</label>
			<input type="number" id="PostVIEWALLHEIGHT" name="PostVIEWALLHEIGHT" value="<?php echo $zbp->Config('tpure')->PostVIEWALLHEIGHT; ?>" min="1" step="1" class="settext">
			<i class="help"></i><span class="helpcon">设置页面已读区域高度(单位px)。</span>
		</dd>
		<dd class="half">
			<label>阅读更多样式</label>
			<div class="layoutset">
				<input type="radio" id="layoutl" name="PostVIEWALLSTYLE" value="1" <?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '1' ? 'checked="checked"' : ''; ?> class="hideradio">
				<label for="layoutl"<?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '1' ? ' class="on"' : ''; ?>><img src="style/images/viewallstyle1.png" alt=""></label>
				<input type="radio" id="layoutr" name="PostVIEWALLSTYLE" value="0" <?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '0' ? 'checked="checked"' : ''; ?> class="hideradio">
				<label for="layoutr"<?php echo $zbp->Config('tpure')->PostVIEWALLSTYLE == '0' ? ' class="on"' : ''; ?>><img src="style/images/viewallstyle0.png" alt=""></label>
			</div>
			<i class="help"></i><span class="helpcon">“ON”为显示未读百分比；<br>“OFF”为显示查看更多按钮样式。</span>
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
            <i class="help"></i><span class="helpcon">列表页辅助信息，自定义选择启用，支持拖拽排序。</span>
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
			<i class="help"></i><span class="helpcon">文章页辅助信息，自定义选择启用，支持拖拽排序。</span>
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
			<label>上下篇翻页</label>
			<input type="text" id="PostSINGLEKEY" name="PostSINGLEKEY" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSINGLEKEY; ?>">
			<i class="help"></i><span class="helpcon">“ON”为开启文章上一篇与下一篇左右键翻页；<br>“OFF”为关闭文章上一篇与下一篇左右键翻页。</span>
		</dd>
		<dd class="half">
			<label>列表分页条</label>
			<input type="text" id="PostPAGEKEY" name="PostPAGEKEY" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostPAGEKEY; ?>">
			<i class="help"></i><span class="helpcon">“ON”为开启列表底部分页条左右键翻页；<br>“OFF”为关闭列表底部分页条左右键翻页。</span>
		</dd>
		<dt>相关文章设置</dt>
		<dd data-stretch="relate" class="half">
			<label>相关文章开关</label>
			<input type="text" id="PostRELATEON" name="PostRELATEON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostRELATEON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为显示文章页相关文章；<br>“OFF”为文章页不加载相关文章。</span>
		</dd>
		<div class="relateinfo"<?php echo $zbp->Config('tpure')->PostRELATEON == 1 ? '' : ' style="display:none"'; ?>>
		<dd class="half">
			<label>仅限本分类</label>
			<input type="text" id="PostRELATECATE" name="PostRELATECATE" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostRELATECATE; ?>">
			<i class="help"></i><span class="helpcon">开启状态为仅显示文章页所在分类下的随机文章；<br>关闭状态为显示含有同标签的随机文章(不限分类)。</span>
		</dd>
		<dd>
			<label for="PostRELATENUM">相关文章条数</label>
			<input type="number" id="PostRELATENUM" name="PostRELATENUM" value="<?php echo $zbp->Config('tpure')->PostRELATENUM; ?>" min="1" step="1" class="settext">
			<i class="help"></i><span class="helpcon">设置文章页相关文章条数，默认6条。</span>
		</dd>
		</div>
		<dt>首页过滤分类文章设置</dt>
		<dd class="half">
            <label for="PostFILTERCATEGORY">首页过滤分类</label>
            <select size="1" name="PostFILTERCATEGORY" id="PostFILTERCATEGORY"><?php echo tpure_Exclude_CategorySelect($zbp->Config('tpure')->PostFILTERCATEGORY); ?></select>
            <i class="help"></i><span class="helpcon">设置首页不显示此分类下的文章。</span>
        </dd>
        <dd class="PostFILTERCATEGORYID half">
            <label for="PostFILTERCATEGORYID">过滤的分类ID</label>
            <input type="text" id="PostFILTERCATEGORYID" name="PostFILTERCATEGORY" value="<?php echo $zbp->Config('tpure')->PostFILTERCATEGORY; ?>" placeholder="不过滤请留空" class="settext">
            <i class="help"></i><span class="helpcon">请填写首页需要屏蔽的分类ID；<br>多个分类ID之间用逗号分隔；<br>不限制分类数量及顺序，不过滤请留空。<br><em>分类ID在左侧分类管理中查看。</em></span>
        </dd>
		<dt>其他设置</dt>
		<dd>
			<label for="PostINTRONUM">摘要字数</label>
			<input type="number" id="PostINTRONUM" name="PostINTRONUM" value="<?php echo $zbp->Config('tpure')->PostINTRONUM; ?>" step="10" class="settext">
			<i class="help"></i><span class="helpcon">列表摘要字数限制，留空则显示系统摘要。</span>
		</dd>
		<dd class="half">
			<label>面包屑导航</label>
			<input type="text" id="PostSITEMAPON" name="PostSITEMAPON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSITEMAPON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为显示面包屑导航；<br>“OFF”为关闭面包屑导航。</span>
		</dd>
		<dd class="half">
			<label>查看全文按钮</label>
			<input type="text" id="PostMOREBTNON" name="PostMOREBTNON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostMOREBTNON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为显示列表查看全文按钮；<br>“OFF”为隐藏列表查看全文按钮。</span>
		</dd>
		<dd class="half">
			<label>文章评论开关</label>
			<input type="text" id="PostARTICLECMTON" name="PostARTICLECMTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostARTICLECMTON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为显示文章（article）评论功能；<br>“OFF”为隐藏文章（article）评论功能。</span>
		</dd>
		<dd class="half">
			<label>页面评论开关</label>
			<input type="text" id="PostPAGECMTON" name="PostPAGECMTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostPAGECMTON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为显示页面（page）评论功能；<br>“OFF”为隐藏页面（page）评论功能。</span>
		</dd>
		<dd class="half">
			<label>导航悬浮开关</label>
			<input type="text" id="PostFIXMENUON" name="PostFIXMENUON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostFIXMENUON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为开启导航菜单浮动屏幕顶部；<br>“OFF”为导航固定页面顶部。</span>
		</dd>
		<dd class="half">
			<label>链接新窗口</label>
			<input type="text" id="PostBLANKON" name="PostBLANKON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBLANKON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为链接最佳SEO状态；<br>“OFF”为全站链接新窗口打开。</span>
		</dd>
		<dd class="half">
			<label>整站变灰</label>
			<input type="text" id="PostGREYON" name="PostGREYON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostGREYON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为开启整站变灰效果；<br>“OFF”为关闭整站变灰效果。</span>
		</dd>
		<dd class="half">
			<label>清理空段落</label>
			<input type="text" id="PostREMOVEPON" name="PostREMOVEPON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostREMOVEPON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为清理文章页空白段落；<br>“OFF”为显示文章页空白段落。</span>
		</dd>
		<dd class="half">
			<label>友好化时间</label>
			<input type="text" id="PostTIMEAGOON" name="PostTIMEAGOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostTIMEAGOON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为友好化时间[格式如：5分钟前]；<br>“OFF”为传统时间[格式如：1970-01-01]；</span>
		</dd>
		<dd class="half">
			<label>返回顶部开关</label>
			<input type="text" id="PostBACKTOTOPON" name="PostBACKTOTOPON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostBACKTOTOPON; ?>">
			<i class="help"></i><span class="helpcon">“ON”为启用"返回顶部"功能；<br>“OFF”为取消"返回顶部"功能。</span>
		</dd>
		<dd class="half">
            <label>无刷新提交</label>
            <input type="text" id="PostAJAXPOSTON" name="PostAJAXPOSTON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostAJAXPOSTON; ?>">
            <i class="help"></i><span class="helpcon">开启状态为主题设置页保存时采用Ajax无刷新提交；<br>关闭状态为常规表单提交方式；<br>初始时未开启也支持无刷新提交。</span>
        </dd>
		<dd class="half">
			<label>保存配置信息</label>
			<input type="text" id="PostSAVECONFIG" name="PostSAVECONFIG" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostSAVECONFIG; ?>">
			<i class="help"></i><span class="helpcon">“ON”为保存配置信息，启用或卸载主题后不清空配置信息；<br>“OFF”为删除配置信息，启用或卸载主题后将清空配置信息。<br>若不再使用本主题，请选择"OFF"提交，则清空配置信息。</span>
		</dd>
		<dd class="setok"><input type="submit" value="保存设置" class="setbtn"></dd>
	</form>
</dl>

<?php
}
if ($act == 'seo') {
    if (isset($_POST['SEOON'])) {
        $zbp->Config('tpure')->SEOON = $_POST['SEOON'];						//关键词设置
    $zbp->Config('tpure')->SEOTITLE = $_POST['SEOTITLE'];						//关键词设置
    $zbp->Config('tpure')->SEOKEYWORDS = $_POST['SEOKEYWORDS'];				//关键词设置
    $zbp->Config('tpure')->SEODESCRIPTION = $_POST['SEODESCRIPTION'];			//描述设置
    $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        $zbp->ShowHint('good');
    } ?>
<form name="seo" method="post" class="setting">
	<input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
<dl>
	<dt>SEO设置</dt>
	<dd data-stretch="seo" class="seoon">
		<label>SEO开关</label>
		<input type="text" id="SEOON" name="SEOON" class="checkbox" value="<?php echo $zbp->Config('tpure')->SEOON; ?>">
		<i class="help"></i><span class="helpcon">“ON”为启用首页/分类/标签/文章自定义SEO信息；<br>“OFF”为关闭自定义SEO信息。<br>开启后，编辑文章/分类/标签时可设置自定义SEO信息。</span>
	</dd>
    <div class="seoinfo"<?php echo $zbp->Config('tpure')->SEOON == 1 ? '' : ' style="display:none"'; ?>>
        <dd><label for="SEOTITLE">首页标题</label><input type="text" name="SEOTITLE" id="SEOTITLE" class="settext" value="<?php echo $zbp->Config('tpure')->SEOTITLE; ?>"><i class="help"></i><span class="helpcon">请设置网站首页标题。</span></dd>
        <dd><label for="SEOKEYWORDS">首页关键词</label><input type="text" name="SEOKEYWORDS" id="SEOKEYWORDS" class="settext" value="<?php echo $zbp->Config('tpure')->SEOKEYWORDS; ?>"><i class="help"></i><span class="helpcon">请设置网站首页关键词。</span></dd>
        <dd><label for="SEODESCRIPTION">首页描述</label><textarea name="SEODESCRIPTION" id="SEODESCRIPTION" cols="30" rows="3" class="setinput"><?php echo $zbp->Config('tpure')->SEODESCRIPTION; ?></textarea><i class="help"></i><span class="helpcon">请设置网站首页描述。</span></dd>
	</div>
	<dd class="setok"><input type="submit" value="保存设置" class="setbtn"></dd>
</dl>
</form>

<?php
}
if ($act == 'color') {
    if (isset($_POST['PostCOLORON'])) {
        $zbp->Config('tpure')->PostCOLORON = $_POST['PostCOLORON'];				//自定义配色开关
		$zbp->Config('tpure')->PostCOLOR = $_POST['PostCOLOR'];					//主色调
		$zbp->Config('tpure')->PostBGCOLOR = $_POST['PostBGCOLOR'];			//页面背景色
		$zbp->Config('tpure')->PostSIDELAYOUT = $_POST['PostSIDELAYOUT'];			//侧栏位置
		$zbp->Config('tpure')->PostCUSTOMCSS = $_POST['PostCUSTOMCSS'];			//自定义CSS
		$tpure_color = tpure_color();
        @file_put_contents($zbp->path . 'zb_users/theme/tpure/include/skin.css', $tpure_color);
        $zbp->SaveConfig('tpure');
        $zbp->BuildTemplate();
        $zbp->ShowHint('good');
    } ?>
<script src="./plugin/jscolor/jscolor.js"></script>
<form name="color" method="post" class="setting">
	<input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken() ?>">
<dl>
	<dt>色彩设置</dt>
	<dd data-stretch="color" class="coloron">
		<label>自定义配色</label>
		<input type="text" id="PostCOLORON" name="PostCOLORON" class="checkbox" value="<?php echo $zbp->Config('tpure')->PostCOLORON; ?>">
		<i class="help"></i><span class="helpcon">“ON”为启用自定义配色；<br>“OFF”为使用主题默认颜色。</span>
	</dd>
	<div class="colorinfo"<?php echo $zbp->Config('tpure')->PostCOLORON == 1 ? '' : ' style="display:none"'; ?>>
	<dd class="half">
		<label for="PostCOLOR">主色调</label>
		<input type="text" name="PostCOLOR" id="PostCOLOR" class="color settext" value="<?php echo $zbp->Config('tpure')->PostCOLOR; ?>">
		<i class="help"></i><span class="helpcon">请设置网站主色调,默认为 004c98。</span>
	</dd>
	<dd class="half">
		<label for="PostBGCOLOR">页面背景色</label>
		<input type="text" name="PostBGCOLOR" id="PostBGCOLOR" class="color settext" value="<?php echo $zbp->Config('tpure')->PostBGCOLOR; ?>">
		<i class="help"></i><span class="helpcon">请设置页面body背景色，默认为 ffffff。</span>
	</dd>
	<dd>
		<label for="">侧栏位置</label>
		<div class="layoutset">
			<input type="radio" id="layoutl" name="PostSIDELAYOUT" value="l" <?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'l' ? 'checked="checked"' : ''; ?> class="hideradio">
			<label for="layoutl"<?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'l' ? ' class="on"' : ''; ?>><img src="style/images/sideleft.png" alt=""></label>
			<input type="radio" id="layoutr" name="PostSIDELAYOUT" value="r" <?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'r' ? 'checked="checked"' : ''; ?> class="hideradio">
			<label for="layoutr"<?php echo $zbp->Config('tpure')->PostSIDELAYOUT == 'r' ? ' class="on"' : ''; ?>><img src="style/images/sideright.png" alt=""></label>
		</div>
		<i class="help"></i><span class="helpcon">请设置侧栏位置，默认为侧栏居右。</span>
	</dd>
	<dd>
		<label for="">自定义CSS</label>
		<div class="layoutset">
			<textarea name="PostCUSTOMCSS" id="PostCUSTOMCSS" cols="30" rows="5" class="setinput"><?php echo $zbp->Config('tpure')->PostCUSTOMCSS; ?></textarea>
		</div>
		<i class="help"></i><span class="helpcon">自定义CSS，辅助配色。</span>
	</dd>
	</div>
	<dd class="setok"><input type="submit" value="保存设置" class="setbtn"></dd>
</dl>
</form>

<?php
} ?>
</div>
</div>
<div class="tfooter">
	<p>Copyright &copy; 2010-<script>document.write(new Date().getFullYear());</script> <a href="https://www.toyean.com/" target="_blank">拓源网</a> all rights reserved.</p>
</div>

<script>ActiveTopMenu("topmenu_tpure");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>