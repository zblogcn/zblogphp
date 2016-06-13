<?php
/**
 * KindEditor for Z-BlogPHP
 * @author 未寒
 * @copyright (C) RainbowSoft Studio
 */
define('KINDEDITOR_IS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

require '../../../../../../zb_system/function/c_system_base.php';
global $zbp;
$zbp->Load();

$root_path = $zbp->usersdir . 'emotion/';
$root_url = $zbp->host . 'zb_users/emotion/';
$emot_ext = explode("|", $zbp->option['ZC_EMOTICONS_FILETYPE']);

if ($handle = opendir($root_path)) {
	while (false !== ($filename = readdir($handle))) {
		if ($filename{0} == '.') {
			continue;
		}

		$file = $root_path . $filename;
		if (is_dir($file)) {
			$emot_dir[] = (KINDEDITOR_IS_WINDOWS ? iconv('GBK', 'UTF-8', $filename) : $filename);
		} else {
			continue;
		}
		if ($emot = opendir($root_path . $filename . '/')) {
			while (false !== ($emotname = readdir($emot))) {
				if ($emotname{0} == '.') {
					continue;
				}

				$emotpath = $root_path . $emotname . '/' . $emotname;
				if (!is_dir($emotpath)) {
					$temp_arr = explode(".", $emotname);
					$file_ext = array_pop($temp_arr);
					$file_ext = trim($file_ext);
					$file_ext = strtolower($file_ext);
					if (in_array($file_ext, $emot_ext) === true) {
						$encoded = (KINDEDITOR_IS_WINDOWS ? iconv('GBK', 'UTF-8', $filename) : $filename);
						$emot_name[$encoded][] = (KINDEDITOR_IS_WINDOWS ? iconv('GBK', 'UTF-8', $emotname) : $emotname);
					}
				}
			}
		}
	}

	closedir($handle);

	//print_r($emot_dir);
	//print_r($emot_name);
}

?>
/*******************************************************************************
* @author panderman <panderman@163.com>
* @site http://www.xunwee.com/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/
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
                        <?php
for ($i = 0; $i < count($emot_dir); $i++) {echo '<div>' . $emot_dir[$i] . '</div>';}
?>
                        </div>\

                            <div id="tabContent" class="neweditor-tab-b">\
                        <?php
for ($i = 0; $i < count($emot_dir); $i++) {echo '<div>' . $i . '</div>';}
?>
                            </div>\
                        </div>\
                    <div id="tabIconReview"><img id="faceReview" class="review" src="<?php echo $zbp->host; ?>zb_system/image/admin/none.gif" /></div></div>\
                    </div>';
        menu.div.append(K(html));
        function removeEvent() {
            K.each(elements, function () {
                this.unbind();
            });
        }

        /*******************************************表情方法开始*****************************************/
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
        emotion.SmileyBox = {
        <?php
for ($i = 0; $i < count($emot_dir); $i++) {
	echo 'tab' . $i . ':[';
	$emot_char = '';
	if ($i == (count($emot_dir) - 1)) {
		echo ']';
		continue;}
	foreach ($emot_name[$emot_dir[$i]] as $v) {
		$emot_char .= "'$v',";
	}
	echo $emot_char . '],';
}
?>};
        emotion.SmileyInfor = emotion.SmileyBox;
        var faceBox = emotion.SmileyBox;
        var inforBox = emotion.SmileyInfor;
        var sBasePath = emotion.SmileyPath;
        initImgBox(faceBox['tab0'], '', 84);
        initImgBox(faceBox['tab1'], '', 40);

        //大对象
        FaceHandler = {
            imageFolders: {<?php for ($i = 0; $i < count($emot_dir); $i++) {echo 'tab' . $i . ':\'' . $emot_dir[$i] . '/\',';}?>},
            imageWidth: {<?php for ($i = 0; $i < count($emot_dir); $i++) {echo 'tab' . $i . ':30,';}?>},
            imageCols: {<?php for ($i = 0; $i < count($emot_dir); $i++) {echo 'tab' . $i . ':11,';}?>},
            imageColWidth: {<?php for ($i = 0; $i < count($emot_dir); $i++) {echo 'tab' . $i . ':3,';}?>},
            imageCss: {<?php for ($i = 0; $i < count($emot_dir); $i++) {echo 'tab' . $i . ':\'' . $emot_dir[$i] . '\',';}?>},
            imageCssOffset: {<?php for ($i = 0; $i < count($emot_dir); $i++) {echo 'tab' . $i . ':30,';}?>},
            tabExist: [<?php for ($i = 0; $i < count($emot_dir); $i++) {echo $i . ',';}?>]
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