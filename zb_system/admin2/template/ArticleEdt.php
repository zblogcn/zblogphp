<?php die(); ?>
{php}
if (isset($_COOKIE['timezone'])) {
    $tz = GetVars('timezone', 'COOKIE');
    if (is_numeric($tz)) {
        date_default_timezone_set(GetTimeZoneByGMT($tz));
    }
    unset($tz);
}
{/php}

<script  src="../script/jquery.tagto.js"></script>
<script  src="../script/jquery-ui-timepicker-addon.js"></script>
{php}
HookFilterPlugin('Filter_Plugin_Edit_Begin');
{/php}

        <form id="edit" name="edit" method="post" action="#">
            <div id="divEditLeft">
                <!-- 4号输出接口 -->
                <div id="response4" class="editmod2">
{php}
HookFilterPlugin('Filter_Plugin_Edit_Response4');
{/php}
                </div>
                <div id="divEditTitle" class="editmod2">
                    <input type="hidden" name="ID" id="edtID" value="{$article->ID}" />
                    <input type="hidden" name="Type" id="edtType" value="{$article->Type}" />
                    <!-- title( -->
                    <div id="titleheader" class="editmod">
                        <label for="edtTitle" class="editinputname">{$lang['msg']['title']}</label>
                        <div>
                            <input type="text" name="Title" id="edtTitle" maxlength="{$option['ZC_ARTICLE_TITLE_MAX']}" onBlur="if(this.value==='') this.value='{$lang['msg']['unnamed']}'" onFocus="if(this.value==='{$lang['msg']['unnamed']}') this.value=''" value="{$article->Title}" />
                        </div>
                    </div>
                    <!-- )title -->

                </div>

                <!-- 5号输出接口 -->
                <div id="response5" class="editmod2">
{php}
HookFilterPlugin('Filter_Plugin_Edit_Response5');
{/php}
                </div>

                <div id="divContent" class="editmod2" style="clear:both;">
                    <div id="cheader" class="editmod editmod3">
                        <label for="editor_content" class="editinputname">
                            {$lang['msg']['content']}
                        </label>
                        &nbsp;&nbsp;
                        <span id="timemsg"></span>
                        <span id="msg2"></span>
                        <span id="msg"></span>
                        <span class="editinputname"></span>
                    </div>
                    <div id="carea" style="margin:5px 0 0 0" class="editmod editmod3">
                        <textarea id="editor_content" name="Content">{FormatString($article->Content, '[html-format]')}</textarea>
                    </div>
                    <div id="contentready" style="display:none">
                        <img alt="loading" id="statloading1" src="../image/admin/loading.gif" />Waiting...
                    </div>
                </div>

                <!-- 1号输出接口 -->
                <div id="response" class="editmod2">
{php}
HookFilterPlugin('Filter_Plugin_Edit_Response');
{/php}
                </div>

                <br />
                <!-- alias( -->
                <div id="alias" class="editmod2">
                    <label for="edtAlias" class="editinputname">
                        {$lang['msg']['alias']}
                    </label>
                    <input type="text" name="Alias" id="edtAlias" maxlength="250" value="{$article->Alias}" />
                </div>
                <!-- )alias -->

                {php}if (!$ispage) { {/php}
                    <!-- tags( -->
                    <div id="tags" class="editmod2">
                        <label for="edtTag" class='editinputname'>
                            {$lang['msg']['tags']}
                        </label>
                        <input type="text" name="Tag" id="edtTag" value="{$article->TagsToNameString()}" />
                        ({$lang['msg']['use_commas_to_separate']})
                        <a href="#" id="showtags">{$lang['msg']['show_common_tags']}</a>
                    </div>
                    <!-- Tags -->
                    <div id="ulTag" class="editmod2" style="display:none;">
                        <div id="ajaxtags">Waiting...</div>
                    </div>
                    <!-- )tags -->

                    <div id="insertintro" class="editmod2" style="padding-top:0.5em;padding-bottom:0.5em;">
                        <span>* {$lang['msg']['help_generate_summary']}
                            <a href="#" onClick="AutoIntro()">[{$lang['msg']['generate_summary']}]</a></span></div>
                {php} } {/php}

                <div id="divIntro" class="editmod2" 
                {php}
                if (!$article->Intro) {
                    echo 'style="display:none;"';
                }
                {/php}
                                                    >
                    <div id="theader" class="editmod editmod3">
                        <label for="editor_intro" class="editinputname">
                            {$lang['msg']['intro']}
                        </label>
                    </div>
                    <div id="tarea" style="margin:5px 0 0 0" class="editmod editmod3">
                        <textarea id="editor_intro" name="Intro">{FormatString($article->Intro, '[html-format]')}</textarea>
                    </div>
                    <div id="introready" style="display:none">
                        <img alt="loading" id="statloading2" src="../image/admin/loading.gif" />Waiting...
                    </div>

                </div>
                <!-- 2号输出接口 -->
                <div id="response2" class="editmod2">
{php}
HookFilterPlugin('Filter_Plugin_Edit_Response2');
{/php}
                </div>


            </div>
            <!-- divEditLeft -->

            <div id="divEditRight">
                <div id="divEditPost">
                    <div id="divBox">
                        <div id="divFloat">
                            <div id='post' class="editmod">
                                <input class="button" style="" type="submit" value="{$lang['msg']['submit']}" id="btnPost" onclick='return checkArticleInfo();' />
                            </div>
                            <!-- cate -->
                            {php}if (!$ispage) {{/php}
                                <div id="cate" class="editmod">
                                    <label for="cmbCateID" class="editinputname" style="max-width:65px;text-overflow:ellipsis;">
                                        {$lang['msg']['category']}
                                    </label>
                                    <select style="" class="edit" size="1" name="CateID" id="cmbCateID">
                                        {OutputOptionItemsOfCategories($article->CateID, $article->Type)}
                                    </select>
                                </div>
                                {php} } {/php}
                            <!-- )cate -->

                            <!-- level -->
                            <div id='level' class="editmod">
                                <label for="cmbPostStatus" class="editinputname" style="max-width:65px;text-overflow:ellipsis;">
                                    {$lang['msg']['status']}
                                </label>
                                <select class="edit" style="" size="1" name="Status" id="cmbPostStatus" onChange="cmbPostStatus.value=this.options[this.selectedIndex].value">
                                    {OutputOptionItemsOfPostStatus($article->Status)}
                                </select>
                            </div>
                            <!-- )level -->

                            <!-- template( -->
                            <div id='template' class="editmod">
                                <label for="cmbTemplate" class="editinputname" style="max-width:65px;text-overflow:ellipsis;">
                                    {$lang['msg']['template']}
                                </label>
                                <select style="" class="edit" size="1" name="Template" id="cmbTemplate" onChange="cmbTemplate.value=this.options[this.selectedIndex].value">
                                    {OutputOptionItemsOfTemplate($article->Template, array('index', '404', 'module', 'search', 'lm-'), array('single', $zbp->GetPostType($article->Type, 'name')))}
                                </select>
                            </div>
                            <!-- )template -->

                            <!-- user( -->
                            <div id='user' class="editmod">
                                <label for="cmbUser" class="editinputname" style="max-width:65px;text-overflow:ellipsis;">
                                    {$lang['msg']['author']}
                                </label>
                                <select style="" size="1" name="AuthorID" id="cmbUser" onChange="cmbUser.value=this.options[this.selectedIndex].value">
                                    {OutputOptionItemsOfMember($article->AuthorID, $article->Type)}
                                </select>
                            </div>
                            <!-- )user -->

                            <!-- newdatetime( -->
                            <div id='newdatetime' class="editmod">
                                <label for="edtDateTime" class="editinputname" style="max-width:65px;text-overflow:ellipsis;">
                                    {$lang['msg']['date']}
                                </label>
                                <input type="text" name="PostTime" id="edtDateTime" value="{$article->Time()}" style="" />
                            </div>

                            <!-- )newdatetime -->

                            <!-- Istop( -->
                            {php}
                            if (!$ispage && $zbp->CheckRights('ArticleAll')) { {/php}
                                <div id='istop' class="editmod">
                                    <label for="edtIstop" class="editinputname">{$lang['msg']['top']}</label>
                                    <select style="width:80px;" size="1" name="IsTop" id="edtIstopType" class="off-hide">
                                        {OutputOptionItemsOfIsTop($article->IsTop)}
                                    </select>
                                </div>
                                {php} } {/php}

                            <!-- )Istop -->

                            <!-- IsLock( -->

                            <div id='islock' class="editmod">
                                <label for="edtIslock" class='editinputname'>{$lang['msg']['disable_comment']}</label>
                                <input id="edtIslock" name="IsLock" style="" type="text" value="{intval($article->IsLock)" class="checkbox" />
                            </div>
                            <!-- )IsLock -->

                            <!-- Navbar( -->
                            {php}if ($ispage) {{/php}
                                <div id='AddNavbar' class="editmod">
                                    <label for="edtAddNavbar" class='editinputname'>{$lang['msg']['add_to_navbar']}</label>
                                    <input type="text" name="AddNavbar" id="edtAddNavbar" value="{intval($zbp->CheckItemToNavbar('page', $article->ID))}" class="checkbox" />
                                </div>`
                                {php} } {/php}
                            <!-- )Navbar -->

                            <!-- 3号输出接口 -->
                            <div id="response3" class="editmod">
{php}
HookFilterPlugin('Filter_Plugin_Edit_Response3');
{/php}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- divEditRight -->

        </form>

    {php}if ($ispage){
    echo '<script>ActiveLeftMenu("aPageMng");</script>';
} elseif ($article->ID == 0) {
    echo '<script>ActiveLeftMenu("aArticleEdt");</script>';
} else {
    echo '<script>ActiveLeftMenu("aArticleMng");</script>';
}
echo '<script>AddHeaderFontIcon("icon-pencil-square-fill");</script>';
    {/php}

    <script>
        /**
         * Gargabe codes
         */
        var tag_loaded = false; //是否已经ajax读取过TAGS
        var sContent = "",
            sIntro = ""; //原内容与摘要
        var isSubmit = false; //是否提交保存
        var contentBarBtn = [],introBarBtn = [],contentReady = [],introReady = [];

        var editor_api = {
            editor: {
                content: {
                    obj: {},
                    get: function() {
                        return ""
                    },
                    insert: function() {
                        return ""
                    },
                    put: function() {
                        return ""
                    },
                    focus: function() {
                        return ""
                    },
                    barBtn: function(name, icon, callback) {
                        contentBarBtn.push({
                            name:name,
                            icon:icon,
                            callback:callback
                        });
                    },
                    ready: function(f){
                        contentReady.push(f);
                    }
                },
                intro: {
                    obj: {},
                    get: function() {
                        return ""
                    },
                    insert: function() {
                        return ""
                    },
                    put: function() {
                        return ""
                    },
                    focus: function() {
                        return ""
                    },
                    barBtn: function(name, icon, callback) {
                        introBarBtn.push({
                            name:name,
                            icon:icon,
                            callback:callback
                        });
                    },
                    ready: function(f){
                        introReady.push(f);
                    }
                }
            }
        };

        //文章内容或摘要变动提示保存
        window.onbeforeunload = function() {
            if (!isSubmit && (editor_api.editor.content.get() !== sContent)) return "{$zbp->lang['error'][71]}";
        };

        function checkArticleInfo() {
            if (isSubmit) return false;
            document.getElementById("edit").action = "{BuildSafeCmdURL($ispage ? 'act=PagePst' : 'act=ArticlePst')}";

            if (!editor_api.editor.content.get()) {
                alert('{$zbp->lang['error'][70]}');
                return false;
            }
            isSubmit = true;
        }

        //日期时间控件
        $.datepicker.regional['{$lang['lang']}'] = {
            closeText: '{$lang['msg']['close']}',
            prevText: '{$lang['msg']['prev_month']}',
            nextText: '{$lang['msg']['next_month']}',
            currentText: '{$lang['msg']['current']}',
            monthNames: ['{$lang['month']['1']}', '{$lang['month']['2']}', '{$lang['month']['3']}', '{$lang['month']['4']}', '{$lang['month']['5']}', '{$lang['month']['6']}', '{$lang['month']['7']}', '{$lang['month']['8']}', '{$lang['month']['9']}', '{$lang['month']['10']}', '{$lang['month']['11']}', '{$lang['month']['12']}'],
            monthNamesShort: ['{$lang['month_abbr']['1']}', '{$lang['month_abbr']['2']}', '{$lang['month_abbr']['3']}', '{$lang['month_abbr']['4']}', '{$lang['month_abbr']['5']}', '{$lang['month_abbr']['6']}', '{$lang['month_abbr']['7']}', '{$lang['month_abbr']['8']}', '{$lang['month_abbr']['9']}', '{$lang['month_abbr']['10']}', '{$lang['month_abbr']['11']}', '{$lang['month_abbr']['12']}'],
            dayNames: ['{$lang['week']['7']}', '{$lang['week']['1']}', '{$lang['week']['2']}', '{$lang['week']['3']}', '{$lang['week']['4']}', '{$lang['week']['5']}', '{$lang['week']['6']}'],
            dayNamesShort: ['{$lang['week_short']['7']}', '{$lang['week_short']['1']}', '{$lang['week_short']['2']}', '{$lang['week_short']['3']}', '{$lang['week_short']['4']}', '{$lang['week_short']['5']}', '{$lang['week_short']['6']}'],
            dayNamesMin: ['{$lang['week_abbr']['7']}', '{$lang['week_abbr']['1']}', '{$lang['week_abbr']['2']}', '{$lang['week_abbr']['3']}', '{$lang['week_abbr']['4']}', '{$lang['week_abbr']['5']}', '{$lang['week_abbr']['6']}'],
            weekHeader: '{$lang['msg']['week_suffix']}',
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: true,
            yearSuffix: ' {$lang['msg']['year_suffix']}  '
        };
        $.datepicker.setDefaults($.datepicker.regional['{$lang['lang']}']);
        $.timepicker.regional['{$lang['lang']}'] = {
            timeOnlyTitle: '{$lang['msg']['time']}',
            timeText: '{$lang['msg']['time']}',
            hourText: '{$lang['msg']['hour']}',
            minuteText: '{$lang['msg']['minute']}',
            secondText: '{$lang['msg']['second']}',
            millisecText: '{$lang['msg']['millisec']}',
            currentText: '{$lang['msg']['current']}',
            closeText: '{$lang['msg']['close']}',
            timeFormat: 'HH:mm:ss',
            ampm: false
        };
        $.timepicker.setDefaults($.timepicker.regional['{$lang['lang']}']);
        $('#edtDateTime').datetimepicker({
            showSecond: true
            //changeMonth: true,
            //changeYear: true
        });


        //显示tags
        $(document).click(function(event) {
            $('#ulTag').slideUp("fast");
        });

        $('#showtags').click(function(event) {
            event.stopPropagation();
            var offset = $(event.target).offset();
            $('#ulTag').css({
                top: offset.top + $(event.target).height() + 20 + "px",
                left: offset.left
            });
            $('#ulTag').slideDown("fast");
            if (tag_loaded === false) {
                var tag=','+$('#edtTag').val()+',';
                $.getScript('{BuildSafeCmdURL('act=misc&type=showtags')}',function(){
                    $('#ajaxtags a').each(function(){
                        if (tag.indexOf($(this).text()) != -1){
                            $(this).addClass('selected');
                        }
                    });
                });
                tag_loaded = true;
            }
            return false;
        });

        function AddKey(i) {
            var strKey = $('#edtTag').val();
            var strNow = "," + i
            if (strKey == "") {
                strNow = i
            }
            if (strKey.indexOf(strNow) == -1) {
                strKey = strKey + strNow;
            }
            $('#edtTag').val(strKey);
        }

        function DelKey(i) {
            var strKey = $('#edtTag').val().replace(/[;，、\s]/,',');
            strKey = ','+strKey+',';
            strKey = strKey.replace(','+i+',',',').replace(/^,(.*?),$/,'$1');
            $('#edtTag').val(strKey);
        }

        //提取摘要
        function AutoIntro() {
            var s = editor_api.editor.content.get();
            if (s.indexOf("<hr class=\"more\" />") > -1) {
                editor_api.editor.intro.put(s.split("<hr class=\"more\" />")[0]);
            } else {
                if (s.indexOf("<hr class=\"more\"/>") > -1) {
                    editor_api.editor.intro.put(s.split("<hr class=\"more\"/>")[0]);
                } else {
                    i = {$zbp->option['ZC_ARTICLE_EXCERPT_MAX']};
                    s = s.replace(/<[^>]+>/g, "");
                    editor_api.editor.intro.put(s.substring(0, i));
                }
            }
            $("#divIntro").show();
            $('html,body').animate({
                scrollTop: $('#divIntro').offset().top
            }, 'fast');
        }

        //文章编辑提交区随动JS开始
        var oDiv = document.getElementById("divFloat");
        var H = 0;
        var Y = oDiv;
        while (Y) {
            H += Y.offsetTop;
            Y = Y.offsetParent;
        };
        $(window).bind("scroll resize", function() {
            var s = document.body.scrollTop || document.documentElement.scrollTop;
            if (s > H) {
                $("#divFloat").addClass("boxfloat");
            } else {
                $("#divFloat").removeClass("boxfloat");
            }
        });


        function editor_init() {
            editor_api.editor.content.obj = $('#editor_content');
            editor_api.editor.intro.obj = $('#editor_intro');
            editor_api.editor.content.get = function() {
                return this.obj.val()
            };
            editor_api.editor.content.put = function(str) {
                return this.obj.val(str)
            };
            editor_api.editor.content.focus = function() {
                return this.obj.focus()
            };
            editor_api.editor.intro.get = function() {
                return this.obj.val()
            };
            editor_api.editor.intro.put = function(str) {
                return this.obj.val(str)
            };
            editor_api.editor.intro.focus = function() {
                return this.obj.focus()
            };
            sContent = editor_api.editor.content.get();
        }


        // Auto-save module
        (function() {
            var $idElement = $('#edtID');
            var articleKey = 'zblogphp_article_' + $idElement.val();
            var isFirstOpenPage = true;
            var hint = '{$lang['error']['93']}';
            var currentStatus = {
                time: new Date().getTime(),
                random: 0,
                data: {},
                content: '',
                intro: ''
            };
            var updateStatus = function() {
                var prevStatus = parseSavedStatus();
                currentStatus.content = editor_api.editor.content.get();
                currentStatus.intro = editor_api.editor.intro.get();

                // The browser is posting data to server, no action should be taken.
                if (!isSubmit) {
                    return;
                }
                // random === 0 means currently didn't save any data
                // If we saved data before, but found data is empty
                // That's mean the content is posted to the server
                // So we don't need to auto-save data,
                // but have to warn user the content is saved by other page.
                if (currentStatus.random !== 0 && prevStatus === null) {
                    if (hint !== '') {
                        alert(hint);
                        hint = '';
                    }
                    return;
                }

                if (prevStatus !== null && currentStatus.time !== prevStatus.time && currentStatus.random !== prevStatus.random) {
                    // That's mean the content of this page is deprecated
                    // But we have no need to check the content should be auto-saved.
                    // Let them have a competition!
                    // We don't need to recover text from localStorage except the first time!
                    // if (prevStatus.time > currentStatus.time) return;
                    if (currentStatus.content === prevStatus.content) return;
                    if (currentStatus.content.trim() === '') return;
                }
                currentStatus.random = Math.random();
                currentStatus.time = new Date().getTime();
                // currentStatus.data = $('#edit').serializeJson();
                localStorage.setItem(articleKey, JSON.stringify(currentStatus));
            };
            var parseSavedStatus = function() {
                var content = localStorage.getItem(articleKey);
                if (!content) return null;
                try {
                    return JSON.parse(content);
                } catch (e) {
                    return null;
                }
            };
            var readStatus = function() {
                var status = parseSavedStatus();
                if (isFirstOpenPage && status !== null) {
                    currentStatus = status;
                    editor_api.editor.content.put(currentStatus.content);
                    editor_api.editor.intro.put(currentStatus.intro);
                    // Object.keys(currentStatus.data).
                }
                isFirstOpenPage = false;
            };
            setInterval(function() {
                updateStatus();
            }, 10000);
            $(document).ready(function() {
                setTimeout(function() {
                    readStatus()
                }, 500);
            });
        })();
    </script>

    {php}
    HookFilterPlugin('Filter_Plugin_Edit_End');
    {/php}

    <script>
        editor_init();
    </script>
