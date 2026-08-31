<?php exit(); ?>
{php}
<?php
if (isset($_COOKIE['timezone'])) {
    $tz = GetVars('timezone', 'COOKIE');
    if (is_numeric($tz)) {
        date_default_timezone_set(GetTimeZoneByGMT($tz));
    }
    unset($tz);
}
?>
{/php}

<script src="{$host}zb_system/script/jquery.tagto.js"></script>
<script src="{$host}zb_system/script/jquery-ui-timepicker-addon.js"></script>
{php}
HookFilterPlugin('Filter_Plugin_Edit_Begin');
{/php}

<form id="edit" class="edit post-edit" name="edit" method="post" action="#">
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
                <input type="text" name="Title" id="edtTitle" maxlength="{$option['ZC_ARTICLE_TITLE_MAX']}" value="{$article->Title}" />
            </div>
            <!-- )title -->

            <!-- alias( -->
            <div id="alias" class="editmod">
                <label for="edtAlias" class="editinputname">
                    {$lang['msg']['alias']}
                </label>
                <input type="text" name="Alias" id="edtAlias" maxlength="250" value="{$article->Alias}" />
            </div>
            <!-- )alias -->

            {if !$ispage}
            <!-- tags( -->
            <div id="tags" class="editmod">
                <label for="edtTag" class='editinputname'>
                    {$lang['msg']['tags']}
                </label>
                <input type="text" name="Tag" id="edtTag" value="{$article->TagsToNameString()}" />
                ({$lang['msg']['use_commas_to_separate']})
                <a href="javascript:;" id="showtags" data-url="{BuildSafeCmdURL('act=misc&type=showtags')}">{$lang['msg']['show_common_tags']}</a>
            </div>
            <!-- Tags -->
            <div id="ulTag" class="editmod2 jq-hidden hidden">
                <div id="ajaxtags">Waiting...</div>
            </div>
            <!-- )tags -->
            {/if}

        </div>

        <!-- 5号输出接口 -->
        <div id="response5" class="editmod2">
            {php}
            HookFilterPlugin('Filter_Plugin_Edit_Response5');
            {/php}
        </div>

        <div id="divContent" class="editmod2">
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
            <div id="carea" class="editmod editmod3">
                <textarea id="editor_content" name="Content">{FormatString($article->Content, '[html-format]')}</textarea>
            </div>
            <div id="contentready" class="hidden">
                <img alt="loading" id="statloading1" src="{$host}zb_system/image/admin/loading.gif" />Waiting...
            </div>
        </div>

        <!-- 1号输出接口 -->
        <div id="response" class="editmod2">
            {php}
            HookFilterPlugin('Filter_Plugin_Edit_Response');
            {/php}
        </div>

        <br />

        {if !$ispage}
        <div id="insertintro" class="editmod2">
            <span>* {$lang['msg']['help_generate_summary']}
                <a href="javascript:;" onClick="AutoIntro()">[{$lang['msg']['generate_summary']}]</a></span>
        </div>
        {/if}

        <div id="divIntro" class="editmod2 {if !$article.Intro}jq-hidden hidden{/if}">
            <div id="theader" class="editmod editmod3">
                <label for="editor_intro" class="editinputname">
                    {$lang['msg']['intro']}
                </label>
            </div>
            <div id="tarea" class="editmod editmod3">
                <textarea id="editor_intro" name="Intro">{FormatString($article->Intro, '[html-format]')}</textarea>
            </div>
            <div id="introready" class="hidden">
                <img alt="loading" id="statloading2" src="{$host}zb_system/image/admin/loading.gif" />Waiting...
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
                        <input class="button" type="submit" value="{$lang['msg']['submit']}" id="btnPost" onclick='return checkArticleInfo();' />
                    </div>
                    <!-- cate -->
                    {if !$ispage}
                    <div id="cate" class="editmod">
                        <label for="cmbCateID" class="editinputname">
                            {$lang['msg']['category']}
                        </label>
                        <select class="edit" size="1" name="CateID" id="cmbCateID">
                            {OutputOptionItemsOfCategories($article->CateID, $article->Type)}
                        </select>
                    </div>
                    {/if}
                    <!-- )cate -->

                    <!-- level -->
                    <div id='level' class="editmod">
                        <label for="cmbPostStatus" class="editinputname">
                            {$lang['msg']['status']}
                        </label>
                        <select class="edit" size="1" name="Status" id="cmbPostStatus" onChange="cmbPostStatus.value=this.options[this.selectedIndex].value">
                            {OutputOptionItemsOfPostStatus($article->Status)}
                        </select>
                    </div>
                    <!-- )level -->

                    <!-- template( -->
                    <div id='template' class="editmod">
                        <label for="cmbTemplate" class="editinputname">
                            {$lang['msg']['template']}
                        </label>
                        <select class="edit" size="1" name="Template" id="cmbTemplate" onChange="cmbTemplate.value=this.options[this.selectedIndex].value">
                            {OutputOptionItemsOfTemplate($article->Template, array('index', '404', 'module', 'search', 'lm-'), array('single', $zbp->GetPostType($article->Type, 'name')))}
                        </select>
                    </div>
                    <!-- )template -->

                    <!-- user( -->
                    <div id='user' class="editmod">
                        <label for="cmbUser" class="editinputname">
                            {$lang['msg']['author']}
                        </label>
                        <select class="edit" size="1" name="AuthorID" id="cmbUser" onChange="cmbUser.value=this.options[this.selectedIndex].value">
                            {OutputOptionItemsOfMember($article->AuthorID, $article->Type)}
                        </select>
                    </div>
                    <!-- )user -->

                    <!-- newdatetime( -->
                    <div id='newdatetime' class="editmod">
                        <label for="edtDateTime" class="editinputname">
                            {$lang['msg']['date']}
                        </label>
                        <input type="text" name="PostTime" id="edtDateTime" value="{$article->Time()}" />
                    </div>

                    <!-- )newdatetime -->

                    <!-- Istop( -->
                    {if (!$ispage && $zbp->CheckRights('ArticleAll'))}
                    <div id='istop' class="editmod">
                        <label for="edtIstop" class="editinputname">{$lang['msg']['top']}</label>
                        <select size="1" name="IsTop" id="edtIstopType" class="off-hide">
                            {OutputOptionItemsOfIsTop($article->IsTop)}
                        </select>
                    </div>
                    {/if}

                    <!-- )Istop -->

                    <!-- IsLock( -->

                    <div id='islock' class="editmod">
                        <label for="edtIslock" class='editinputname'>{$lang['msg']['disable_comment']}</label>
                        <input id="edtIslock" name="IsLock" type="text" value="{intval($article->IsLock)}" class="checkbox" />
                    </div>
                    <!-- )IsLock -->

                    <!-- Navbar( -->
                    {if $ispage}
                    <div id='AddNavbar' class="editmod">
                        <label for="edtAddNavbar" class='editinputname'>{$lang['msg']['add_to_navbar']}</label>
                        <input type="text" name="AddNavbar" id="edtAddNavbar" value="{intval($zbp->CheckItemToNavbar('page', $article->ID))}" class="checkbox" />
                    </div>`
                    {/if}
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

<script>
    // 标题输入框的聚焦/失焦逻辑（保持与内联事件一致）
    (() => {
        const $title = $('#edtTitle');
        if (!$title.length) return;
        const unnamed = "{$lang['msg']['unnamed']}";
        $title.on('focus', function() {
            if (this.value === unnamed) this.value = '';
        });
        $title.on('blur', function() {
            if (this.value === '') this.value = unnamed;
        });
    })();

    // 显示 tags
    (() => {
        let tag_loaded = false; // 是否已经 ajax 读取过 TAGS
        $(document).click(function(event) {
            $('#ulTag').slideUp("fast");
        });

        $('#showtags').click(function(event) {
            event.stopPropagation();
            const offset = $(event.target).offset();
            $('#ulTag').css({
                top: offset.top + $(event.target).height() + 20 + "px",
                left: offset.left
            });
            $('#ulTag').slideToggle("fast");
            if (tag_loaded === false) {
                const tag = ',' + $('#edtTag').val() + ',';
                const url = $(this).data('url');
                $.getScript(url, function() {
                    $('#ajaxtags a').each(function() {
                        if (tag.indexOf($(this).text()) != -1) {
                            $(this).addClass('selected');
                        }
                    });
                });
                tag_loaded = true;
            }
            return false;
        });
    })();

    /* ----- */

    // 编辑器接口构造函数
    const makeEditorSlot = (barList, readyList) => ({
        obj: {},
        get: () => "",
        insert: () => "",
        put: () => "",
        focus: () => "",
        barBtn: (name, icon, callback) => {
            barList.push({
                name: name,
                icon: icon,
                callback: callback
            });
        },
        ready: (fn) => {
            readyList.push(fn);
        }
    });

    const contentBarBtn = [],
        introBarBtn = [],
        contentReady = [],
        introReady = [];

    // 编辑器接口
    const editor_api = {
        editor: {
            content: makeEditorSlot(contentBarBtn, contentReady),
            intro: makeEditorSlot(introBarBtn, introReady)
        }
    };

    // 编辑器内容缓存
    let sContent = "",
        sIntro = "";

    // 编辑器初始化，插件需要覆盖此函数
    function editor_init() {
        // 内容编辑器接口实现
        editor_api.editor.content.obj = $('#editor_content');
        editor_api.editor.content.get = function() {
            return this.obj.val()
        };
        editor_api.editor.content.put = function(str) {
            return this.obj.val(str)
        };
        editor_api.editor.content.focus = function() {
            return this.obj.focus()
        };

        // 摘要编辑器接口实现
        editor_api.editor.intro.obj = $('#editor_intro');
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

    /* ----- */

    let isSubmit = false; // 是否提交保存

    // 文章内容或摘要变动提示保存
    window.onbeforeunload = function() {
        if (!isSubmit && (editor_api.editor.content.get() !== sContent)) return "{$zbp->lang['error'][71]}";
    };

    function checkArticleInfo() {
        if (isSubmit) return false;
        $("#edit").attr("action", "{BuildSafeCmdURL($ispage ? 'act=PagePst' : 'act=ArticlePst')}");
        if (!editor_api.editor.content.get()) {
            alert("{$zbp->lang['error'][70]}");
            return false;
        }
        isSubmit = true;
    }

    /* ----- */

    // 日期时间控件
    $.datepicker.regional["{$lang['lang']}"] = {
        closeText: "{$lang['msg']['close']}",
        prevText: "{$lang['msg']['prev_month']}",
        nextText: "{$lang['msg']['next_month']}",
        currentText: "{$lang['msg']['current']}",
        monthNames: ["{$lang['month']['1']}", "{$lang['month']['2']}", "{$lang['month']['3']}", "{$lang['month']['4']}", "{$lang['month']['5']}", "{$lang['month']['6']}", "{$lang['month']['7']}", "{$lang['month']['8']}", "{$lang['month']['9']}", "{$lang['month']['10']}", "{$lang['month']['11']}", "{$lang['month']['12']}"],
        monthNamesShort: ["{$lang['month_abbr']['1']}", "{$lang['month_abbr']['2']}", "{$lang['month_abbr']['3']}", "{$lang['month_abbr']['4']}", "{$lang['month_abbr']['5']}", "{$lang['month_abbr']['6']}", "{$lang['month_abbr']['7']}", "{$lang['month_abbr']['8']}", "{$lang['month_abbr']['9']}", "{$lang['month_abbr']['10']}", "{$lang['month_abbr']['11']}", "{$lang['month_abbr']['12']}"],
        dayNames: ["{$lang['week']['7']}", "{$lang['week']['1']}", "{$lang['week']['2']}", "{$lang['week']['3']}", "{$lang['week']['4']}", "{$lang['week']['5']}", "{$lang['week']['6']}"],
        dayNamesShort: ["{$lang['week_short']['7']}", "{$lang['week_short']['1']}", "{$lang['week_short']['2']}", "{$lang['week_short']['3']}", "{$lang['week_short']['4']}", "{$lang['week_short']['5']}", "{$lang['week_short']['6']}"],
        dayNamesMin: ["{$lang['week_abbr']['7']}", "{$lang['week_abbr']['1']}", "{$lang['week_abbr']['2']}", "{$lang['week_abbr']['3']}", "{$lang['week_abbr']['4']}", "{$lang['week_abbr']['5']}", "{$lang['week_abbr']['6']}"],
        weekHeader: "{$lang['msg']['week_suffix']}",
        dateFormat: "yy-mm-dd",
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: " {$lang['msg']['year_suffix']}  "
    };
    $.datepicker.setDefaults($.datepicker.regional["{$lang['lang']}"]);
    $.timepicker.regional["{$lang['lang']}"] = {
        timeOnlyTitle: "{$lang['msg']['time']}",
        timeText: "{$lang['msg']['time']}",
        hourText: "{$lang['msg']['hour']}",
        minuteText: "{$lang['msg']['minute']}",
        secondText: "{$lang['msg']['second']}",
        millisecText: "{$lang['msg']['millisec']}",
        currentText: "{$lang['msg']['current']}",
        closeText: "{$lang['msg']['close']}",
        timeFormat: "HH:mm:ss",
        ampm: false
    };
    $.timepicker.setDefaults($.timepicker.regional["{$lang['lang']}"]);
    $('#edtDateTime').datetimepicker({
        showSecond: true
        // changeMonth: true,
        // changeYear: true
    });

    // 提取摘要
    function AutoIntro() {
        let s = editor_api.editor.content.get();
        if (s.indexOf("<hr class=\"more\" />") > -1) {
            editor_api.editor.intro.put(s.split("<hr class=\"more\" />")[0]);
        } else {
            if (s.indexOf("<hr class=\"more\"/>") > -1) {
                editor_api.editor.intro.put(s.split("<hr class=\"more\"/>")[0]);
            } else {
                i = parseInt("{$zbp.option['ZC_ARTICLE_EXCERPT_MAX']}");
                s = s.replace(/<[^>]+>/g, "");
                editor_api.editor.intro.put(s.substring(0, i));
            }
        }
        $("#divIntro").slideDown('slow');
        $('html,body').delay(500).animate({
            scrollTop: $('#divIntro').offset().top
        }, 'fast');
    }
</script>

{php}
HookFilterPlugin('Filter_Plugin_Edit_End');
{/php}

<script>
    // 调用编辑器初始化
    editor_init();
</script>
