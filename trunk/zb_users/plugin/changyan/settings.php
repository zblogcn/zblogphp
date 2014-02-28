<?php
ini_set('max_execution_time', '0');
require_once CHANGYAN_PLUGIN_PATH . '/Handler.php';
$changyanPlugin = Changyan_Handler::getInstance();

include_once dirname(__FILE__) . '/header.html';
?>

<div class="margin heiti" style="width: 800px">
    <br /><br />
    <table>
        <tr>
            <td>
                <p class="start">&nbsp;</p>
            </td>
            <td>
                <h3>账号设置</h3>
            </td>
        </tr>
        <tr>
            <td />
            <td>
                <table>
                    <tr>
                        <td>APP ID:</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="appID"
                                   class="inputbox inputbox-disable"
                                   disabled="disabled"
                                   value="<?php
                                   $aScript = $changyanPlugin->getOption('changyan_script');
                                   if (!empty($aScript)) {
                                       $appID = explode("'", $aScript);
                                       $appID = $appID[1];
                                       echo trim($appID);
                                   }
                                   ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>APP KEY:</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="appKey"
                                   class="inputbox inputbox-disable"
                                   disabled="disabled"
                                   value="<?php
                                   $appKey = $changyanPlugin->getOption('changyan_appKey');
                                   if (!empty($appKey)) {
                                       echo trim($appKey);
                                   }
                                   ?>">
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: left;">
                            <input type="button" id="appButton"
                                   class="button button-rounded" value="修改"
                                   onclick="saveAppKey_AppID();return false;"
                                   style="width: 100px; text-align: center; vertical-align: middle" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br /><br />
    <table>
        <tr>
            <td>
                <p class="start">&nbsp;</p>
            </td>
            <td>
                <h3>数据同步</h3>
            </td>
        </tr>
        <tr>
            <td />
            <td>
                <table>
                    <tr>
                        <td>
                            <div id="cyan-WP2cyan">
                                <p class="message-start">
                                    <input type="button" id="appButton"
                                           class="button button-rounded button-primary" value="同步本地评论到畅言"
                                           onclick="sync2Cyan('F');"
                                           style="width: 160px; text-align: center; vertical-align: middle" />
                                </p>

                                <p class="status"></p>

                                <p class="message-complete">同步完成</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div id="cyan-export">
                                <p class="message-start">
                                    <input type="button" id="appButton"
                                           class="button button-rounded button-primary" value="同步畅言评论到本地"
                                           onClick="sync2WPress();return false;"
                                           style="width: 160px; text-align: center; vertical-align: middle" />
                                </p>

                                <p class="status"></p>

                                <p class="message-complete">同步完成</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" id="changyanCron" name="changyanCronCheckbox" value="1"
                                    <?php if (get_option('changyan_isCron')) echo 'checked'; ?> /> 定时从畅言同步评论到本地
                            </label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<?php
include_once dirname(__FILE__) . '/scripts.html';
?>
