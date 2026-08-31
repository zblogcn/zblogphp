<?php exit(); ?>
{php}
<?php
$modSourceMaps = [];
foreach ($zbp->modules as $module) {
    $modSourceMaps[$module->FileName] = $module->Source;
}
?>
{/php}
<script>
    // const functions = {json_encode($modSourceMaps)};
</script>
<div class="module-layout">
    <div class="widget-left">
        <div class="widget-list">
            <div class="widget-list-group">
                <div class="widget-list-header">{$zbp.lang['msg']['system_module']}</div>
                <div class="widget-list-note">{$zbp.lang['msg']['drag_module_to_sidebar']}</div>
                <div class="widget-list-content">
                    {foreach $sm as $module}
                    {php}CreateModuleDiv($module);{/php}
                    {/foreach}
                </div>
            </div>

            <div class="widget-list-group">
                <div class="widget-list-header">{$zbp.lang['msg']['user_module']}</div>
                <div class="widget-list-content">
                    {foreach $um as $module}
                    {php}CreateModuleDiv($module);{/php}
                    {/foreach}
                </div>
            </div>

            <div class="widget-list-group">
                <div class="widget-list-header">{$zbp.lang['msg']['plugin_module']}</div>
                <div class="widget-list-content">
                    {foreach $pm as $module}
                    {php}CreateModuleDiv($module);{/php}
                    {/foreach}
                </div>
            </div>

            <div class="widget-list-group">
                <div class="widget-list-header">{$zbp.lang['msg']['theme_module']}</div>
                <div class="widget-list-content">
                    {foreach $tm as $module}
                    {php}CreateModuleDiv($module);{/php}
                    {/foreach}
                </div>
            </div>
        </div>
    </div>

    <div class="widget-right">
        <form id="edit" class="hidden" method="post" action="{BuildSafeCmdURL('act=SidebarSet')}">
            {foreach $sideids as $curKey => $curValue}
            {php}$curOption = 'ZC_SIDEBAR' . $curValue . '_ORDER';{/php}
            <input type="hidden" id="strsidebar{$curValue}" name="edtSidebar{$curValue}" value="{$zbp.option[$curOption]}" />
            {/foreach}
        </form>

        {foreach $sideids as $curKey => $curValue}
        {php}$id = 'sidebar' . $curValue;{/php}
        <div class="siderbar-list">
            <div class="siderbar-drop" id="siderbar{$curValue}">
                <div class="siderbar-header">
                    {$zbp.lang['msg'][$id]}&nbsp;
                    <img class="roll" src="{$host}zb_system/image/admin/loading.gif" width="16" alt="" />
                    <span class="ui-icon ui-icon-triangle-1-s"></span>
                </div>
                <div class="siderbar-sort-list">
                    <div class="siderbar-note">
                        {str_replace('%s', count($zbp.template.$id), $zbp.lang['msg']['sidebar_module_count'])}
                    </div>
                    {foreach $zbp.template.$id as $module}
                    {php}CreateModuleDiv($module, false);{/php}
                    {/foreach}
                </div>
            </div>
        </div>
        {/foreach}
    </div>
</div>

<script>
    $(function() {
        function sortFunction() {
            const sideList = JSON.parse('{json_encode($sideids)}');
            const postData = {};
            for (const key in sideList) {
                if (!Object.hasOwn(sideList, key)) continue;
                const val = sideList[key];
                let str = "";
                $(`#siderbar${val} .widget`).each(function(i) {
                    str += $(this).find(".funid").html() + "|";
                });
                $(`#strsidebar${val}`).val(str);
                postData[`sidebar${val}`] = str;
            }

            // console.log(sideList, postData);

            // 添加表单提交
            $.post($("#edit").attr("action"), postData,
                function(data) {
                    //alert("Data Loaded: " + data);
                });
        }

        function hideWidget(item) {
            item.find(".ui-icon").removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-w");
            t = item.next();
            t.find(".widget").hide("fast").end().show();
            t.find(".siderbar-note>span").text(t.find(".widget").length);
        }

        function showWidget(item) {
            item.find(".ui-icon").removeClass("ui-icon-triangle-1-w").addClass("ui-icon-triangle-1-s");
            t = item.next();
            t.find(".widget").show("fast");
            t.find(".siderbar-note>span").text(t.find(".widget").length);
        }

        $(".siderbar-header").click(function() {
            if ($(this).hasClass("clicked")) {
                showWidget($(this));
                $(this).removeClass("clicked");
            } else {
                hideWidget($(this));
                $(this).addClass("clicked");
            }
        });

        $(".siderbar-sort-list").sortable({
            items: '.widget',
            start: function(event, ui) {
                showWidget(ui.item.parent().prev());
            },
            stop: function(event, ui) {
                var c = ui.item.find(".funid").html();
                var siderbarName = [];
                ui.item.parent().find(".funid").each(function(item, element) {
                    var c = $(element).html();
                    if (siderbarName[c] !== undefined) {
                        siderbarName[c] += 1
                    } else {
                        siderbarName[c] = 1
                    }
                })
                if (siderbarName[c] > 1) {
                    ui.item.remove();
                };

                $(this).parent().find(".roll").show("slow");
                sortFunction();
                $(this).parent().find(".roll").hide("slow");
                showWidget($(this).parent().prev());
            }
        }).disableSelection();

        $(".widget-list .widget").draggable({
            connectToSortable: ".siderbar-sort-list",
            revert: "invalid",
            containment: "document",
            helper: "clone",
            cursor: "move"
        }).disableSelection();

        $(".widget-list").droppable({
            accept: ".siderbar-sort-list>.widget",
            drop: function(event, ui) {
                ui.draggable.remove();
            }
        });
    });
</script>
