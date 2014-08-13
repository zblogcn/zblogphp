<?php
define('EDITOR4COMMENT_IS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

require '../../../../../../zb_system/function/c_system_base.php';

header('Content-Type: application/x-javascript; Charset=utf-8');

function scansubdir($dir)
{
     $files = array(); 
     if ( $handle = opendir($dir) ) {
         while ( ($file = readdir($handle)) !== false ) {
             if ( $file != ".." && $file != "." ) {
                 if ( is_dir($dir . "/" . $file) ) {
                     $f= scandir($dir . "/" . $file);
                     $files[$file] = preg_grep("/(.*).gif|png|jpg$/",$f);
                     foreach($files[$file] as $name => $value) {
                        if (EDITOR4COMMENT_IS_WINDOWS)
                            $files[$file][$name] = iconv('GBK', 'UTF-8', $value);
                        //$files[$file][$name] = urlencode($value);
                     }
                    
                     //$files[$file] = preg_replace("/\.(.+)$/", "", $files[$file]);
                 }else {
                     //$files[] = $file;
                 }
             }
         }
         closedir($handle);
         return $files;
     }
}

$d = $blogpath."zb_users/emotion";
$f = scansubdir($d);
$x = 0;
$output = array();
while (list($key, $i) = each($f)) {
    $en =$key;
    $output[] = array('title' => (EDITOR4COMMENT_IS_WINDOWS ? iconv('GBK', 'UTF-8', $en) : $en), 'count' => count($i), 'file' => $i, 'x' => $x);
    $x++;
}
?>
(function(){

    var editor = null;

    UM.registerWidget('emotion',{

        tpl: "<link type=\"text/css\" rel=\"stylesheet\" href=\"<%=emotion_url%>emotion.css\">" +
            "<div class=\"edui-emotion-tab-Jpanel edui-emotion-wrapper\">" +
            "<ul class=\"edui-emotion-Jtabnav edui-tab-nav\">" +
<?php foreach($output as $name => $value) {?>
            "<li class=\"edui-tab-item\"><a data-context=\".edui-emotion-Jtab<?php echo $value['x']?>\" hideFocus=\"true\" class=\"edui-tab-text\"><?php echo $value['title']?></a></li>" + 
<?php }?>
            "<li class=\"edui-tab-item\"><a data-context=\".edui-emotion-Jtab<?php echo $x?>\" hideFocus=\"true\" class=\"edui-tab-text\" style=\"display:none\"></a></li>" + 
            "<li class=\"edui-emotion-tabs\"></li>" +
            "</ul>" +
            "<div class=\"edui-tab-content edui-emotion-JtabBodys\">" +
<?php foreach($output as $name => $value) {?>
            "<div class=\"edui-emotion-Jtab<?php echo $value['x']?> edui-tab-pane\"></div>" + 
<?php }?>
            "<div class=\"edui-emotion-Jtab<?php echo $x?> edui-tab-pane\"></div>" + 
            "</div>" +
            "<div class=\"edui-emotion-JtabIconReview edui-emotion-preview-box\">" +
            "<img class=\'edui-emotion-JfaceReview edui-emotion-preview-img\'/>" +
            "</div>",

        sourceData: {
            emotion: {
                tabNum:<?php echo count($value['title'])+2?>, //切换面板数量
                SmilmgName:{ <?php foreach($output as $name => $value) {?>
'edui-emotion-Jtab<?php echo $value['x'];?>':['<?php echo $value['title'];?>', <?php echo count($value['file']);?>], <?php }?> 'edui-emotion-Jtab<?php echo $x?>': ['', 0]},
                imageFolders:{ <?php foreach($output as $name => $value) {?>
'edui-emotion-Jtab<?php echo $value['x'];?>':'<?php echo urlencode($value['title']);?>/', <?php }?> 'edui-emotion-Jtab<?php echo $x?>': 'none/'},
                imageCss:{<?php foreach($output as $name => $value) {?>
'edui-emotion-Jtab<?php echo $value['x'];?>':'<?php echo $value['title'];?>', <?php }?> 'edui-emotion-Jtab<?php echo $x?>': 'none'},
                imageCssOffset:{<?php foreach($output as $name => $value) {?>
'edui-emotion-Jtab<?php echo $value['x'];?>':35, <?php }?> 'edui-emotion-Jtab<?php echo $x?>': 35}, //图片偏移
                SmileyInfor:{
<?php foreach($output as $name => $value) {?>
                    'edui-emotion-Jtab<?php echo $value['x'];?>':['<?php echo implode("','", $value['file']);?>'],
<?php }?>
                    'edui-emotion-Jtab<?php echo $x?>': []
                }
            }
        },


        initContent:function( _editor, $widget ){
            var me = this,
                emotion = me.sourceData.emotion,
                emotionUrl = UMEDITOR_CONFIG.UMEDITOR_HOME_URL + 'dialogs/emotion/',
                options = $.extend( {}, {
                    emotion_url: emotionUrl
                }),
                $root = me.root();

            if( me.inited ) {
                me.preventDefault();
                this.switchToFirst();
                return;
            }

            me.inited = true;

            editor = _editor;
            this.widget = $widget;

            emotion.SmileyPath = bloghost + 'zb_users/emotion/';
            emotion.SmileyBox = me.createTabList( emotion.tabNum );
            emotion.tabExist = me.createArr( emotion.tabNum );

            //options['cover_img'] = emotion.SmileyPath + (editor.options.emotionLocalization ? '0.gif' : 'default/0.gif');

            $root.html( $.parseTmpl( me.tpl, options ) );

            me.tabs = $.eduitab({selector:".edui-emotion-tab-Jpanel"});

            //缓存预览对象
            me.previewBox = $root.find(".edui-emotion-JtabIconReview");
            me.previewImg = $root.find(".edui-emotion-JfaceReview");

            me.initImgName();

        },
        initEvent:function(){

            var me = this;

            //防止点击过后关闭popup
            me.root().on('click', function(e){
                return false;
            });

            //移动预览
            me.root().delegate( 'td', 'mouseover mouseout', function( evt ){

                var $td = $( this),
                    url = $td.attr('data-surl') || null;

                if( url ) {
                    me[evt.type]( this, url , $td.attr('data-posflag') );
                }

                return false;

            } );

            //点击选中
            me.root().delegate( 'td', 'click', function( evt ){

                var $td = $( this),
                    realUrl = $td.attr('data-realurl') || null;

                if( realUrl ) {
                    me.insertSmiley( realUrl.replace( /'/g, "\\'" ), evt );
                }

                return false;

            } );

            //更新模板
            me.tabs.edui().on("beforeshow", function( evt ){

                var contentId = $(evt.target).attr('data-context').replace( /^.*\.(?=[^\s]*$)/, '' );

                evt.stopPropagation();

                me.updateTab( contentId );

            });

            this.switchToFirst();

        },
        initImgName: function() {

            var emotion = this.sourceData.emotion;

            for ( var pro in emotion.SmilmgName ) {
                var tempName = emotion.SmilmgName[pro],
                    tempBox = emotion.SmileyBox[pro],
                    tempStr = "";

                if ( tempBox.length ) return;

                for ( var i = 1; i <= tempName[1]; i++ ) {
                console.log(tempBox)
                    tempStr = tempName[0];
                    tempBox.push( tempStr );
                }
            }

        },
        /**
         * 切换到第一个tab
         */
        switchToFirst: function(){
            this.root().find(".edui-emotion-Jtabnav .edui-tab-text:first").trigger('click');
        },
        updateTab: function( contentBoxId ) {

            var me = this,
                emotion = me.sourceData.emotion;

            me.autoHeight( contentBoxId );

            if ( !emotion.tabExist[ contentBoxId ] ) {

                emotion.tabExist[ contentBoxId ] = true;
                me.createTab( contentBoxId );

            }

        },
        autoHeight: function( ) {
            this.widget.height(this.root() + 2);
        },
        createTabList: function( tabNum ) {
            //var obj = {};
            //for ( var i = 0; i < tabNum; i++ ) {
            //    obj["edui-emotion-Jtab" + i] = [];
            //}
            return this.sourceData.emotion.SmileyInfor;
        },
        mouseover: function( td, srcPath, posFlag ) {

            posFlag -= 0;

            $(td).css( 'backgroundColor', '#ACCD3C' );

            this.previewImg.css( "backgroundImage", "url(" + srcPath + ")" );
            posFlag && this.previewBox.addClass('edui-emotion-preview-left');
            this.previewBox.show();

        },
        mouseout: function( td ) {
            $(td).css( 'backgroundColor', 'transparent' );
            this.previewBox.removeClass('edui-emotion-preview-left').hide();
        },
        insertSmiley: function( url, evt ) {
            var obj = {
                src: url
            };
            obj._src = obj.src;
            editor.execCommand( 'insertimage', obj );
            if ( !evt.ctrlKey ) {
                //关闭预览
                this.previewBox.removeClass('edui-emotion-preview-left').hide();
                this.widget.edui().hide();
            }
        },
        createTab: function( contentBoxId ) {

            var faceVersion = "?v=1.1", //版本号
                me = this,
                $contentBox = this.root().find("."+contentBoxId),
                emotion = me.sourceData.emotion,
                imagePath = emotion.SmileyPath + emotion.imageFolders[ contentBoxId ], //获取显示表情和预览表情的路径
                positionLine = 11 / 2, //中间数
                iWidth = iHeight = 35, //图片长宽
                iColWidth = 3, //表格剩余空间的显示比例
                tableCss = emotion.imageCss[ contentBoxId ],
                cssOffset = emotion.imageCssOffset[ contentBoxId ],
                textHTML = ['<table border="1" class="edui-emotion-smileytable">'],
                i = 0, imgNum = emotion.SmileyBox[ contentBoxId ].length, imgColNum = 11, faceImage,
                sUrl, realUrl, posflag, offset, infor;

            for ( ; i < imgNum; ) {
                textHTML.push( '<tr>' );
                for ( var j = 0; j < imgColNum; j++, i++ ) {
                    faceImage = emotion.SmileyBox[ contentBoxId ][i];
                    if ( faceImage ) {
                        sUrl = imagePath + faceImage + faceVersion;
                        realUrl = imagePath + faceImage;
                        posflag = j < positionLine ? 0 : 1;
                        offset = cssOffset * i * (-1) - 1;
                        infor = emotion.SmileyInfor[ contentBoxId ][i];

                        textHTML.push( '<td  class="edui-emotion-' + tableCss + '" data-surl="'+ sUrl +'" data-realurl="'+ realUrl +'" data-posflag="'+ posflag +'" align="center">' );
                        textHTML.push( '<span>' );
                        textHTML.push( '<img  style="background-position:left ' + offset + 'px;" title="' + infor + '" src="' + realUrl + '" width="' + iWidth + '" height="' + iHeight + '"></img>' );
                        textHTML.push( '</span>' );
                    } else {
                        textHTML.push( '<td bgcolor="#FFFFFF">' );
                    }
                    textHTML.push( '</td>' );
                }
                textHTML.push( '</tr>' );
            }
            textHTML.push( '</table>' );
            textHTML = textHTML.join( "" );
            $contentBox.html( textHTML );
        },
        createArr: function( tabNum ) {
            var arr = [];
            for ( var i = 0; i < tabNum; i++ ) {
                arr[i] = 0;
            }
            return arr;
        },
        width:603,
        height:400
    });

})();