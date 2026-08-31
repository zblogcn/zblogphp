<?php exit(); ?>
<form method="post" action="{BuildSafeCmdURL('act=SettingSav')}">
    <div class="content-box">
        <div class="content-box-header">
            <ul class="content-box-tabs">
                <li><a href="#tab1" class="default-tab"><span>{$zbp->lang['msg']['basic_setting']}</span></a></li>
                <li><a href="#tab2"><span>{$zbp->lang['msg']['global_setting']}</span></a></li>
                <li><a href="#tab3"><span>{$zbp->lang['msg']['page_setting']}</span></a></li>
                <li><a href="#tab4"><span>{$zbp->lang['msg']['comment_setting']}</span></a></li>
                <li><a href="#tab5"><span>{$zbp->langs->msg->backend_setting}</span></a></li>
                <li><a href="#tab6"><span>{$zbp->lang['msg']['api_setting']}</span></a></li>
                <li><a href="#tab7"><span>{$zbp->lang['msg']['ai_setting']}</span></a></li>
            </ul>
            <div class="clear"></div>
        </div>
        <div class="content-box-content">
            <!-- Tab 1 -->
            <div class="tab-content default-tab" id="tab1">
                <table class="table_hover table_striped tableFull">
                    <tr>
                        <td class="td25"><b>{$zbp->lang['msg']['blog_host']}</b></td>
                        <td><input id="ZC_BLOG_HOST" name="ZC_BLOG_HOST" type="text" value="{$zbp->option['ZC_BLOG_HOST']}" readonly oninput="disableSubmit($(this).val())" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['blog_name']}</b></td>
                        <td><input id="ZC_BLOG_NAME" name="ZC_BLOG_NAME" type="text" value="{$zbp->option['ZC_BLOG_NAME']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['blog_subname']}</b></td>
                        <td><input id="ZC_BLOG_SUBNAME" name="ZC_BLOG_SUBNAME" type="text" value="{$zbp->option['ZC_BLOG_SUBNAME']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['copyright']}</b></td>
                        <td><textarea id="ZC_BLOG_COPYRIGHT" name="ZC_BLOG_COPYRIGHT" class="textarea-90">{$zbp->option['ZC_BLOG_COPYRIGHT']}</textarea></td>
                    </tr>
                </table>
            </div>
            <!-- Tab 2 -->
            <div class="tab-content" id="tab2">
                <table class="table_hover table_striped tableFull">
                    <tr>
                        <td class="td25"><b>{$zbp->lang['msg']['blog_timezone']}</b></td>
                        <td><select id="ZC_TIME_ZONE_NAME" name="ZC_TIME_ZONE_NAME" class="select-90">{CreateOptionsOfTimeZone($zbp->option['ZC_TIME_ZONE_NAME'])}</select></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['blog_language']}</b></td>
                        <td><select id="ZC_BLOG_LANGUAGEPACK" name="ZC_BLOG_LANGUAGEPACK" class="select-90">{CreateOptionsOfLang($zbp->option['ZC_BLOG_LANGUAGEPACK'])}</select></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['debug_mode']}</b></td>
                        <td><input id="ZC_DEBUG_MODE" name="ZC_DEBUG_MODE" type="text" value="{$zbp->option['ZC_DEBUG_MODE']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->langs->msg->show_warning_error}</b></td>
                        <td><input id="ZC_DEBUG_MODE_WARNING" name="ZC_DEBUG_MODE_WARNING" type="text" value="{$zbp->option['ZC_DEBUG_MODE_WARNING']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['additional_security']}</b></td>
                        <td><input id="ZC_ADDITIONAL_SECURITY" name="ZC_ADDITIONAL_SECURITY" type="text" value="{$zbp->option['ZC_ADDITIONAL_SECURITY']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['using_cdn_guest_type']}</b></td>
                        <td><select id="ZC_USING_CDN_GUESTIP_TYPE" name="ZC_USING_CDN_GUESTIP_TYPE" class="select-90">{CreateOptionsOfGuestIPType($zbp->option['ZC_USING_CDN_GUESTIP_TYPE'])}</select></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['enable_xmlrpc']}</b></td>
                        <td><input id="ZC_XMLRPC_ENABLE" name="ZC_XMLRPC_ENABLE" type="text" value="{$zbp->option['ZC_XMLRPC_ENABLE']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['close_site']}</b></td>
                        <td><input id="ZC_CLOSE_SITE" name="ZC_CLOSE_SITE" type="text" value="{$zbp->option['ZC_CLOSE_SITE']}" class="checkbox" /></td>
                    </tr>
                </table>
            </div>
            <!-- Tab 3 -->
            <div class="tab-content" id="tab3">
                <table class="table_hover table_striped tableFull">
                    <tr>
                        <td class="td25"><b>{$zbp->lang['msg']['display_count']}</b></td>
                        <td><input id="ZC_DISPLAY_COUNT" name="ZC_DISPLAY_COUNT" type="text" value="{$zbp->option['ZC_DISPLAY_COUNT']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['display_subcategorys']}</b></td>
                        <td><input id="ZC_DISPLAY_SUBCATEGORYS" name="ZC_DISPLAY_SUBCATEGORYS" type="text" value="{$zbp->option['ZC_DISPLAY_SUBCATEGORYS']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['pagebar_count']}</b></td>
                        <td><input id="ZC_PAGEBAR_COUNT" name="ZC_PAGEBAR_COUNT" type="text" value="{$zbp->option['ZC_PAGEBAR_COUNT']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['search_count']}</b></td>
                        <td><input id="ZC_SEARCH_COUNT" name="ZC_SEARCH_COUNT" type="text" value="{$zbp->option['ZC_SEARCH_COUNT']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['syntax_high_lighter']}</b></td>
                        <td><input id="ZC_SYNTAXHIGHLIGHTER_ENABLE" name="ZC_SYNTAXHIGHLIGHTER_ENABLE" type="text" value="{$zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE']}" class="checkbox" /></td>
                    </tr>
                </table>
            </div>
            <!-- Tab 4 -->
            <div class="tab-content" id="tab4">
                <table class="table_hover table_striped tableFull">
                    <tr>
                        <td class="td25"><b>{$zbp->lang['msg']['comment_turnoff']}</b></td>
                        <td><input id="ZC_COMMENT_TURNOFF" name="ZC_COMMENT_TURNOFF" type="text" value="{$zbp->option['ZC_COMMENT_TURNOFF']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['comment_audit']}</b></td>
                        <td><input id="ZC_COMMENT_AUDIT" name="ZC_COMMENT_AUDIT" type="text" value="{$zbp->option['ZC_COMMENT_AUDIT']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['comment_reverse_order']}</b></td>
                        <td><input id="ZC_COMMENT_REVERSE_ORDER" name="ZC_COMMENT_REVERSE_ORDER" type="text" value="{$zbp->option['ZC_COMMENT_REVERSE_ORDER']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['comments_display_count']}</b></td>
                        <td><input id="ZC_COMMENTS_DISPLAY_COUNT" name="ZC_COMMENTS_DISPLAY_COUNT" type="text" value="{$zbp->option['ZC_COMMENTS_DISPLAY_COUNT']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['comment_verify_enable']}</b></td>
                        <td><input id="ZC_COMMENT_VERIFY_ENABLE" name="ZC_COMMENT_VERIFY_ENABLE" type="text" value="{$zbp->option['ZC_COMMENT_VERIFY_ENABLE']}" class="checkbox" /></td>
                    </tr>
                </table>
            </div>
            <!-- Tab 5 -->
            <div class="tab-content" id="tab5">
                <table class="table_hover table_striped tableFull">
                    <tr>
                        <td class="td25"><b>{$zbp->lang['msg']['allow_upload_type']}</b></td>
                        <td><input id="ZC_UPLOAD_FILETYPE" name="ZC_UPLOAD_FILETYPE" type="text" value="{$zbp->option['ZC_UPLOAD_FILETYPE']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['allow_upload_size']}</b></td>
                        <td><input id="ZC_UPLOAD_FILESIZE" name="ZC_UPLOAD_FILESIZE" type="text" value="{$zbp->option['ZC_UPLOAD_FILESIZE']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->langs->msg->get_text_intro}</b></td>
                        <td><input id="ZC_ARTICLE_INTRO_WITH_TEXT" name="ZC_ARTICLE_INTRO_WITH_TEXT" type="text" value="{$zbp->option['ZC_ARTICLE_INTRO_WITH_TEXT']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['manage_count']}</b></td>
                        <td><input id="ZC_MANAGE_COUNT" name="ZC_MANAGE_COUNT" type="text" value="{$zbp->option['ZC_MANAGE_COUNT']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->langs->msg->enable_post_batch_delete}</b></td>
                        <td><input id="ZC_POST_BATCH_DELETE" name="ZC_POST_BATCH_DELETE" type="text" value="{$zbp->option['ZC_POST_BATCH_DELETE']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->langs->msg->delete_member_with_alldata}</b></td>
                        <td><input id="ZC_DELMEMBER_WITH_ALLDATA" name="ZC_DELMEMBER_WITH_ALLDATA" type="text" value="{$zbp->option['ZC_DELMEMBER_WITH_ALLDATA']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->langs->msg->category_legacy_display}</b></td>
                        <td><input id="ZC_CATEGORY_MANAGE_LEGACY_DISPLAY" name="ZC_CATEGORY_MANAGE_LEGACY_DISPLAY" type="text" value="{$zbp->option['ZC_CATEGORY_MANAGE_LEGACY_DISPLAY']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->langs->msg->enable_login_verify}</b></td>
                        <td><input id="ZC_LOGIN_VERIFY_ENABLE" name="ZC_LOGIN_VERIFY_ENABLE" type="text" value="{$zbp->option['ZC_LOGIN_VERIFY_ENABLE']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>后台主题</b></td>
                        <td><select id="ZC_BACKEND_ID" name="ZC_BACKEND_ID" class="select-90">
                                {foreach $zbp->backendapps as $backendapp}
                                <option value="{$backendapp->id}" {if $zbp->option['ZC_BACKEND_ID'] == $backendapp->id}selected="selected"{/if}>{$backendapp->name}</option>
                                {/foreach}
                            </select></td>
                    </tr>
                </table>
            </div>
            <!-- Tab 6 -->
            <div class="tab-content" id="tab6">
                <table class="table_hover table_striped tableFull">
                    <tr>
                        <td class="td25"><b>{$zbp->lang['msg']['enable_api']}</b></td>
                        <td><input id="ZC_API_ENABLE" name="ZC_API_ENABLE" type="text" value="{$zbp->option['ZC_API_ENABLE']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['enable_api_throttle']}</b></td>
                        <td><input id="ZC_API_THROTTLE_ENABLE" name="ZC_API_THROTTLE_ENABLE" type="text" value="{$zbp->option['ZC_API_THROTTLE_ENABLE']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['api_throttle_max_reqs_per_min']}</b></td>
                        <td><input id="ZC_API_THROTTLE_MAX_REQS_PER_MIN" name="ZC_API_THROTTLE_MAX_REQS_PER_MIN" type="text" value="{$zbp->option['ZC_API_THROTTLE_MAX_REQS_PER_MIN']}" class="input-90" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['api_display_count']}</b></td>
                        <td><input id="ZC_API_DISPLAY_COUNT" name="ZC_API_DISPLAY_COUNT" type="text" value="{$zbp->option['ZC_API_DISPLAY_COUNT']}" class="input-90" /></td>
                    </tr>
                </table>
            <!-- Tab 7 -->
            <div class="tab-content" id="tab7">
                <table class="table_hover table_striped tableFull">
                    <tr>
                        <td class="td25"><b>{$zbp->lang['msg']['ai_url']}</b></td>
                        <td><input id="ZC_AI_API_URL" name="ZC_AI_API_URL" type="text" value="{$zbp->option['ZC_AI_API_URL']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['ai_key']}</b></td>
                        <td><input id="ZC_AI_API_KEY" name="ZC_AI_API_KEY" type="text" value="{$zbp->option['ZC_AI_API_KEY']}" class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td><b>{$zbp->lang['msg']['ai_model']}</b></td>
                        <td><input id="ZC_AI_API_MODEL" name="ZC_AI_API_MODEL" type="text" value="{$zbp->option['ZC_AI_API_MODEL']}" class="input-90" /></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <p><input type="submit" class="button" value="{$zbp->lang['msg']['submit']}" id="btnPost" /></p>

</form>
