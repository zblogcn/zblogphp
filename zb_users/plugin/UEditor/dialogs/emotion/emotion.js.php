<?php
define('UEDITOR_IS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

require '../../../../../zb_system/function/c_system_base.php';

header('Content-Type: application/x-javascript; Charset=utf-8');
?>
window.onload = function () {
    editor.setOpt({
        emotionLocalization:false
    });

    emotion.SmileyPath = editor.options.emotionLocalization === true ? 'images/' : "<?php echo $bloghost; ?>zb_users/emotion/";
<?php
function scansubdir($dir)
{
    $files = array();
    if ($handle = opendir($dir)) {
        while (($file = readdir($handle)) !== false) {
            if ($file != ".." && $file != ".") {
                if (is_dir($dir . "/" . $file)) {
                    $f = scandir($dir . "/" . $file);
                    $files[$file] = preg_grep("/(.*).gif|png|jpg|webp$/", $f);
                    foreach ($files[$file] as $name => $value) {
                        if (UEDITOR_IS_WINDOWS) {
                            $files[$file][$name] = iconv('GBK', 'UTF-8', $value);
                        }
                    }
                } else {
                    //$files[] = $file;
                }
            }
        }
        closedir($handle);

        return $files;
    }
}

$d = $blogpath . "zb_users/emotion";

$f = scansubdir($d);

$x = 0;
?>
<?php
foreach ($f as $key => $i) {
    $e = implode("','", $i);
    $en = $key;
    $en = (UEDITOR_IS_WINDOWS ? iconv('GBK', 'UTF-8', $en) : $en); ?>
	emotion.tabNum++;
	emotion.SmilmgName["tab<?php echo $x; ?>"]=['<?php echo substr($i[2], strlen($i[2]) - 3); ?>',<?php echo count($i); ?>];
	emotion.imageFolders["tab<?php echo $x; ?>"]='<?php echo urlencode($en); ?>/';
	emotion.imageCss["tab<?php echo $x; ?>"]='<?php echo $en; ?>';
	emotion.imageCssOffset["tab<?php echo $x; ?>"]=35;
	emotion.SmileyInfor["tab<?php echo $x; ?>"]=['<?php echo $e; ?>'];
	var tp=$G('tabHeads'),tc=$G('tabBodys');
	var dtp=document.createElement("span");
        dtp.innerHTML  ="<?php echo $en; ?>";
		tp.appendChild(dtp);
		tp.innerHTML = tp.innerHTML+" \n";
	var dtc=document.createElement("div");
		dtc.id='tab<?php echo $x; ?>';
		tc.appendChild(dtc);

<?php
    $x++;
}?>

    emotion.SmileyBox = createTabList( emotion.tabNum );
    emotion.tabExist = createArr( emotion.tabNum );

    initImgName();
    initEvtHandler( "tabHeads" );
};

function initImgName() {
    for ( var pro in emotion.SmilmgName ) {
        var tempName = emotion.SmilmgName[pro],
            tempBox = emotion.SmileyBox[pro],
			tempStr=emotion.SmileyInfor[pro];

        if ( tempBox.length ) return;
        for ( var i = 0; i < tempName[1]; i++ ) {
            tempBox.push( tempStr[i]);
        }
    }
}

function initEvtHandler( conId ) {
    var tabHeads = $G( conId );
    for ( var i = 0, j = 0; i < tabHeads.childNodes.length; i++ ) {
        var tabObj = tabHeads.childNodes[i];
        if ( tabObj.nodeType == 1 ) {
            domUtils.on( tabObj, "click", (function ( index ) {
                return function () {
                    switchTab( index );
                };
            })( j ) );
            j++;
        }
    }
    switchTab( 0 );
    $G( "tabIconReview" ).style.display = 'none';
}

function InsertSmiley( url, evt ) {
    var obj = {
        src:editor.options.emotionLocalization ? editor.options.UEDITOR_HOME_URL + "dialogs/emotion/" + url : url
    };
    obj.data_ue_src = obj.src;
    editor.execCommand( 'insertimage', obj );
    if ( !evt.ctrlKey ) {
        dialog.popup.hide();
    }
}

function switchTab( index ) {

    autoHeight( index );
    if ( emotion.tabExist[index] == 0 ) {
        emotion.tabExist[index] = 1;
        createTab( 'tab' + index );
    }
    //获取呈现元素句柄数组
    var tabHeads = $G( "tabHeads" ).getElementsByTagName( "span" ),
            tabBodys = $G( "tabBodys" ).getElementsByTagName( "div" ),
            i = 0, L = tabHeads.length;
    //隐藏所有呈现元素
    for ( ; i < L; i++ ) {
        tabHeads[i].className = "";
        tabBodys[i].style.display = "none";
    }
    //显示对应呈现元素
    tabHeads[index].className = "focus";
    tabBodys[index].style.display = "block";
}

function autoHeight( index ) {
    var iframe = dialog.getDom( "iframe" ),
            parent = iframe.parentNode.parentNode;
}


function createTab( tabName ) {
    var faceVersion = "?v=1.1", //版本号
            tab = $G( tabName ), //获取将要生成的Div句柄
            imagePath = emotion.SmileyPath + emotion.imageFolders[tabName], //获取显示表情和预览表情的路径
            positionLine = 11 / 2, //中间数
            iWidth = iHeight = 35, //图片长宽
            iColWidth = 3, //表格剩余空间的显示比例
            tableCss = emotion.imageCss[tabName],
            cssOffset = emotion.imageCssOffset[tabName],
            textHTML = ['<table class="smileytable">'],
            i = 0, imgNum = emotion.SmileyBox[tabName].length, imgColNum = 11, faceImage,
            sUrl, realUrl, posflag, offset, infor,infordecode;

    for ( ; i < imgNum; ) {
        textHTML.push( '<tr>' );
        for ( var j = 0; j < imgColNum; j++, i++ ) {
            faceImage = emotion.SmileyBox[tabName][i];
            if ( faceImage ) {
                sUrl = imagePath + faceImage + faceVersion;
                realUrl = imagePath + faceImage;
                posflag = j < positionLine ? 0 : 1;
                offset = cssOffset * i * (-1) - 1;
                infor = emotion.SmileyInfor[tabName][i];
				infordecode=decodeURI(infor.replace(/\%23/g,"#"));

                textHTML.push( '<td  class="' + tableCss + '"   border="1" width="' + iColWidth + '%" style="border-collapse:collapse;" align="center"  bgcolor="transparent" onclick="InsertSmiley(\'' + realUrl.replace( /'/g, "\\'" ) + '\',event)" onmouseover="over(this,\'' + sUrl + '\',\'' + posflag + '\')" onmouseout="out(this)">' );
                textHTML.push( '<span  style="display:block;">' );
                textHTML.push( '<img  style="max-height:'+ iHeight +';max-width:'+ iWidth +';" title="' + infordecode.substr(0,infordecode.length-4) + '" src="'+sUrl+'"></img>' );
                textHTML.push( '</span>' );
            } else {
                textHTML.push( '<td width="' + iColWidth + '%"   bgcolor="#FFFFFF">' );
            }
            textHTML.push( '</td>' );
        }
        textHTML.push( '</tr>' );
    }
    textHTML.push( '</table>' );
    textHTML = textHTML.join( "" );
    tab.innerHTML = textHTML;
}

function over( td, srcPath, posFlag ) {
    td.style.backgroundColor = "#ACCD3C";
    $G( 'faceReview' ).style.backgroundImage = "url('" + srcPath + "')";
	$G( 'faceReview' ).src = "<?php echo $bloghost; ?>zb_system/image/admin/none.gif";
    if ( posFlag == 1 ) $G( "tabIconReview" ).className = "show";
    $G( "tabIconReview" ).style.display = 'block';
}

function out( td ) {
    td.style.backgroundColor = "transparent";
    var tabIconRevew = $G( "tabIconReview" );
    tabIconRevew.className = "";
    tabIconRevew.style.display = 'none';
}

function createTabList( tabNum ) {
    var obj = {};
    for ( var i = 0; i < tabNum; i++ ) {
        obj["tab" + i] = [];
    }
    return obj;
}

function createArr( tabNum ) {
    var arr = [];
    for ( var i = 0; i < tabNum; i++ ) {
        arr[i] = 0;
    }
    return arr;
}

